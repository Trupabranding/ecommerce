<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import CheckoutView from '../Components/CheckoutView.vue';

const cartItems = JSON.parse(localStorage.getItem('herd_cart') || '[]');

const subtotal = cartItems.reduce((sum: number, item: any) => sum + (item.product.price * item.quantity), 0);
const tax = subtotal * 0.08;
const shipping = subtotal > 150 ? 0 : 15;
const total = subtotal + tax + shipping;

function handleOrderComplete(details: any) {
  console.log('Order complete:', details);
  // Redirect to order confirmation
  localStorage.removeItem('herd_cart');
  setTimeout(() => {
    router.visit('/order/demo-order-001');
  }, 500);
}

function handleBackToCart() {
  window.history.back();
}
</script>

<template>
  <CheckoutView
    :items="cartItems"
    :subtotal="subtotal"
    :tax="tax"
    :shipping="shipping"
    :total="total"
    @order-complete="handleOrderComplete"
    @back-to-cart="handleBackToCart"
  />
</template>
