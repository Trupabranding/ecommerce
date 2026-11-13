# Inertia.js Vue 3 Setup - Herd eCommerce Storefront

## ✅ What's Been Completed

### 1. **Vue Components Imported**
All components from `herd-minimalist-e-commerce` have been copied to:
- `resources/js/Components/` (8 Vue files)
  - `Header.vue` - Navigation header with cart badge
  - `HomeHeroView.vue` - Hero section for homepage
  - `ShopCatalogView.vue` - Product catalog grid
  - `ProductDetailView.vue` - Single product detail page
  - `CartDrawer.vue` - Shopping cart drawer
  - `CheckoutView.vue` - Checkout form
  - `OrderConfirmationView.vue` - Order confirmation page
  - `ProductCard.vue` - Individual product card component

### 2. **Inertia Pages Created**
Professional page wrappers in `resources/js/Pages/`:
- `Home.vue` - Homepage
- `Shop.vue` - Shop catalog page
- `Product.vue` - Product detail page
- `Checkout.vue` - Checkout page
- `Order.vue` - Order confirmation page

### 3. **Layout System**
- `resources/js/Layouts/AppLayout.vue` - Base layout wrapper

### 4. **Build Configuration**
✅ `vite.config.js` - Updated with Vue 3 plugin  
✅ `tailwind.config.js` - Configured with brand colors (cream, dark, orange)  
✅ `postcss.config.js` - PostCSS setup for Tailwind  
✅ `tsconfig.json` - TypeScript configuration  
✅ `package.json` - Updated with dependencies:
  - `@inertiajs/vue3`
  - `vue` 3.4
  - `lucide-vue-next` (icons)
  - `tailwindcss` 3.4
  - `@vitejs/plugin-vue`

### 5. **Laravel Integration**
✅ `app/Http/Middleware/HandleInertiaRequests.php` - Inertia middleware  
✅ `resources/views/app.blade.php` - Main Blade template for Inertia  
✅ `routes/web.php` - Added storefront routes:
  - `GET /` → Home page
  - `GET /shop` → Shop catalog
  - `GET /products/{id}` → Product detail
  - `GET /checkout` → Checkout page
  - `GET /order/{id}` → Order confirmation

### 6. **Styling**
✅ `resources/css/app.css` - Tailwind imports + design system CSS  
✅ `resources/css/design-system.css` - Minimalist design system (from Vue project)

### 7. **Utilities**
✅ `resources/js/products.ts` - Product data utilities  
✅ `resources/js/types.ts` - TypeScript type definitions

---

## 🚀 Next Steps - Getting Started

### Step 1: Install Dependencies
```bash
cd /Users/tomc/Herd/ecom
npm install
```

### Step 2: Add Middleware to HTTP Kernel
Edit `app/Http/Middleware/Kernel.php` and add to `$routeMiddleware`:
```php
'inertia' => \App\Http\Middleware\HandleInertiaRequests::class,
```

### Step 3: Register Middleware in Route
Update `routes/web.php` to wrap routes with middleware:
```php
Route::middleware(['inertia'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home', [
            'cart' => [],
            'products' => [],
        ]);
    })->name('home');
    // ... other routes
});
```

### Step 4: Start Dev Servers (Two terminals)
```bash
# Terminal 1 - Vite dev server
npm run dev

# Terminal 2 - Laravel server
php artisan serve
```

### Step 5: Visit in Browser
```
http://localhost:8000
```

---

## 📁 Project Structure

```
resources/
├── css/
│   ├── app.css (main CSS with Tailwind imports)
│   ├── design-system.css (minimalist design variables)
│   └── settings.css (existing Filament styles)
├── js/
│   ├── app.js (Inertia initialization)
│   ├── bootstrap.js (axios setup)
│   ├── products.ts (product utilities)
│   ├── types.ts (TypeScript definitions)
│   ├── Components/
│   │   ├── Header.vue
│   │   ├── HomeHeroView.vue
│   │   ├── ShopCatalogView.vue
│   │   ├── ProductDetailView.vue
│   │   ├── CartDrawer.vue
│   │   ├── CheckoutView.vue
│   │   ├── OrderConfirmationView.vue
│   │   └── ProductCard.vue
│   ├── Pages/
│   │   ├── Home.vue
│   │   ├── Shop.vue
│   │   ├── Product.vue
│   │   ├── Checkout.vue
│   │   └── Order.vue
│   └── Layouts/
│       └── AppLayout.vue
└── views/
    └── app.blade.php (Inertia root template)

app/Http/Middleware/
└── HandleInertiaRequests.php

routes/
└── web.php (storefront routes)

config/
├── tailwind.config.js
├── postcss.config.js
└── tsconfig.json
```

---

## 🎨 Design Features

### Color Palette (Tailwind)
- **Primary:** `brand-cream` (#faf9f7) - backgrounds
- **Secondary:** `brand-dark` (#0a0a0a) - text, headers
- **Accent:** `brand-orange` (#ff5722) - CTAs, highlights

### Typography
- **Headlines:** Georgia serif, italic
- **Body:** System fonts (clean sans-serif)
- **Tracking:** Professional letter spacing

### Responsive Design
- Mobile-first approach
- Breakpoints: sm, md, lg, xl, 2xl
- Touch-friendly targets (44x44px min)

---

## 🔌 Key Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `@inertiajs/vue3` | ^2.0 | Inertia Vue 3 adapter |
| `vue` | ^3.4 | Vue 3 framework |
| `lucide-vue-next` | ^0.294 | Icon library |
| `tailwindcss` | ^3.4 | Utility CSS framework |
| `@vitejs/plugin-vue` | ^5.0 | Vite Vue plugin |

---

## 📝 Important Notes

1. **Middleware Registration:** The `HandleInertiaRequests` middleware must be added to your HTTP kernel
2. **Routes:** All routes using Inertia need the middleware applied
3. **Assets:** CSS and JS are handled by Vite, not traditional Laravel asset compilation
4. **Components:** Vue components use `<script setup>` syntax (Vue 3)
5. **TypeScript:** Full TypeScript support enabled for development

---

## 🛠️ Development Workflow

### Running in Development
```bash
# In one terminal - Vite watch mode
npm run dev

# In another terminal - Laravel server
php artisan serve

# In a third terminal - Optional: Laravel queue
php artisan queue:work
```

### Building for Production
```bash
npm run build
```

### Type Checking
```bash
vue-tsc --noEmit
```

---

## ✨ Next Development Tasks

- [ ] Connect components to real Laravel API endpoints
- [ ] Implement shopping cart state management (Pinia or Vuex)
- [ ] Add product API endpoints
- [ ] Set up checkout form submission
- [ ] Implement order creation flow
- [ ] Add authentication (if needed)
- [ ] Optimize images and assets
- [ ] Add analytics/tracking

---

**Status:** ✅ Setup Complete - Ready for Development  
**Last Updated:** July 13, 2026

