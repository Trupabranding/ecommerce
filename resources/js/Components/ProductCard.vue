<script setup lang="ts">
import { ref } from 'vue';
import { Product } from '../types';
import { Plus } from 'lucide-vue-next';

interface Props {
  product: Product;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'select', product: Product): void;
  (e: 'quick-add', product: Product): void;
}>();

const currentImage = ref(props.product.images[0]);
const isHovered = ref(false);

function handleMouseEnter() {
  isHovered.value = true;
  if (props.product.images[1]) {
    currentImage.value = props.product.images[1];
  }
}

function handleMouseLeave() {
  isHovered.value = false;
  currentImage.value = props.product.images[0];
}
</script>

<template>
  <div 
    id="product-card"
    class="group flex flex-col h-full bg-white border border-brand-dark/5 overflow-hidden transition-all duration-500 hover:shadow-xl hover:border-brand-dark/15"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <!-- Product Image Container -->
    <div 
      class="relative aspect-[3/4] overflow-hidden bg-brand-cream cursor-pointer"
      @click="emit('select', product)"
    >
      <img 
        :src="currentImage" 
        :alt="product.name"
        referrerpolicy="no-referrer"
        class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
      />

      <!-- Quick Add Overlay (Visible on Hover) -->
      <div 
        class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-gradient-to-t from-brand-dark/30 to-transparent flex justify-center"
      >
        <button 
          id="btn-quick-add"
          @click.stop="emit('quick-add', product)"
          class="w-full bg-brand-dark hover:bg-brand-dark/90 text-brand-cream text-xs font-medium tracking-[0.2em] py-3 px-4 shadow-lg transition-colors flex items-center justify-center space-x-2"
        >
          <Plus class="w-4 h-4" />
          <span>QUICK ADD</span>
        </button>
      </div>

      <!-- Out of Stock Badge -->
      <div 
        v-if="!product.inStock" 
        class="absolute top-4 left-4 bg-brand-dark/90 text-brand-cream text-[9px] font-mono tracking-widest px-2.5 py-1"
      >
        SOLD OUT
      </div>
      
      <!-- New/Featured Indicator -->
      <div 
        v-else-if="product.featured" 
        class="absolute top-4 left-4 bg-white/90 border border-brand-dark/10 text-brand-dark text-[9px] font-mono tracking-widest px-2.5 py-1"
      >
        FEATURED
      </div>
    </div>

    <!-- Info Section -->
    <div class="p-5 flex flex-col flex-1">
      <div class="flex items-center justify-between text-[10px] font-mono tracking-widest text-brand-muted uppercase">
        <span>{{ product.category }}</span>
        <div class="flex space-x-1">
          <span 
            v-for="color in product.colors" 
            :key="color.name"
            class="w-2.5 h-2.5 rounded-full border border-brand-dark/10"
            :style="{ backgroundColor: color.value }"
            :title="color.name"
          ></span>
        </div>
      </div>

      <h3 
        id="product-card-title"
        @click="emit('select', product)"
        class="mt-2 text-base font-serif font-medium text-brand-dark cursor-pointer hover:text-brand-dark/75 transition-colors line-clamp-1"
      >
        {{ product.name }}
      </h3>

      <div class="mt-2 flex items-baseline justify-between">
        <span class="text-sm font-mono font-medium text-brand-dark">
          ${{ product.price.toFixed(2) }}
        </span>
        <span class="text-[10px] font-mono text-brand-muted hover:text-brand-dark transition-colors cursor-pointer" @click="emit('select', product)">
          VIEW DETAILS →
        </span>
      </div>
    </div>
  </div>
</template>
