<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import OrderConfirmationView from '../Components/OrderConfirmationView.vue';

defineProps<{
  id: string;
}>();

// Mock order data - in real app, fetch from backend
const mockOrder = {
  items: JSON.parse(sessionStorage.getItem('lastOrder') || '[]'),
  address: {
    name: 'Customer Name',
    address: '123 Main St',
    city: 'New York',
    state: 'NY',
    zip: '10001',
  },
  paymentMethod: 'Credit Card',
  subtotal: 875,
  discountAmount: 0,
  shippingCost: 15,
  tax: 70,
  total: 960,
};

function handleContinueShopping() {
  router.visit('/shop');
}
</script>

<template>
  <OrderConfirmationView
    :items="mockOrder.items"
    :address="mockOrder.address"
    :paymentMethod="mockOrder.paymentMethod"
    :subtotal="mockOrder.subtotal"
    :discountAmount="mockOrder.discountAmount"
    :shippingCost="mockOrder.shippingCost"
    :tax="mockOrder.tax"
    :total="mockOrder.total"
    @continue-shopping="handleContinueShopping"
  />
</template>
