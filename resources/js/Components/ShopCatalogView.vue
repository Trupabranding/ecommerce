<script setup lang="ts">
import { ref, computed } from 'vue';
import { Product } from '../types';
import { CATEGORIES } from '../products';
import ProductCard from './ProductCard.vue';
import { SlidersHorizontal, Search, RotateCcw } from 'lucide-vue-next';

interface Props {
  products: Product[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'select-product', product: Product): void;
  (e: 'quick-add', product: Product): void;
}>();

// Filter & Sort State
const activeCategory = ref('All');
const searchQuery = ref('');
const sortBy = ref('default');

const filteredProducts = computed(() => {
  let result = [...props.products];

  // 1. Filter by category
  if (activeCategory.value !== 'All') {
    result = result.filter(p => p.category === activeCategory.value);
  }

  // 2. Filter by search query
  if (searchQuery.value.trim() !== '') {
    const q = searchQuery.value.toLowerCase().trim();
    result = result.filter(p => p.name.toLowerCase().includes(q) || p.description.toLowerCase().includes(q));
  }

  // 3. Sort
  if (sortBy.value === 'price-low') {
    result.sort((a, b) => a.price - b.price);
  } else if (sortBy.value === 'price-high') {
    result.sort((a, b) => b.price - a.price);
  } else if (sortBy.value === 'rating') {
    result.sort((a, b) => b.rating - a.rating);
  }

  return result;
});

function resetFilters() {
  activeCategory.value = 'All';
  searchQuery.value = '';
  sortBy.value = 'default';
}
</script>

<template>
  <div id="shop-catalog-view" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16">
    <!-- Header visual -->
    <div class="text-center max-w-xl mx-auto mb-12 space-y-4">
      <span class="text-[10px] font-mono tracking-[0.25em] text-brand-muted block uppercase">COLLECTIONS</span>
      <h1 id="shop-title" class="text-3xl md:text-5xl font-serif font-medium tracking-wide text-brand-dark">SHOP ALL</h1>
      <p class="text-xs md:text-sm text-brand-muted leading-relaxed font-sans font-light">
        Discover the full architectural range. Hand-spun organic cottons, flowing wool blends, heavyweight knits, and deconstructed outerwear designed with deliberate spatial elegance.
      </p>
    </div>

    <!-- Filters and Utilities layout bar -->
    <div class="border-y border-brand-dark/10 py-6 mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
      
      <!-- Categories Pill Tabs -->
      <div class="flex flex-wrap items-center gap-2">
        <button 
          id="btn-category-tab"
          v-for="cat in CATEGORIES" 
          :key="cat"
          @click="activeCategory = cat"
          class="px-4 py-2 rounded-full text-xs font-mono tracking-widest uppercase transition-all cursor-pointer"
          :class="activeCategory === cat ? 'bg-brand-dark text-brand-cream border border-brand-dark' : 'bg-white text-brand-dark/60 border border-brand-dark/10 hover:border-brand-dark'"
        >
          {{ cat }}
        </button>
      </div>

      <!-- Controls Right: Search and Sort -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
        
        <!-- Search filter input field -->
        <div class="relative flex items-center border border-brand-dark/15 rounded-md bg-white px-3 py-2 w-full sm:w-60 focus-within:border-brand-dark transition-colors">
          <Search class="w-4 h-4 text-brand-dark/30 mr-2.5" />
          <input 
            id="input-catalog-search"
            type="text" 
            placeholder="Search catalog..." 
            v-model="searchQuery"
            class="bg-transparent text-xs text-brand-dark outline-none w-full tracking-wider placeholder:text-brand-dark/30"
          />
        </div>

        <!-- Sorting dropdown selection -->
        <div class="relative flex items-center border border-brand-dark/15 rounded-md bg-white px-3 py-2 w-full sm:w-48">
          <SlidersHorizontal class="w-3.5 h-3.5 text-brand-dark/40 mr-2.5" />
          <select 
            id="select-catalog-sort"
            v-model="sortBy"
            class="bg-transparent text-xs text-brand-dark outline-none w-full tracking-wider cursor-pointer font-sans appearance-none"
          >
            <option value="default">SORT BY: DEFAULT</option>
            <option value="price-low">PRICE: LOW TO HIGH</option>
            <option value="price-high">PRICE: HIGH TO LOW</option>
            <option value="rating">RATING: HIGHEST</option>
          </select>
        </div>

      </div>
    </div>

    <!-- Active filters overview -->
    <div v-if="searchQuery || activeCategory !== 'All' || sortBy !== 'default'" class="flex items-center justify-between mb-8 bg-brand-cream border border-brand-dark/5 p-4 rounded-lg">
      <div class="text-xs font-mono text-brand-muted flex items-center flex-wrap gap-2">
        <span>ACTIVE FILTERS:</span>
        <span v-if="activeCategory !== 'All'" class="bg-white border border-brand-dark/10 px-2.5 py-0.5 rounded font-bold text-brand-dark">
          CAT: {{ activeCategory.toUpperCase() }}
        </span>
        <span v-if="searchQuery" class="bg-white border border-brand-dark/10 px-2.5 py-0.5 rounded font-bold text-brand-dark">
          SEARCH: "{{ searchQuery }}"
        </span>
        <span v-if="sortBy !== 'default'" class="bg-white border border-brand-dark/10 px-2.5 py-0.5 rounded font-bold text-brand-dark">
          SORT: {{ sortBy.toUpperCase() }}
        </span>
      </div>
      <button 
        id="btn-catalog-reset"
        @click="resetFilters" 
        class="inline-flex items-center text-xs font-mono text-brand-dark hover:opacity-75 transition-opacity"
      >
        <RotateCcw class="w-3.5 h-3.5 mr-1.5" />
        RESET
      </button>
    </div>

    <!-- Result description count -->
    <div class="text-xs font-mono text-brand-muted mb-6">
      SHOWING {{ filteredProducts.length }} OF {{ products.length }} SILHOUETTES
    </div>

    <!-- Catalog Product Grid -->
    <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <div 
        v-for="product in filteredProducts" 
        :key="product.id"
        class="h-full"
      >
        <ProductCard 
          :product="product" 
          @select="emit('select-product', $event)"
          @quick-add="emit('quick-add', $event)"
        />
      </div>
    </div>

    <!-- Empty search/filter state -->
    <div v-else class="text-center py-24 bg-white border border-brand-dark/5 rounded-lg max-w-xl mx-auto">
      <SlidersHorizontal class="w-12 h-12 text-brand-dark/15 mx-auto mb-4 stroke-1" />
      <h3 class="font-serif text-lg font-medium text-brand-dark">No silhouettes found</h3>
      <p class="text-xs text-brand-muted mt-2 max-w-sm mx-auto leading-relaxed">
        We couldn't find any items matching your selected criteria. Try adjusting your filters or search keywords.
      </p>
      <button 
        id="btn-catalog-clear-all"
        @click="resetFilters"
        class="mt-6 bg-brand-dark hover:bg-brand-dark/90 text-brand-cream text-xs font-mono tracking-widest py-3 px-6 transition-colors"
      >
        CLEAR ALL FILTERS
      </button>
    </div>

  </div>
</template>
