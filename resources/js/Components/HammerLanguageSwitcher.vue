<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from '@/i18n';
import { Globe, ChevronDown, Check } from 'lucide-vue-next';

const { currentLocale, setLocale } = useI18n();
const isLangDropdownOpen = ref(false);
const dropdownRef = ref(null);

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
  if (currentLocale.value === lang) {
    isLangDropdownOpen.value = false;
    return;
  }

  setLocale(lang);
  isLangDropdownOpen.value = false;

  // Seamlessly update browser URL path without reloading or losing form inputs
  if (typeof window !== 'undefined') {
    const currentPath = window.location.pathname;
    const currentSearch = window.location.search;
    let newPath = currentPath;

    if (lang === 'ar') {
      if (!newPath.startsWith('/ar')) {
        newPath = '/ar' + (newPath === '/' ? '' : newPath);
      }
    } else {
      if (newPath.startsWith('/ar')) {
        newPath = newPath.replace(/^\/ar/, '') || '/';
      }
    }

    if (newPath !== currentPath) {
      window.history.pushState({}, '', newPath + currentSearch);
    }
  }
};
</script>

<template>
  <div ref="dropdownRef" class="relative">
    <button
      type="button"
      @click="isLangDropdownOpen = !isLangDropdownOpen"
      class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-700 text-zinc-200 hover:text-white text-xs font-semibold transition-all shadow-md backdrop-blur-md cursor-pointer select-none"
      :title="currentLocale === 'ar' ? 'اختر اللغة' : 'Select Language'"
    >
      <Globe class="w-4 h-4 text-red-500 shrink-0" />
      <span class="font-medium text-white">{{ currentLocale === 'ar' ? 'العربية' : 'English' }}</span>
      <ChevronDown 
        class="w-3.5 h-3.5 text-zinc-400 transition-transform duration-200" 
        :class="{ 'rotate-180': isLangDropdownOpen }" 
      />
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="isLangDropdownOpen"
      class="absolute right-0 rtl:right-auto rtl:left-0 mt-2 w-44 rounded-2xl bg-[#121216]/95 border border-zinc-800 p-1.5 shadow-2xl shadow-black/90 z-50 animate-in fade-in slide-in-from-top-2 duration-150 backdrop-blur-xl"
    >
      <button
        type="button"
        @click="switchLanguage('en')"
        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors cursor-pointer text-left rtl:text-right"
        :class="currentLocale === 'en' 
          ? 'bg-red-600/20 text-white font-bold border border-red-500/40' 
          : 'text-zinc-400 hover:bg-zinc-800/80 hover:text-white'"
      >
        <span>English</span>
        <Check v-if="currentLocale === 'en'" class="w-3.5 h-3.5 text-red-500" />
      </button>

      <button
        type="button"
        @click="switchLanguage('ar')"
        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors mt-1 cursor-pointer text-left rtl:text-right"
        :class="currentLocale === 'ar' 
          ? 'bg-red-600/20 text-white font-bold border border-red-500/40' 
          : 'text-zinc-400 hover:bg-zinc-800/80 hover:text-white'"
      >
        <span>العربية</span>
        <Check v-if="currentLocale === 'ar'" class="w-3.5 h-3.5 text-red-500" />
      </button>
    </div>
  </div>
</template>
