# Herd Settings UI/UX Design - Implementation Plan

## Overview

Build a professional settings interface inspired by Profitco's design. Focus on:
- Hierarchical navigation
- Clean, organized layouts
- Reusable UI components
- Professional visual design

**Scope:** UI/UX design patterns ONLY (not feature toggles or complex logic)

---

## Phase 1: Core Components (6-8 hours)

### 1.1 Settings Sidebar Component

**File:** `app/Filament/Admin/Components/SettingsSidebar.php` (Livewire)

**Features:**
- Icon + text navigation items
- Active state highlighting
- Collapsible sections
- Nested subcategories
- Search/filter categories

**Visual Design:**
```
Settings
├─ [icon] General
├─ [icon] Shop
│  ├─ Products
│  ├─ Categories
│  └─ Inventory
├─ [icon] Payments
├─ [icon] Shipping
├─ [icon] Notifications
├─ [icon] Security
├─ [icon] Integrations
└─ [icon] Billing
```

**CSS Requirements:**
- Sidebar width: 250px (matches Profitco)
- Item padding: 12px 16px
- Icon size: 20px
- Active background: Brand color
- Hover state: Subtle background change
- Smooth transitions

**Scope:** ~200 lines PHP + ~150 lines CSS

---

### 1.2 Settings Layout Component

**File:** `resources/views/components/settings-layout.blade.php`

**Structure:**
```
┌─────────────────────────────────────┐
│ Breadcrumb (Settings > Category)    │
├──────────────┬──────────────────────┤
│              │  [Tab Bar]           │
│  Sidebar     ├──────────────────────┤
│  Navigation  │  [Content Area]      │
│              │                      │
│              │  [Form/Sections]     │
│              │                      │
└──────────────┴──────────────────────┘
```

**Components:**
- Breadcrumb navigation
- Tab bar for subcategories
- Main content area
- Sidebar on left (sticky)

**Scope:** ~100 lines Blade + styling

---

### 1.3 Settings Tab Component

**File:** `app/Filament/Admin/Components/SettingsTabs.php`

**Features:**
- Horizontal tab navigation
- Active state indicator (underline)
- Responsive (scrollable on mobile)
- Icon + text support
- Smooth transitions

**Visual:**
```
[General] [Shop] [Payments] [Shipping] [More ▼]
  └─ underline under active tab
```

**Scope:** ~120 lines PHP + CSS

---

### 1.4 Settings Section Component

**File:** `app/Filament/Admin/Components/SettingsSection.php`

**Features:**
- Card-based layout
- Section heading + optional description
- Form fields grouped logically
- Save/Reset buttons
- Success/error messaging

**Visual:**
```
┌────────────────────────────────────┐
│ Section Title                      │
│ Optional description text          │
├────────────────────────────────────┤
│ [Form fields organized]            │
│                                    │
│ [Save] [Reset]                     │
└────────────────────────────────────┘
```

**Scope:** ~150 lines PHP

---

## Phase 2: Settings Pages (8-10 hours)

### 2.1 General Settings Page

**File:** `app/Filament/Admin/Pages/Settings/GeneralSettings.php`

**Content Sections:**
1. Site Information
   - Site name
   - Tagline
   - Logo
   - Favicon

2. Regional Settings
   - Timezone
   - Locale
   - Date format
   - Currency

3. Business Information
   - Legal name
   - Support email
   - Support phone
   - Address

**Form Style:**
- Clean grid layout (2 columns)
- Labels above inputs
- Helper text below fields
- Grouped into sections

**Scope:** ~250 lines

---

### 2.2 Shop Settings Page

**File:** `app/Filament/Admin/Pages/Settings/ShopSettings.php`

**Tabs:**
1. Products
   - Default status
   - Auto-publish on stock
   - SKU requirements

2. Categories
   - Nested depth limit
   - Default image size
   - SEO settings

3. Inventory
   - Low stock threshold
   - Auto-reorder points
   - Stock notifications

**Scope:** ~300 lines

---

### 2.3 Payments Settings Page

**File:** `app/Filament/Admin/Pages/Settings/PaymentSettings.php`

**Tabs:**
1. Payment Methods
   - Enabled methods
   - Default method
   - Method settings

2. Gateways
   - Gateway selection
   - API credentials
   - Test/Live mode

3. Checkout
   - Payment timeout
   - Retry attempts
   - Email confirmations

**Scope:** ~300 lines

---

### 2.4 Shipping Settings Page

**File:** `app/Filament/Admin/Pages/Settings/ShippingSettings.php`

**Tabs:**
1. Carriers
   - Enabled carriers
   - Default carrier
   - Carrier rates

2. Zones
   - Shipping zones
   - Zone rules
   - Rate tables

3. Defaults
   - Free shipping threshold
   - Default service level
   - Handling fees

**Scope:** ~300 lines

---

## Phase 3: Navigation Integration (4-6 hours)

### 3.1 Settings Menu Resource

**File:** `app/Filament/Admin/Resources/SettingsResource.php`

**Purpose:**
- Central settings resource
- Links to all settings pages
- Single entry point in Filament navigation

**URL Structure:**
```
/admin/settings/general
/admin/settings/shop
/admin/settings/payments
/admin/settings/shipping
/admin/settings/notifications
/admin/settings/security
/admin/settings/integrations
/admin/settings/billing
```

**Scope:** ~150 lines

---

### 3.2 Settings Navigation in Admin Panel

**Update:** `app/Providers/Filament/AdminPanelProvider.php`

**Add Settings Link:**
- Navigation menu item (icon: cog)
- Positioned in main menu
- Links to /admin/settings/general

**Scope:** ~20 lines

---

## Phase 4: Visual Design & Styling (6-8 hours)

### 4.1 Design System / CSS Variables

**File:** `resources/css/settings.css`

**Color Palette:**
```
Primary brand color (for active states)
Secondary color (for accents)
Neutral grays (background, borders, text)
Success/warning/error colors
```

**Spacing System:**
```
xs: 4px
sm: 8px
md: 16px
lg: 24px
xl: 32px
```

**Typography:**
```
Headings: Bold, 18-24px
Body text: Regular, 14px
Labels: Medium, 12px
Descriptions: Light, 13px
```

**Component Sizing:**
```
Sidebar width: 250px
Max content width: 1200px
Tab height: 48px
Section card padding: 24px
Form field height: 40px
```

### 4.2 Tailwind/CSS Utilities

Create utility classes:
- `.settings-sidebar`
- `.settings-nav`
- `.settings-tabs`
- `.settings-section`
- `.settings-form-group`
- `.settings-button-group`

**Scope:** ~300 lines CSS

---

### 4.3 Responsive Design

**Desktop (1200px+):**
- Full sidebar visible
- 2-column form layout
- All tabs visible

**Tablet (768px - 1199px):**
- Collapsible sidebar (hamburger menu)
- Single-column forms
- Tab scrolling

**Mobile (<768px):**
- Full-screen sidebar (overlay)
- Stacked forms
- Tab carousel

**Scope:** ~150 lines responsive CSS

---

## Phase 5: Form Components & Interactions (6-8 hours)

### 5.1 Settings Form Components

Leverage Filament's form components but style for settings context:
- Text inputs
- Selects / multiselects
- Rich text editors
- File uploads
- Toggle switches
- Radio buttons

**Custom wrapper:** `SettingsFormField.php`
- Consistent styling
- Help text styling
- Error message styling
- Visual hierarchy

**Scope:** ~120 lines

---

### 5.2 Save/Reset Interactions

**Save Behavior:**
- Validate on client-side
- Show loading spinner
- Success toast notification
- Update page state

**Reset Behavior:**
- Confirmation modal
- Revert to saved values
- Show which fields changed
- Toast confirmation

**Scope:** ~100 lines JavaScript/Livewire

---

### 5.3 Unsaved Changes Warning

**Implementation:**
- Track form state
- Warn before navigation
- Highlight changed fields
- Show "Unsaved Changes" indicator

**Scope:** ~80 lines

---

## Phase 6: Documentation & Polish (4-6 hours)

### 6.1 Settings Component Library

**File:** `docs/SETTINGS_UI_COMPONENTS.md`

**Document:**
- Sidebar component usage
- Tab component usage
- Section component usage
- Form field styling
- Color palette
- Spacing guidelines
- Responsive breakpoints

**Scope:** ~500 lines markdown

---

### 6.2 Settings Pages Guide

**File:** `docs/SETTINGS_PAGES_GUIDE.md`

**Document:**
- How to create new settings page
- File structure
- Component composition
- Filament integration
- Validation patterns
- Success message patterns

**Scope:** ~400 lines markdown

---

### 6.3 Visual Polish

- Hover states on all interactive elements
- Smooth transitions (300ms default)
- Loading states on buttons
- Error state styling
- Accessibility: ARIA labels, focus states
- Keyboard navigation support

**Scope:** ~100 lines CSS + accessibility checks

---

## File Structure

```
app/Filament/Admin/
├─ Components/
│  ├─ SettingsSidebar.php
│  ├─ SettingsTabs.php
│  └─ SettingsSection.php
├─ Pages/Settings/
│  ├─ GeneralSettings.php
│  ├─ ShopSettings.php
│  ├─ PaymentSettings.php
│  ├─ ShippingSettings.php
│  ├─ NotificationSettings.php
│  ├─ SecuritySettings.php
│  ├─ IntegrationSettings.php
│  └─ BillingSettings.php
└─ Resources/
   └─ SettingsResource.php

resources/
├─ css/
│  └─ settings.css
└─ views/components/
   └─ settings-layout.blade.php

docs/
├─ SETTINGS_UI_COMPONENTS.md
└─ SETTINGS_PAGES_GUIDE.md
```

---

## Visual Design Reference

**Sidebar Style (from Profitco):**
- Light gray background (#f5f5f5)
- Dark text for items
- Colored background for active item (brand color)
- Smooth hover transitions
- Icon + text alignment
- 16px left padding

**Main Content Style:**
- White background
- Max-width 1000px
- Generous padding (32px)
- Clean typography hierarchy
- Card-based sections

**Tab Style:**
- Light gray background for inactive tabs
- No visible borders
- Bottom accent line for active tab
- Smooth color transitions
- Icons + text labels

**Form Style:**
- Clear labels
- Grouped fields in sections
- Helper text in light gray
- Consistent field heights
- Action buttons aligned right

---

## Implementation Order

1. **Week 1:** Components (Sidebar, Tabs, Layout, Section)
2. **Week 2:** General Settings page + styling
3. **Week 3:** Shop, Payments, Shipping pages
4. **Week 4:** Polish, documentation, responsive design

**Total Effort:** 28-36 hours (3.5-4.5 days solo)

---

## Success Metrics

- [ ] Settings sidebar displays all categories cleanly
- [ ] All forms are organized and easy to scan
- [ ] Mobile/tablet responsive (tested on 3 breakpoints)
- [ ] Active states clearly visible
- [ ] Save/Reset interactions work smoothly
- [ ] Loading states provide clear feedback
- [ ] Zero layout shifts on interaction
- [ ] Accessibility score: 90+
- [ ] Page load time: <300ms
- [ ] UI matches Profitco aesthetic

---

## Optional Enhancements (Post-Phase 6)

1. **Dark Mode** - Dark theme variant
2. **Settings Search** - Global search across all settings
3. **Settings History** - View/audit change history
4. **Settings Profiles** - Save/restore setting combinations
5. **Animated Transitions** - Page enter/exit animations
6. **Quick Actions** - Floating action buttons for common tasks
7. **Keyboard Shortcuts** - Power user navigation
8. **Settings Export/Import** - Backup/restore all settings
