<script setup lang="ts">
import { ShoppingBag, X, Plus, Minus, Trash2 } from 'lucide-vue-next';
import { CartItem } from '../types';

interface Props {
  isOpen: boolean;
  items: CartItem[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'update-quantity', id: string, quantity: number): void;
  (e: 'remove', id: string): void;
  (e: 'checkout'): void;
  (e: 'navigate', view: string): void;
}>();

function getSubtotal() {
  return props.items.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
}
</script>

<template>
  <div 
    id="cart-drawer"
    v-if="isOpen" 
    class="fixed inset-0 z-50 overflow-hidden"
    role="dialog" 
    aria-modal="true"
  >
    <!-- Background overlay backdrop -->
    <div 
      @click="emit('close')"
      class="absolute inset-0 bg-brand-dark/30 backdrop-blur-xs transition-opacity duration-500 ease-out"
    ></div>

    <div class="absolute inset-y-0 right-0 max-w-full flex">
      <!-- Panel content -->
      <div class="w-screen max-w-md bg-white flex flex-col shadow-2xl h-full border-l border-brand-dark/10">
        <!-- Header -->
        <div class="px-6 py-6 border-b border-brand-dark/5 flex items-center justify-between">
          <div class="flex items-center space-x-2">
            <ShoppingBag class="w-5 h-5 text-brand-dark" />
            <span class="font-serif text-lg font-medium tracking-wider text-brand-dark">SHOPPING BAG</span>
            <span class="font-mono text-xs text-brand-muted">({{ items.length }})</span>
          </div>
          <button 
            id="btn-cart-close-drawer"
            @click="emit('close')" 
            class="p-2 -mr-2 text-brand-dark hover:opacity-70 transition-opacity"
            aria-label="Close cart"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Scrollable cart items list -->
        <div class="flex-1 overflow-y-auto px-6 py-6 divide-y divide-brand-dark/5">
          <div v-if="items.length === 0" class="h-full flex flex-col items-center justify-center text-center py-12">
            <ShoppingBag class="w-12 h-12 text-brand-dark/10 mb-4 stroke-1" />
            <h3 class="font-serif text-lg font-medium text-brand-dark">Your bag is empty</h3>
            <p class="text-xs text-brand-muted mt-2 max-w-xs leading-relaxed">
              Browse our collections to find carefully curated items to add to your personal wardrobe.
            </p>
            <button 
              id="btn-cart-shop-now"
              @click="emit('close'); emit('navigate', 'shop')"
              class="mt-6 bg-brand-dark hover:bg-brand-dark/90 text-brand-cream text-xs font-medium tracking-[0.2em] py-3 px-8 transition-all cursor-pointer"
            >
              SHOP NEW ARRIVALS
            </button>
          </div>

          <div 
            v-else 
            v-for="item in items" 
            :key="item.id" 
            class="py-6 flex first:pt-0 last:pb-0"
          >
            <!-- Product image -->
            <div class="h-24 w-18 flex-shrink-0 overflow-hidden bg-brand-cream border border-brand-dark/5">
              <img 
                :src="item.product.images[0]" 
                :alt="item.product.name"
                referrerpolicy="no-referrer"
                class="h-full w-full object-cover object-center"
              />
            </div>

            <!-- Product info -->
            <div class="ml-4 flex flex-1 flex-col">
              <div>
                <div class="flex justify-between text-sm font-medium text-brand-dark">
                  <h3 class="font-serif text-base line-clamp-1 pr-4">{{ item.product.name }}</h3>
                  <p class="font-mono ml-4 font-medium text-brand-dark">${{ (item.product.price * item.quantity).toFixed(2) }}</p>
                </div>
                <div class="mt-1 flex items-center space-x-3 text-xs text-brand-muted font-mono">
                  <span class="flex items-center">
                    SIZE: <strong class="text-brand-dark ml-1">{{ item.selectedSize }}</strong>
                  </span>
                  <span class="flex items-center">
                    COLOR: 
                    <span 
                      class="w-2.5 h-2.5 rounded-full border border-brand-dark/10 mx-1.5 inline-block"
                      :style="{ backgroundColor: item.selectedColor.value }"
                    ></span>
                    <strong class="text-brand-dark">{{ item.selectedColor.name }}</strong>
                  </span>
                </div>
              </div>

              <!-- Item actions -->
              <div class="flex flex-1 items-end justify-between text-sm">
                <!-- Quantity selector -->
                <div class="flex items-center border border-brand-dark/10 rounded-full bg-brand-cream overflow-hidden">
                  <button 
                    id="btn-qty-minus"
                    @click="emit('update-quantity', item.id, item.quantity - 1)"
                    :disabled="item.quantity <= 1"
                    class="p-1.5 hover:bg-brand-dark/5 text-brand-dark disabled:opacity-30 transition-colors"
                  >
                    <Minus class="w-3 h-3" />
                  </button>
                  <span class="px-3 text-xs font-mono font-medium text-brand-dark">{{ item.quantity }}</span>
                  <button 
                    id="btn-qty-plus"
                    @click="emit('update-quantity', item.id, item.quantity + 1)"
                    class="p-1.5 hover:bg-brand-dark/5 text-brand-dark transition-colors"
                  >
                    <Plus class="w-3 h-3" />
                  </button>
                </div>

                <!-- Delete button -->
                <button 
                  id="btn-cart-remove-item"
                  @click="emit('remove', item.id)"
                  class="p-2 text-brand-muted hover:text-red-600 transition-colors cursor-pointer"
                  title="Remove item"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer calculation section -->
        <div v-if="items.length > 0" class="border-t border-brand-dark/10 px-6 py-6 bg-brand-cream/40">
          <div class="flex justify-between text-base font-medium text-brand-dark">
            <span class="font-serif tracking-wide">SUBTOTAL</span>
            <span class="font-mono text-lg font-bold">${{ getSubtotal().toFixed(2) }}</span>
          </div>
          <p class="mt-1 text-xs text-brand-muted font-sans leading-normal">
            Shipping and taxes are calculated at checkout. Free shipping on orders over $150.
          </p>
          <div class="mt-6 flex flex-col space-y-3">
            <button 
              id="btn-cart-checkout"
              @click="emit('checkout')"
              class="w-full bg-brand-dark hover:bg-brand-dark/95 text-brand-cream text-xs font-medium tracking-[0.25em] py-4 shadow-lg transition-colors cursor-pointer"
            >
              PROCEED TO CHECKOUT
            </button>
            <button 
              id="btn-cart-continue-shopping"
              @click="emit('close')"
              class="w-full text-center text-xs font-medium tracking-[0.2em] py-2 text-brand-dark hover:opacity-75 transition-opacity cursor-pointer"
            >
              CONTINUE SHOPPING
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
