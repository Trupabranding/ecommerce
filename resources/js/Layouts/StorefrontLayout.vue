<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Header from '../Components/Header.vue';
import Footer from '../Components/Footer.vue';
import CartDrawer from '../Components/CartDrawer.vue';
import Toast from '../Components/Toast.vue';

interface CartItem {
  id: string;
  product: any;
  selectedSize: string;
  selectedColor: any;
  quantity: number;
}

interface Props {
  title?: string;
}

withDefaults(defineProps<Props>(), {
  title: 'Herd eCommerce',
});

// Shared State for all Storefront Pages
const cartItems = ref<CartItem[]>([]);
const isCartOpen = ref(false);
const toastMessage = ref('');
const isToastVisible = ref(false);
const currentView = ref('home');

// Computed cart count
const cartCount = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

// Toast management
function showToast(message: string) {
  toastMessage.value = message;
  isToastVisible.value = true;
}

function handleCloseToast() {
  isToastVisible.value = false;
}

// Navigation
function handleNavigate(view: string) {
  currentView.value = view;
  if (view === 'home') {
    router.visit('/');
  } else if (view === 'shop') {
    router.visit('/shop');
  }
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
      const itemName = cartItems.value[itemIndex].product?.name || 'Item';
      cartItems.value.splice(itemIndex, 1);
      showToast(`REMOVED ${itemName.toUpperCase()} FROM BAG`);
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
  router.visit('/checkout');
}

// Public method for child pages to add items
function addToCart(product: any, size: string, color: any, quantity: number = 1) {
  const cartItemId = `${product.id}-${size}-${color.value}`;
  const existingIndex = cartItems.value.findIndex(item => item.id === cartItemId);

  if (existingIndex > -1) {
    cartItems.value[existingIndex].quantity += quantity;
  } else {
    cartItems.value.push({
      id: cartItemId,
      product,
      selectedSize: size,
      selectedColor: color,
      quantity
    });
  }

  showToast(`ADDED ${quantity}x ${product.name.toUpperCase()} TO BAG`);
  setTimeout(() => {
    isCartOpen.value = true;
  }, 400);

  persistCart();
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

// Expose methods to child pages
defineExpose({
  addToCart,
  showToast,
  cartItems,
  cartCount,
});
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
