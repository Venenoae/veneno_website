<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
  ChevronDown,
  Menu,
  X,
  Globe,
  Check
} from 'lucide-vue-next';
import { useI18n } from '../i18n';
import { getServicesByLocale } from '../data/services';

const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['open-quote']);

const { t, currentLocale, setLocale } = useI18n();

const isMobileMenuOpen = ref(false);
const isServicesDropdownOpen = ref(false);
const isLangDropdownOpen = ref(false);

const dropdownRef = ref(null);

const activeServices = computed(() => {
  return getServicesByLocale(currentLocale.value);
});

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isLangDropdownOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const switchLanguage = (lang) => {
  isLangDropdownOpen.value = false;
  if (currentLocale.value === lang) return;
  setLocale(lang);
};

const scrollTo = (elementId) => {
  isMobileMenuOpen.value = false;
  const el = document.getElementById(elementId);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' });
  } else {
    window.location.href = `/#${elementId}`;
  }
};
</script>

<template>
  <header class="sticky top-0 z-40 bg-zinc-950/95 backdrop-blur-xl border-b border-zinc-800/80 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        
        <!-- Logo -->
        <Link href="/" class="flex items-center group cursor-pointer">
          <img 
            src="/images/logo.png" 
            alt="Veneno Auto Care Center" 
            title="Veneno Auto Care Center" 
            class="h-9 sm:h-10 w-auto object-contain transition-transform duration-200 group-hover:scale-105" 
          />
        </Link>

        <!-- Desktop Traditional Navigation -->
        <nav class="hidden xl:flex items-center gap-7">
          <Link href="/" class="text-xs font-semibold uppercase tracking-wider text-zinc-300 hover:text-white transition-colors cursor-pointer">
            {{ t('nav.home') }}
          </Link>

          <a href="#about" @click.prevent="scrollTo('about')" class="text-xs font-semibold uppercase tracking-wider text-zinc-300 hover:text-white transition-colors cursor-pointer">
            {{ t('nav.about') }}
          </a>

          <a href="#why-us" @click.prevent="scrollTo('why-us')" class="text-xs font-semibold uppercase tracking-wider text-zinc-300 hover:text-white transition-colors cursor-pointer">
            {{ t('nav.whyUs') }}
          </a>

          <!-- Services Dropdown -->
          <div class="relative" @mouseenter="isServicesDropdownOpen = true" @mouseleave="isServicesDropdownOpen = false">
            <a href="#services" @click.prevent="scrollTo('services')" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-zinc-300 hover:text-white transition-colors py-2 cursor-pointer">
              <span>{{ t('nav.services') }}</span>
              <ChevronDown class="w-3.5 h-3.5 text-zinc-400 transition-transform" :class="{ 'rotate-180': isServicesDropdownOpen }" />
            </a>

            <div v-if="isServicesDropdownOpen" class="absolute top-full -left-10 w-96 py-2 glass-panel rounded-2xl shadow-2xl shadow-black/90 border border-zinc-800 animate-in fade-in slide-in-from-top-2 duration-150 grid grid-cols-1 divide-y divide-zinc-800/60 max-h-[460px] overflow-y-auto">
              <Link
                v-for="service in activeServices"
                :key="service.slug"
                :href="`/services/${service.slug}`"
                class="flex items-center justify-between px-4 py-2.5 hover:bg-zinc-800/70 transition-colors group"
              >
                <div>
                  <div class="text-xs font-semibold text-zinc-200 group-hover:text-red-400 transition-colors">{{ service.name }}</div>
                  <div class="text-[10px] text-zinc-500 font-mono">{{ service.tagline }}</div>
                </div>
                <span class="text-[9px] px-2 py-0.5 rounded-full bg-red-950/60 text-red-300 border border-red-800/40 font-mono">{{ service.badge }}</span>
              </Link>
            </div>
          </div>

          <a href="#certificates" @click.prevent="scrollTo('certificates')" class="text-xs font-semibold uppercase tracking-wider text-zinc-300 hover:text-white transition-colors cursor-pointer">
            {{ t('nav.certificates') }}
          </a>

          <a href="#contact" @click.prevent="scrollTo('contact')" class="text-xs font-semibold uppercase tracking-wider text-zinc-300 hover:text-white transition-colors cursor-pointer">
            {{ t('nav.contact') }}
          </a>

          <!-- Hammer Challenge Special Event Link -->
          <Link 
            :href="currentLocale === 'ar' ? '/ar/hammer-challenge' : '/hammer-challenge'" 
            class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-lime-500/20 via-emerald-500/15 to-lime-500/20 border border-[#a3e635]/50 text-[#a3e635] hover:text-white hover:border-[#a3e635] text-xs font-bold uppercase tracking-wider transition-all duration-200 shadow-sm shadow-lime-500/10 hover:shadow-lime-500/30 cursor-pointer"
          >
            <span class="w-2 h-2 rounded-full bg-[#a3e635] animate-ping"></span>
            <span>{{ t('nav.hammer') }}</span>
          </Link>
        </nav>

        <!-- Right Side: Language Switcher & Get a Quote CTA -->
        <div class="hidden sm:flex items-center gap-3">
          
          <!-- Modern Dropdown Globe Selector -->
          <div ref="dropdownRef" class="relative">
            <button
              @click="isLangDropdownOpen = !isLangDropdownOpen"
              class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-800 text-zinc-200 hover:text-white text-xs font-semibold transition-all shadow-sm"
              title="Select Language / اختر اللغة"
            >
              <Globe class="w-4 h-4 text-red-500" />
              <span>{{ currentLocale === 'ar' ? 'العربية' : 'English' }}</span>
              <ChevronDown class="w-3.5 h-3.5 text-zinc-400 transition-transform" :class="{ 'rotate-180': isLangDropdownOpen }" />
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="isLangDropdownOpen"
              class="absolute right-0 rtl:right-auto rtl:left-0 mt-2 w-44 rounded-2xl glass-panel border border-zinc-800 p-1.5 shadow-2xl shadow-black/80 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
            >
              <button
                @click="switchLanguage('en')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-colors"
                :class="currentLocale === 'en' ? 'bg-red-600/20 text-white font-bold border border-red-500/30' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white'"
              >
                <span>English</span>
                <Check v-if="currentLocale === 'en'" class="w-3.5 h-3.5 text-red-400" />
              </button>

              <button
                @click="switchLanguage('ar')"
                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-colors mt-1"
                :class="currentLocale === 'ar' ? 'bg-red-600/20 text-white font-bold border border-red-500/30' : 'text-zinc-400 hover:bg-zinc-800 hover:text-white'"
              >
                <span>العربية</span>
                <Check v-if="currentLocale === 'ar'" class="w-3.5 h-3.5 text-red-400" />
              </button>
            </div>
          </div>

          <!-- Get a Quote CTA -->
          <button
            @click="emit('open-quote')"
            class="px-4 py-2 rounded-xl text-xs font-display font-semibold uppercase tracking-wider text-white bg-gradient-to-r from-red-600 via-red-500 to-red-700 hover:from-red-500 hover:to-red-600 shadow-lg shadow-red-600/30 transition-all duration-300 transform hover:-translate-y-0.5"
          >
            <span>{{ t('nav.getQuote') }}</span>
          </button>
        </div>

        <!-- Mobile Language & Menu Toggle -->
        <div class="xl:hidden flex items-center gap-2">
          <!-- Mobile Dropdown Selector -->
          <div class="flex items-center p-1 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-mono">
            <button
              @click="switchLanguage('en')"
              class="px-2 py-1 rounded-lg text-xs"
              :class="currentLocale === 'en' ? 'bg-red-600 text-white font-bold' : 'text-zinc-400'"
            >
              EN
            </button>
            <button
              @click="switchLanguage('ar')"
              class="px-2 py-1 rounded-lg text-xs"
              :class="currentLocale === 'ar' ? 'bg-red-600 text-white font-bold' : 'text-zinc-400'"
            >
              عربي
            </button>
          </div>

          <button
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="p-2 rounded-xl bg-zinc-900 text-zinc-300 border border-zinc-800"
            aria-label="Toggle Navigation Menu"
          >
            <Menu v-if="!isMobileMenuOpen" class="w-5 h-5" />
            <X v-else class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <div v-if="isMobileMenuOpen" class="xl:hidden glass-panel border-b border-zinc-800 px-6 py-6 space-y-4 animate-in fade-in slide-in-from-top-4 duration-200">
      <Link href="/" @click="isMobileMenuOpen = false" class="block text-base font-semibold text-zinc-200">{{ t('nav.home') }}</Link>
      
      <!-- Hammer Challenge Mobile Highlight -->
      <Link 
        :href="currentLocale === 'ar' ? '/ar/hammer-challenge' : '/hammer-challenge'" 
        @click="isMobileMenuOpen = false" 
        class="flex items-center justify-between p-3 rounded-xl bg-gradient-to-r from-lime-500/20 via-zinc-900 to-zinc-900 border border-[#a3e635]/40 text-[#a3e635] font-bold text-sm"
      >
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-[#a3e635] animate-ping"></span>
          <span>{{ t('nav.hammer') }}</span>
        </div>
        <span class="text-[10px] px-2 py-0.5 rounded-full bg-lime-950/60 text-[#a3e635] border border-[#a3e635]/40 font-mono">12 SEPT</span>
      </Link>

      <a href="#about" @click.prevent="scrollTo('about')" class="block text-base font-semibold text-zinc-200">{{ t('nav.about') }}</a>
      <a href="#why-us" @click.prevent="scrollTo('why-us')" class="block text-base font-semibold text-zinc-200">{{ t('nav.whyUs') }}</a>
      <a href="#services" @click.prevent="scrollTo('services')" class="block text-base font-semibold text-zinc-200">{{ t('nav.services') }}</a>
      <a href="#certificates" @click.prevent="scrollTo('certificates')" class="block text-base font-semibold text-zinc-200">{{ t('nav.certificates') }}</a>
      <a href="#contact" @click.prevent="scrollTo('contact')" class="block text-base font-semibold text-zinc-200">{{ t('nav.contact') }}</a>
      
      <div class="py-2 border-y border-zinc-800/80 space-y-2">
        <div class="text-xs font-mono uppercase text-zinc-500 tracking-wider">{{ t('nav.allServices') }}</div>
        <div class="grid grid-cols-1 gap-1">
          <Link
            v-for="service in activeServices"
            :key="service.slug"
            :href="`/services/${service.slug}`"
            @click="isMobileMenuOpen = false"
            class="block text-xs text-zinc-300 hover:text-red-400 py-1 pl-2"
          >
            • {{ service.name }}
          </Link>
        </div>
      </div>

      <div class="pt-2 flex flex-col gap-2.5">
        <button
          @click="emit('open-quote'); isMobileMenuOpen = false;"
          class="w-full py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 text-white font-display font-semibold text-center uppercase tracking-wider text-xs shadow-lg shadow-red-600/30 active:scale-95 transition-all"
        >
          {{ t('nav.getQuote') }}
        </button>

        <a
          href="https://wa.me/97126344403?text=Hello%20Veneno%20Auto%20Care,%20I%20would%20like%20to%20inquire%20about%20your%20services."
          target="_blank"
          rel="noopener noreferrer"
          class="w-full py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700/80 hover:border-emerald-500/50 text-white font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-md active:scale-95"
        >
          <svg class="w-4 h-4 text-[#25D366] fill-current" viewBox="0 0 24 24">
            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"/>
          </svg>
          <span>{{ t('nav.whatsapp') }}</span>
        </a>
      </div>
    </div>
  </header>
</template>
