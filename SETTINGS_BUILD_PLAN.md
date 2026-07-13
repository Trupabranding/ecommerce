# Settings Application Build Plan for Herd eCommerce

## Overview

Integrate an enterprise-grade settings management system inspired by Profitco's architecture. This system will provide:
- Hierarchical, multi-level settings organization
- Feature toggles with per-branch customization
- Role-based settings access control
- Progressive enhancement of current Filament admin panel

## Phased Implementation

### Phase 1: Foundation (P4) - 8-10 hours

#### 1.1 Settings Table Architecture
**Goal:** Build the database layer for settings storage

Files to create:
- `database/migrations/create_settings_tables.php`
  - `admin_settings` (tenant-scoped)
  - `setting_features` (feature toggles)
  - `setting_values` (per-branch overrides)
  - `setting_categories` (hierarchical grouping)

- `domain/Settings/Models/AdminSetting.php`
- `domain/Settings/Models/SettingFeature.php`
- `domain/Settings/Models/SettingValue.php`
- `app/Settings/*.php` (Laravel Settings package integration)

**Scope:** ~150 lines DDL + 3 Eloquent models  
**Success:** Database schema supports hierarchical, multi-tenant settings with feature toggles

#### 1.2 Settings Resource in Filament
**Goal:** Create the admin UI for settings management

Files to create:
- `app/Filament/Admin/Resources/SettingsResource.php` (main resource)
- `app/Filament/Admin/Resources/SettingsResource/Pages/ManageSettings.php`
- `app/Filament/Admin/Resources/SettingsResource/Widgets/SettingsCategoriesWidget.php`

**Components:**
- Left sidebar with category navigation (mirrors Profitco's sidebar)
- Tab-based subcategory navigation
- Feature toggle grid with on/off switches
- "Customize" action buttons for advanced features

**Scope:** ~200 lines resource + 150 lines page + 100 lines widget  
**Success:** Settings page renders with category navigation and basic toggles

#### 1.3 Feature Toggle System
**Goal:** Implement enable/disable logic for features

Files to create:
- `domain/Settings/Actions/ToggleFeatureAction.php`
- `domain/Settings/Queries/GetEnabledFeaturesQuery.php`
- `app/Filament/Admin/Resources/SettingsResource/Actions/ToggleFeatureAction.php`

**Scope:** ~80 lines  
**Success:** Toggling a feature in UI persists to database and affects feature visibility

---

### Phase 2: Feature Management (P4) - 10-12 hours

#### 2.1 Feature Categories & Organization
**Goal:** Organize features into business-logical groups

Implement categories (mirroring Profitco):
- Plan (Strategy, OKR Management, Task Management, etc.)
- Process (Portfolios, Timesheets, Meetings, etc.)
- People (Performance, Goals, Recognition, etc.)
- Operations (custom for ecommerce: Inventory, Payments, Shipping, etc.)

Files to create:
- `domain/Settings/Enums/FeatureCategory.php`
- `database/seeders/SettingFeaturesSeeder.php` (seed initial features)
- `app/Filament/Admin/Resources/SettingsResource/Pages/FeatureCategoriesPage.php`

**Scope:** ~250 lines  
**Success:** Features are organized and displayed by category in UI

#### 2.2 Customization Modals
**Goal:** Deep-dive settings for complex features

Files to create:
- `app/Filament/Admin/Resources/SettingsResource/Actions/CustomizeFeatureAction.php`
- `app/Filament/Admin/Resources/SettingsResource/Forms/FeatureCustomizationForm.php`
- Modal forms for each customizable feature

**Scope:** ~200 lines base + 50 lines per feature  
**Success:** Clicking "Customize" opens modal with feature-specific settings

---

### Phase 3: Multi-Branch Settings (P4) - 8-10 hours

#### 3.1 Branch-Specific Overrides
**Goal:** Allow per-branch feature customization

Files to update:
- `domain/Settings/Models/SettingValue.php` (add branch_uuid foreign key)
- `app/Filament/Admin/Resources/SettingsResource/Pages/ManageSettings.php` (branch context selector)

**Logic:**
- Global settings as defaults
- Per-branch overrides when branch is selected
- Clear visual distinction (inheritance vs override)

**Scope:** ~120 lines  
**Success:** Settings UI shows branch selector and respects branch-specific values

#### 3.2 Settings Inheritance
**Goal:** Implement cascading settings logic

Files to create:
- `domain/Settings/Actions/GetEffectiveSettingAction.php` (resolves global/branch value)
- `domain/Settings/Queries/GetBranchSettingsQuery.php`

**Logic:**
```
if (branch_uuid setting exists) return branch_value
else if (feature is_global) return global_value
else return feature_default
```

**Scope:** ~80 lines  
**Success:** Code can query `GetEffectiveSettingAction` and get correct value for context

---

### Phase 4: Role-Based Access (P4) - 6-8 hours

#### 4.1 Settings Policies
**Goal:** Control who can view/edit which settings

Files to create:
- `domain/Settings/Policies/SettingsPolicy.php`
- Add policy checks to SettingsResource

**Rules:**
- Super admin: view/edit all settings
- Branch manager: edit branch-scoped settings only
- Operator: view-only access
- Custom roles: granular permission mapping

**Scope:** ~100 lines  
**Success:** Settings access follows role hierarchy

---

### Phase 5: UI/UX Polish (P4) - 6-8 hours

#### 5.1 Sidebar Navigation Component
**Goal:** Reusable settings navigation sidebar

Files to create:
- `app/Filament/Admin/Components/SettingsSidebar.php` (Livewire component)
- CSS/styling to match Profitco design

**Features:**
- Active state highlighting
- Icon + text navigation
- Collapsible sections
- Search/filter categories

**Scope:** ~150 lines component + 100 lines CSS  
**Success:** Sidebar matches Profitco UX, is reusable across settings pages

#### 5.2 Feature Grid Component
**Goal:** Reusable toggle grid for features

Files to create:
- `app/Filament/Admin/Components/FeatureToggleGrid.php` (Livewire component)
- `resources/css/feature-grid.css`

**Features:**
- Organized into rows (Plan, Process, People sections)
- Toggle switches with descriptions
- "Customize" buttons per feature
- Disabled state styling

**Scope:** ~200 lines component + 150 lines CSS  
**Success:** Grid matches Profitco's layout, is maintainable

---

### Phase 6: Testing & Documentation (P4) - 5-7 hours

#### 6.1 Feature Tests
**Goal:** Comprehensive test coverage

Files to create:
- `tests/Feature/Filament/Admin/Resources/SettingsResourceTest.php`
- `tests/Feature/Settings/ToggleFeatureTest.php`
- `tests/Feature/Settings/SettingsInheritanceTest.php`
- `tests/Feature/Settings/SettingsPolicyTest.php`

**Coverage:**
- Settings page renders correctly
- Toggle feature on/off persists
- Branch-scoped settings override global
- Policies enforce access control
- Inheritance logic works correctly

**Scope:** ~400 lines tests  
**Success:** Tests pass, all features verified

#### 6.2 Documentation
**Goal:** Developer & operator guides

Files to create:
- `docs/SETTINGS_ARCHITECTURE.md` (technical overview)
- `docs/SETTINGS_USER_GUIDE.md` (operator guide)
- Feature configuration reference

---

## Implementation Priority Order

1. **P4-T1 (Week 1):** Phase 1 (Foundation) - Database + basic UI
2. **P4-T2 (Week 2):** Phase 2 (Feature Management) - Categories + customization
3. **P4-T3 (Week 3):** Phase 3 (Multi-Branch) + Phase 4 (RBAC)
4. **P4-T4 (Week 4):** Phase 5 (UI Polish) + Phase 6 (Tests + Docs)

**Total Effort:** ~54-60 hours (6-8 weeks solo, or 2 weeks with 2-3 developers)

---

## Success Metrics

- [ ] Settings page loads <500ms
- [ ] 100+ settings testable in UI
- [ ] 0 unauthorized access to restricted settings
- [ ] Branch-scoped settings work correctly
- [ ] All feature toggles persist correctly
- [ ] 90%+ test coverage for settings logic
- [ ] UI matches Profitco aesthetic

---

## Integration Points with Herd Codebase

### Leverage Existing Patterns

1. **Filament Resource architecture** → Use for settings management
2. **Policy-based authorization** → Extend for settings access
3. **Branch multi-tenancy** → Branch-scoped setting overrides
4. **Domain-Driven Design** → Settings domain in `/domain/Settings/`
5. **Action pattern** → ToggleFeatureAction, GetEffectiveSettingAction

### New Dependencies

- `spatie/laravel-settings` (optional, for value persistence)
- Or custom implementation using Eloquent models

---

## Risk Mitigation

**Risk 1: Settings explosion** (too many toggles to manage)  
→ Limit initial features to 20-30 core features; expand in future

**Risk 2: Performance degradation** (querying settings on every request)  
→ Cache settings values with TTL, invalidate on toggle

**Risk 3: Breaking changes** (removing a setting breaks code)  
→ Deprecation warnings for features; soft-delete settings records

**Risk 4: Branch setting conflicts** (override creates confusion)  
→ Clear UI labeling: "Using global value (8 branches override)"

---

## Optional Enhancements (Post-Phase 6)

1. **Settings Audit Trail** - Track who changed what, when
2. **Settings Backup/Restore** - Rollback to previous configurations
3. **Settings Export/Import** - Clone settings across tenants
4. **Advanced Scheduling** - Schedule feature rollout dates
5. **A/B Testing Integration** - Settings-based feature flags
6. **Analytics Dashboard** - Feature adoption metrics
