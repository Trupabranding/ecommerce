<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { PRODUCTS } from './products';
import { Product, CartItem, ColorOption, ShippingAddress } from './types';
import Header from './components/Header.vue';
import HomeHeroView from './components/HomeHeroView.vue';
import ShopCatalogView from './components/ShopCatalogView.vue';
import ProductDetailView from './components/ProductDetailView.vue';
import CartDrawer from './components/CartDrawer.vue';
import CheckoutView from './components/CheckoutView.vue';
import OrderConfirmationView from './components/OrderConfirmationView.vue';
import { ArrowUp, Instagram, Twitter, Mail, HelpCircle, ShieldAlert } from 'lucide-vue-next';

// View State router
const currentView = ref<'home' | 'shop' | 'product-detail' | 'checkout' | 'confirmation'>('home');
const selectedProduct = ref<Product | null>(null);

// Cart State
const cartItems = ref<CartItem[]>([]);
const isCartOpen = ref(false);

// Toast Notification
const toastMessage = ref('');
const isToastVisible = ref(false);

// Completed Order Recap
const completedOrderDetails = ref<{
  items: CartItem[];
  address: ShippingAddress;
  paymentMethod: string;
  subtotal: number;
  discountAmount: number;
  shippingCost: number;
  tax: number;
  total: number;
} | null>(null);

// Scroll to top on view changes
function changeView(view: 'home' | 'shop' | 'product-detail' | 'checkout' | 'confirmation') {
  currentView.value = view;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Compute total number of items in the cart
const cartCount = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.quantity, 0);
});

// Compute featured products for Home View
const featuredProducts = computed(() => {
  return PRODUCTS.filter(p => p.featured).slice(0, 3);
});

// Select product and open Detail view
function handleSelectProduct(product: Product) {
  selectedProduct.value = product;
  changeView('product-detail');
}

// Show standard toast popup
function showToast(message: string) {
  toastMessage.value = message;
  isToastVisible.value = true;
  setTimeout(() => {
    isToastVisible.value = false;
  }, 3500);
}

// Add Item to cart
function handleAddToCart(product: Product, size: string, color: ColorOption, quantity: number) {
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
  
  // Open cart automatically to give beautiful feedback
  setTimeout(() => {
    isCartOpen.value = true;
  }, 400);
}

// Quick Add from Catalog
function handleQuickAdd(product: Product) {
  const size = product.sizes[0] || 'S';
  const color = product.colors[0] || { name: 'Noir', value: '#1C1C1C' };
  handleAddToCart(product, size, color, 1);
}

// Update item quantities
function handleUpdateQuantity(id: string, qty: number) {
  const itemIndex = cartItems.value.findIndex(item => item.id === id);
  if (itemIndex > -1) {
    if (qty <= 0) {
      cartItems.value.splice(itemIndex, 1);
    } else {
      cartItems.value[itemIndex].quantity = qty;
    }
  }
}

// Remove item from cart
function handleRemoveItem(id: string) {
  const itemIndex = cartItems.value.findIndex(item => item.id === id);
  if (itemIndex > -1) {
    const itemName = cartItems.value[itemIndex].product.name;
    cartItems.value.splice(itemIndex, 1);
    showToast(`REMOVED ${itemName.toUpperCase()} FROM BAG`);
  }
}

// Initiate checkout process
function handleProceedToCheckout() {
  if (cartItems.value.length === 0) return;
  isCartOpen.value = false;
  changeView('checkout');
}

// Order completion placement
function handleOrderComplete(details: { address: ShippingAddress; paymentMethod: string; discountCode: string; discountAmount: number }) {
  const subtotal = cartItems.value.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
  const shipCost = (subtotal > 150 || details.discountCode === 'FREESHIP') ? 0 : 15.00;
  const tax = (subtotal - details.discountAmount) * 0.08;
  const total = subtotal - details.discountAmount + shipCost + tax;

  completedOrderDetails.value = {
    items: [...cartItems.value],
    address: details.address,
    paymentMethod: details.paymentMethod,
    subtotal,
    discountAmount: details.discountAmount,
    shippingCost: shipCost,
    tax,
    total
  };

  // Clear the active cart and open the confirmation
  cartItems.value = [];
  changeView('confirmation');
}

function handleContinueShopping() {
  completedOrderDetails.value = null;
  changeView('shop');
}

// Scroll to top helper
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// LocalStorage load
onMounted(() => {
  const savedCart = localStorage.getItem('herd_cart');
  if (savedCart) {
    try {
      cartItems.value = JSON.parse(savedCart);
    } catch (e) {
      console.error('Failed to parse cart items', e);
    }
  }
});

// Autosave cart items
import { watch } from 'vue';
watch(cartItems, (newVal) => {
  localStorage.setItem('herd_cart', JSON.stringify(newVal));
}, { deep: true });
</script>

<template>
  <div class="min-h-screen flex flex-col bg-brand-cream text-brand-dark selection:bg-brand-dark selection:text-brand-cream">
    
    <!-- Toast Popup Notifications -->
    <Transition
      enter-active-class="transition duration-500 ease-out"
      enter-from-class="transform translate-y-4 opacity-0 scale-95"
      enter-to-class="transform translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-300 ease-in"
      leave-from-class="transform translate-y-0 opacity-100 scale-100"
      leave-to-class="transform translate-y-4 opacity-0 scale-95"
    >
      <div 
        id="toast-notification"
        v-if="isToastVisible" 
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-brand-dark text-brand-cream px-6 py-4 shadow-2xl flex items-center space-x-3.5 border border-white/10"
      >
        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
        <span class="text-xs font-mono font-bold tracking-widest">{{ toastMessage }}</span>
      </div>
    </Transition>

    <!-- Site Header -->
    <Header 
      :cartCount="cartCount" 
      :activeView="currentView" 
      @navigate="changeView"
      @open-cart="isCartOpen = true"
    />

    <!-- Main View Content Router Area -->
    <main class="flex-1">
      <Transition mode="out-in">
        <HomeHeroView 
          v-if="currentView === 'home'" 
          :featuredProducts="featuredProducts"
          @navigate="changeView"
          @select-product="handleSelectProduct"
        />

        <ShopCatalogView 
          v-else-if="currentView === 'shop'" 
          :products="PRODUCTS"
          @select-product="handleSelectProduct"
          @quick-add="handleQuickAdd"
        />

        <ProductDetailView 
          v-else-if="currentView === 'product-detail' && selectedProduct" 
          :product="selectedProduct"
          @back="changeView('shop')"
          @add-to-cart="handleAddToCart"
        />

        <CheckoutView 
          v-else-if="currentView === 'checkout'" 
          :items="cartItems"
          @back-to-cart="changeView('shop')"
          @order-complete="handleOrderComplete"
        />

        <OrderConfirmationView 
          v-else-if="currentView === 'confirmation' && completedOrderDetails" 
          v-bind="completedOrderDetails"
          @continue-shopping="handleContinueShopping"
        />
      </Transition>
    </main>

    <!-- Shopping Cart Drawer Overlay -->
    <CartDrawer 
      :isOpen="isCartOpen" 
      :items="cartItems" 
      @close="isCartOpen = false"
      @update-quantity="handleUpdateQuantity"
      @remove="handleRemoveItem"
      @checkout="handleProceedToCheckout"
      @navigate="changeView"
    />

    <!-- Editorial Site Footer -->
    <footer class="bg-brand-dark text-brand-cream pt-20 pb-8 border-t border-white/5 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
          
          <!-- Column 1: Newsletter -->
          <div class="md:col-span-5 space-y-5">
            <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">THE HERD CIRCLE</span>
            <h3 class="font-serif text-2xl font-light leading-relaxed">
              Sign up for private seasonal launches, editorial journals, and atelier events.
            </h3>
            <div class="flex border-b border-brand-cream/20 py-2 focus-within:border-brand-cream transition-colors">
              <input 
                id="footer-email-input"
                type="email" 
                placeholder="EMAIL ADDRESS" 
                class="bg-transparent text-xs text-brand-cream outline-none w-full tracking-widest placeholder:text-brand-cream/30"
              />
              <button 
                id="btn-footer-subscribe"
                @click="showToast('THANK YOU FOR JOINING THE CIRCLE')"
                class="text-xs font-mono tracking-widest hover:text-white/70 transition-colors cursor-pointer"
              >
                JOIN
              </button>
            </div>
          </div>

          <!-- Column 2: Navigation Links -->
          <div class="md:col-span-3 space-y-4 md:pl-10">
            <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">NAVIGATION</span>
            <div class="flex flex-col space-y-2.5 text-xs tracking-wider text-brand-cream/60">
              <button @click="changeView('home')" class="text-left hover:text-brand-cream cursor-pointer">HOME</button>
              <button @click="changeView('shop')" class="text-left hover:text-brand-cream cursor-pointer">SHOP COLLECTION</button>
              <a href="#" class="hover:text-brand-cream transition-colors">EDITORIAL JOURNAL</a>
              <a href="#" class="hover:text-brand-cream transition-colors">THE STUDIO STORY</a>
            </div>
          </div>

          <!-- Column 3: Contact / Support -->
          <div class="md:col-span-2 space-y-4">
            <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">SUPPORT</span>
            <div class="flex flex-col space-y-2.5 text-xs tracking-wider text-brand-cream/60">
              <a href="#" class="hover:text-brand-cream transition-colors">SHIPPING & EXPEDITING</a>
              <a href="#" class="hover:text-brand-cream transition-colors">EXCHANGES & RETURNS</a>
              <a href="#" class="hover:text-brand-cream transition-colors">SUSTAINABILITY PLEDGE</a>
              <a href="#" class="hover:text-brand-cream transition-colors">CONTACT STUDIO</a>
            </div>
          </div>

          <!-- Column 4: Social / Currency -->
          <div class="md:col-span-2 space-y-6">
            <div>
              <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block mb-3">CURRENCY</span>
              <select id="footer-currency-select" class="bg-zinc-950 border border-white/10 rounded px-2.5 py-1 text-xs text-brand-cream font-mono outline-none tracking-widest">
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="JPY">JPY (¥)</option>
              </select>
            </div>
            
            <div class="space-y-3">
              <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">CONNECT</span>
              <div class="flex items-center space-x-3 text-brand-cream/60">
                <a href="#" class="hover:text-brand-cream transition-colors" aria-label="Instagram"><Instagram class="w-4 h-4" /></a>
                <a href="#" class="hover:text-brand-cream transition-colors" aria-label="Twitter"><Twitter class="w-4 h-4" /></a>
                <a href="#" class="hover:text-brand-cream transition-colors" aria-label="Mail"><Mail class="w-4 h-4" /></a>
              </div>
            </div>
          </div>

        </div>

        <!-- Scroll back to top & copyrights -->
        <div class="border-t border-white/5 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-[10px] font-mono text-brand-cream/30 tracking-widest text-center sm:text-left">
            © 2026 HERD DESIGN. ALL RIGHTS RESERVED. ETHICALLY PRODUCED & ARCHIVED.
          </div>
          
          <button 
            id="btn-scroll-top"
            @click="scrollToTop"
            class="flex items-center space-x-2 text-[10px] font-mono tracking-widest text-brand-cream/50 hover:text-white transition-colors cursor-pointer group"
          >
            <span>SCROLL TO TOP</span>
            <ArrowUp class="w-3.5 h-3.5 transition-transform group-hover:-translate-y-0.5" />
          </button>
        </div>
      </div>
    </footer>

  </div>
</template>

<style>
/* Vue View Route Transition effects */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}

.v-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.v-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
