<script setup lang="ts">
import { ref } from 'vue';
import { Check, AlertCircle } from 'lucide-vue-next';

const promoCode = ref('');
const appliedCode = ref('');
const discount = ref(0);
const message = ref('');
const isError = ref(false);

const validCodes: Record<string, number> = {
  'WELCOME10': 0.10,
  'SUMMER20': 0.20,
  'VIPEXTRA': 0.15,
  'FREESHIP': 0,
};

const emit = defineEmits<{
  (e: 'apply-discount', discount: number, code: string): void;
}>();

function applyPromo() {
  const code = promoCode.value.toUpperCase().trim();

  if (!code) {
    message.value = 'Enter a promo code';
    isError.value = true;
    return;
  }

  if (validCodes[code] !== undefined) {
    if (code === 'FREESHIP') {
      message.value = 'Free shipping applied!';
      discount.value = 0;
    } else {
      discount.value = validCodes[code];
      message.value = `${Math.round(discount.value * 100)}% discount applied!`;
    }
    isError.value = false;
    appliedCode.value = code;
    promoCode.value = '';
    emit('apply-discount', discount.value, code);
  } else {
    message.value = 'Invalid promo code';
    isError.value = true;
    discount.value = 0;
    appliedCode.value = '';
  }
}

function removePromo() {
  appliedCode.value = '';
  discount.value = 0;
  message.value = '';
  emit('apply-discount', 0, '');
}
</script>

<template>
  <div class="bg-brand-cream/40 border-t border-brand-dark/10 px-6 py-4">
    <!-- Applied Promo Display -->
    <div v-if="appliedCode" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <Check class="w-4 h-4 text-green-600" />
        <span class="text-xs font-medium text-green-700">
          {{ appliedCode }} - {{ Math.round(discount * 100) }}% off
        </span>
      </div>
      <button
        @click="removePromo"
        class="text-xs text-green-600 hover:text-green-700 underline cursor-pointer"
      >
        Remove
      </button>
    </div>

    <!-- Promo Input -->
    <div v-if="!appliedCode" class="space-y-2">
      <label class="text-xs font-mono tracking-widest text-brand-dark block">PROMO CODE</label>
      <div class="flex gap-2">
        <input
          v-model="promoCode"
          type="text"
          placeholder="Enter code..."
          @keyup.enter="applyPromo"
          class="flex-1 text-xs px-3 py-2 border border-brand-dark/10 rounded focus:outline-none focus:border-brand-dark transition-colors"
          maxlength="20"
        />
        <button
          @click="applyPromo"
          class="px-4 py-2 bg-brand-dark hover:bg-brand-dark/90 text-brand-cream text-xs font-medium transition-colors cursor-pointer rounded"
        >
          Apply
        </button>
      </div>
    </div>

    <!-- Message -->
    <div v-if="message" class="mt-2 flex items-center space-x-2">
      <component :is="isError ? AlertCircle : Check" class="w-3.5 h-3.5" :class="isError ? 'text-red-600' : 'text-green-600'" />
      <span :class="isError ? 'text-red-600' : 'text-green-600'" class="text-xs">
        {{ message }}
      </span>
    </div>

    <!-- Available codes hint -->
    <p v-if="!appliedCode" class="mt-3 text-[10px] text-brand-muted">
      Try: WELCOME10, SUMMER20, VIPEXTRA, FREESHIP
    </p>
  </div>
</template>
