<script setup lang="ts">
import { Instagram, Twitter, Mail, ArrowUp } from 'lucide-vue-next';

const props = defineProps<{
  currentView?: string;
}>();

const emit = defineEmits<{
  (e: 'navigate', view: string): void;
  (e: 'scroll-top'): void;
}>();

function handleNavigate(view: string) {
  emit('navigate', view);
}

function handleScrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
  emit('scroll-top');
}

function handleSubscribe() {
  const email = (document.getElementById('footer-email-input') as HTMLInputElement)?.value;
  if (email) {
    console.log('Subscribed:', email);
    (document.getElementById('footer-email-input') as HTMLInputElement).value = '';
  }
}
</script>

<template>
  <footer class="bg-brand-dark text-brand-cream pt-20 pb-8 border-t border-white/5 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">

        <!-- Column 1: Newsletter -->
        <div class="md:col-span-5 space-y-5">
          <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">THE HERD CIRCLE</span>
          <h3 class="font-serif text-2xl font-light leading-relaxed">
            Sign up for private seasonal launches, editorial journals, and atelier events.
          </h3>
          <div class="flex border-b border-brand-cream/20 py-2 focus-within:border-brand-cream transition-colors">
            <input
              id="footer-email-input"
              type="email"
              placeholder="EMAIL ADDRESS"
              class="bg-transparent text-xs text-brand-cream outline-none w-full tracking-widest placeholder:text-brand-cream/30"
            />
            <button
              id="btn-footer-subscribe"
              @click="handleSubscribe"
              class="text-xs font-mono tracking-widest hover:text-white/70 transition-colors cursor-pointer"
            >
              JOIN
            </button>
          </div>
        </div>

        <!-- Column 2: Navigation Links -->
        <div class="md:col-span-3 space-y-4 md:pl-10">
          <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">NAVIGATION</span>
          <div class="flex flex-col space-y-2.5 text-xs tracking-wider text-brand-cream/60">
            <button @click="handleNavigate('home')" class="text-left hover:text-brand-cream cursor-pointer">HOME</button>
            <button @click="handleNavigate('shop')" class="text-left hover:text-brand-cream cursor-pointer">SHOP COLLECTION</button>
            <a href="#" class="hover:text-brand-cream transition-colors">EDITORIAL JOURNAL</a>
            <a href="#" class="hover:text-brand-cream transition-colors">THE STUDIO STORY</a>
          </div>
        </div>

        <!-- Column 3: Contact / Support -->
        <div class="md:col-span-2 space-y-4">
          <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">SUPPORT</span>
          <div class="flex flex-col space-y-2.5 text-xs tracking-wider text-brand-cream/60">
            <a href="#" class="hover:text-brand-cream transition-colors">SHIPPING & EXPEDITING</a>
            <a href="#" class="hover:text-brand-cream transition-colors">EXCHANGES & RETURNS</a>
            <a href="#" class="hover:text-brand-cream transition-colors">SUSTAINABILITY PLEDGE</a>
            <a href="#" class="hover:text-brand-cream transition-colors">CONTACT STUDIO</a>
          </div>
        </div>

        <!-- Column 4: Social / Currency -->
        <div class="md:col-span-2 space-y-6">
          <div>
            <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block mb-3">CURRENCY</span>
            <select id="footer-currency-select" class="bg-zinc-950 border border-white/10 rounded px-2.5 py-1 text-xs text-brand-cream font-mono outline-none tracking-widest">
              <option value="USD">USD ($)</option>
              <option value="EUR">EUR (€)</option>
              <option value="JPY">JPY (¥)</option>
            </select>
          </div>

          <div class="space-y-3">
            <span class="text-xs font-mono tracking-[0.3em] text-brand-cream/40 uppercase block">CONNECT</span>
            <div class="flex items-center space-x-3 text-brand-cream/60">
              <a href="#" class="hover:text-brand-cream transition-colors" aria-label="Instagram"><Instagram class="w-4 h-4" /></a>
              <a href="#" class="hover:text-brand-cream transition-colors" aria-label="Twitter"><Twitter class="w-4 h-4" /></a>
              <a href="#" class="hover:text-brand-cream transition-colors" aria-label="Mail"><Mail class="w-4 h-4" /></a>
            </div>
          </div>
        </div>

      </div>

      <!-- Scroll back to top & copyrights -->
      <div class="border-t border-white/5 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-[10px] font-mono text-brand-cream/30 tracking-widest text-center sm:text-left">
          © 2026 HERD DESIGN. ALL RIGHTS RESERVED. ETHICALLY PRODUCED & ARCHIVED.
        </div>

        <button
          id="btn-scroll-top"
          @click="handleScrollToTop"
          class="flex items-center space-x-2 text-[10px] font-mono tracking-widest text-brand-cream/50 hover:text-white transition-colors cursor-pointer group"
        >
          <span>SCROLL TO TOP</span>
          <ArrowUp class="w-3.5 h-3.5 transition-transform group-hover:-translate-y-0.5" />
        </button>
      </div>
    </div>
  </footer>
</template>
