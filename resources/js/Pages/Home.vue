<script setup lang="ts">
import { computed, inject } from 'vue';
import { router } from '@inertiajs/vue3';
import HomeHeroView from '../Components/HomeHeroView.vue';
import { PRODUCTS } from '../products';

const layoutInstance = inject('layoutInstance');

// Get featured products
const featuredProducts = computed(() => {
  return PRODUCTS.filter(p => p.featured).slice(0, 3);
});

function handleNavigate(view: string) {
  if (view === 'shop') {
    router.visit('/shop');
  }
}

function handleSelectProduct(product: any) {
  router.visit(`/products/${product.id}`);
}
</script>

<template>
  <HomeHeroView
    :featuredProducts="featuredProducts"
    @navigate="handleNavigate"
    @select-product="handleSelectProduct"
  />
</template>
