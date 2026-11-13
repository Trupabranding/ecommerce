# Quick Start - Herd eCommerce Storefront

## ✅ Prerequisites Verified

- [x] npm packages installed (120 packages)
- [x] All Vue components copied
- [x] All Inertia pages created
- [x] Footer component extracted
- [x] Toast notifications implemented
- [x] AppLayout with state management
- [x] Vite config updated for Vue 3
- [x] Tailwind CSS configured
- [x] TypeScript configured
- [x] Laravel Inertia middleware created
- [x] Routes added to web.php

---

## 🚀 Starting the Dev Servers

### Step 1: Ensure you're in the project root
```bash
cd /Users/tomc/Herd/ecom
```

### Step 2: Start Vite (Terminal 1)
```bash
npm run dev
```

Expected output:
```
  VITE v6.4.3  ready in XXX ms

  ➜  Local:   http://localhost:5173/
  ➜  press h to show help
```

### Step 3: Start Laravel (Terminal 2)
```bash
php artisan serve
```

Expected output:
```
   INFO  Server running on [http://127.0.0.1:8000].

   Press Ctrl+C to quit
```

### Step 4: Open in Browser
```
http://localhost:8000
```

---

## 🧪 Testing the Setup

### Test 1: Home Page Loads
- [ ] Navigate to http://localhost:8000
- [ ] See "HERD" logo in header
- [ ] See hero section
- [ ] See featured products
- [ ] Footer displays with newsletter signup
- [ ] No console errors

### Test 2: Navigation Works
- [ ] Click "SHOP ALL" in header
- [ ] Page transitions smoothly
- [ ] URL changes to /shop
- [ ] Product grid displays

### Test 3: Product Detail
- [ ] Click on a product card
- [ ] Product detail page loads
- [ ] Product images show
- [ ] Add to cart button present

### Test 4: Shopping Cart
- [ ] Click "Add to cart"
- [ ] Toast notification appears ("ADDED... TO BAG")
- [ ] Cart drawer opens automatically
- [ ] Cart count badge updates in header
- [ ] Close cart drawer
- [ ] Cart count stays (1)
- [ ] Refresh page
- [ ] Cart items persist (from localStorage)

### Test 5: Cart Operations
- [ ] Open cart drawer
- [ ] Increase quantity
- [ ] Decrease quantity
- [ ] Remove item
- [ ] Toast shows "REMOVED... FROM BAG"
- [ ] Cart updates correctly

### Test 6: Toast Notifications
- [ ] Toast appears at bottom center
- [ ] Message is visible
- [ ] Disappears after 3.5 seconds
- [ ] Multiple toasts queue properly

### Test 7: Footer
- [ ] Footer displays at bottom
- [ ] Newsletter email input works
- [ ] Join button responsive
- [ ] Navigation links present
- [ ] Social media icons display
- [ ] Currency selector works
- [ ] Scroll to top button works

### Test 8: Responsive Design
- [ ] Resize browser to mobile (< 640px)
- [ ] Navigation collapses
- [ ] Product grid shows 1 column
- [ ] All content readable
- [ ] Touch targets are large enough

---

## 🐛 Troubleshooting

### Vite Build Error
```
Error: Cannot find package '@vitejs/plugin-vue'
```
**Solution:** Run `npm install` again
```bash
npm install
```

### Inertia Rendering Error
```
Error: route is not defined
```
**Solution:** Ensure `HandleInertiaRequests` middleware is registered
- Check `app/Http/Middleware/HandleInertiaRequests.php` exists
- Verify middleware is in HTTP kernel

### CSS Not Loading
```
Tailwind styles missing
```
**Solution:** Ensure Vite is running and CSS is importing correctly
- Check `resources/css/app.css` has Tailwind directives
- Verify Vite is bundling CSS

### Components Not Found
```
Module '@/Components/Header.vue' has no default export
```
**Solution:** These are IDE type resolution issues, not runtime errors
- They'll resolve once dev server is fully running
- Check browser console - should be no actual errors

### No Toast Notifications
**Solution:** Check that Toast component is imported in AppLayout
- Verify `Toast.vue` exists in Components folder
- Ensure AppLayout has `<Toast />` element

### Cart Not Persisting
**Solution:** Check browser localStorage is enabled
- Open DevTools → Application → Local Storage
- Should see `herd_cart` key with JSON data

---

## 📊 Verifying the Setup

### Check File Structure
```bash
# Verify key files exist
ls -la resources/js/Layouts/AppLayout.vue
ls -la resources/js/Components/Footer.vue
ls -la resources/js/Pages/Home.vue
ls -la resources/views/app.blade.php
ls -la app/Http/Middleware/HandleInertiaRequests.php
```

### Check Routes
```bash
# List all routes
php artisan route:list | grep -E "GET|POST"
```

Expected routes:
```
GET    /              home
GET    /shop          shop.index
GET    /products/{id} products.show
GET    /checkout      checkout.index
GET    /order/{id}    orders.show
```

### Check npm Packages
```bash
# Verify Vue 3 is installed
npm ls vue @inertiajs/vue3 tailwindcss

# Should show:
# vue@3.4.x
# @inertiajs/vue3@2.0.x
# tailwindcss@3.4.x
```

---

## 🎯 Common First Steps

1. **Start dev servers** (both Vite and Laravel)
2. **Open http://localhost:8000** in browser
3. **Check browser DevTools console** for errors
4. **Test navigation** between Home and Shop
5. **Test add to cart** functionality
6. **Refresh page** to verify localStorage persistence
7. **Open browser DevTools** → Network tab to see Vite HMR

---

## 🔗 Useful Links

- **Vite:** http://localhost:5173 (dev server)
- **Laravel:** http://localhost:8000 (app)
- **DevTools Console:** Check for errors
- **Vite HMR:** Auto-refresh on file changes

---

## 📝 Next Steps After Verification

Once everything is working:

1. **Connect to API**
   - Create product endpoints
   - Create cart endpoints
   - Create order endpoints

2. **Implement Features**
   - Product filters/search
   - User authentication
   - Wishlist
   - Reviews/ratings

3. **Add Database**
   - Store products in DB
   - Store orders
   - Store user carts

4. **Optimize Performance**
   - Image optimization
   - Code splitting
   - Lazy loading
   - Caching

---

## 🆘 Get Help

### Check These Files
- `FULL_UI_INTEGRATION.md` - Complete documentation
- `DESIGN_SYSTEM.md` - Design specifications
- `INERTIA_SETUP.md` - Setup instructions

### Common Issues
- Type errors in IDE → ignore until build completes
- Dev server not starting → check port 5173 is free
- Routes not found → check `routes/web.php` routes
- Styles not applying → check Vite is running

---

**Status:** ✅ Ready for Development  
**Last Updated:** July 14, 2026

