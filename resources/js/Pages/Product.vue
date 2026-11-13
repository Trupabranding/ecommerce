<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import ProductDetailView from '../Components/ProductDetailView.vue';
import { PRODUCTS } from '../products';

const props = defineProps<{
  id: string;
}>();

const product = computed(() => {
  return PRODUCTS.find(p => p.id === props.id);
});

function handleAddToCart(product: any, size: string, color: any, quantity: number) {
  console.log('Add to cart:', { product, size, color, quantity });
}

function handleBackToShop() {
  router.visit('/shop');
}
</script>

<template>
  <ProductDetailView
    v-if="product"
    :product="product"
    @back="handleBackToShop"
    @add-to-cart="handleAddToCart"
  />
</template>
