<script setup lang="ts">
import { ref, watch } from 'vue';

interface Props {
  message: string;
  isVisible: boolean;
  duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
  duration: 3500,
});

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const internalVisible = ref(props.isVisible);

watch(() => props.isVisible, (newVal) => {
  internalVisible.value = newVal;
  if (newVal) {
    setTimeout(() => {
      internalVisible.value = false;
      emit('close');
    }, props.duration);
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition duration-500 ease-out"
    enter-from-class="transform translate-y-4 opacity-0 scale-95"
    enter-to-class="transform translate-y-0 opacity-100 scale-100"
    leave-active-class="transition duration-300 ease-in"
    leave-from-class="transform translate-y-0 opacity-100 scale-100"
    leave-to-class="transform translate-y-4 opacity-0 scale-95"
  >
    <div
      v-if="internalVisible"
      id="toast-notification"
      class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-brand-dark text-brand-cream px-6 py-4 shadow-2xl flex items-center space-x-3.5 border border-white/10"
    >
      <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
      <span class="text-xs font-mono font-bold tracking-widest">{{ message }}</span>
    </div>
  </Transition>
</template>
