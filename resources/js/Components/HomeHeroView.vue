<script setup lang="ts">
import { Product } from '../types';
import { ArrowRight, Sparkles, MoveRight } from 'lucide-vue-next';

interface Props {
  featuredProducts: Product[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'navigate', view: string): void;
  (e: 'select-product', product: Product): void;
}>();
</script>

<template>
  <div id="home-hero-view" class="space-y-16 md:space-y-28">
    
    <!-- Hero Banner Cover Section -->
    <section class="relative w-full h-[85vh] bg-brand-dark flex items-center overflow-hidden">
      <!-- High-fashion background photo -->
      <div class="absolute inset-0 z-0">
        <img 
          src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?q=80&w=1200&auto=format&fit=crop" 
          alt="High fashion models"
          referrerpolicy="no-referrer"
          class="w-full h-full object-cover opacity-60 object-[50%_25%]"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark via-brand-dark/25 to-brand-dark/40"></div>
      </div>

      <!-- Hero Typography Overlay -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10 text-white flex flex-col justify-end h-full pb-16 md:pb-24">
        <span class="text-xs font-mono tracking-[0.4em] text-brand-cream/80 uppercase mb-4 block">
          CURATED SARTORIAL EDITIONS
        </span>
        <h1 class="text-5xl md:text-8xl font-serif font-bold tracking-tight leading-none mb-6">
          The Art of <br />
          <span class="italic font-light">Reduction.</span>
        </h1>
        <p class="text-sm md:text-base text-brand-cream/70 max-w-lg mb-8 tracking-wide leading-relaxed font-sans font-light">
          Sculptural shapes, organic heavy drapes, and a stark black-and-white high-contrast visual palette. Redefine your dressing philosophy with the Herd winter collection.
        </p>

        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
          <button 
            id="btn-hero-shop-all"
            @click="emit('navigate', 'shop')"
            class="bg-white text-brand-dark hover:bg-brand-cream text-xs font-medium tracking-[0.25em] py-4 px-8 shadow-2xl transition-all duration-300 flex items-center justify-center space-x-2 cursor-pointer"
          >
            <span>SHOP COLLECTION</span>
            <ArrowRight class="w-4 h-4" />
          </button>
          
          <button 
            id="btn-hero-philosophy"
            @click="emit('navigate', 'shop')"
            class="border border-white/40 hover:border-white text-white text-xs font-medium tracking-[0.25em] py-4 px-8 transition-colors duration-300 cursor-pointer"
          >
            EXPLORE THE SHAPES
          </button>
        </div>
      </div>

      <!-- Quick subtle bottom scrolling info -->
      <div class="absolute bottom-4 right-8 hidden lg:block text-right text-[10px] font-mono tracking-widest text-white/40">
        PARIS • MILAN • TOKYO • NEW YORK
      </div>
    </section>

    <!-- Brand Philosophy Section (Bento layout style) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <!-- Text details -->
        <div class="lg:col-span-5 space-y-6">
          <span class="text-[10px] font-mono tracking-[0.25em] text-brand-muted block uppercase">OUR PHILOSOPHY</span>
          <h2 class="text-3xl md:text-4xl font-serif font-medium text-brand-dark leading-tight">
            Raw Silhouettes, Sourced Consciously.
          </h2>
          <p class="text-sm text-brand-muted leading-relaxed font-sans">
            Herd operates on the singular belief that garments should serve as architectural extensions of the self. We avoid frivolous detail, temporary trends, and sensory saturation. 
          </p>
          <p class="text-sm text-brand-muted leading-relaxed font-sans">
            Instead, we design heavy drapes that command space, utilizing certified organic, bio-degradable fibers spun in historically verified mills in Italy and Japan. Clean. Durable. Striking.
          </p>
          <div class="pt-4">
            <button 
              id="btn-philosophy-explore"
              @click="emit('navigate', 'shop')" 
              class="inline-flex items-center text-xs font-mono tracking-widest text-brand-dark hover:opacity-75 transition-opacity group cursor-pointer"
            >
              <span>DISCOVER ETHICAL SUSTAINABILITY</span>
              <MoveRight class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" />
            </button>
          </div>
        </div>

        <!-- Visual photo boxes -->
        <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="aspect-[4/5] bg-brand-cream overflow-hidden border border-brand-dark/5 relative group rounded">
            <img 
              src="https://images.unsplash.com/photo-1548624149-f9b1859aa7d0?q=80&w=600&auto=format&fit=crop" 
              alt="Atelier drape details" 
              referrerpolicy="no-referrer"
              class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
            />
            <div class="absolute inset-0 bg-brand-dark/10 group-hover:bg-transparent transition-colors duration-500"></div>
            <span class="absolute bottom-4 left-4 text-white text-[10px] font-mono tracking-widest uppercase">I. THE DOUBLE BREASTED CURVE</span>
          </div>
          <div class="aspect-[4/5] bg-brand-cream overflow-hidden border border-brand-dark/5 relative group rounded mt-0 sm:mt-8">
            <img 
              src="https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?q=80&w=600&auto=format&fit=crop" 
              alt="Tailoring wool trousers" 
              referrerpolicy="no-referrer"
              class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
            />
            <div class="absolute inset-0 bg-brand-dark/10 group-hover:bg-transparent transition-colors duration-500"></div>
            <span class="absolute bottom-4 left-4 text-white text-[10px] font-mono tracking-widest uppercase">II. Tailored drape curves</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Curations Carousel Row -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-baseline justify-between border-b border-brand-dark/10 pb-4 mb-8">
        <div>
          <span class="text-[10px] font-mono tracking-[0.25em] text-brand-muted block uppercase">SEASONAL FOCUS</span>
          <h2 class="text-2xl md:text-3xl font-serif font-medium text-brand-dark mt-1">Featured Curated Silhouettes</h2>
        </div>
        <button 
          id="btn-featured-view-all"
          @click="emit('navigate', 'shop')" 
          class="text-xs font-mono tracking-widest text-brand-dark hover:opacity-70 transition-opacity cursor-pointer"
        >
          VIEW ALL PRODUCTS →
        </button>
      </div>

      <!-- Responsive Grid of Featured Products -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="product in featuredProducts" 
          :key="product.id"
          class="h-full"
        >
          <!-- Individual Product Card wrapper -->
          <div 
            class="group flex flex-col h-full bg-white border border-brand-dark/5 overflow-hidden transition-all duration-500 hover:shadow-lg hover:border-brand-dark/15 rounded-lg"
          >
            <!-- Image Area -->
            <div 
              @click="emit('select-product', product)"
              class="relative aspect-[3/4] overflow-hidden bg-brand-cream cursor-pointer"
            >
              <img 
                :src="product.images[0]" 
                :alt="product.name"
                referrerpolicy="no-referrer"
                class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
              />
              <div class="absolute inset-0 bg-brand-dark/5 opacity-100 group-hover:opacity-0 transition-opacity"></div>
              
              <!-- Features banner details subtle -->
              <div class="absolute bottom-4 left-4 text-[10px] text-white bg-brand-dark/65 px-2.5 py-1 font-mono rounded backdrop-blur-xs">
                ${{ product.price.toFixed(2) }}
              </div>
            </div>

            <!-- Text details -->
            <div class="p-5 flex flex-col flex-1">
              <span class="text-[9px] font-mono tracking-widest text-brand-muted uppercase">{{ product.category }}</span>
              <h3 
                @click="emit('select-product', product)"
                class="mt-1.5 text-base font-serif font-medium text-brand-dark hover:opacity-75 transition-opacity cursor-pointer"
              >
                {{ product.name }}
              </h3>
              <p class="mt-2 text-xs text-brand-muted font-sans line-clamp-2 leading-relaxed">
                {{ product.description }}
              </p>
              
              <button 
                id="btn-featured-view-details"
                @click="emit('select-product', product)"
                class="mt-4 text-xs font-mono tracking-widest text-brand-dark/70 hover:text-brand-dark transition-colors text-left flex items-center cursor-pointer"
              >
                <span>EXPLORE GARMENT</span>
                <MoveRight class="w-3.5 h-3.5 ml-2" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Editorial Full Width Statement -->
    <section class="bg-brand-dark text-white py-20 md:py-28 relative overflow-hidden">
      <div class="max-w-4xl mx-auto px-4 text-center relative z-10 space-y-6">
        <Sparkles class="w-6 h-6 mx-auto text-brand-cream/40 mb-4" />
        <h2 class="text-3xl md:text-5xl font-serif font-light tracking-wide leading-tight italic">
          "Garments should act as clean, raw lines that define human presence without clutter."
        </h2>
        <p class="text-xs font-mono tracking-[0.3em] text-brand-cream/60">
          — HERD ATELIER DESIGN PHILOSOPHY
        </p>
        <div class="pt-6">
          <button 
            id="btn-statement-shop"
            @click="emit('navigate', 'shop')"
            class="bg-brand-cream text-brand-dark hover:bg-white text-xs font-mono tracking-[0.2em] py-3.5 px-8 rounded-full shadow-lg transition-all cursor-pointer"
          >
            DISCOVER WINTER CATALOG
          </button>
        </div>
      </div>
      <!-- Background subtle graphics -->
      <div class="absolute -right-16 -bottom-16 w-64 h-64 border border-white/5 rounded-full"></div>
      <div class="absolute -left-16 -top-16 w-64 h-64 border border-white/5 rounded-full"></div>
    </section>

  </div>
</template>
