# Storefront Backend API - Ready for Integration

**Status:** ✅ Complete - Ready for frontend integration  
**Date:** July 14, 2026  
**Version:** 1.0

---

## 📋 What's Complete

### ✅ Backend API Infrastructure
- [x] Created API controllers for Products, Categories, Orders
- [x] Registered API routes in `routes/api.php`
- [x] Configured API route loading in `bootstrap/app.php`
- [x] Controllers use existing domain models (no duplication)
- [x] All endpoints properly documented

### ✅ Vue Composables
- [x] `useProducts.js` - Fetch products with search, filter, sort
- [x] `useCategories.js` - Fetch all categories
- [x] `useOrders.js` - Create orders and fetch order details
- [x] Error handling and loading states included

### ✅ Database Seeders
- [x] Removed simple models (`App\Product`, `App\Category`, etc.)
- [x] Removed conflicting migrations
- [x] Updated `EcommerceStorefrontSeeder` to work with domain models
- [x] Removed `CategorySeeder` and `EcommerceProductSeeder` that caused conflicts

### ✅ Documentation
- [x] API_INTEGRATION.md - Comprehensive API documentation
- [x] All endpoints documented with examples
- [x] Vue component integration patterns included
- [x] Testing instructions provided

---

## 🎯 API Endpoints Ready

All endpoints prefixed with `/api/storefront/`

### Products
```
GET /api/storefront/products              # List all products (paginated)
GET /api/storefront/products/{uuid}       # Single product + related items
GET /api/storefront/category/{name}       # Products by category
```

Query parameters:
- `page=1` - Pagination page
- `per_page=15` - Items per page
- `sort=newest|price_asc|price_desc|popular`
- `search=query` - Search products
- `category=name` - Filter by category

### Categories
```
GET /api/storefront/categories            # List all categories
GET /api/storefront/categories/{uuid}     # Single category
```

### Orders
```
POST /api/storefront/orders               # Create order
GET /api/storefront/orders/{orderNumber}  # Get order details
```

---

## 💻 Frontend Components Ready to Update

The following Vue components need to be updated to use the API instead of hardcoded data:

### 1. **Home.vue** - Featured Products
```javascript
// Current: Uses PRODUCTS array
// Update: Use useProducts().fetchAll() with sort=newest

import { useProducts } from '@/composables/useProducts'

onMounted(async () => {
  const { products } = useProducts()
  await products.fetchAll(1, 4, { sort: 'newest' })
})
```

### 2. **Shop.vue** - Product Catalog with Filtering
```javascript
// Current: Filters hardcoded PRODUCTS array
// Update: Use API with filters

import { useProducts } from '@/composables/useProducts'

const products = useProducts()

const handleSort = async (sortBy) => {
  await products.sort(sortBy)
}

const handleSearch = async (query) => {
  await products.search(query)
}
```

### 3. **Product.vue** - Product Details
```javascript
// Current: Looks up product by ID in PRODUCTS array
// Update: Fetch from API

import { useProducts } from '@/composables/useProducts'
import { useRoute } from 'vue-router'

const route = useRoute()
const { currentProduct, relatedProducts } = useProducts()

onMounted(async () => {
  await currentProduct.fetchOne(route.params.id)
})
```

### 4. **Category.vue** - Category Page
```javascript
// Current: Filters PRODUCTS array by category
// Update: Use category API endpoint

import { useProducts } from '@/composables/useProducts'
import { useRoute } from 'vue-router'

const route = useRoute()
const { products } = useProducts()

onMounted(async () => {
  await products.fetchByCategory(route.params.category)
})
```

### 5. **CartDrawer.vue** - Order Submission
```javascript
// Current: Logs to console
// Update: POST to orders API

import { useOrders } from '@/composables/useOrders'

const { create, orderNumber } = useOrders()

const submitOrder = async () => {
  const orderData = {
    email: form.value.email,
    shipping_address: form.value.shipping,
    billing_address: form.value.billing,
    payment_method: 'card',
    items: cart.value.items,
    subtotal: totalSubtotal.value,
    tax: totalTax.value,
    shipping: totalShipping.value,
    discount: totalDiscount.value,
    discount_code: promoCode.value,
  }

  try {
    await create(orderData)
    // Redirect to order confirmation
    window.location.href = `/order/${orderNumber.value}`
  } catch (error) {
    showError(error.message)
  }
}
```

---

## 🚀 Quick Start

### 1. Test the API

```bash
# Get all products
curl http://localhost:8000/api/storefront/products

# Get single product
curl http://localhost:8000/api/storefront/products/product-uuid

# Get products by category
curl http://localhost:8000/api/storefront/category/Outerwear

# Get categories
curl http://localhost:8000/api/storefront/categories
```

### 2. Update Components

Start with one component at a time:

1. Home.vue → Use `useProducts().fetchAll()` for featured products
2. Shop.vue → Use `useProducts()` with filters and sorting
3. Product.vue → Use `useProducts().fetchOne()` for details
4. Category.vue → Use `useProducts().fetchByCategory()`
5. CartDrawer.vue → Use `useOrders().create()` to submit orders

### 3. Test End-to-End

1. Navigate to Home - should load products from API
2. Navigate to Shop - should show all products with working filters
3. Click product - should show details + related items from API
4. Add to cart - should work as before
5. Checkout - should submit order to API

---

## 🔍 Data Flow

```
Vue Component
    ↓
composable (useProducts, useCategories, useOrders)
    ↓
Fetch API (/api/storefront/...)
    ↓
Controller (ProductController, CategoryController, OrderController)
    ↓
Domain Models (Domain\Shop\Product, Domain\Shop\Category, etc.)
    ↓
Database
```

---

## ✨ Benefits of This Approach

1. **No Duplication** - Uses existing domain models
2. **Type Safe** - Domain models have full type hints
3. **Relationship Loading** - Eloquent eager loads categories, brands, SKUs
4. **Media Handling** - Uses Spatie MediaLibrary for images
5. **Activity Logging** - Orders automatically logged
6. **Scalable** - Can add filters, search, sorting without changing models
7. **Admin Compatible** - Same data used by admin panels

---

## 📊 What Was Removed

These files were conflicting with the existing domain architecture and have been removed:

- ❌ `app/Product.php` - Simple model (use Domain\Shop\Product instead)
- ❌ `app/Category.php` - Simple model (use Domain\Shop\Category instead)
- ❌ `app/Order.php` - Simple model (not yet needed)
- ❌ `app/OrderItem.php` - Simple model (not yet needed)
- ❌ `database/migrations/*products*` - Conflicting migrations
- ❌ `database/migrations/*categories*` - Conflicting migrations
- ❌ `database/migrations/*orders*` - Conflicting migrations
- ❌ `database/seeders/EcommerceProductSeeder.php` - Used simple models
- ❌ `database/seeders/CategorySeeder.php` - Had schema mismatches

**Why?** The existing domain-driven architecture is more sophisticated with UUIDs, MediaLibrary integration, and proper relationships. Creating simple duplicate models caused conflicts.

---

## 🎓 Learning Resources

- API_INTEGRATION.md - Full API documentation
- Each composable has JSDoc comments explaining functions
- Controllers show how to use domain models with API responses

---

## ✅ Next Steps (Frontend Work)

**Priority 1 - Core Integration:**
1. Update Home.vue to use `useProducts().fetchAll()`
2. Update Shop.vue to use filtered API calls
3. Update Product.vue to fetch from `/api/storefront/products/{id}`
4. Update Category.vue to use `/api/storefront/category/{name}`
5. Update CartDrawer.vue to POST to `/api/storefront/orders`

**Priority 2 - Enhancement:**
6. Add loading spinners during API calls
7. Add error messages when API fails
8. Add pagination controls to product listings
9. Implement proper order confirmation page
10. Add breadcrumb navigation

**Priority 3 - Polish:**
11. Cache category list (changes rarely)
12. Prefetch related products on navigation
13. Add product quick view modal
14. Implement wishlist functionality
15. Add reviews/ratings

---

## 📞 API Support

If an API endpoint doesn't return expected data:

1. Check the response format in API_INTEGRATION.md
2. Verify the product/category exists in database
3. Use `php artisan tinker` to inspect domain models directly
4. Check controller logic in `app/Http/Controllers/Api/Storefront/`

Example in tinker:
```php
>>> $product = \Domain\Shop\Product\Models\Product::first()
>>> $product->skus()->with('attributeOptions')->get()
>>> $product->getFirstMediaUrl('image')
```

---

## 🎉 Summary

The backend API infrastructure is **complete and ready**. All endpoints are functional and working with the existing domain models. Frontend components now just need to be updated to call these endpoints instead of using hardcoded data.

**Estimated remaining frontend work:** 4-6 hours to update all 5 main components + testing

