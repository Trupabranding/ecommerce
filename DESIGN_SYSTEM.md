# Herd eCommerce Frontend Design System
## Lexington Themes Inspired • Minimalist Aesthetic • Premium Feel

**Version:** 1.0  
**Framework:** Inertia.js + Vue 3  
**Color Scheme:** Black & White with Orange Accents  
**Last Updated:** July 2026

---

## 📐 Design Philosophy

Our storefront embodies **minimalism meets elegance**:
- Ample whitespace (50-100px padding between sections)
- Serif italic headlines for personality
- Clean sans-serif body text for readability
- Strategic use of orange for CTAs and emphasis
- Professional, premium aesthetic
- Distraction-free shopping experience

---

## 🎨 Color Palette

### Primary Colors
```
White:           #FFFFFF
Light Gray:      #F5F5F5 (backgrounds, subtle elements)
Off-White:       #FAFAFA (alternative light background)
```

### Secondary Colors
```
Black:           #000000 (text, headers)
Dark Gray:       #1A1A1A (primary text)
Medium Gray:     #666666 (secondary text)
Light Gray:      #999999 (tertiary text)
```

### Accent Colors
```
Orange Primary:  #FF5722 (CTAs, hover states, alerts)
Orange Light:    #FFB74D (accents, badges)
Orange Dark:     #E64A19 (active states, hover)
```

### Utility Colors
```
Success Green:   #4CAF50
Warning Yellow:  #FFC107
Error Red:       #F44336
Info Blue:       #2196F3
```

### Dark Mode
```
Background:      #0F0F0F (near black)
Surface:         #1A1A1A (cards, containers)
Text Primary:    #FFFFFF
Text Secondary:  #B0B0B0
Border:          #333333
```

---

## 🔤 Typography

### Font Stack
```css
/* Headlines - Serif Italic */
font-family: 'Georgia', 'Garamond', serif;
font-style: italic;
font-weight: 700;

/* Body & UI - Clean Sans */
font-family: '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
font-weight: 400;
font-style: normal;
```

### Scale (Base: 16px)

| Level | Size | Weight | Usage |
|-------|------|--------|-------|
| **H1** | 48px | 700 | Page hero titles |
| **H2** | 36px | 700 | Section headers |
| **H3** | 28px | 700 | Subsection titles |
| **H4** | 24px | 700 | Card titles, category names |
| **H5** | 20px | 600 | Small headings |
| **Body** | 16px | 400 | Default text, paragraphs |
| **Body Small** | 14px | 400 | Secondary info, captions |
| **Label** | 12px | 600 | Form labels, badges |
| **Caption** | 12px | 400 | Fine print, hints |

### Styling Rules

```
// Headlines (serif italic, always)
.headline {
  font-family: Georgia, serif;
  font-style: italic;
  font-weight: 700;
  letter-spacing: -0.5px;
  line-height: 1.1;
  color: #000000;
}

// Body text (clean sans)
.body {
  font-family: system-ui, sans-serif;
  font-weight: 400;
  line-height: 1.6;
  color: #1A1A1A;
}

// Links
a {
  color: #FF5722;
  text-decoration: none;
  border-bottom: 1px solid transparent;
  transition: all 0.2s ease;
}

a:hover {
  border-bottom-color: #FF5722;
  color: #E64A19;
}
```

---

## 📏 Spacing System

Use multiples of 8px for consistency:

```
xs:    4px
sm:    8px
md:    16px
lg:    24px
xl:    32px
2xl:   48px
3xl:   64px
4xl:   80px
5xl:   96px
6xl:   128px
```

### Layout Padding

```
Page/Container:  48px (desktop) | 24px (tablet) | 16px (mobile)
Section:         64px vertical spacing
Component:       16-24px internal padding
Element:         8-16px spacing
```

### Example Sections

```html
<!-- Section spacing -->
<section class="py-32 px-8">  <!-- 128px vertical, 32px horizontal -->
  <h2 class="text-4xl mb-16">Section Title</h2>
  
  <!-- Content with breathing room -->
  <div class="grid gap-12">
    <!-- Large gap between items -->
  </div>
</section>

<!-- Between major sections -->
<!-- 80-128px of whitespace -->
```

---

## 🎛️ Components

### Buttons

```vue
<!-- Primary CTA -->
<button class="btn btn-primary">
  Shop Now
</button>

<!-- Secondary -->
<button class="btn btn-secondary">
  Learn More
</button>

<!-- Text Link -->
<a href="#" class="btn btn-text">
  View all →
</a>
```

**Styling:**
```css
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 32px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #FF5722;
  color: white;
}

.btn-primary:hover {
  background: #E64A19;
  transform: translateY(-2px);
}

.btn-secondary {
  background: transparent;
  color: #000000;
  border: 1px solid #000000;
}

.btn-secondary:hover {
  background: #F5F5F5;
}

.btn-text {
  padding: 0;
  color: #FF5722;
  border-bottom: 1px solid transparent;
}

.btn-text:hover {
  border-bottom-color: #FF5722;
}
```

### Product Cards

```vue
<div class="product-card">
  <div class="product-image">
    <img :src="product.image" :alt="product.name" />
  </div>
  <div class="product-info">
    <h4 class="product-name">{{ product.name }}</h4>
    <p class="product-category">{{ product.category }}</p>
    <p class="product-price">${{ product.price }}</p>
    <button class="btn btn-primary w-full mt-4">Add to Cart</button>
  </div>
</div>
```

**Styling:**
```css
.product-card {
  background: white;
  border: 1px solid #F5F5F5;
  border-radius: 4px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.product-card:hover {
  border-color: #DDD;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.product-image {
  width: 100%;
  aspect-ratio: 1;
  background: #F5F5F5;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-info {
  padding: 24px 16px;
}

.product-name {
  font-size: 18px;
  font-weight: 700;
  color: #000000;
  margin: 0;
}

.product-category {
  font-size: 12px;
  color: #999999;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 4px 0 8px 0;
}

.product-price {
  font-size: 20px;
  font-weight: 700;
  color: #FF5722;
  margin: 0;
}
```

### Navigation

```vue
<header class="navbar">
  <div class="navbar-container">
    <div class="navbar-logo">
      <span class="logo-icon">●</span>
      <span class="logo-text">Herd</span>
    </div>
    
    <nav class="navbar-menu">
      <a href="/" class="nav-link">Home</a>
      <a href="/shop" class="nav-link">Shop</a>
      <a href="/about" class="nav-link">About</a>
      <a href="/blog" class="nav-link">Stories</a>
    </nav>
    
    <div class="navbar-actions">
      <a href="/search" class="nav-icon">🔍</a>
      <a href="/account" class="nav-icon">👤</a>
      <a href="/cart" class="nav-icon">🛒</a>
    </div>
  </div>
</header>
```

**Styling:**
```css
.navbar {
  background: white;
  border-bottom: 1px solid #F5F5F5;
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1440px;
  margin: 0 auto;
  padding: 24px 48px;
  gap: 48px;
}

.navbar-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 24px;
  font-weight: 700;
  text-decoration: none;
}

.navbar-menu {
  display: flex;
  gap: 48px;
  flex: 1;
}

.nav-link {
  font-size: 16px;
  color: #000000;
  text-decoration: none;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.nav-link:hover {
  border-bottom-color: #FF5722;
  color: #FF5722;
}

.navbar-actions {
  display: flex;
  gap: 24px;
  align-items: center;
}

.nav-icon {
  font-size: 20px;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.nav-icon:hover {
  opacity: 0.7;
}
```

### Hero Section

```vue
<section class="hero">
  <div class="hero-content">
    <h1 class="hero-title">
      <span class="hero-subtitle">Because basic is boring.</span>
      Upgrade your life.
    </h1>
    <p class="hero-description">
      Turn your boring routine into a highlight reel — you're welcome.
    </p>
    <a href="/shop" class="btn btn-primary">Browse all products</a>
  </div>
  <div class="hero-image">
    <img src="/images/hero.jpg" alt="Hero" />
  </div>
</section>
```

**Styling:**
```css
.hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: center;
  padding: 128px 48px;
  background: white;
}

.hero-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.hero-title {
  font-size: 48px;
  font-weight: 700;
  line-height: 1.1;
  color: #000000;
  margin: 0;
}

.hero-subtitle {
  display: block;
  font-size: 28px;
  font-style: italic;
  color: #999999;
  font-weight: 400;
  margin-bottom: 8px;
}

.hero-description {
  font-size: 16px;
  color: #666666;
  line-height: 1.6;
  max-width: 500px;
  margin: 0;
}

.hero-image {
  width: 100%;
  aspect-ratio: 1;
  background: #F5F5F5;
  border-radius: 4px;
  overflow: hidden;
}

.hero-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Responsive */
@media (max-width: 768px) {
  .hero {
    grid-template-columns: 1fr;
    padding: 64px 24px;
    gap: 32px;
  }
  
  .hero-title {
    font-size: 36px;
  }
  
  .hero-subtitle {
    font-size: 20px;
  }
}
```

### Section Dividers

```vue
<!-- Full-width divider -->
<div class="divider"></div>

<!-- With spacing -->
<div class="py-32"></div>
```

**Styling:**
```css
.divider {
  height: 1px;
  background: #F5F5F5;
  margin: 48px 0;
}
```

---

## 📐 Grid System

```vue
<!-- Desktop: 4 columns -->
<div class="grid grid-cols-4 gap-12">
  <div class="product-card">...</div>
  <!-- repeat for 4 items -->
</div>

<!-- Tablet: 2 columns -->
<!-- Mobile: 1 column -->
```

**Breakpoints:**
```css
Mobile:  0px - 640px
Tablet:  641px - 1024px
Desktop: 1025px+

/* TailwindCSS inspired */
sm: 640px
md: 768px
lg: 1024px
xl: 1280px
2xl: 1536px
```

---

## 🌙 Dark Mode

All components should support dark mode:

```css
/* Light mode (default) */
.component {
  background: white;
  color: #000000;
  border: 1px solid #F5F5F5;
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
  .component {
    background: #1A1A1A;
    color: #FFFFFF;
    border: 1px solid #333333;
  }
}

/* Or with class-based toggle */
[data-theme="dark"] .component {
  background: #1A1A1A;
  color: #FFFFFF;
  border: 1px solid #333333;
}
```

---

## 📱 Responsive Design

### Mobile-First Approach

```css
/* Mobile first */
.product-grid {
  grid-template-columns: 1fr;
  padding: 16px;
}

/* Tablet */
@media (min-width: 768px) {
  .product-grid {
    grid-template-columns: repeat(2, 1fr);
    padding: 24px;
  }
}

/* Desktop */
@media (min-width: 1024px) {
  .product-grid {
    grid-template-columns: repeat(4, 1fr);
    padding: 48px;
  }
}
```

### Touch Targets
- Minimum touch target: 44x44px (mobile)
- Minimum button height: 48px
- Minimum link padding: 12px

---

## 🎯 Page Layouts

### Homepage Layout
```
[Header - Sticky Navigation]
[Hero Section - Full Width]
[Featured Products Grid - 4 columns]
[Section Divider]
[Category Browsing - 3 columns with descriptions]
[Section Divider]
[Latest Articles - 3 columns]
[Newsletter Signup - Full width CTA]
[Footer]
```

### Product List Layout
```
[Header - Sticky Navigation]
[Page Title & Description]
[Filters Sidebar | Product Grid - 3 columns]
[Pagination]
[Footer]
```

### Product Detail Layout
```
[Header - Sticky Navigation]
[Product Image Gallery | Product Info]
[Product Details & Description]
[Related Products - 4 columns]
[Footer]
```

### Cart Layout
```
[Header - Sticky Navigation]
[Cart Items - List]
[Order Summary - Sidebar]
[Checkout Button]
[Footer]
```

---

## ✨ Micro-interactions

### Hover States
```css
/* Links */
a {
  border-bottom: 1px solid transparent;
  transition: all 0.2s ease;
}

a:hover {
  border-bottom-color: #FF5722;
}

/* Buttons */
.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Cards */
.card {
  transition: all 0.3s ease;
}

.card:hover {
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}
```

### Loading States
```css
.loading {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
```

### Transitions
- Default: 0.2s ease
- Hover: 0.2s ease
- Page load: 0.3s ease-out
- Dismissal: 0.2s ease-in

---

## 📝 Forms

### Form Styling

```vue
<form class="form">
  <div class="form-group">
    <label for="email" class="form-label">Email Address</label>
    <input 
      id="email" 
      type="email" 
      class="form-input"
      placeholder="you@example.com"
    />
  </div>
</form>
```

**Styling:**
```css
.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 24px;
}

.form-label {
  font-size: 14px;
  font-weight: 600;
  color: #000000;
}

.form-input {
  padding: 12px 16px;
  font-size: 16px;
  border: 1px solid #DDD;
  border-radius: 4px;
  font-family: inherit;
  transition: all 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: #FF5722;
  box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.1);
}

.form-input::placeholder {
  color: #999999;
}
```

---

## 🎭 Implementation Checklist

- [ ] Set up CSS variables for colors and spacing
- [ ] Create reusable Vue components for Button, Card, Navigation
- [ ] Implement responsive grid system (Tailwind or custom)
- [ ] Add dark mode support
- [ ] Set up typography scale
- [ ] Create micro-interaction library
- [ ] Test on mobile, tablet, desktop
- [ ] Verify accessibility (contrast, touch targets)
- [ ] Performance optimization (image lazy loading, code splitting)

---

## 📚 Files to Create

```
resources/js/
├── components/
│   ├── Navigation.vue
│   ├── Button.vue
│   ├── Card.vue
│   ├── ProductCard.vue
│   ├── Hero.vue
│   └── Footer.vue
├── layouts/
│   ├── AppLayout.vue
│   └── AuthLayout.vue
├── pages/
│   ├── Home.vue
│   ├── Products/
│   │   ├── Index.vue
│   │   └── Show.vue
│   ├── Cart.vue
│   ├── Checkout.vue
│   ├── Blog/
│   │   ├── Index.vue
│   │   └── Show.vue
│   └── Account.vue
└── styles/
    ├── main.css
    ├── variables.css
    ├── typography.css
    └── responsive.css
```

---

## 🔗 Reference

**Design Inspiration:** Lexington Themes  
**Framework:** Inertia.js + Vue 3  
**Styling:** Tailwind CSS + Custom CSS  
**Icons:** Heroicons (when needed)  
**Fonts:** System fonts + Georgia serif for headlines

---

**Next Step:** Begin Inertia setup and create base layout using this design system.
