<script setup lang="ts">
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import ShopCatalogView from '../Components/ShopCatalogView.vue';
import { PRODUCTS } from '../products';
import { Filter } from 'lucide-vue-next';

const props = defineProps<{
  category: string;
}>();

const sortBy = ref('newest');
const showFilters = ref(false);

// Filter products by category
const filteredProducts = computed(() => {
  let products = PRODUCTS.filter(p => p.category === props.category);

  // Sort
  switch (sortBy.value) {
    case 'price-low':
      return products.sort((a, b) => a.price - b.price);
    case 'price-high':
      return products.sort((a, b) => b.price - a.price);
    case 'popular':
      return products.sort((a, b) => b.reviewsCount - a.reviewsCount);
    default:
      return products;
  }
});

function handleSelectProduct(product: any) {
  router.visit(`/products/${product.id}`);
}

function handleQuickAdd(product: any) {
  console.log('Quick add:', product.id);
}
</script>

<template>
  <div class="w-full">
    <!-- Category Header -->
    <div class="bg-brand-cream border-b border-brand-dark/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-4xl md:text-5xl font-serif font-medium text-brand-dark mb-2">
              {{ category }}
            </h1>
            <p class="text-sm text-brand-muted">
              {{ filteredProducts.length }} items
            </p>
          </div>

          <!-- Sort & Filter Controls -->
          <div class="flex items-center space-x-4">
            <select
              v-model="sortBy"
              class="text-xs font-mono tracking-widest text-brand-dark bg-white border border-brand-dark/10 px-3 py-2 rounded cursor-pointer"
            >
              <option value="newest">NEWEST</option>
              <option value="price-low">PRICE: LOW TO HIGH</option>
              <option value="price-high">PRICE: HIGH TO LOW</option>
              <option value="popular">MOST POPULAR</option>
            </select>

            <button
              @click="showFilters = !showFilters"
              class="p-2 text-brand-dark hover:bg-brand-dark/5 rounded transition-colors"
            >
              <Filter class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <ShopCatalogView
      :products="filteredProducts"
      @select-product="handleSelectProduct"
      @quick-add="handleQuickAdd"
    />
  </div>
</template>
