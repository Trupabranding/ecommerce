<script setup lang="ts">
import { computed } from 'vue';
import { CheckCircle, Truck, Package, ShoppingBag, ArrowRight } from 'lucide-vue-next';
import { CartItem, ShippingAddress } from '../types';

interface Props {
  items: CartItem[];
  address: ShippingAddress;
  paymentMethod: string;
  subtotal: number;
  discountAmount: number;
  shippingCost: number;
  tax: number;
  total: number;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'continue-shopping'): void;
}>();

const orderId = computed(() => {
  const randomPart = Math.floor(100000 + Math.random() * 900000);
  return `HRD-2026-${randomPart}`;
});

const estimatedDeliveryDate = computed(() => {
  const date = new Date();
  date.setDate(date.getDate() + 4);
  return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
});
</script>

<template>
  <div id="order-confirmation-view" class="max-w-3xl mx-auto px-4 py-16 md:py-24 text-center">
    
    <!-- Success Icon & Celebration -->
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-dark text-brand-cream mb-8">
      <CheckCircle class="w-8 h-8 stroke-1" />
    </div>

    <p class="text-xs font-mono tracking-[0.25em] text-brand-muted uppercase mb-3">THANK YOU FOR YOUR ORDER</p>
    <h1 id="confirmation-title" class="text-4xl md:text-5xl font-serif font-medium text-brand-dark tracking-wide mb-6">
      Sartorial Elegance is on its Way.
    </h1>
    <p class="text-sm text-brand-muted max-w-lg mx-auto leading-relaxed mb-12">
      Your order has been placed successfully and is currently being curated by our atelier. A confirmation email with shipping details has been dispatched to <strong class="text-brand-dark">{{ address.email }}</strong>.
    </p>

    <!-- Order Metadata Card -->
    <div class="bg-white border border-brand-dark/10 rounded-lg p-6 md:p-8 text-left space-y-6 shadow-sm mb-12">
      
      <!-- ID and Estimated Delivery -->
      <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-brand-dark/5 pb-4 space-y-2 md:space-y-0">
        <div>
          <span class="text-[10px] font-mono text-brand-muted">ORDER ID</span>
          <p class="font-mono text-sm font-bold text-brand-dark mt-0.5">{{ orderId }}</p>
        </div>
        <div class="md:text-right">
          <span class="text-[10px] font-mono text-brand-muted">ESTIMATED ARRIVAL</span>
          <p class="text-sm font-medium text-brand-dark mt-0.5">{{ estimatedDeliveryDate }}</p>
        </div>
      </div>

      <!-- Live Delivery Progress Bar -->
      <div class="py-4">
        <span class="text-[10px] font-mono text-brand-muted block mb-4">DELIVERY STATUS</span>
        <div class="flex items-center justify-between relative">
          <!-- Connective line -->
          <div class="absolute left-0 right-0 h-0.5 bg-brand-dark/10 top-3 -z-0"></div>
          <div class="absolute left-0 w-1/3 h-0.5 bg-brand-dark top-3 -z-0"></div>

          <!-- Step 1 -->
          <div class="flex flex-col items-center z-10 bg-white px-2">
            <div class="w-6.5 h-6.5 rounded-full bg-brand-dark text-brand-cream flex items-center justify-center text-xs border border-brand-dark">
              ✓
            </div>
            <span class="text-[9px] font-mono font-bold text-brand-dark mt-2">CONFIRMED</span>
          </div>

          <!-- Step 2 -->
          <div class="flex flex-col items-center z-10 bg-white px-2">
            <div class="w-6.5 h-6.5 rounded-full bg-brand-dark text-brand-cream flex items-center justify-center text-xs border border-brand-dark animate-pulse">
              <Package class="w-3.5 h-3.5" />
            </div>
            <span class="text-[9px] font-mono font-bold text-brand-dark mt-2">CURATING</span>
          </div>

          <!-- Step 3 -->
          <div class="flex flex-col items-center z-10 bg-white px-2">
            <div class="w-6.5 h-6.5 rounded-full bg-brand-cream text-brand-dark/30 flex items-center justify-center text-xs border border-brand-dark/10">
              <Truck class="w-3.5 h-3.5" />
            </div>
            <span class="text-[9px] font-mono text-brand-muted mt-2">SHIPPED</span>
          </div>

          <!-- Step 4 -->
          <div class="flex flex-col items-center z-10 bg-white px-2">
            <div class="w-6.5 h-6.5 rounded-full bg-brand-cream text-brand-dark/30 flex items-center justify-center text-xs border border-brand-dark/10">
              ★
            </div>
            <span class="text-[9px] font-mono text-brand-muted mt-2">DELIVERED</span>
          </div>
        </div>
      </div>

      <!-- Recaps Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-brand-dark/5">
        <div>
          <span class="text-[10px] font-mono text-brand-muted block mb-1">SHIPPING ADDRESS</span>
          <p class="text-sm font-medium text-brand-dark leading-relaxed font-sans">
            {{ address.firstName }} {{ address.lastName }}<br />
            {{ address.address }}<br />
            <span v-if="address.apartment">{{ address.apartment }}<br /></span>
            {{ address.postalCode }} {{ address.city }}<br />
            {{ address.country }}
          </p>
        </div>
        <div>
          <span class="text-[10px] font-mono text-brand-muted block mb-1">PAYMENT DETAILS</span>
          <p class="text-sm font-medium text-brand-dark font-sans leading-relaxed">
            Method: <strong class="font-mono text-xs">{{ paymentMethod }}</strong><br />
            Auth Status: <span class="text-green-700 font-bold text-xs uppercase font-mono">Approved</span><br />
            Reference: <span class="font-mono text-xs text-brand-muted">PAY-{{ Math.floor(Math.random() * 90000) }}</span>
          </p>
        </div>
      </div>

      <!-- Brief products recaps -->
      <div class="border-t border-brand-dark/5 pt-6">
        <span class="text-[10px] font-mono text-brand-muted block mb-3">ITEMS ORDERED</span>
        <div class="space-y-4">
          <div v-for="item in items" :key="item.id" class="flex items-center justify-between text-xs font-sans">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-11 bg-brand-cream overflow-hidden border border-brand-dark/5">
                <img :src="item.product.images[0]" :alt="item.product.name" referrerpolicy="no-referrer" class="w-full h-full object-cover" />
              </div>
              <div>
                <p class="font-serif font-medium text-brand-dark">{{ item.product.name }}</p>
                <p class="text-[10px] font-mono text-brand-muted mt-0.5">SIZE: {{ item.selectedSize }} • QTY: {{ item.quantity }}</p>
              </div>
            </div>
            <span class="font-mono text-brand-dark font-medium">${{ (item.product.price * item.quantity).toFixed(2) }}</span>
          </div>
        </div>
      </div>

      <!-- Billing maths -->
      <div class="border-t border-brand-dark/5 pt-4 space-y-1.5 text-xs font-mono text-right flex flex-col items-end">
        <div class="flex justify-between w-full max-w-xs text-brand-muted">
          <span>SUBTOTAL:</span>
          <span>${{ subtotal.toFixed(2) }}</span>
        </div>
        <div v-if="discountAmount > 0" class="flex justify-between w-full max-w-xs text-green-700">
          <span>DISCOUNT:</span>
          <span>-${{ discountAmount.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between w-full max-w-xs text-brand-muted">
          <span>SHIPPING:</span>
          <span>${{ shippingCost.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between w-full max-w-xs text-brand-muted">
          <span>TAX (8%):</span>
          <span>${{ tax.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between w-full max-w-xs text-sm font-bold text-brand-dark pt-2 border-t border-brand-dark/10 mt-1">
          <span class="font-serif">TOTAL CHARGED:</span>
          <span>${{ total.toFixed(2) }}</span>
        </div>
      </div>

    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-center gap-4">
      <button 
        id="btn-confirmation-shop"
        @click="emit('continue-shopping')"
        class="bg-brand-dark hover:bg-brand-dark/95 text-brand-cream text-xs font-medium tracking-[0.25em] py-4 px-10 shadow-lg transition-colors inline-flex items-center justify-center space-x-2 cursor-pointer"
      >
        <span>CONTINUE SHOPPING</span>
        <ArrowRight class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>
