<script setup lang="ts">
import { ref } from 'vue';
import { ShoppingBag, Menu, X, Search } from 'lucide-vue-next';

interface Props {
  cartCount: number;
  activeView: string;
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'navigate', view: string): void;
  (e: 'open-cart'): void;
}>();

const isMobileMenuOpen = ref(false);
const isSearchOpen = ref(false);
const searchQuery = ref('');

const navItems = [
  { label: 'HOME', view: 'home' },
  { label: 'SHOP ALL', view: 'shop' },
];

function handleNavigate(view: string) {
  emit('navigate', view);
  isMobileMenuOpen.value = false;
}
</script>

<template>
  <header class="sticky top-0 z-40 w-full bg-brand-cream/80 backdrop-blur-md border-b border-brand-dark/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      
      <!-- Mobile Toggle -->
      <button 
        id="btn-mobile-menu-open"
        @click="isMobileMenuOpen = true" 
        class="md:hidden p-2 -ml-2 text-brand-dark hover:opacity-70 transition-opacity"
        aria-label="Open menu"
      >
        <Menu class="w-6 h-6" />
      </button>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center space-x-10 text-xs font-medium tracking-[0.2em] text-brand-dark">
        <button 
          id="nav-link-home"
          v-for="item in navItems" 
          :key="item.view"
          @click="handleNavigate(item.view)"
          class="relative py-2 transition-colors hover:text-brand-dark cursor-pointer"
          :class="activeView === item.view ? 'text-brand-dark' : 'text-brand-dark/50'"
        >
          {{ item.label }}
          <span 
            v-if="activeView === item.view" 
            class="absolute bottom-0 left-0 w-full h-[1.5px] bg-brand-dark transition-transform origin-left scale-x-100"
          ></span>
        </button>
      </nav>

      <!-- Centered Brand Logo -->
      <div class="flex-1 md:flex-initial text-center md:text-left">
        <button 
          id="btn-logo-home"
          @click="handleNavigate('home')" 
          class="inline-block cursor-pointer"
        >
          <span class="font-serif text-3xl font-bold tracking-[0.15em] text-brand-dark hover:opacity-80 transition-opacity">
            HERD
          </span>
        </button>
      </div>

      <!-- Right Utility Controls -->
      <div class="flex items-center space-x-3 sm:space-x-6">
        
        <!-- Search bar input toggler (Desktop) -->
        <div class="hidden sm:flex items-center border border-brand-dark/10 rounded-full px-3 py-1.5 bg-white/50 focus-within:border-brand-dark/40 transition-colors">
          <Search class="w-4 h-4 text-brand-dark/40 mr-2" />
          <input 
            id="input-search-query"
            type="text" 
            placeholder="Search..." 
            v-model="searchQuery"
            @keyup.enter="handleNavigate('shop')"
            class="bg-transparent text-xs text-brand-dark outline-none w-28 focus:w-44 transition-all tracking-wider placeholder:text-brand-dark/30"
          />
        </div>

        <!-- Search button (Mobile) -->
        <button 
          id="btn-mobile-search"
          @click="isSearchOpen = !isSearchOpen" 
          class="sm:hidden p-2 text-brand-dark hover:opacity-70 transition-opacity"
        >
          <Search class="w-5 h-5" />
        </button>

        <!-- Shopping Cart Icon with Badge -->
        <button 
          id="btn-cart-open"
          @click="emit('open-cart')" 
          class="relative p-2 text-brand-dark hover:opacity-70 transition-opacity flex items-center cursor-pointer"
          aria-label="Open shopping cart"
        >
          <ShoppingBag class="w-5 h-5" />
          <span 
            v-if="cartCount > 0"
            class="absolute top-1 right-1 bg-brand-dark text-[10px] font-mono font-medium text-brand-cream w-4 h-4 rounded-full flex items-center justify-center scale-90"
          >
            {{ cartCount }}
          </span>
        </button>
      </div>
    </div>

    <!-- Collapsible Mobile Search Panel -->
    <div 
      v-if="isSearchOpen" 
      class="sm:hidden w-full border-t border-brand-dark/5 bg-brand-cream px-4 py-3 flex items-center"
    >
      <div class="flex-1 flex items-center border border-brand-dark/10 rounded-full px-3 py-2 bg-white">
        <Search class="w-4 h-4 text-brand-dark/40 mr-2" />
        <input 
          id="input-search-query-mobile"
          type="text" 
          placeholder="Search products..." 
          v-model="searchQuery"
          @keyup.enter="handleNavigate('shop'); isSearchOpen = false"
          class="bg-transparent text-xs text-brand-dark outline-none w-full tracking-wider"
        />
      </div>
      <button 
        id="btn-mobile-search-close"
        @click="isSearchOpen = false" 
        class="ml-3 text-xs font-medium tracking-widest text-brand-dark/50 hover:text-brand-dark"
      >
        CANCEL
      </button>
    </div>

    <!-- Mobile Drawer Side Menu Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      class="fixed inset-0 z-50 bg-brand-dark/20 backdrop-blur-xs flex md:hidden"
    >
      <div class="w-4/5 max-w-sm bg-brand-cream h-full p-8 flex flex-col shadow-2xl relative">
        <button 
          id="btn-mobile-menu-close"
          @click="isMobileMenuOpen = false" 
          class="absolute top-6 right-6 p-2 text-brand-dark hover:opacity-70 transition-opacity"
          aria-label="Close menu"
        >
          <X class="w-6 h-6" />
        </button>

        <div class="mt-12 flex flex-col space-y-8 text-xl font-serif tracking-[0.1em] font-medium text-brand-dark">
          <button 
            id="btn-mobile-nav-home"
            @click="handleNavigate('home')" 
            class="text-left py-2 border-b border-brand-dark/5 hover:pl-2 transition-all duration-300"
          >
            HOME
          </button>
          <button 
            id="btn-mobile-nav-shop"
            @click="handleNavigate('shop')" 
            class="text-left py-2 border-b border-brand-dark/5 hover:pl-2 transition-all duration-300"
          >
            SHOP ALL
          </button>
          <div class="pt-8">
            <span class="text-xs font-sans tracking-[0.2em] text-brand-dark/40 block mb-4">COLLECTIONS</span>
            <button 
              id="btn-mobile-nav-outerwear"
              @click="handleNavigate('shop')" 
              class="text-left font-sans text-sm tracking-[0.1em] block py-1.5 text-brand-dark/70"
            >
              Outerwear
            </button>
            <button 
              id="btn-mobile-nav-knitwear"
              @click="handleNavigate('shop')" 
              class="text-left font-sans text-sm tracking-[0.1em] block py-1.5 text-brand-dark/70"
            >
              Knitwear
            </button>
            <button 
              id="btn-mobile-nav-trousers"
              @click="handleNavigate('shop')" 
              class="text-left font-sans text-sm tracking-[0.1em] block py-1.5 text-brand-dark/70"
            >
              Trousers
            </button>
          </div>
        </div>

        <div class="mt-auto pt-8 border-t border-brand-dark/10">
          <span class="text-xs font-sans tracking-widest text-brand-dark/40 block">HERD EDITORIAL</span>
          <p class="text-xs font-sans text-brand-dark/60 mt-2 leading-relaxed">
            Crafting raw silhouettes, heavy drapes, and high-contrast styling for the contemporary aesthetic.
          </p>
        </div>
      </div>
    </div>
  </header>
</template>
