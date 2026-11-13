<script setup lang="ts">
import { ref, watch } from 'vue';
import { Product, ColorOption } from '../types';
import { ChevronLeft, Star, Shield, HelpCircle, ChevronDown, ChevronUp, Plus, Minus } from 'lucide-vue-next';

interface Props {
  product: Product;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'back'): void;
  (e: 'add-to-cart', product: Product, size: string, color: ColorOption, quantity: number): void;
}>();

const selectedSize = ref(props.product.sizes[0] || 'S');
const selectedColor = ref<ColorOption>(props.product.colors[0] || { name: 'Default', value: '#111111' });
const quantity = ref(1);
const activeImage = ref(props.product.images[0]);

// Collapsible panels
const activeAccordion = ref<string | null>('features');

function toggleAccordion(id: string) {
  if (activeAccordion.value === id) {
    activeAccordion.value = null;
  } else {
    activeAccordion.value = id;
  }
}

function handleAddToCart() {
  emit('add-to-cart', props.product, selectedSize.value, selectedColor.value, quantity.value);
}

// Reset view states when product changes
watch(() => props.product.id, () => {
  selectedSize.value = props.product.sizes[0] || 'S';
  selectedColor.value = props.product.colors[0] || { name: 'Default', value: '#111111' };
  quantity.value = 1;
  activeImage.value = props.product.images[0];
  activeAccordion.value = 'features';
});
</script>

<template>
  <div id="product-detail-view" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16">
    <!-- Back to collection link -->
    <button 
      id="btn-detail-back"
      @click="emit('back')" 
      class="inline-flex items-center text-xs font-mono tracking-widest text-brand-dark/50 hover:text-brand-dark mb-8 md:mb-12 group transition-colors cursor-pointer"
    >
      <ChevronLeft class="w-4 h-4 mr-1 transition-transform group-hover:-translate-x-1" />
      BACK TO ALL COLLECTIONS
    </button>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
      
      <!-- Left Column: Rich Gallery / Images -->
      <div class="lg:col-span-7 flex flex-col space-y-4">
        <!-- Main Image Container -->
        <div class="aspect-[3/4] bg-white border border-brand-dark/5 overflow-hidden relative">
          <img 
            :src="activeImage" 
            :alt="product.name" 
            referrerpolicy="no-referrer"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Thumbnail Row -->
        <div class="grid grid-cols-4 gap-4">
          <button 
            id="btn-thumbnail"
            v-for="(img, idx) in product.images" 
            :key="idx"
            @click="activeImage = img"
            class="aspect-[3/4] border overflow-hidden bg-brand-cream relative cursor-pointer"
            :class="activeImage === img ? 'border-brand-dark border-2' : 'border-brand-dark/5 hover:border-brand-dark/30'"
          >
            <img 
              :src="img" 
              alt="Thumbnail" 
              referrerpolicy="no-referrer"
              class="w-full h-full object-cover"
            />
          </button>
        </div>
      </div>

      <!-- Right Column: Detail Content -->
      <div class="lg:col-span-5 flex flex-col">
        <!-- Breadcrumb & category -->
        <span class="text-[10px] font-mono tracking-[0.2em] text-brand-muted uppercase mb-2">
          {{ product.category }}
        </span>

        <!-- Title -->
        <h1 id="detail-title" class="text-3xl md:text-4xl font-serif font-medium text-brand-dark leading-tight">
          {{ product.name }}
        </h1>

        <!-- Rating and price -->
        <div class="mt-4 flex items-center justify-between border-b border-brand-dark/10 pb-6">
          <div class="flex items-center space-x-4">
            <span class="font-mono text-xl font-bold text-brand-dark">${{ product.price.toFixed(2) }}</span>
            <span class="text-xs text-brand-cream bg-brand-dark px-2 py-0.5 font-mono">Tax Included</span>
          </div>
          
          <div class="flex items-center space-x-1.5">
            <div class="flex text-yellow-500">
              <Star v-for="i in 5" :key="i" class="w-3.5 h-3.5 fill-current" />
            </div>
            <span class="font-mono text-xs text-brand-muted">({{ product.reviewsCount }} reviews)</span>
          </div>
        </div>

        <!-- Long description -->
        <p class="mt-6 text-sm text-brand-muted font-sans leading-relaxed">
          {{ product.description }}
        </p>

        <!-- Dynamic configuration swatches -->
        <div class="mt-8 space-y-6">
          
          <!-- Colors -->
          <div>
            <div class="flex justify-between items-baseline mb-3">
              <span class="text-xs font-mono tracking-widest text-brand-dark">COLOR:</span>
              <span class="text-xs font-medium text-brand-muted font-sans">{{ selectedColor.name }}</span>
            </div>
            <div class="flex space-x-3.5">
              <button 
                id="btn-color-swatch"
                v-for="color in product.colors" 
                :key="color.name"
                @click="selectedColor = color"
                class="w-7 h-7 rounded-full border flex items-center justify-center transition-all duration-300 relative cursor-pointer"
                :class="selectedColor.name === color.name ? 'border-brand-dark border-2 scale-105' : 'border-brand-dark/10 hover:scale-105'"
                :title="color.name"
              >
                <span 
                  class="w-5 h-5 rounded-full" 
                  :style="{ backgroundColor: color.value }"
                ></span>
              </button>
            </div>
          </div>

          <!-- Sizes -->
          <div>
            <div class="flex justify-between items-baseline mb-3">
              <span class="text-xs font-mono tracking-widest text-brand-dark">SIZE:</span>
              <button class="text-xs text-brand-muted underline hover:text-brand-dark transition-colors font-sans">
                Size Guide
              </button>
            </div>
            <div class="flex flex-wrap gap-2.5">
              <button 
                id="btn-size-option"
                v-for="size in product.sizes" 
                :key="size"
                @click="selectedSize = size"
                class="min-w-[48px] h-11 border text-xs font-mono font-medium flex items-center justify-center px-3 transition-all duration-300 cursor-pointer"
                :class="selectedSize === size ? 'bg-brand-dark text-brand-cream border-brand-dark' : 'bg-white text-brand-dark border-brand-dark/15 hover:border-brand-dark'"
              >
                {{ size }}
              </button>
            </div>
          </div>

          <!-- Quantity Controller -->
          <div>
            <span class="text-xs font-mono tracking-widest text-brand-dark block mb-3">QUANTITY:</span>
            <div class="flex items-center border border-brand-dark/15 rounded-md w-32 bg-white overflow-hidden">
              <button 
                id="btn-detail-qty-minus"
                @click="quantity = Math.max(1, quantity - 1)"
                class="p-2.5 hover:bg-brand-dark/5 text-brand-dark transition-colors w-10 flex justify-center"
                :disabled="quantity <= 1"
              >
                <Minus class="w-3.5 h-3.5" />
              </button>
              <input 
                id="input-detail-qty"
                type="number" 
                v-model.number="quantity" 
                min="1"
                class="w-12 text-center text-sm font-mono font-medium text-brand-dark border-none focus:outline-none"
              />
              <button 
                id="btn-detail-qty-plus"
                @click="quantity++"
                class="p-2.5 hover:bg-brand-dark/5 text-brand-dark transition-colors w-10 flex justify-center"
              >
                <Plus class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          <!-- Add to Cart -->
          <div class="pt-4">
            <button 
              id="btn-detail-add-to-cart"
              @click="handleAddToCart"
              class="w-full bg-brand-dark hover:bg-brand-dark/95 text-brand-cream text-xs font-medium tracking-[0.25em] py-4.5 shadow-xl transition-all hover:translate-y-[-1px] duration-300 cursor-pointer"
            >
              ADD TO BAG • ${{ (product.price * quantity).toFixed(2) }}
            </button>
          </div>
        </div>

        <!-- Collapsible Accordion sections -->
        <div class="mt-12 border-t border-brand-dark/15 divide-y divide-brand-dark/10">
          
          <!-- Features Accordion -->
          <div class="py-4">
            <button 
              id="btn-accordion-features"
              @click="toggleAccordion('features')"
              class="w-full flex items-center justify-between text-left text-xs font-mono tracking-widest text-brand-dark py-2 cursor-pointer"
            >
              <span>FEATURES</span>
              <ChevronDown v-if="activeAccordion !== 'features'" class="w-4 h-4 text-brand-muted" />
              <ChevronUp v-else class="w-4 h-4 text-brand-muted" />
            </button>
            
            <div 
              v-if="activeAccordion === 'features'" 
              class="mt-3 pl-4 text-xs text-brand-muted leading-relaxed font-sans space-y-2.5"
            >
              <li v-for="(feat, idx) in product.features" :key="idx" class="list-disc pl-1">
                {{ feat }}
              </li>
            </div>
          </div>

          <!-- Details Accordion -->
          <div class="py-4">
            <button 
              id="btn-accordion-details"
              @click="toggleAccordion('details')"
              class="w-full flex items-center justify-between text-left text-xs font-mono tracking-widest text-brand-dark py-2 cursor-pointer"
            >
              <span>SPECIFICATIONS & CARE</span>
              <ChevronDown v-if="activeAccordion !== 'details'" class="w-4 h-4 text-brand-muted" />
              <ChevronUp v-else class="w-4 h-4 text-brand-muted" />
            </button>
            
            <div 
              v-if="activeAccordion === 'details'" 
              class="mt-3 divide-y divide-brand-dark/5"
            >
              <div 
                v-for="detail in product.details" 
                :key="detail.label" 
                class="py-2.5 flex justify-between text-xs font-sans"
              >
                <span class="text-brand-muted font-medium">{{ detail.label }}</span>
                <span class="text-brand-dark font-medium text-right ml-4">{{ detail.value }}</span>
              </div>
            </div>
          </div>

          <!-- Shipping Accordion -->
          <div class="py-4">
            <button 
              id="btn-accordion-shipping"
              @click="toggleAccordion('shipping')"
              class="w-full flex items-center justify-between text-left text-xs font-mono tracking-widest text-brand-dark py-2 cursor-pointer"
            >
              <span>SHIPPING & RETURNS</span>
              <ChevronDown v-if="activeAccordion !== 'shipping'" class="w-4 h-4 text-brand-muted" />
              <ChevronUp v-else class="w-4 h-4 text-brand-muted" />
            </button>
            
            <div 
              v-if="activeAccordion === 'shipping'" 
              class="mt-3 text-xs text-brand-muted leading-relaxed font-sans space-y-3"
            >
              <p>
                <strong>Free Standard Shipping:</strong> Complimentary on all domestic orders over $150. Delivery within 3–5 business days.
              </p>
              <p>
                <strong>Express Delivery:</strong> Available at checkout for flat $25. Delivery within 1-2 business days.
              </p>
              <p>
                <strong>Hassle-Free Returns:</strong> We accept returns of unworn, unwashed items in original packaging within 30 days of delivery. Prepaid return labels provided.
              </p>
            </div>
          </div>
        </div>

        <!-- Trust Badges -->
        <div class="mt-8 bg-white border border-brand-dark/5 p-4 rounded-lg flex items-center justify-between space-x-4 text-center">
          <div class="flex flex-col items-center flex-1">
            <Shield class="w-5 h-5 text-brand-dark mb-1 stroke-1" />
            <span class="text-[9px] font-mono tracking-wider text-brand-dark">SECURE CHECKOUT</span>
          </div>
          <div class="h-8 w-[1px] bg-brand-dark/10"></div>
          <div class="flex flex-col items-center flex-1">
            <Star class="w-5 h-5 text-brand-dark mb-1 stroke-1" />
            <span class="text-[9px] font-mono tracking-wider text-brand-dark">PREMIUM SILHOUETTES</span>
          </div>
          <div class="h-8 w-[1px] bg-brand-dark/10"></div>
          <div class="flex flex-col items-center flex-1">
            <HelpCircle class="w-5 h-5 text-brand-dark mb-1 stroke-1" />
            <span class="text-[9px] font-mono tracking-wider text-brand-dark">24/7 SUPPORT</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>
