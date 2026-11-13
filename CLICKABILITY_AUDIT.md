# Clickability Audit - Issues and Fixes

**Date:** July 14, 2026  
**Status:** Issues identified and ready to fix

---

## 🚨 Issues Found

### Issue 1: Shop.vue Missing Route Import
**Severity:** HIGH  
**Location:** `resources/js/Pages/Shop.vue:6`  
**Problem:**
```javascript
window.location.href = route('products.show', { id: product.id });
// Error: route() is not defined
```
**Fix:** Import `route` from `@inertiajs/vue3`

### Issue 2: StorefrontLayout Navigation Broken
**Severity:** HIGH  
**Location:** `resources/js/Layouts/StorefrontLayout.vue:47`  
**Problem:**
```javascript
function handleNavigate(view: string) {
  currentView.value = view;  // Only updates a ref, doesn't navigate!
}
```
**Fix:** Actually navigate to pages using `route()` helper

### Issue 3: Cart Icon Not Functional
**Severity:** MEDIUM  
**Location:** `resources/js/Components/Header.vue:104`  
**Problem:** Cart icon emits `open-cart` event which is handled by layout, but navigation logic in layout is broken, so cart modal might not open properly

**Fix:** Ensure StorefrontLayout properly handles `open-cart` event (it does, but test it)

### Issue 4: Product Card Emit Mismatch
**Severity:** MEDIUM  
**Location:** `resources/js/Components/ProductCard.vue:42,99,109`  
**Problem:** ProductCard emits `select` events, but ShopCatalogView listens for them and re-emits as `select-product`. Shop.vue should listen to `select-product`.

**Status:** Actually this is working correctly - the event chain is:
- ProductCard emits `select`
- ShopCatalogView re-emits as `select-product`
- Shop.vue listens to `select-product`

But Shop.vue handler is broken due to missing `route` import.

---

## ✅ Required Fixes

### Fix 1: Add Route Import to Shop.vue
```typescript
import { route } from '@inertiajs/vue3';
```

### Fix 2: Add Route Import to StorefrontLayout.vue
```typescript
import { router } from '@inertiajs/vue3';
```

### Fix 3: Update StorefrontLayout Navigation Handler
```javascript
function handleNavigate(view: string) {
  currentView.value = view;
  // Actually navigate to the page
  if (view === 'home') {
    router.visit(route('home'));
  } else if (view === 'shop') {
    router.visit(route('shop.index'));
  }
}
```

### Fix 4: Add Product Navigation to Shop.vue
```javascript
function handleSelectProduct(product: any) {
  router.visit(route('products.show', { id: product.id }));
}
```

### Fix 5: Verify Cart Operations
The cart should be working since:
- Header emits `open-cart` ✓
- Layout listens to it and sets `isCartOpen = true` ✓
- CartDrawer is conditionally rendered ✓

But test it anyway.

---

## 📋 Component Navigation Flow

### Current (Broken) Flow:
```
Header Button Click
    ↓
Emits 'navigate' event with view='home'|'shop'
    ↓
StorefrontLayout.handleNavigate()
    ↓
Updates currentView ref (NO PAGE NAVIGATION)
    ↓
❌ Page doesn't change
```

### Fixed Flow:
```
Header Button Click
    ↓
Emits 'navigate' event with view='home'|'shop'
    ↓
StorefrontLayout.handleNavigate()
    ↓
1. Updates currentView ref
2. Calls router.visit() to actual Laravel route
    ↓
✅ Page navigates
```

### Product Selection Flow (Broken):
```
ProductCard Click
    ↓
Emits 'select' event
    ↓
ShopCatalogView re-emits 'select-product'
    ↓
Shop.vue.handleSelectProduct()
    ↓
Tries to call route('products.show', { id })
    ↓
❌ route() is not defined - ERROR
```

### Product Selection Flow (Fixed):
```
ProductCard Click
    ↓
Emits 'select' event
    ↓
ShopCatalogView re-emits 'select-product'
    ↓
Shop.vue.handleSelectProduct()
    ↓
Calls router.visit(route('products.show', { id }))
    ↓
✅ Navigates to product detail page
```

---

## 🔍 Verification Checklist

- [ ] Shop.vue imports `route` from '@inertiajs/vue3'
- [ ] StorefrontLayout.vue imports `router` from '@inertiajs/vue3'
- [ ] StorefrontLayout.handleNavigate() calls router.visit()
- [ ] Shop.vue.handleSelectProduct() calls router.visit()
- [ ] Test navbar HOME button - should navigate to home
- [ ] Test navbar SHOP ALL button - should navigate to shop
- [ ] Test product card click - should navigate to product detail
- [ ] Test product card "VIEW DETAILS" link - should navigate to product detail
- [ ] Test quick add button - should show in cart
- [ ] Test cart icon click - should open cart drawer

---

## 📝 Test Cases

### Test 1: Navbar HOME Click
1. Navigate to Shop page
2. Click HOME button in navbar
3. Expected: Navigate to home page with featured products

### Test 2: Navbar SHOP ALL Click
1. On home page
2. Click SHOP ALL button
3. Expected: Navigate to shop page with all products

### Test 3: Product Card Click
1. On shop page
2. Click any product card image or title
3. Expected: Navigate to product detail page for that product

### Test 4: View Details Link
1. On shop page
2. Click "VIEW DETAILS →" link
3. Expected: Navigate to product detail page

### Test 5: Quick Add
1. On shop page
2. Hover over product card
3. Click "QUICK ADD" button
4. Expected: Product added to cart, cart count increments, toast shown

### Test 6: Cart Icon
1. Add item to cart (from quick add or product details)
2. Click shopping bag icon in header
3. Expected: Cart drawer opens showing items

---

## 🎯 Impact

**Before Fix:**
- ❌ Navbar links don't work
- ❌ Product cards can't be clicked
- ❌ Cart icon might not work
- ❌ Can't navigate anywhere

**After Fix:**
- ✅ Navbar links navigate properly
- ✅ Product cards are fully clickable
- ✅ Cart icon opens cart drawer
- ✅ Full navigation flow working

---

## 📊 Summary

| Component | Issue | Status |
|-----------|-------|--------|
| Header | Navbar buttons emit but layout doesn't navigate | Ready to fix |
| ProductCard | Emits select event correctly | Working (blocked by Shop.vue) |
| ShopCatalogView | Re-emits events correctly | Working |
| Shop.vue | Missing route import, handler broken | Ready to fix |
| StorefrontLayout | Navigation handler incomplete | Ready to fix |
| CartDrawer | Conditional rendering works | Should work |

**Total Issues:** 4  
**Severity:** 2 HIGH, 2 MEDIUM  
**Estimated Fix Time:** 15-30 minutes

