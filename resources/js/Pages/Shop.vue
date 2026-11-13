<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import ShopCatalogView from '../Components/ShopCatalogView.vue';
import { PRODUCTS } from '../products';

function handleSelectProduct(product: any) {
  router.visit(`/products/${product.id}`);
}

function handleQuickAdd(product: any) {
  console.log('Quick add:', product.id);
}
</script>

<template>
  <ShopCatalogView
    :products="PRODUCTS"
    @select-product="handleSelectProduct"
    @quick-add="handleQuickAdd"
  />
</template>
