<script setup lang="ts">
import { computed } from 'vue';
import ProductCard from './ProductCard.vue';
import { Product } from '../types';
import { PRODUCTS } from '../products';

interface Props {
  product: Product;
  limit?: number;
}

const props = withDefaults(defineProps<Props>(), {
  limit: 4,
});

const emit = defineEmits<{
  (e: 'select-product', product: Product): void;
}>();

// Get related products from same category, excluding current product
const relatedProducts = computed(() => {
  return PRODUCTS.filter(
    p => p.category === props.product.category && p.id !== props.product.id
  ).slice(0, props.limit);
});
</script>

<template>
  <div v-if="relatedProducts.length > 0" class="mt-20">
    <!-- Section Title -->
    <div class="mb-12">
      <h2 class="text-3xl font-serif font-medium text-brand-dark mb-2">
        Related Products
      </h2>
      <p class="text-sm text-brand-muted">
        Explore more from {{ product.category }}
      </p>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <ProductCard
        v-for="product in relatedProducts"
        :key="product.id"
        :product="product"
        @click="emit('select-product', product)"
      />
    </div>
  </div>
</template>
