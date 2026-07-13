# Project Backlog and Release Changelog

## Snapshot
- Date: 2026-07-11
- Baseline test status: 168 passed, 54 todo, 0 failed
- Static analysis: `composer analyse --no-interaction` passes with no errors
- Focus of remaining work: test coverage debt, minor code hygiene, and deployment hardening checks

## Changelog (Latest Completed)
- Stabilized branch-feature gating across Filament resources, visibility, and access control.
- Improved seeders for idempotency and conditional branch behavior.
- Added customer bootstrap automation with profile support via `app:bootstrap-customer --profile`.
- Fixed console-safe settings activity logging to avoid command-time crashes.
- Fixed environment indicator palette mapping to avoid Filament runtime errors.
- Aligned test environment to support branch resource feature tests via `.env.testing`.

## Backlog (Prioritized)

### P0 - Delivery Readiness (High)
1. Convert Filament resource TODO tests to executable coverage.
- Why: 54 TODO tests hide regression risk in admin CRUD and authorization flows.
- Scope: Replace `todo(...)` with implemented tests for create/edit/delete/restore/view/index across Admin, Role, Branch, Brand, Customer, Order, Product, and SkuStock resources.
- Primary files:
  - `tests/Feature/Filament/Admin/Resources/Access/**`
  - `tests/Feature/Filament/Admin/Resources/Shop/**`

2. Add explicit feature-flag matrix tests for branch on/off behavior.
- Why: branch feature is now policy-critical; behavior should be locked by tests, not only config assumptions.
- Scope:
  - Branch disabled: no navigation, no access, no branch columns/filters/selectors, export omits branch data.
  - Branch enabled: branch routes/resources render and branch-specific controls appear.
- Suggested file to add:
  - `tests/Feature/Filament/Admin/FeatureFlags/BranchFeatureFlagTest.php`

3. Replace all current Filament `todo(...)` placeholders with executable tests (explicit inventory).
- Why: this is the largest unresolved quality gap and the easiest work to parallelize across agents.
- Remaining TODO inventory (54 total):
  - `tests/Feature/Filament/Admin/Resources/Access/ActivityResource/Pages/ListActivitiesTest.php` (1)
  - `tests/Feature/Filament/Admin/Resources/Access/AdminResource/Pages/CreateAdminTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Access/AdminResource/Pages/EditAdminTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Access/AdminResourceTest.php` (3)
  - `tests/Feature/Filament/Admin/Resources/Access/RoleResource/Pages/CreateRoleTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Access/RoleResource/Pages/EditRoleTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Access/RoleResource/Pages/ListRolesTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Access/RoleResourceTest.php` (1)
  - `tests/Feature/Filament/Admin/Resources/Shop/BranchResource/Pages/CreateBranchTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/BranchResource/Pages/EditBranchTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/BranchResourceTest.php` (3)
  - `tests/Feature/Filament/Admin/Resources/Shop/BrandResource/Pages/CreateBrandTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/BrandResource/Pages/EditBrandTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/BrandResourceTest.php` (3)
  - `tests/Feature/Filament/Admin/Resources/Shop/CustomerResource/Pages/CreateCustomerTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/CustomerResource/Pages/EditCustomerTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/CustomerResourceTest.php` (3)
  - `tests/Feature/Filament/Admin/Resources/Shop/OrderResource/Pages/CreateOrderTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/OrderResource/Pages/ViewOrderTest.php` (1)
  - `tests/Feature/Filament/Admin/Resources/Shop/OrderResourceTest.php` (3)
  - `tests/Feature/Filament/Admin/Resources/Shop/ProductResource/Pages/CreateProductTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/ProductResource/Pages/EditProductTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/ProductResourceTest.php` (3)
  - `tests/Feature/Filament/Admin/Resources/Shop/SkuStockResource/Pages/EditSkuStockTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/SkuStockResource/Pages/ListSkuStocksTest.php` (2)
  - `tests/Feature/Filament/Admin/Resources/Shop/SkuStockResourceTest.php` (1)
- Acceptance criteria:
  - All above files have zero `todo(...)` calls.
  - Full test suite remains green after each resource family batch.
  - Branch feature flag scenarios are explicitly covered in relevant tests.

### P1 - Quality and Maintainability (Medium)
1. Resolve IDE diagnostics that are currently non-blocking but noisy.
- `tests/Feature/Http/Controllers/API/Shop/Cart/CartControllerTest.php`
  - Issue: dynamic properties `$branch` and `$customer` reported as undefined.
  - Expected fix: declare typed test-scoped variables/pattern compatible with Pest and static analyzers.
- `app/Filament/Admin/Resources/Access/AdminResource.php`
  - Issue: unused import `impersonateAction`.
  - Expected fix: remove import or use symbol if intended.
- `database/seeders/Auth/RoleSeeder.php`
  - Issue: analyzer warning on `givePermissionTo` (likely type narrowing mismatch with Role contract/plugin class).
  - Expected fix: tighten concrete type annotations or helper wrappers acceptable to analyzer.
- `app/Providers/Filament/AdminPanelProvider.php`
  - Issue: style/lint hint around `Width::Full` from editor diagnostics.
  - Expected fix: align with configured style rule or suppress if intentional and framework-valid.

2. Complete missing assertion noted in order API tests.
- `tests/Feature/Http/Controllers/API/Shop/Order/OrderControllerTest.php`
  - TODO comment: `assert order item`.
  - Expected fix: assert persisted order-item structure, quantities, and price integrity.

3. Seeder diagnostics hardening (current editor/runtime hygiene).
- `database/seeders/Auth/RoleSeeder.php`
  - Current issue: editor diagnostics report `Undefined method 'givePermissionTo'` at role permission assignment call sites.
  - Expected fix: use concrete role model typing or analyzer-friendly wrappers while preserving runtime behavior.
- `database/seeders/Auth/AdminSeeder.php`
  - Current issue: ensure no accidental partial text/stray token edits remain from manual edits.
  - Expected fix: keep file compile-clean and enforce seeder idempotency assumptions with a dedicated seeder smoke test.

### P2 - Multi-Tenant Deployment Hardening (Medium)
1. Add smoke tests for bootstrap command profile precedence.
- Why: deployment automation depends on deterministic precedence (`CLI > profile > existing/default`).
- Scope:
  - Test command with and without `--profile`.
  - Validate resulting settings and super-admin identity.
- Suggested file to add:
  - `tests/Feature/Console/BootstrapCustomerCommandTest.php`

2. Document production rollout checklist for customer onboarding.
- Why: reduce operator error during per-customer deployment.
- Scope:
  - Required env vars (`SUPER_ADMIN_PASSWORD_HASH`, app URL, DB/queue/cache/mail).
  - First-run commands and verification commands.
  - Expected post-deploy checks (admin login, order flow, branch behavior by flag).
- Suggested file to add/update:
  - `README.md` section "Production Customer Onboarding"

### P3 - Product Pages UX Enhancement (Medium)
**Related Document:** See `PRODUCT_PAGES_AUDIT.md` for detailed analysis and code examples.

This section integrates audit findings into actionable product page improvements, organized by tier. Can be executed in parallel with or after P0-P2.

#### Tier 1: Quick Wins (4-6 hours) - IMPLEMENT FIRST
**Goal:** Add dedicated view page with professional UX and product metrics.

1. Create ViewProduct page
   - Why: Currently products edited directly from list (no read-only view)
   - Files to create:
     - `app/Filament/Admin/Resources/Shop/ProductResource/Pages/ViewProduct.php`
   - Components:
     - Infolist display of product details (name, SKU, status, category, brand)
     - Media gallery section (inline image display)
     - Stock summary widget (total SKUs, total stock count)
     - Related infolists (tags, metadata)
   - Scope: ~80-100 lines of Filament infolist code

2. Add ProductStats widget
   - Why: Quick metrics at a glance improve product assessment
   - Files to create:
     - `app/Filament/Admin/Resources/Shop/ProductResource/Widgets/ProductStats.php`
   - Metrics:
     - Total SKUs count
     - Total stock quantity
     - Order count (related orders)
     - Low stock alert indicator
   - Scope: ~40-50 lines

3. Update routing and table actions
   - Files to update:
     - `app/Filament/Admin/Resources/Shop/ProductResource.php` (add view route)
     - `app/Filament/Admin/Resources/Shop/ProductResource/Pages/ListProducts.php` (add ViewAction)
   - Changes:
     - Add `'view' => ViewProduct::route('/{record}')` to getPages()
     - Add `ViewAction::make()` to table recordActions
   - Scope: ~5-10 lines total

4. Add view/edit navigation
   - Both pages have action buttons to toggle between viewing and editing
   - Clear visual intent separation

**Expected Result:**
- Professional dedicated product detail page
- Quick metrics overview without editing friction
- Clear separation of view vs edit intent
- +30% perceived performance (better UX flow)

**Testing Scope (1-2 hours):**
- View page renders correctly with products having 1, 5, 20+ SKUs
- Media gallery displays correctly
- Widget calculations accurate
- Navigation between view/edit works both ways
- Mobile responsive

#### Tier 2: Enhanced UX (6-8 hours) - IMPLEMENT SECOND
**Goal:** Better visual data presentation and SKU management.

1. SKU Pricing Matrix
   - Why: Admins can't easily scan all variants without opening nested relation manager
   - Files to create:
     - `resources/views/components/sku-pricing-matrix.blade.php`
   - Features:
     - Table showing all SKUs: code, price, stock level, status
     - Color-coded stock status (Low/Medium/Good)
     - Sortable by code/price
     - Clickable rows link to SKU detail
   - Integration: Add as custom section in ViewProduct page
   - Scope: ~80-100 lines

2. Activity Timeline
   - Why: Activities shown in relation manager are hard to scan as audit trail
   - Files to create:
     - `resources/views/components/product-activity-timeline.blade.php`
   - Features:
     - Chronological timeline of product changes
     - Who changed what and when
     - Icon/badge for change type
   - Integration: Add as tab or collapsed section in ViewProduct
   - Scope: ~60-80 lines

3. Form/View Toggle Actions
   - Files to update:
     - `app/Filament/Admin/Resources/Shop/ProductResource/Pages/ViewProduct.php`
     - `app/Filament/Admin/Resources/Shop/ProductResource/Pages/EditProduct.php`
   - Changes:
     - Add view/edit toggle buttons
     - Same pattern as Filament examples
   - Scope: ~20-30 lines

**Expected Result:**
- Better SKU overview without opening relation manager
- Quick visual assessment of variant health and pricing
- Activity audit trail visible in timeline format
- +20% efficiency for managing products with 5-20+ SKUs

**Testing Scope (1-2 hours):**
- SKU matrix displays all variants correctly
- Stock status colors render properly (3 levels)
- Activity timeline shows all model changes
- Toggle buttons work both directions
- Mobile responsive layout

#### Tier 3: Advanced Features (8+ hours) - DO LATER
**Goal:** Premium features and product intelligence (defer until Tier 1-2 proven valuable).

1. Product Preview Component
   - Why: Admins can't see how product appears to customers
   - Files to create:
     - `resources/views/components/product-preview.blade.php`
   - Features:
     - Renders product card as customers would see it
     - Media gallery, price range, stock status
     - Live rendering of storefront preview
   - Scope: ~100-150 lines

2. Product Analytics Widget
   - Why: No visibility into product performance (sales, trends)
   - Files to create:
     - `app/Filament/Admin/Resources/Shop/ProductResource/Widgets/ProductAnalytics.php`
   - Features:
     - Order trends chart (last 3 months)
     - Sales velocity metrics
     - Revenue contribution percentage
   - Scope: ~80-100 lines

3. Enhanced SKU Modal
   - Why: Relation manager not optimal for bulk SKU management
   - Features:
     - Dedicated modal for managing variants
     - Bulk price/status updates
     - Better pricing workflow
   - Scope: ~120-150 lines

**Expected Result:**
- Admins see exactly how customers view products
- Product performance metrics visible at a glance
- Better SKU management interface
- Professional, feature-rich UX

## Agent Handoff Plan
1. Start with P0 test conversion in batches by resource family (`Access` then `Shop`).
2. Add branch on/off matrix tests before touching UI behavior again.
3. Tackle P1 diagnostics cleanup after P0 is green to avoid mixing behavior and hygiene changes.
4. Finish with P2 deployment smoke tests and docs.
5. *Optional:* Execute P3 Tier 1 (4-6 hours) in parallel or after P0-P1 if team capacity available.

## Suggested Parallelization
1. Agent A: Access resources TODO conversion (`tests/Feature/Filament/Admin/Resources/Access/**`).
2. Agent B: Shop resources TODO conversion (`tests/Feature/Filament/Admin/Resources/Shop/**`).
3. Agent C: branch feature matrix + bootstrap command smoke tests.
4. Agent D: diagnostics cleanup in `RoleSeeder`, `AdminResource`, `CartControllerTest`, and `AdminPanelProvider`.
5. *Optional Agent E:* P3 Tier 1 product page improvements (ViewProduct page + ProductStats widget) if team desires parallel UX work.

**Note:** P3 Tier 1 is independent of P0-P2 and can be implemented by a dedicated agent while other agents handle test coverage and diagnostics. All data already optimized by Week 1-2 performance sprint, so no query impact.

## Actionable Items For Other Agents

### Agent A - Access Filament Tests
- Goal:
  - Replace all `todo(...)` placeholders in Access resource tests with runnable tests.
- Files:
  - `tests/Feature/Filament/Admin/Resources/Access/ActivityResource/Pages/ListActivitiesTest.php`
  - `tests/Feature/Filament/Admin/Resources/Access/AdminResource/**`
  - `tests/Feature/Filament/Admin/Resources/Access/RoleResource/**`
- Required coverage:
  - List page render and table assertions.
  - Create and edit page form submission assertions.
  - Delete, force-delete, and restore behavior where available.
- Run before handoff:
  - `php artisan test tests/Feature/Filament/Admin/Resources/Access`
- Done when:
  - No `todo(...)` remains under Access test paths.
  - Access test path passes with no failures.

### Agent B - Shop Filament Tests
- Goal:
  - Replace all `todo(...)` placeholders in Shop resource tests with runnable tests.
- Files:
  - `tests/Feature/Filament/Admin/Resources/Shop/BranchResource/**`
  - `tests/Feature/Filament/Admin/Resources/Shop/BrandResource/**`
  - `tests/Feature/Filament/Admin/Resources/Shop/CustomerResource/**`
  - `tests/Feature/Filament/Admin/Resources/Shop/OrderResource/**`
  - `tests/Feature/Filament/Admin/Resources/Shop/ProductResource/**`
  - `tests/Feature/Filament/Admin/Resources/Shop/SkuStockResource/**`
- Required coverage:
  - List, create, edit, and view pages where applicable.
  - Delete/restore operations where resource supports them.
  - Branch-aware behavior assertions for resources that depend on branch state.
- Run before handoff:
  - `php artisan test tests/Feature/Filament/Admin/Resources/Shop`
- Done when:
  - No `todo(...)` remains under Shop test paths.
  - Shop test path passes with no failures.

### Agent C - Feature Flags and Bootstrap Command
- Goal:
  - Lock critical branch feature behavior and bootstrap precedence with dedicated tests.
- Files to add:
  - `tests/Feature/Filament/Admin/FeatureFlags/BranchFeatureFlagTest.php`
  - `tests/Feature/Console/BootstrapCustomerCommandTest.php`
- Required coverage:
  - Branch enabled and disabled mode assertions for access and UI exposure.
  - Command precedence checks: CLI option overrides profile, profile overrides defaults.
  - Assertions on resulting seeded admin/settings state.
- Run before handoff:
  - `php artisan test tests/Feature/Filament/Admin/FeatureFlags/BranchFeatureFlagTest.php`
  - `php artisan test tests/Feature/Console/BootstrapCustomerCommandTest.php`
- Done when:
  - Both test files pass and protect existing behavior from regression.

### Agent D - Diagnostics and Hygiene
- Goal:
  - Remove high-noise editor diagnostics without changing functional behavior.
- Files:
  - `tests/Feature/Http/Controllers/API/Shop/Cart/CartControllerTest.php`
  - `app/Filament/Admin/Resources/Access/AdminResource.php`
  - `database/seeders/Auth/RoleSeeder.php`
  - `app/Providers/Filament/AdminPanelProvider.php`
  - `tests/Feature/Http/Controllers/API/Shop/Order/OrderControllerTest.php`
- Required coverage:
  - Replace dynamic test properties with analyzer-friendly declarations.
  - Remove unused imports.
  - Resolve analyzer typing friction around permission assignment methods.
  - Implement missing order-item assertion.
- Run before handoff:
  - `composer analyse --no-interaction`
  - `php artisan test tests/Feature/Http/Controllers/API/Shop/Cart/CartControllerTest.php`
  - `php artisan test tests/Feature/Http/Controllers/API/Shop/Order/OrderControllerTest.php`
- Done when:
  - Targeted diagnostics are resolved or intentionally documented inline.
  - No behavior regressions in targeted tests.

## Integration Gate (After All Agents)
1. Run full suite:
  - `php artisan test`
2. Run static analysis:
  - `composer analyse --no-interaction`
3. Update this backlog:
  - Replace open actionable items with completed changelog entries.

## Definition of Done (Next Milestone)
- TODO tests reduced from 54 to 0, or all remaining TODOs justified in comments with ticket references.
- Branch feature matrix tests pass in both enabled and disabled modes.
- No avoidable IDE diagnostics in touched files.
- Bootstrap command behavior verified by automated tests.
- Onboarding checklist documented and reproducible.

### P4 - Settings Application (Post-Production) (20-40 hours)

**Goal:** Enterprise-grade settings management system for org configuration, feature toggles, and role-based access.

Reference: `SETTINGS_BUILD_PLAN.md` for full implementation roadmap.

**Why:** Current admin panel lacks:
- Hierarchical settings organization (now flat)
- Feature toggles for gradual rollouts
- Branch-scoped configuration overrides
- User-friendly settings discovery

**Phases:**

1. **Foundation (8-10 hours)**
   - Settings table architecture (settings, features, values, categories)
   - Basic Filament resource for settings management
   - Feature toggle system with persistence

2. **Feature Management (10-12 hours)**
   - Feature categories (Plan, Process, People, Operations)
   - Customization modals for advanced features
   - Feature seeding with sensible defaults

3. **Multi-Branch Settings (8-10 hours)**
   - Branch-specific setting overrides
   - Settings inheritance logic (global > branch > default)
   - Branch context selector in UI

4. **Role-Based Access Control (6-8 hours)**
   - Settings policies (super admin, branch manager, operator, custom)
   - Granular permission mapping
   - Access-controlled setting visibility

5. **UI/UX Polish (6-8 hours)**
   - Settings sidebar component (navigation, search)
   - Feature toggle grid component (groups, descriptions, actions)
   - Responsive design matching Profitco aesthetic

6. **Testing & Documentation (5-7 hours)**
   - Feature tests (toggles, inheritance, policies)
   - Integration tests with Filament
   - Developer & operator guides

**Success Criteria:**
- Settings page loads <500ms
- 100+ settings manageable in UI
- Zero unauthorized setting access
- Branch overrides work correctly
- 90%+ test coverage
- Full documentation

See `SETTINGS_BUILD_PLAN.md` for detailed task breakdown, file structure, and implementation patterns.
