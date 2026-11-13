# Storefront API Integration Guide

**Status:** API infrastructure complete  
**Date:** July 14, 2026  
**Architecture:** REST API with domain models

---

## 🎯 Overview

The storefront API is built on the existing domain-driven architecture. It exposes product, category, and order data through clean REST endpoints, allowing the Vue frontend to fetch dynamic data instead of using hardcoded values.

### Key Design Decisions

✅ **Leverage existing domain models** - Uses `Domain\Shop\Product`, `Domain\Shop\Category`, `Domain\Shop\Order`  
✅ **Minimal API layer** - Controllers format domain data as JSON  
✅ **No duplication** - Works with existing ProductSeeder and domain relationships  
✅ **Product variants via SKUs** - Uses existing `Domain\Shop\Product\Models\Sku` for pricing and attributes  
✅ **Media via MediaLibrary** - Images handled by Spatie MediaLibrary (existing setup)  

---

## 📡 API Endpoints

All endpoints are prefixed with `/api/storefront/`

### Products

**GET `/api/storefront/products`**
- List all active products with pagination
- Query params: `per_page=15`, `sort=price_asc|price_desc|newest|popular`, `category=name`, `search=query`
- Response: paginated product list with category, brand, SKUs, media

**GET `/api/storefront/products/{uuid}`**
- Get single product with related items from same category
- Returns: product + 4 related products

**GET `/api/storefront/category/{category}`**
- Get all products in a category
- Param: `{category}` can be category name or UUID
- Response: paginated product list

### Categories

**GET `/api/storefront/categories`**
- List all visible root categories
- Response: categories with image URLs and product counts

**GET `/api/storefront/categories/{uuid}`**
- Get single category details
- Returns: category metadata + product count

### Orders

**POST `/api/storefront/orders`**
- Create new order from cart
- Body: email, shipping_address, billing_address, payment_method, items array, totals
- Response: created order with order_number

**GET `/api/storefront/orders/{orderNumber}`**
- Get order details by order number
- Returns: order info + order items

---

## 🔗 Response Format

All successful responses follow this format:

```json
{
  "data": { /* single resource */ },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

Single resource endpoints:

```json
{
  "data": { /* resource */ },
  "related": [ /* related items if applicable */ ]
}
```

---

## 📦 Product Response Structure

```json
{
  "uuid": "uuid-string",
  "name": "Product Name",
  "description": "Long description",
  "category": {
    "uuid": "category-uuid",
    "name": "Category Name",
    "description": "Category desc"
  },
  "brand": {
    "uuid": "brand-uuid",
    "name": "Brand Name"
  },
  "skus": [
    {
      "uuid": "sku-uuid",
      "price": 10000,
      "attribute_options": [
        {
          "name": "Color",
          "value": "Red",
          "field_type": "color_picker"
        },
        {
          "name": "Size",
          "value": "M",
          "field_type": "text"
        }
      ]
    }
  ],
  "media": [
    {
      "id": 1,
      "name": "product-image.jpg",
      "url": "https://..."
    }
  ]
}
```

---

## 🛠️ Frontend Integration

### Vue Composables

Create API service layer with composables in `resources/js/composables/`:

**`useProducts.js`**
```javascript
import { ref } from 'vue'

export const useProducts = () => {
  const products = ref([])
  const loading = ref(false)
  const error = ref(null)

  const fetchAll = async (page = 1, perPage = 15, filters = {}) => {
    loading.value = true
    try {
      const params = new URLSearchParams({
        page,
        per_page: perPage,
        ...filters
      })
      const response = await fetch(`/api/storefront/products?${params}`)
      const json = await response.json()
      products.value = json.data
      return json.meta
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  const fetchOne = async (uuid) => {
    loading.value = true
    try {
      const response = await fetch(`/api/storefront/products/${uuid}`)
      return await response.json()
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  return { products, loading, error, fetchAll, fetchOne }
}
```

**`useCategories.js`**
```javascript
import { ref } from 'vue'

export const useCategories = () => {
  const categories = ref([])

  const fetchAll = async () => {
    const response = await fetch('/api/storefront/categories')
    const json = await response.json()
    categories.value = json.data
  }

  return { categories, fetchAll }
}
```

**`useCart.js`** (session-based for now)
```javascript
import { ref, computed } from 'vue'

export const useCart = () => {
  const items = ref([])

  const addItem = (product, sku, quantity = 1) => {
    items.value.push({
      product_id: product.uuid,
      product_name: product.name,
      product_price: sku.price,
      sku_id: sku.uuid,
      size: sku.attributes.find(a => a.name === 'Size')?.value,
      color: sku.attributes.find(a => a.name === 'Color')?.value,
      quantity,
      total: sku.price * quantity,
    })
  }

  const subtotal = computed(() => 
    items.value.reduce((sum, item) => sum + item.total, 0)
  )

  return { items, addItem, subtotal }
}
```

**`useOrders.js`**
```javascript
import { ref } from 'vue'

export const useOrders = () => {
  const order = ref(null)
  const loading = ref(false)

  const create = async (data) => {
    loading.value = true
    try {
      const response = await fetch('/api/storefront/orders', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      order.value = await response.json()
      return order.value
    } finally {
      loading.value = false
    }
  }

  const fetch = async (orderNumber) => {
    const response = await fetch(`/api/storefront/orders/${orderNumber}`)
    return await response.json()
  }

  return { order, loading, create, fetch }
}
```

---

## 🔄 Component Integration

### Home.vue - Featured Products

```vue
<script setup>
import { onMounted, ref } from 'vue'

const products = ref([])

onMounted(async () => {
  const response = await fetch('/api/storefront/products?sort=newest&per_page=4')
  const json = await response.json()
  products.value = json.data
})
</script>
```

### Shop.vue - Product Catalog

```vue
<script setup>
import { ref, computed } from 'vue'

const products = ref([])
const currentPage = ref(1)
const filters = ref({ sort: 'newest', category: null })

const loadProducts = async () => {
  const params = new URLSearchParams({
    page: currentPage.value,
    per_page: 15,
    ...filters.value
  })
  const response = await fetch(`/api/storefront/products?${params}`)
  const json = await response.json()
  products.value = json.data
}

onMounted(() => loadProducts())
</script>
```

### Product.vue - Single Product Detail

```vue
<script setup>
const route = useRoute()

const { data: productData } = await fetch(`/api/storefront/products/${route.params.id}`).then(r => r.json())
const product = productData.product
const related = productData.related
</script>
```

### Category.vue - Category Page

```vue
<script setup>
const route = useRoute()

const loadCategory = async () => {
  const response = await fetch(`/api/storefront/category/${route.params.category}`)
  const json = await response.json()
  // Use json.data for products
}
</script>
```

### CartDrawer.vue - Cart Management

```vue
<script setup>
const submitOrder = async () => {
  const orderData = {
    email: form.email,
    shipping_address: form.shipping,
    billing_address: form.billing,
    payment_method: 'card',
    items: cart.items,
    subtotal: cart.subtotal,
    tax: cart.tax,
    shipping: cart.shipping,
    discount: cart.discount,
    discount_code: promoCode.value
  }
  
  const response = await fetch('/api/storefront/orders', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(orderData)
  })
  
  const result = await response.json()
  // Redirect to order confirmation page
  window.location.href = `/order/${result.order_number}`
}
</script>
```

---

## 🔐 CORS & Security

- API runs on `/api` prefix (same domain)
- No CORS issues for same-domain requests
- All POST endpoints accept JSON
- Validation happens server-side
- Rate limiting applies to order creation endpoint

---

## 🧪 Testing the API

Using curl or Postman:

```bash
# Get all products
curl http://localhost:8000/api/storefront/products

# Get single product
curl http://localhost:8000/api/storefront/products/{uuid}

# Get products by category
curl http://localhost:8000/api/storefront/category/Outerwear

# Get all categories
curl http://localhost:8000/api/storefront/categories

# Create order
curl -X POST http://localhost:8000/api/storefront/orders \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "shipping_address": {...},
    "items": [...],
    "subtotal": 10000,
    "tax": 800,
    "shipping": 1500
  }'
```

---

## 📋 Implementation Checklist

### Backend (Already Complete)
- [x] Create API controllers in `app/Http/Controllers/Api/Storefront/`
- [x] ProductController with index, show, byCategory methods
- [x] CategoryController with index and show methods
- [x] OrderController with store and show methods
- [x] Register API routes in `routes/api.php`
- [x] Add API route registration in `bootstrap/app.php`

### Frontend (Next Steps)
- [ ] Create composables in `resources/js/composables/`
- [ ] Update Home.vue to fetch featured products from `/api/storefront/products`
- [ ] Update Shop.vue to use ProductController API
- [ ] Update Product.vue to fetch single product + related items
- [ ] Update Category.vue to use byCategory endpoint
- [ ] Update CartDrawer.vue to submit orders to POST `/api/storefront/orders`
- [ ] Remove hardcoded `PRODUCTS` array from `products.ts`
- [ ] Test all workflows end-to-end

### Quality Assurance
- [ ] Load test API with multiple concurrent requests
- [ ] Verify pagination works correctly
- [ ] Test filtering and sorting on products
- [ ] Verify orders persist to database
- [ ] Test error handling and validation

---

## 🚀 Deployment Notes

1. **Database**: Ensure `CategorySeeder` and `ProductSeeder` have run to populate data
2. **Routes**: API routes are registered via `bootstrap/app.php`, no additional config needed
3. **Middleware**: API endpoints run through default middleware stack, no special auth required for now
4. **Rate Limiting**: Add throttle middleware if needed (e.g., for order creation)
5. **Logging**: All API requests automatically logged by Laravel's request/response middleware

---

## 📈 Performance Optimization

- **Eager loading**: Controllers use `with()` to eager load relationships
- **Pagination**: Default 15 items per page, configurable via query param
- **Caching**: Consider adding cache tags to category list (doesn't change often)
- **JSON responses**: Automatically compressed by Laravel

---

## 🔮 Future Enhancements

- Add wishlist endpoints
- Implement review/rating endpoints
- Add search/autocomplete endpoint
- Implement customer authentication
- Add order tracking endpoint
- Create admin endpoints for analytics
- Implement GraphQL alternative to REST API

---

**Next Phase:** Update frontend components to use API endpoints instead of hardcoded data

