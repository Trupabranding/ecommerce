<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import Header from '../Components/Header.vue';
import Footer from '../Components/Footer.vue';
import CartDrawer from '../Components/CartDrawer.vue';
import Toast from '../Components/Toast.vue';

interface Props {
  title?: string;
  cart?: any[];
  products?: any[];
}

withDefaults(defineProps<Props>(), {
  title: 'Herd eCommerce',
  cart: () => [],
  products: () => [],
});

// State Management
const cartItems = ref<any[]>([]);
const isCartOpen = ref(false);
const toastMessage = ref('');
const isToastVisible = ref(false);
const currentView = ref('home');

// Compute cart count
const cartCount = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

// Navigation handler
function handleNavigate(view: string) {
  currentView.value = view;
  // In a real implementation, this would use Inertia.visit()
  // window.location.href = route(view);
}

// Cart operations
function handleOpenCart() {
  isCartOpen.value = true;
}

function handleCloseCart() {
  isCartOpen.value = false;
}

function handleUpdateQuantity(id: string, qty: number) {
  const itemIndex = cartItems.value.findIndex(item => item.id === id);
  if (itemIndex > -1) {
    if (qty <= 0) {
      cartItems.value.splice(itemIndex, 1);
    } else {
      cartItems.value[itemIndex].quantity = qty;
    }
  }
  persistCart();
}

function handleRemoveItem(id: string) {
  const itemIndex = cartItems.value.findIndex(item => item.id === id);
  if (itemIndex > -1) {
    const itemName = cartItems.value[itemIndex].product?.name || 'Item';
    cartItems.value.splice(itemIndex, 1);
    showToast(`REMOVED ${itemName.toUpperCase()} FROM BAG`);
  }
  persistCart();
}

function handleCheckout() {
  isCartOpen.value = false;
  // Navigate to checkout
  window.location.href = route('checkout.index');
}

// Toast notification
function showToast(message: string) {
  toastMessage.value = message;
  isToastVisible.value = true;
}

function handleCloseToast() {
  isToastVisible.value = false;
}

// Cart persistence
function persistCart() {
  localStorage.setItem('herd_cart', JSON.stringify(cartItems.value));
}

// Load cart from localStorage
onMounted(() => {
  const savedCart = localStorage.getItem('herd_cart');
  if (savedCart) {
    try {
      cartItems.value = JSON.parse(savedCart);
    } catch (e) {
      console.error('Failed to load cart:', e);
    }
  }
});

// Watch cart changes
watch(cartItems, persistCart, { deep: true });
</script>

<template>
  <div class="min-h-screen flex flex-col bg-brand-cream text-brand-dark selection:bg-brand-dark selection:text-brand-cream">

    <!-- Toast Notifications -->
    <Toast
      :message="toastMessage"
      :isVisible="isToastVisible"
      @close="handleCloseToast"
    />

    <!-- Header -->
    <Header
      :cartCount="cartCount"
      :activeView="currentView"
      @navigate="handleNavigate"
      @open-cart="handleOpenCart"
    />

    <!-- Main Content Area -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Cart Drawer -->
    <CartDrawer
      v-if="isCartOpen"
      :items="cartItems"
      @close="handleCloseCart"
      @update-quantity="handleUpdateQuantity"
      @remove="handleRemoveItem"
      @checkout="handleCheckout"
    />

    <!-- Footer -->
    <Footer
      :currentView="currentView"
      @navigate="handleNavigate"
      @scroll-top="() => window.scrollTo({ top: 0, behavior: 'smooth' })"
    />
  </div>
</template>

<style scoped>
:root {
  --color-brand-cream: #faf9f7;
  --color-brand-dark: #0a0a0a;
  --color-brand-orange: #ff5722;
}
</style>
