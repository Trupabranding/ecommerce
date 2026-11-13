# Backend Integration Guide - Ecommerce-First Approach

**Status:** Planning Phase  
**Date:** July 14, 2026  
**Strategy:** Leverage existing DDD architecture for API-driven storefront

---

## 🎯 Strategy: Hybrid Approach

Given the existing domain-driven design (DDD) architecture with sophisticated models in `/domain`, we'll:

1. **Keep existing DDD models** (Domain\Shop\Product, Domain\Shop\Category, etc.)
2. **Create API routes** for storefront consumption
3. **Use API Resources** to format data for frontend
4. **Build seeders** using existing domain models
5. **Connect Vue frontend** to backend APIs instead of hardcoded data

---

## 📋 Integration Steps

### Phase 1: API Endpoints (This Week)

#### Create storefront API routes
```php
// routes/api.php
Route::prefix('storefront')->group(function () {
    // Products
    Route::get('/products', 'ProductController@index');
    Route::get('/products/{id}', 'ProductController@show');
    Route::get('/products/category/{category}', 'ProductController@byCategory');
    
    // Categories
    Route::get('/categories', 'CategoryController@index');
    
    // Cart (session-based for now)
    Route::post('/cart/add', 'CartController@add');
    Route::put('/cart/update/{id}', 'CartController@update');
    Route::delete('/cart/{id}', 'CartController@remove');
    
    // Orders
    Route::post('/orders', 'OrderController@store');
    Route::get('/orders/{id}', 'OrderController@show');
});
```

#### Create API Controllers
- `ProductController@index` - Get all products with pagination
- `ProductController@show` - Get single product with related items
- `ProductController@byCategory` - Filter by category
- `CategoryController@index` - Get all categories
- `OrderController@store` - Create order from cart
- `OrderController@show` - Get order details

#### Create API Resources
- `ProductResource` - Format product for API
- `CategoryResource` - Format category for API  
- `OrderResource` - Format order for API
- `OrderItemResource` - Format order items

### Phase 2: Frontend API Integration (Next Week)

#### Create API composables (Vue)
```javascript
// resources/js/composables/useProducts.js
export const useProducts = () => {
  const products = ref([])
  
  const fetchAll = async () => {
    const response = await fetch('/api/storefront/products')
    products.value = await response.json()
  }
  
  return { products, fetchAll }
}
```

#### Update Vue components
- Remove hardcoded `PRODUCTS` from `products.ts`
- Replace with API calls via composables
- Add loading states
- Add error handling

#### Update Inertia pages
- Update `Home.vue` to fetch featured products
- Update `Shop.vue` to fetch products with filters
- Update `Product.vue` to fetch single product + related
- Update `Category.vue` to fetch category products

### Phase 3: Cart & Orders (Week 3)

#### Implement cart API
- POST `/api/storefront/cart/add` - Add item
- PUT `/api/storefront/cart/update/{id}` - Update quantity
- DELETE `/api/storefront/cart/{id}` - Remove item
- GET `/api/storefront/cart` - Get current cart

#### Implement orders
- POST `/api/storefront/orders` - Create order
- GET `/api/storefront/orders/{id}` - Get order confirmation
- Store shipping address, payment method, totals

---

## 🔌 Data Flow Diagram

```
Vue Components
    ↓
Composables (useProducts, useCart, useOrders)
    ↓
API Routes (/api/storefront/...)
    ↓
Controllers (ProductController, OrderController, etc.)
    ↓
Domain Models (Domain\Shop\Product, etc.)
    ↓
Database
```

---

## 📝 Implementation Checklist

### Controllers (Create in `app/Http/Controllers/Storefront/`)

- [ ] `ProductController`
  - `index()` - Paginated list
  - `show($id)` - Single product + related items
  - `byCategory($category)` - Filter by category
  - Apply filters, sorting, search

- [ ] `CategoryController`
  - `index()` - Get all categories

- [ ] `CartController`
  - `add()` - Add to session-based cart
  - `update($id)` - Update quantity
  - `remove($id)` - Remove item

- [ ] `OrderController`
  - `store()` - Create order from cart
  - `show($id)` - Get order details
  - Generate order number, totals

### API Resources (Create in `app/Http/Resources/`)

- [ ] `ProductResource`
- [ ] `CategoryResource`
- [ ] `OrderResource`
- [ ] `OrderItemResource`

### Vue Composables (Create in `resources/js/composables/`)

- [ ] `useProducts.js` - Fetch products, search, filter
- [ ] `useCategories.js` - Fetch categories
- [ ] `useCart.js` - Manage cart operations
- [ ] `useOrders.js` - Create and retrieve orders

### Route Updates

- [ ] Update `routes/api.php` with storefront routes
- [ ] Add middleware for API (throttle, CORS if needed)

### Frontend Updates

- [ ] Remove hardcoded `PRODUCTS` from `products.ts`
- [ ] Update `Home.vue` - fetch featured products
- [ ] Update `Shop.vue` - fetch all products
- [ ] Update `Product.vue` - fetch single + related
- [ ] Update `Category.vue` - fetch by category
- [ ] Update `CartDrawer.vue` - use cart API
- [ ] Update `CheckoutView.vue` - submit order via API

---

## 🗄️ Existing Domain Models to Use

```
Domain\Shop\Product\Models\Product
Domain\Shop\Category\Models\Category
Domain\Shop\Order\Models\Order
Domain\Shop\Order\Models\OrderItem
Domain\Shop\Branch\Models\Branch
Domain\Shop\Brand\Models\Brand
Domain\Shop\Customer\Models\Customer
```

These models have:
- Relationships already configured
- Scopes for filtering
- Events for logging
- Actions for complex operations

**DO NOT** create duplicate models. Build APIs around existing domain models.

---

## 🔐 Security Considerations

- [ ] Validate all product IDs exist before returning
- [ ] Enforce order permissions (customers can only see their orders)
- [ ] Rate limit cart operations to prevent abuse
- [ ] Validate addresses and payment info server-side
- [ ] Use CSRF tokens for state-changing operations
- [ ] Log all orders and transactions

---

## 📊 API Response Format

All endpoints return JSON in this format:

```json
{
  "data": { /* resource or array of resources */ },
  "meta": {
    "page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

Error responses:

```json
{
  "message": "Product not found",
  "errors": { /* validation errors */ }
}
```

---

## 🧪 Testing Approach

### Unit Tests
- Test controllers return correct resources
- Test API resources format data properly

### Feature Tests
- Test full API workflows (list → detail → add to cart → checkout)
- Test validation and error cases
- Test permissions and access control

### Frontend Tests (Using @testing-library/vue)
- Test composables fetch data correctly
- Test components render API responses
- Test error states and loading states

---

## 🚀 Deployment Checklist

Before going live:

- [ ] All API endpoints tested
- [ ] Rate limiting configured
- [ ] CORS configured if needed
- [ ] Frontend points to `/api/storefront/...`
- [ ] Error handling on frontend
- [ ] Loading states on all async operations
- [ ] Cart persists correctly
- [ ] Orders save to database
- [ ] Analytics tracking in place

---

## 📈 Performance Optimization

- **Pagination**: Use Laravel's paginate on products
- **Eager Loading**: Use `with()` to load relationships
- **Caching**: Cache category list, popular products
- **Lazy Loading**: Load related products only when needed
- **Compression**: API responses are JSON (inherently compact)

---

## 🔄 Data Sync Strategy

### Cart
- Session-based initially (no login required)
- Upgrade to database if user logs in
- LocalStorage backup for guest cart

### Orders
- Always saved to database immediately
- Track by email or customer ID
- Send confirmation email (using existing mail system)

### Inventory
- Check stock before adding to cart
- Reserve stock when order placed
- Update stock when order ships

---

## 📞 Next Actions

1. Create `/app/Http/Controllers/Storefront/` directory
2. Create API controllers following Laravel conventions
3. Create API resources to format responses
4. Add API routes to `routes/api.php`
5. Create Vue composables for API calls
6. Update components to use APIs instead of hardcoded data
7. Test full workflow end-to-end

---

**Status:** Ready for implementation  
**Estimated Effort:** 40-50 hours across 3 weeks  
**Next Phase:** Begin with ProductController and API resources

