<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BeforeAfterSlider from '@/Components/BeforeAfterSlider.vue';
import QuoteModal from '@/Components/QuoteModal.vue';
import QuoteSection from '@/Components/QuoteSection.vue';
import WhatsAppWidget from '@/Components/WhatsAppWidget.vue';
import {
  ShieldCheck,
  Clock,
  Award,
  CheckCircle2,
  Sparkles,
  ArrowLeft,
  Phone,
  ArrowUpRight
} from 'lucide-vue-next';
import { useI18n } from '@/i18n';
import { getServicesByLocale } from '@/data/services';

const props = defineProps({
  locale: {
    type: String,
    default: 'en',
  },
  service: Object,
  allServices: Array,
});

const { t, currentLocale, setLocale } = useI18n();


const activeAllServices = computed(() => {
  return getServicesByLocale(currentLocale.value);
});

const activeService = computed(() => {
  const list = activeAllServices.value;
  return list.find((s) => s.slug === props.service.slug) || props.service;
});

const isQuoteModalOpen = ref(false);
</script>

<template>
  <Head>
    <title>{{ `${activeService.name} — Veneno Auto Care Center Abu Dhabi & UAE` }}</title>
    <meta head-key="description" name="description" :content="activeService.description" />
    <meta head-key="og:title" property="og:title" :content="`${activeService.name} — Veneno Auto Care Center`" />
    <meta head-key="og:description" property="og:description" :content="activeService.description" />
    <meta head-key="og:image" property="og:image" :content="activeService.image" />
    <meta head-key="og:image:secure_url" property="og:image:secure_url" :content="activeService.image" />
    <meta head-key="twitter:title" name="twitter:title" :content="`${activeService.name} — Veneno Auto Care Center`" />
    <meta head-key="twitter:description" name="twitter:description" :content="activeService.description" />
    <meta head-key="twitter:image" name="twitter:image" :content="activeService.image" />
  </Head>

  <div class="min-h-screen flex flex-col bg-zinc-950 text-zinc-100 font-sans selection:bg-red-600 selection:text-white">
    <Navbar :services="activeAllServices" @open-quote="isQuoteModalOpen = true" />

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8 sm:space-y-12">
      <!-- Breadcrumb & Back -->
      <div class="flex items-center justify-between">
        <Link href="/#services" class="inline-flex items-center gap-2 text-xs font-mono text-zinc-400 hover:text-white transition-colors">
          <ArrowLeft class="w-4 h-4 rtl:rotate-180" />
          <span>{{ t('services.backToServices') }}</span>
        </Link>

        <div class="flex items-center gap-2 text-xs font-mono text-zinc-500">
          <span>VENENO</span>
          <span>/</span>
          <span class="text-red-400 uppercase">{{ activeService.category }}</span>
        </div>
      </div>

      <!-- Service Title Banner (Optimized Ad Landing Page Hero) -->
      <div class="glass-panel p-5 sm:p-10 rounded-2xl sm:rounded-3xl border border-zinc-800 space-y-4 sm:space-y-6 relative overflow-hidden">
        <!-- Ambient Red Glow -->
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Meta Tags & Warranty Badges -->
        <div class="flex flex-wrap items-center gap-2">
          <span v-if="activeService.badge" class="px-3 py-1 rounded-full bg-red-600 text-white font-mono text-[11px] sm:text-xs font-bold uppercase tracking-wider shadow-lg shadow-red-600/20">
            {{ activeService.badge }}
          </span>
          <span v-if="activeService.warranty" class="px-3 py-1 rounded-full bg-zinc-900 border border-zinc-700 text-zinc-200 font-mono text-[11px] sm:text-xs">
            {{ activeService.warranty }}
          </span>
          <span v-if="activeService.duration_hours" class="px-3 py-1 rounded-full bg-zinc-900 border border-zinc-700 text-zinc-300 font-mono text-[11px] sm:text-xs flex items-center gap-1.5">
            <Clock class="w-3.5 h-3.5 text-red-500" />
            <span>{{ t('services.hoursApplication', { hours: activeService.duration_hours }) }}</span>
          </span>
        </div>

        <div class="space-y-2 sm:space-y-3">
          <h1 class="text-2xl sm:text-5xl font-display uppercase font-semibold tracking-wide text-white leading-tight">
            {{ activeService.name }}
          </h1>

          <!-- Prominent Starting Price -->
          <div v-if="activeService.starting_price" class="flex items-baseline gap-2 pt-1">
            <span class="text-xs text-zinc-400 font-mono uppercase tracking-wider">{{ t('services.startingFrom') }}</span>
            <span class="text-2xl sm:text-3xl font-bold font-mono text-red-500 tracking-tight">{{ activeService.starting_price }}</span>
          </div>

          <p class="text-xs sm:text-base text-zinc-300 max-w-3xl leading-relaxed">
            {{ activeService.description }}
          </p>
        </div>

        <!-- High-Conversion CTAs -->
        <div class="pt-2 grid grid-cols-2 sm:flex items-center gap-2.5 sm:gap-3.5">
          <button
            @click="isQuoteModalOpen = true"
            class="px-4 sm:px-7 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-display font-semibold text-xs uppercase tracking-wider shadow-2xl shadow-red-600/40 flex items-center justify-center gap-2 transition-all text-center"
          >
            <Sparkles class="w-4 h-4" />
            <span>{{ t('services.getQuote') }}</span>
          </button>

          <a
            :href="`https://wa.me/97126344403?text=Hi%20Veneno%20Auto%20Care,%20I%20would%20like%20an%20instant%20quote%20for%20${encodeURIComponent(activeService.name)}.`"
            target="_blank"
            rel="noopener noreferrer"
            class="px-4 sm:px-6 py-3.5 sm:py-4 rounded-xl sm:rounded-2xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 hover:border-emerald-500/50 text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all shadow-lg text-center"
          >
            <svg class="w-4 h-4 text-[#25D366] fill-current" viewBox="0 0 24 24">
              <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"/>
            </svg>
            <span>{{ t('services.whatsappForService') }}</span>
          </a>
        </div>
      </div>

      <!-- Workshop Comparison Slider for this service -->
      <div v-if="activeService.before_image && activeService.after_image" class="space-y-4">
        <BeforeAfterSlider
          :beforeImage="activeService.before_image"
          :afterImage="activeService.after_image"
          :beforeLabel="activeService.before_label"
          :afterLabel="activeService.after_label"
          :title="`${activeService.name}`"
          :subtitle="activeService.tagline || t('lab.caption')"
        />
      </div>

      <!-- Treatment Deliverables -->
      <div class="glass-panel p-5 sm:p-8 rounded-2xl sm:rounded-3xl border border-zinc-800 space-y-4 sm:space-y-5">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-xl bg-red-600/20 text-red-500 border border-red-500/30">
            <ShieldCheck class="w-5 h-5" />
          </div>
          <div>
            <h3 class="text-lg sm:text-xl font-display uppercase font-semibold tracking-wide text-white">
              {{ t('services.protocolTitle') }}
            </h3>
            <p class="text-xs text-zinc-400">{{ t('services.protocolSubtitle') }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div
            v-for="(feat, idx) in (activeService.features || [])"
            :key="idx"
            class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-zinc-900/60 border border-zinc-800 text-xs text-zinc-200"
          >
            <CheckCircle2 class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
            <span class="leading-relaxed">{{ feat }}</span>
          </div>
        </div>
      </div>

      <!-- On-Page Quote Section for this service -->
      <QuoteSection />

      <!-- Other Services Grid Navigation -->
      <div class="space-y-4 sm:space-y-6 pt-6 border-t border-zinc-900">
        <h3 class="text-lg sm:text-xl font-display font-semibold uppercase tracking-wider text-white">
          {{ t('services.exploreOther') }}
        </h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
          <Link
            v-for="s in activeAllServices.filter(x => x.slug !== activeService.slug).slice(0, 4)"
            :key="s.slug"
            :href="`/services/${s.slug}`"
            class="p-4 sm:p-5 rounded-2xl glass-panel border border-zinc-800 hover:border-red-500/40 transition-all group flex flex-col justify-between"
          >
            <div>
              <span class="text-[9px] sm:text-[10px] font-mono text-red-400 uppercase line-clamp-1">{{ s.category }}</span>
              <h4 class="text-xs sm:text-sm font-bold text-white group-hover:text-red-400 transition-colors mt-1 line-clamp-2">{{ s.name }}</h4>
            </div>
            <div class="flex items-center gap-1 text-[10px] sm:text-[11px] font-mono text-zinc-400 group-hover:text-white mt-3 sm:mt-4">
              <span>{{ t('services.viewTreatment') }}</span>
              <ArrowUpRight class="w-3.5 h-3.5 text-red-500 rtl:rotate-90" />
            </div>
          </Link>
        </div>
      </div>
    </main>

    <Footer :services="activeAllServices" @open-quote="isQuoteModalOpen = true" />

    <QuoteModal
      :is-open="isQuoteModalOpen"
      :preselected-service="activeService.name"
      @close="isQuoteModalOpen = false"
    />

    <WhatsAppWidget />
  </div>
</template>
