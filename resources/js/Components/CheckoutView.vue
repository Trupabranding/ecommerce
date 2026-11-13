<script setup lang="ts">
import { ref, computed } from 'vue';
import { CartItem, ShippingAddress, PaymentDetails } from '../types';
import { CreditCard, Shield, ChevronLeft, Ticket, CheckCircle } from 'lucide-vue-next';

interface Props {
  items: CartItem[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'back-to-cart'): void;
  (e: 'order-complete', orderDetails: { address: ShippingAddress; paymentMethod: string; discountCode: string; discountAmount: number }): void;
}>();

// Address Fields
const addressForm = ref<ShippingAddress>({
  email: 'editorial@herd.design',
  firstName: 'Clara',
  lastName: 'Hansen',
  address: '84 Rue des Archives',
  apartment: 'Apt 4B',
  city: 'Paris',
  state: 'Île-de-France',
  country: 'France',
  postalCode: '75003',
  phone: '+33 1 42 27 78 90'
});

// Card Fields
const paymentForm = ref<PaymentDetails>({
  cardNumber: '',
  cardName: '',
  expiry: '',
  cvc: ''
});

// Form states
const activeStep = ref<'shipping' | 'payment'>('shipping');
const promoCode = ref('');
const appliedDiscount = ref({ code: '', amount: 0, percent: 0 });
const promoError = ref('');
const isSubmitting = ref(false);

const subtotal = computed(() => {
  return props.items.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
});

const shippingCost = computed(() => {
  if (subtotal.value > 150 || appliedDiscount.value.code === 'FREESHIP') {
    return 0;
  }
  return 15.00;
});

const discountAmount = computed(() => {
  if (appliedDiscount.value.percent > 0) {
    return subtotal.value * appliedDiscount.value.percent;
  }
  return appliedDiscount.value.amount;
});

const tax = computed(() => {
  return (subtotal.value - discountAmount.value) * 0.08;
});

const total = computed(() => {
  return subtotal.value - discountAmount.value + shippingCost.value + tax.value;
});

// Real-time formatted card number
const formattedCardNumber = computed(() => {
  const cleanNum = paymentForm.value.cardNumber.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
  const matches = cleanNum.match(/\d{4,16}/g);
  const match = (matches && matches[0]) || '';
  const parts = [];

  for (let i = 0, len = match.length; i < len; i += 4) {
    parts.push(match.substring(i, i + 4));
  }

  if (parts.length > 0) {
    return parts.join(' ');
  } else {
    return paymentForm.value.cardNumber;
  }
});

// Promo code applier
function applyPromo() {
  promoError.value = '';
  const code = promoCode.value.toUpperCase().trim();
  
  if (code === 'HERD20') {
    appliedDiscount.value = { code: 'HERD20', amount: 0, percent: 0.20 };
    promoCode.value = '';
  } else if (code === 'FREESHIP') {
    appliedDiscount.value = { code: 'FREESHIP', amount: 0, percent: 0 };
    promoCode.value = '';
  } else if (code === 'WELCOME50') {
    appliedDiscount.value = { code: 'WELCOME50', amount: 50, percent: 0 };
    promoCode.value = '';
  } else {
    promoError.value = 'Invalid discount code.';
  }
}

function removePromo() {
  appliedDiscount.value = { code: '', amount: 0, percent: 0 };
}

// Quick fill function to make testing easier!
function quickFillDemo() {
  addressForm.value = {
    email: 'test@example.com',
    firstName: 'Julian',
    lastName: 'Vance',
    address: '12 Bond Street',
    apartment: 'Floor 3',
    city: 'New York',
    state: 'NY',
    country: 'United States',
    postalCode: '10012',
    phone: '+1 212 555 0199'
  };
  paymentForm.value = {
    cardNumber: '4111 2222 3333 4444',
    cardName: 'JULIAN VANCE',
    expiry: '12/28',
    cvc: '382'
  };
}

function handlePlaceOrder() {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  
  // Simulate payment processing loader
  setTimeout(() => {
    isSubmitting.value = false;
    emit('order-complete', {
      address: addressForm.value,
      paymentMethod: `Visa ending in ${formattedCardNumber.value.slice(-4) || '4444'}`,
      discountCode: appliedDiscount.value.code,
      discountAmount: discountAmount.value
    });
  }, 2000);
}
</script>

<template>
  <div id="checkout-view" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16">
    <!-- Back to Cart action -->
    <button 
      id="btn-checkout-back"
      @click="emit('back-to-cart')" 
      class="inline-flex items-center text-xs font-mono tracking-widest text-brand-dark/50 hover:text-brand-dark mb-8 group transition-colors cursor-pointer"
    >
      <ChevronLeft class="w-4 h-4 mr-1 transition-transform group-hover:-translate-x-1" />
      BACK TO SHOPPING BAG
    </button>

    <h1 id="checkout-title" class="text-3xl font-serif font-medium tracking-wide text-brand-dark mb-10">CHECKOUT</h1>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
      
      <!-- Left Column: Checkout Forms (7 Cols) -->
      <div class="lg:col-span-7 space-y-8">
        
        <!-- Step Indicator -->
        <div class="flex items-center justify-between border-b border-brand-dark/10 pb-4">
          <div class="flex space-x-6 text-xs font-mono tracking-widest">
            <button 
              id="btn-step-shipping"
              @click="activeStep = 'shipping'"
              class="pb-4 -mb-4 border-b-2 font-bold cursor-pointer"
              :class="activeStep === 'shipping' ? 'border-brand-dark text-brand-dark' : 'border-transparent text-brand-muted'"
            >
              1. SHIPPING DETAILS
            </button>
            <button 
              id="btn-step-payment"
              @click="activeStep = 'payment'"
              class="pb-4 -mb-4 border-b-2 font-bold cursor-pointer"
              :class="activeStep === 'payment' ? 'border-brand-dark text-brand-dark' : 'border-transparent text-brand-muted'"
            >
              2. SECURE PAYMENT
            </button>
          </div>

          <button 
            id="btn-quick-fill"
            @click="quickFillDemo" 
            class="text-[10px] font-mono tracking-wider text-brand-cream bg-brand-dark/80 px-2.5 py-1 rounded hover:bg-brand-dark transition-colors"
          >
            AUTO-FILL DEMO DATA
          </button>
        </div>

        <!-- STEP 1: Shipping Address Form -->
        <div v-if="activeStep === 'shipping'" class="space-y-6">
          <h2 class="text-lg font-serif font-medium text-brand-dark">Delivery Address</h2>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col">
              <label for="shipping-email" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">EMAIL ADDRESS</label>
              <input 
                id="shipping-email"
                type="email" 
                v-model="addressForm.email"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
            <div class="flex flex-col">
              <label for="shipping-phone" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">PHONE NUMBER</label>
              <input 
                id="shipping-phone"
                type="text" 
                v-model="addressForm.phone"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col">
              <label for="shipping-firstname" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">FIRST NAME</label>
              <input 
                id="shipping-firstname"
                type="text" 
                v-model="addressForm.firstName"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
            <div class="flex flex-col">
              <label for="shipping-lastname" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">LAST NAME</label>
              <input 
                id="shipping-lastname"
                type="text" 
                v-model="addressForm.lastName"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
          </div>

          <div class="flex flex-col">
            <label for="shipping-address" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">STREET ADDRESS</label>
            <input 
              id="shipping-address"
              type="text" 
              v-model="addressForm.address"
              placeholder="House number and street name"
              class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
            />
          </div>

          <div class="flex flex-col">
            <label for="shipping-apartment" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">APARTMENT, SUITE, ETC. (OPTIONAL)</label>
            <input 
              id="shipping-apartment"
              type="text" 
              v-model="addressForm.apartment"
              placeholder="Apartment, unit, suite, floor, etc."
              class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex flex-col">
              <label for="shipping-city" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">CITY</label>
              <input 
                id="shipping-city"
                type="text" 
                v-model="addressForm.city"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
            <div class="flex flex-col">
              <label for="shipping-state" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">STATE / REGION</label>
              <input 
                id="shipping-state"
                type="text" 
                v-model="addressForm.state"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
            <div class="flex flex-col">
              <label for="shipping-zip" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">POSTAL CODE</label>
              <input 
                id="shipping-zip"
                type="text" 
                v-model="addressForm.postalCode"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>
          </div>

          <div class="flex flex-col">
            <label for="shipping-country" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">COUNTRY</label>
            <select 
              id="shipping-country"
              v-model="addressForm.country"
              class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans appearance-none"
            >
              <option value="France">France</option>
              <option value="United States">United States</option>
              <option value="United Kingdom">United Kingdom</option>
              <option value="Germany">Germany</option>
              <option value="Japan">Japan</option>
            </select>
          </div>

          <button 
            id="btn-goto-payment"
            @click="activeStep = 'payment'"
            class="w-full bg-brand-dark hover:bg-brand-dark/95 text-brand-cream text-xs font-medium tracking-[0.2em] py-4 transition-all duration-300 mt-4 cursor-pointer"
          >
            CONTINUE TO SECURE PAYMENT
          </button>
        </div>

        <!-- STEP 2: Payment Form -->
        <div v-if="activeStep === 'payment'" class="space-y-8">
          
          <!-- Credit Card 3D Mock-up Box -->
          <div class="relative w-full max-w-[400px] h-56 mx-auto rounded-2xl bg-gradient-to-br from-neutral-900 to-zinc-800 p-6 text-white shadow-2xl flex flex-col justify-between overflow-hidden">
            <!-- Overlay lights -->
            <div class="absolute -top-10 -left-10 w-44 h-44 bg-zinc-700/20 rounded-full blur-3xl"></div>
            
            <div class="flex justify-between items-start z-10">
              <div class="flex flex-col">
                <span class="text-[9px] font-mono tracking-widest text-zinc-400">HERD PREMIUM</span>
                <span class="font-serif text-lg font-bold tracking-widest mt-1">HERD</span>
              </div>
              <CreditCard class="w-8 h-8 text-zinc-300" />
            </div>

            <div class="my-6 z-10">
              <!-- Chip and layout -->
              <div class="w-10 h-8 rounded-md bg-amber-200/40 border border-amber-300/30 mb-3 relative overflow-hidden">
                <div class="absolute inset-x-2 top-2 h-[1px] bg-amber-400/20"></div>
                <div class="absolute inset-y-2 left-3 w-[1px] bg-amber-400/20"></div>
              </div>
              <div class="font-mono text-lg tracking-[0.18em] text-zinc-100">
                {{ formattedCardNumber || '•••• •••• •••• ••••' }}
              </div>
            </div>

            <div class="flex justify-between items-end z-10">
              <div class="flex flex-col">
                <span class="text-[7px] font-mono tracking-wider text-zinc-400 uppercase">CARDHOLDER</span>
                <span class="text-xs font-mono font-medium tracking-wide truncate max-w-[200px] mt-0.5 uppercase">
                  {{ paymentForm.cardName || 'YOUR FULL NAME' }}
                </span>
              </div>
              <div class="flex flex-col text-right">
                <span class="text-[7px] font-mono tracking-wider text-zinc-400">EXPIRES</span>
                <span class="text-xs font-mono font-medium tracking-widest mt-0.5">
                  {{ paymentForm.expiry || 'MM/YY' }}
                </span>
              </div>
            </div>
          </div>

          <div class="space-y-5">
            <h2 class="text-lg font-serif font-medium text-brand-dark">Card Information</h2>

            <div class="flex flex-col">
              <label for="payment-cardholder" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">CARDHOLDER NAME</label>
              <input 
                id="payment-cardholder"
                type="text" 
                v-model="paymentForm.cardName"
                placeholder="Full name on card"
                class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
              />
            </div>

            <div class="flex flex-col">
              <label for="payment-cardnumber" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">CARD NUMBER</label>
              <div class="relative">
                <input 
                  id="payment-cardnumber"
                  type="text" 
                  v-model="paymentForm.cardNumber"
                  placeholder="0000 0000 0000 0000"
                  maxlength="19"
                  class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans w-full pr-10"
                />
                <CreditCard class="w-4 h-4 absolute right-3.5 top-3.5 text-brand-dark/30" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="flex flex-col">
                <label for="payment-expiry" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">EXPIRATION DATE</label>
                <input 
                  id="payment-expiry"
                  type="text" 
                  v-model="paymentForm.expiry"
                  placeholder="MM/YY"
                  maxlength="5"
                  class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
                />
              </div>
              <div class="flex flex-col">
                <label for="payment-cvc" class="text-[10px] font-mono tracking-wider text-brand-dark mb-1">SECURITY CODE (CVC)</label>
                <input 
                  id="payment-cvc"
                  type="text" 
                  v-model="paymentForm.cvc"
                  placeholder="123"
                  maxlength="4"
                  class="border border-brand-dark/15 rounded px-4 py-2.5 text-sm bg-white focus:border-brand-dark outline-none font-sans"
                />
              </div>
            </div>
          </div>

          <div class="flex flex-col space-y-3 pt-2">
            <button 
              id="btn-complete-order"
              @click="handlePlaceOrder"
              :disabled="isSubmitting"
              class="w-full bg-brand-dark hover:bg-brand-dark/95 text-brand-cream text-xs font-medium tracking-[0.25em] py-4.5 shadow-xl transition-all flex items-center justify-center space-x-3 cursor-pointer"
            >
              <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span v-else>PLACE ORDER • ${{ total.toFixed(2) }}</span>
            </button>
            
            <button 
              id="btn-checkout-step-back"
              @click="activeStep = 'shipping'"
              class="w-full text-center text-xs font-medium tracking-widest text-brand-dark/50 hover:text-brand-dark py-2 transition-colors cursor-pointer"
            >
              GO BACK TO SHIPPING
            </button>
          </div>
        </div>

      </div>

      <!-- Right Column: Order Summary (5 Cols) -->
      <div class="lg:col-span-5 bg-white border border-brand-dark/10 p-6 rounded-lg shadow-sm space-y-6">
        <h2 class="text-base font-serif font-medium tracking-wider text-brand-dark uppercase border-b border-brand-dark/5 pb-4">
          Order Summary
        </h2>

        <!-- Items list -->
        <div class="divide-y divide-brand-dark/5 max-h-72 overflow-y-auto pr-2">
          <div v-for="item in items" :key="item.id" class="py-4 flex items-center justify-between first:pt-0 last:pb-0">
            <div class="flex items-center space-x-3">
              <div class="w-12 h-16 bg-brand-cream overflow-hidden border border-brand-dark/5 flex-shrink-0">
                <img :src="item.product.images[0]" :alt="item.product.name" referrerpolicy="no-referrer" class="w-full h-full object-cover" />
              </div>
              <div>
                <h4 class="font-serif text-sm font-medium text-brand-dark line-clamp-1 max-w-[160px]">{{ item.product.name }}</h4>
                <p class="text-[10px] font-mono text-brand-muted mt-0.5">SIZE: {{ item.selectedSize }} • QTY: {{ item.quantity }}</p>
              </div>
            </div>
            <span class="font-mono text-sm font-medium text-brand-dark">${{ (item.product.price * item.quantity).toFixed(2) }}</span>
          </div>
        </div>

        <!-- Promocode Input -->
        <div class="border-t border-b border-brand-dark/10 py-5">
          <div class="flex space-x-2">
            <div class="relative flex-1">
              <input 
                id="promo-code-input"
                type="text" 
                placeholder="PROMO CODE" 
                v-model="promoCode"
                @keyup.enter="applyPromo"
                class="w-full border border-brand-dark/15 rounded px-3 py-2 text-xs bg-brand-cream/40 focus:border-brand-dark outline-none font-mono uppercase tracking-widest placeholder:text-brand-dark/20"
              />
              <Ticket class="w-3.5 h-3.5 absolute right-3 top-2.5 text-brand-dark/20" />
            </div>
            <button 
              id="btn-promo-apply"
              @click="applyPromo"
              class="bg-brand-dark hover:bg-brand-dark/90 text-brand-cream text-xs font-mono px-4 py-2 transition-colors cursor-pointer"
            >
              APPLY
            </button>
          </div>
          <p v-if="promoError" class="text-xs text-red-600 font-mono mt-2 pl-1">{{ promoError }}</p>
          
          <div v-if="appliedDiscount.code" class="mt-3 bg-green-50 border border-green-200 rounded p-2.5 flex items-center justify-between">
            <div class="flex items-center text-green-800 text-xs font-mono">
              <CheckCircle class="w-3.5 h-3.5 mr-2" />
              <span>CODE: {{ appliedDiscount.code }} APPLIED!</span>
            </div>
            <button 
              id="btn-promo-remove"
              @click="removePromo" 
              class="text-[10px] font-mono text-green-700 hover:text-green-900 underline uppercase"
            >
              Remove
            </button>
          </div>

          <div class="mt-3 pl-1 text-[9px] font-mono text-brand-muted space-y-1">
            <span class="block text-brand-dark/55">Available promo codes for testing:</span>
            <span class="block">• <strong class="text-brand-dark">HERD20</strong> — 20% off entire order</span>
            <span class="block">• <strong class="text-brand-dark">FREESHIP</strong> — Free shipping on any order</span>
            <span class="block">• <strong class="text-brand-dark">WELCOME50</strong> — Flat $50 discount</span>
          </div>
        </div>

        <!-- Calculations -->
        <div class="space-y-2.5 text-xs font-mono">
          <div class="flex justify-between text-brand-muted">
            <span>SUBTOTAL</span>
            <span>${{ subtotal.toFixed(2) }}</span>
          </div>
          <div v-if="discountAmount > 0" class="flex justify-between text-green-700 font-medium">
            <span>DISCOUNT ({{ appliedDiscount.code }})</span>
            <span>-${{ discountAmount.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between text-brand-muted">
            <span>SHIPPING</span>
            <span v-if="shippingCost > 0">${{ shippingCost.toFixed(2) }}</span>
            <span v-else class="text-green-700 font-bold">FREE</span>
          </div>
          <div class="flex justify-between text-brand-muted">
            <span>TAX (8%)</span>
            <span>${{ tax.toFixed(2) }}</span>
          </div>
          <div class="h-[1px] bg-brand-dark/10 my-3"></div>
          <div class="flex justify-between text-sm font-bold text-brand-dark">
            <span class="font-serif">TOTAL</span>
            <span>${{ total.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Trust stamp -->
        <div class="bg-brand-cream p-4 border border-brand-dark/5 text-center text-[10px] font-mono tracking-wider text-brand-muted flex items-center justify-center space-x-2">
          <Shield class="w-4 h-4 text-brand-dark stroke-1" />
          <span>SSL SECURE PAYMENT ASSURED</span>
        </div>

      </div>
    </div>
  </div>
</template>
