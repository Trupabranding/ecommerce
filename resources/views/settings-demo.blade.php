<x-settings-layout
    activeCategory="general"
    :tabs="[
        ['id' => 'general', 'label' => 'General Settings', 'url' => '#'],
        ['id' => 'advanced', 'label' => 'Advanced', 'url' => '#'],
        ['id' => 'beta', 'label' => 'Beta Features', 'url' => '#'],
    ]"
    activeTab="general"
    :breadcrumbs="[
        ['label' => 'General', 'url' => '#'],
        ['label' => 'Site Settings', 'url' => null],
    ]"
>
    <x-settings-section
        title="Site Information"
        description="Basic information about your store"
    >
        <form class="settings-form">
            <div class="settings-form-group">
                <label class="settings-form-label">Store Name</label>
                <input type="text" class="settings-form-input" value="Herd eCommerce" />
                <span class="settings-form-help">The name of your online store</span>
            </div>

            <div class="settings-form-group">
                <label class="settings-form-label">Tagline</label>
                <input type="text" class="settings-form-input" value="Your perfect shopping destination" />
                <span class="settings-form-help">A short description displayed in headers</span>
            </div>

            <div class="settings-button-group">
                <button type="button" class="settings-button settings-button-secondary">Reset</button>
                <button type="submit" class="settings-button settings-button-primary">Save Changes</button>
            </div>
        </form>
    </x-settings-section>

    <x-settings-section
        title="Regional Settings"
        description="Configure timezone, locale, and currency"
    >
        <form class="settings-form">
            <div class="settings-form-group">
                <label class="settings-form-label">Timezone</label>
                <select class="settings-form-input">
                    <option>America/New_York</option>
                    <option selected>UTC</option>
                    <option>Europe/London</option>
                    <option>Asia/Tokyo</option>
                </select>
                <span class="settings-form-help">Used for all timestamps in the system</span>
            </div>

            <div class="settings-form-group">
                <label class="settings-form-label">Currency</label>
                <select class="settings-form-input">
                    <option>USD</option>
                    <option selected>EUR</option>
                    <option>GBP</option>
                </select>
                <span class="settings-form-help">Default currency for all products</span>
            </div>

            <div class="settings-button-group">
                <button type="button" class="settings-button settings-button-secondary">Reset</button>
                <button type="submit" class="settings-button settings-button-primary">Save Changes</button>
            </div>
        </form>
    </x-settings-section>

    <x-settings-section
        title="Business Information"
        description="Legal and support contact details"
    >
        <form class="settings-form">
            <div class="settings-form-group">
                <label class="settings-form-label">Legal Business Name</label>
                <input type="text" class="settings-form-input" value="Herd Inc." />
            </div>

            <div class="settings-form-group">
                <label class="settings-form-label">Support Email</label>
                <input type="email" class="settings-form-input" value="support@herd.test" />
            </div>

            <div class="settings-form-group">
                <label class="settings-form-label">Support Phone</label>
                <input type="tel" class="settings-form-input" value="+1 (555) 123-4567" />
            </div>

            <div class="settings-button-group">
                <button type="button" class="settings-button settings-button-secondary">Reset</button>
                <button type="submit" class="settings-button settings-button-primary">Save Changes</button>
            </div>
        </form>
    </x-settings-section>
</x-settings-layout>
