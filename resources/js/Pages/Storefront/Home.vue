<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BeforeAfterSlider from '@/Components/BeforeAfterSlider.vue';
import WorksGallery from '@/Components/WorksGallery.vue';
import QuoteSection from '@/Components/QuoteSection.vue';
import QuoteModal from '@/Components/QuoteModal.vue';
import WhatsAppWidget from '@/Components/WhatsAppWidget.vue';
import {
  Sparkles,
  ShieldCheck,
  Phone,
  Award,
  ChevronRight,
  ChevronDown,
  Star,
  CheckCircle2,
  MapPin,
  Clock,
  Mail,
  ArrowUpRight,
  Users,
  Target,
  Compass,
  BadgeCheck,
  Zap,
  HeartHandshake,
  Gem,
  Car,
  Wrench,
  Layers,
  Gift,
  Flame,
  Trophy,
  Scale,
  ExternalLink
} from 'lucide-vue-next';
import { useI18n } from '@/i18n';
import { getServicesByLocale } from '@/data/services';

const props = defineProps({
  locale: {
    type: String,
    default: 'en',
  },
  services: {
    type: Array,
    default: () => [],
  },
  galleryImages: {
    type: Array,
    default: () => [],
  },
});

const { t, currentLocale, setLocale } = useI18n();

const activeServices = computed(() => {
  return getServicesByLocale(currentLocale.value);
});

const isQuoteModalOpen = ref(false);
const selectedServiceName = ref('');

const openQuoteWithService = (serviceName) => {
  selectedServiceName.value = serviceName;
  isQuoteModalOpen.value = true;
};

const getServiceUrl = (slug) => {
  return currentLocale.value === 'ar' ? `/ar/services/${slug}` : `/services/${slug}`;
};

// Synchronized Live Countdown for ADIHEX Campaign (Ends 6 Sept 2026 22:00 GST)
const countdown = ref({
  days: '00',
  hours: '00',
  minutes: '00',
  seconds: '00',
});
const hammerCountdown = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
let countdownTimer = null;

const calculateCountdown = () => {
  const now = new Date().getTime();
  const targetDate = new Date('2026-09-06T22:00:00+04:00').getTime();
  const diff = Math.max(0, targetDate - now);

  const pad = (n) => String(n).padStart(2, '0');
  countdown.value = {
    days: pad(Math.floor(diff / (1000 * 60 * 60 * 24))),
    hours: pad(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))),
    minutes: pad(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))),
    seconds: pad(Math.floor((diff % (1000 * 60)) / 1000)),
  };

  const hammerTarget = new Date('2026-09-12T19:00:00+04:00').getTime();
  const hammerDiff = Math.max(0, hammerTarget - Date.now());
  hammerCountdown.value = {
    days: pad(Math.floor(hammerDiff / (1000 * 60 * 60 * 24))),
    hours: pad(Math.floor((hammerDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))),
    minutes: pad(Math.floor((hammerDiff % (1000 * 60 * 60)) / (1000 * 60))),
    seconds: pad(Math.floor((hammerDiff % (1000 * 60)) / 1000)),
  };
};

onMounted(() => {
  calculateCountdown();
  countdownTimer = setInterval(calculateCountdown, 1000);
});

onUnmounted(() => {
  if (countdownTimer) clearInterval(countdownTimer);
});
</script>

<template>
  <Head>
    <title>{{ currentLocale === 'ar' ? 'مركز فينينو للعناية بالسيارات — أضخم مركز متعدد العلامات في أبوظبي والإمارات' : 'Veneno Auto Care Center — The Ultimate Art of Care in UAE' }}</title>
    <meta head-key="description" name="description" :content="t('hero.subtitle')" />
    <meta head-key="og:image" property="og:image" content="/images/main-branch.webp" />
    <meta head-key="og:image:secure_url" property="og:image:secure_url" content="/images/main-branch.webp" />
    <meta head-key="twitter:image" name="twitter:image" content="/images/main-branch.webp" />
  </Head>

  <div class="min-h-screen flex flex-col bg-cyber-grid bg-zinc-950 text-zinc-100 font-sans selection:bg-red-600 selection:text-white relative overflow-x-hidden">
    <!-- Navbar -->
    <Navbar :services="activeServices" @open-quote="isQuoteModalOpen = true" />

    <!-- 1. CINEMATIC VIDEO HERO SECTION -->
    <section id="home" class="relative min-h-[85vh] sm:min-h-[92vh] flex items-center justify-center overflow-hidden bg-zinc-950">
      <!-- Background Video Overlay & Media -->
      <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none select-none">
        <!-- YouTube Background Video with Enhanced Transparency & Clarity -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[100vw] h-[56.25vw] min-h-[100vh] min-w-[177.77vh] opacity-80 filter brightness-90 contrast-105">
          <iframe
            class="w-full h-full pointer-events-none"
            src="https://www.youtube-nocookie.com/embed/I4cZjZX3S00?autoplay=1&mute=1&loop=1&playlist=I4cZjZX3S00&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&enablejsapi=1&playsinline=1"
            title="Veneno Auto Care Center Hero Video"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
          ></iframe>
        </div>

        <!-- Lightened Transparent Gradient Overlays -->
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-zinc-950/20"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/50 via-transparent to-zinc-950/50"></div>
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-red-600/15 rounded-full blur-3xl pointer-events-none animate-glow-pulse"></div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-24 text-center">
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-display font-semibold uppercase tracking-wider text-white max-w-5xl mx-auto leading-[1.15] drop-shadow-md">
          {{ t('hero.titleLine1') }} <br class="hidden sm:inline" />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-white to-red-400">
            {{ t('hero.titleLine2') }}
          </span>
        </h1>

        <p class="mt-4 sm:mt-6 text-xs sm:text-lg text-zinc-200 max-w-2xl mx-auto font-light leading-relaxed drop-shadow line-clamp-2 sm:line-clamp-none">
          {{ t('hero.subtitle') }}
        </p>

        <!-- CTA Action Button: Request Quote -->
        <div class="mt-6 sm:mt-10 flex items-center justify-center max-w-xs sm:max-w-md mx-auto">
          <!-- Primary CTA: Request Quote -->
          <button
            @click="isQuoteModalOpen = true"
            class="h-14 px-8 sm:px-12 rounded-xl sm:rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-red-700 hover:from-red-500 hover:to-red-600 active:scale-95 text-white font-display font-bold text-xs sm:text-sm uppercase tracking-wider shadow-2xl shadow-red-600/40 border border-red-400/30 transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2.5 text-center leading-snug w-full sm:w-auto"
          >
            <Sparkles class="w-4 h-4 shrink-0 text-white animate-pulse" />
            <span>{{ t('hero.getQuoteBtn') }}</span>
          </button>
        </div>

        <!-- Trust Stats Banner (from PDF) -->
        <div class="mt-8 sm:mt-16 grid grid-cols-4 gap-2 sm:gap-4 max-w-4xl mx-auto">
          <div class="glass-panel p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-zinc-800 text-center flex flex-col justify-center">
            <div class="text-lg sm:text-3xl font-display font-bold italic text-white">150+</div>
            <div class="text-[9px] sm:text-[11px] font-mono uppercase text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1">{{ t('hero.statTeam') }}</div>
          </div>
          <!-- 3M Logo -->
          <div class="glass-panel p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-zinc-800 text-center flex flex-col items-center justify-center">
            <div class="h-7 sm:h-12 flex items-center justify-center">
              <svg class="h-6 sm:h-10 w-auto text-red-500 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-label="3M Logo">
                <path fill="currentColor" d="M18.903 5.954L17.17 13.03l-1.739-7.076h-5.099v2.613C9.72 6.28 7.56 5.706 5.558 5.674 3.12 5.641.563 6.701.469 9.936h3.373c0-.977.747-1.536 1.588-1.523 1.032-.008 1.508.434 1.533 1.124-.036.597-.387 1.014-1.525 1.014H4.303V12.9h1.03c.584 0 1.399.319 1.431 1.155.04.995-.652 1.435-1.501 1.443-1.517-.053-1.763-1.225-1.763-2.23H0c.015.677-.151 5.091 5.337 5.059 2.629.025 4.464-1.085 5.003-2.613v2.342h3.455v-7.632l1.867 7.634h3.018l1.875-7.626v7.634H24V5.954h-5.097zm-8.561 7.06c-.429-.893-1.034-1.284-1.376-1.407.714-.319 1.09-.751 1.376-1.614v3.021z"/>
              </svg>
            </div>
            <div class="text-[9px] sm:text-[11px] font-mono uppercase text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1">{{ t('hero.statDealer') }}</div>
          </div>

          <!-- GYEON Logo -->
          <div class="glass-panel p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-zinc-800 text-center flex flex-col items-center justify-center">
            <div class="h-7 sm:h-12 flex items-center justify-center">
              <svg class="h-5 sm:h-9 w-auto text-white fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 135.377 45.757" aria-label="GYEON Logo" role="img">
                <path fill="currentColor" d="M79.865,44.962a10.235,10.235,0,0,1-3.63-2.153,11.023,11.023,0,0,1-2.36-3.328,16.684,16.684,0,0,1-1.332-4.2,27.788,27.788,0,0,1-.4-4.869v-14.8Q72.145,0,84.871,0,97.625,0,97.624,15.613V30.195a28.607,28.607,0,0,1-.387,4.882,16.874,16.874,0,0,1-1.318,4.246,11.5,11.5,0,0,1-2.372,3.412,10.064,10.064,0,0,1-3.642,2.215,16.118,16.118,0,0,1-10.041.013Zm3-37.486a3.448,3.448,0,0,0-1.209,1.639,10.453,10.453,0,0,0-.546,2.055,14.464,14.464,0,0,0-.157,2.214V32.422q0,6.483,3.919,6.483,3.944,0,3.945-6.483V13.385q0-6.508-3.945-6.51A3.312,3.312,0,0,0,82.864,7.476ZM6.836,44.889a9.063,9.063,0,0,1-3.17-2.3,13.055,13.055,0,0,1-2.093-3.363,17.538,17.538,0,0,1-1.21-3.953A23.343,23.343,0,0,1,0,31.125V15.612Q0,0,12.848,0a14.948,14.948,0,0,1,4.828.721,10.194,10.194,0,0,1,3.544,1.994,10.208,10.208,0,0,1,2.347,3.132A15.6,15.6,0,0,1,24.9,9.885a26.037,26.037,0,0,1,.411,4.8v3.2H16.889V14.192q0-1.148-.059-2.019a12.758,12.758,0,0,0-.3-1.932,5.974,5.974,0,0,0-.654-1.749A3.374,3.374,0,0,0,14.688,7.34a3.51,3.51,0,0,0-1.84-.465q-4.04,0-4.04,6.51V32.4q0,6.485,4.04,6.484,4.017,0,4.018-6.729v-2.3H14.373l-2.291-6.386H25.31V44.9H19.624l-1.113-3.083a8.177,8.177,0,0,1-3.169,2.852,8.96,8.96,0,0,1-4.235,1.089A10.259,10.259,0,0,1,6.836,44.889ZM52.3,44.9V17.583L56.954.856H70.043L68.251,7.29H61.108V18.964h6.406l-1.742,6.263H61.108v13.24h8.967l-1.8,6.434Zm-17.765,0V30.341L26.355.856h8.3l4.066,19.355q.75-3.18,2.25-9.677T43.2.856h8.058l-8.2,29.486V44.9Zm84.818,0-9.532-24.444V44.9h-7.576V.832h6.34l7.187,17.334,10.962,26.713V44.9Zm-1.209-34.737V.832h8.591V31.1Zm12.1-7.022a2.568,2.568,0,1,1,5.136,0,2.568,2.568,0,1,1-5.136,0Zm.4,0a2.168,2.168,0,1,0,4.336,0,2.168,2.168,0,1,0-4.336,0Zm2.975,1.444a.172.172,0,0,1-.134-.094l-.56-1.121h-.6c-.027,0-.042.013-.042.028V4.464a.112.112,0,0,1-.12.121h-.2a.107.107,0,0,1-.12-.121V1.792a.107.107,0,0,1,.12-.121h1.053a.815.815,0,0,1,.908.85.762.762,0,0,1-.56.8l.56,1.133c.054.095,0,.136-.079.136Zm-1.335-2.5v.85c0,.027.015.027.042.027H133c.361,0,.507-.161.507-.444s-.146-.46-.507-.46h-.679C132.3,2.061,132.282,2.076,132.282,2.088Z"></path>
              </svg>
            </div>
            <div class="text-[9px] sm:text-[11px] font-mono uppercase text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1">{{ t('hero.statMultiBrand') }}</div>
          </div>

          <!-- ISO Official Logos -->
          <div class="glass-panel p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border border-zinc-800 text-center flex flex-col items-center justify-center">
            <div class="h-7 sm:h-12 flex items-center justify-center gap-1.5 sm:gap-2">
              <img src="/images/certifications/iso-9001.svg" alt="ISO 9001:2015" class="h-5 sm:h-8 w-auto object-contain rounded bg-white p-0.5 shadow" />
              <img src="/images/certifications/iso-45001.png" alt="ISO 45001:2018" class="h-5 sm:h-8 w-auto object-contain rounded bg-white p-0.5 shadow" />
            </div>
            <div class="text-[9px] sm:text-[11px] font-mono uppercase text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1">{{ t('hero.statIso') }}</div>
          </div>
        </div>

        <!-- Scroll Down Explore Hook -->
        <div class="mt-8 sm:mt-12 flex justify-center">
          <a
            href="#about"
            class="inline-flex items-center gap-2.5 px-4 sm:px-5 py-2 rounded-full glass-panel border border-zinc-700/80 hover:border-red-500/60 text-[11px] sm:text-xs font-mono text-zinc-300 hover:text-white transition-all shadow-xl shadow-black/60 group animate-float-pulse"
          >
            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
            <span class="font-medium tracking-wide">{{ t('hero.exploreScroll') }}</span>
            <ChevronDown class="w-3.5 h-3.5 text-red-500 group-hover:translate-y-0.5 transition-transform" />
          </a>
        </div>
      </div>
    </section>

    <!-- HAMMER CHALLENGE FEATURED EVENT CAMPAIGN -->
    <section class="relative overflow-hidden border-b border-zinc-800/80 bg-gradient-to-b from-[#070709] via-[#0d0d12] to-[#070709] py-10 sm:py-16">
      <div class="pointer-events-none absolute left-1/2 top-0 h-[350px] w-[900px] -translate-x-1/2 bg-gradient-to-b from-red-600/15 via-amber-500/10 to-transparent blur-3xl"></div>
      <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(#27272a_1px,transparent_1px)] [background-size:24px_24px] opacity-20"></div>
      <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border-2 border-amber-500/30 bg-gradient-to-b from-zinc-900/90 via-zinc-950/95 to-black p-5 shadow-2xl shadow-black/90 sm:p-8 lg:p-10">
          <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>
          <div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">
            <div class="space-y-5 text-center lg:col-span-7 lg:text-left">
              <div class="flex flex-wrap items-center justify-center gap-2.5 lg:justify-start"><span class="inline-flex items-center gap-2 rounded-full border border-amber-500/40 bg-amber-500/15 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-300"><Flame class="h-3.5 w-3.5 text-amber-400" /> {{ t('hammer.eventSeries') }}</span><span class="inline-flex items-center gap-1.5 rounded-full border border-zinc-700/80 bg-zinc-900 px-3 py-1.5 text-xs font-mono text-zinc-300"><MapPin class="h-3.5 w-3.5 text-red-500" /> {{ t('hammer.venue') }}</span></div>
              <div><h2 class="font-display text-3xl font-bold uppercase leading-tight tracking-tight text-white sm:text-5xl">{{ t('hammer.title') }}<br /><span class="bg-gradient-to-r from-amber-400 via-amber-200 to-red-500 bg-clip-text text-transparent">{{ t('hammer.final') }}</span></h2><p class="mt-3 text-xs font-mono uppercase tracking-wider text-zinc-400">{{ t('hammer.date') }}</p></div>
              <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3"><div class="rounded-2xl border border-zinc-800/80 bg-zinc-950/70 p-3 text-left"><p class="text-[10px] font-mono uppercase text-zinc-500">{{ t('hammer.checkIn') }}</p><p class="mt-1 text-sm font-bold text-white">7:30 PM</p></div><div class="rounded-2xl border border-zinc-800/80 bg-zinc-950/70 p-3 text-left"><p class="text-[10px] font-mono uppercase text-zinc-500">{{ t('hammer.starts') }}</p><p class="mt-1 text-sm font-bold text-white">8:00 PM</p></div><div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-3 text-left"><p class="text-[10px] font-mono uppercase text-amber-300">{{ t('hammer.firstPrize') }}</p><p class="mt-1 text-sm font-black text-white">AED 15,000</p></div></div>
              <div class="rounded-2xl border border-amber-500/30 bg-zinc-950/90 p-4 text-center shadow-xl"><div class="flex items-center justify-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-amber-400"><Clock class="h-3.5 w-3.5" /> {{ t('hammer.countdown') }}</div><div class="mt-3 grid grid-cols-4 gap-2"><div v-for="unit in [{ key: 'days', label: t('hammer.days') }, { key: 'hours', label: t('hammer.hours') }, { key: 'minutes', label: t('hammer.minutes') }, { key: 'seconds', label: t('hammer.seconds') }]" :key="unit.key" class="rounded-xl border border-zinc-800 bg-zinc-900 p-2.5"><div class="font-mono text-lg font-bold text-amber-300">{{ hammerCountdown[unit.key] }}</div><div class="text-[9px] font-mono uppercase text-zinc-500">{{ unit.label }}</div></div></div></div>
              <div class="flex flex-wrap items-center justify-center gap-3 lg:justify-start"><Link href="/hammer-challenge/register" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-red-950/50 transition hover:brightness-110 sm:w-auto">{{ t('hammer.register') }} <ArrowUpRight class="h-4 w-4" /></Link><Link href="/hammer-challenge/terms" class="text-xs font-semibold text-zinc-400 underline underline-offset-4 hover:text-amber-300">{{ t('hammer.terms') }}</Link></div>
            </div>
            <div class="flex flex-col gap-4 lg:col-span-5"><div class="rounded-2xl border border-zinc-800 bg-zinc-950/90 p-4 text-center shadow-xl"><div class="flex items-center justify-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-amber-400"><Trophy class="h-3.5 w-3.5" /> {{ t('hammer.prizePodium') }}</div><div class="mt-4 border-b border-amber-500/20 pb-4"><p class="text-[10px] font-mono uppercase text-amber-300">{{ t('hammer.firstCash') }}</p><p class="mt-1 text-4xl font-black text-white">AED 15,000</p></div></div><div class="relative min-h-[260px] overflow-hidden rounded-2xl border border-amber-500/35 bg-zinc-950/80 p-3 shadow-xl sm:min-h-[320px]"><div class="absolute inset-3 border border-amber-500/15"></div><picture class="relative flex h-full min-h-[235px] items-center justify-center sm:min-h-[295px]"><source media="(max-width: 639px)" srcset="/images/hammer/Hammer3.jpeg" /><img src="/images/hammer/Hammer1.jpeg" alt="Veneno Hammer Challenge campaign artwork" class="h-full max-h-[330px] w-full object-cover drop-shadow-[0_18px_35px_rgba(239,68,68,0.14)]" /></picture><div class="pointer-events-none absolute bottom-5 left-5 text-[9px] font-mono uppercase tracking-[0.2em] text-amber-300/80"></div></div></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. ABOUT US & VISION / MISSION SECTION -->
    <section id="about" class="py-12 sm:py-24 bg-zinc-950/90 relative border-t border-zinc-900 overflow-hidden">
      <!-- Ambient Glow Orb in Background -->
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none animate-glow-pulse"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 items-center">
          
          <!-- Main Branch Photo -->
          <div class="lg:col-span-6">
            <div class="rounded-3xl overflow-hidden glass-panel border border-zinc-800 w-full h-48 sm:h-[480px] relative group shadow-2xl shadow-black/60">
              <img
                src="/images/main-branch.webp"
                alt="Veneno Auto Care Center — Main Branch Abu Dhabi"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
              />
              <!-- Subtle overlay badge -->
              <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl sm:rounded-2xl glass-panel border border-zinc-700/80 text-[11px] sm:text-xs font-mono text-zinc-300 shadow-xl backdrop-blur-md">
                  <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                  <span class="text-red-400 font-bold uppercase tracking-wider">Abu Dhabi</span>
                  <span class="text-zinc-600">|</span>
                  <span class="line-clamp-1">Veneno Auto Care — Main Branch</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Story, Heritage & Vision/Mission Content -->
          <div class="lg:col-span-6 space-y-4 sm:space-y-6">
            <div>
              <span class="text-xs font-mono uppercase tracking-widest text-red-500 font-bold">{{ t('about.tag') }}</span>
              <h2 class="text-2xl sm:text-4xl font-display font-semibold uppercase tracking-wider text-white mt-1">
                {{ t('about.title') }}
              </h2>
            </div>

            <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed font-light">
              {{ t('about.p1') }}
            </p>

            <p class="hidden sm:block text-sm text-zinc-400 leading-relaxed">
              {{ t('about.p2') }}
            </p>

            <!-- Vision & Mission Cards -->
            <div class="grid grid-cols-2 gap-2.5 sm:gap-4 pt-1 sm:pt-2">
              <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-zinc-900/70 border border-zinc-800 space-y-1.5 sm:space-y-2">
                <div class="flex items-center gap-1.5 sm:gap-2 text-red-500">
                  <Compass class="w-4 h-4 sm:w-5 sm:h-5" />
                  <h4 class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-white">{{ t('about.visionTitle') }}</h4>
                </div>
                <p class="text-[10px] sm:text-xs text-zinc-400 leading-relaxed line-clamp-2 sm:line-clamp-none">{{ t('about.visionText') }}</p>
              </div>

              <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-zinc-900/70 border border-zinc-800 space-y-1.5 sm:space-y-2">
                <div class="flex items-center gap-1.5 sm:gap-2 text-red-500">
                  <Target class="w-4 h-4 sm:w-5 sm:h-5" />
                  <h4 class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-white">{{ t('about.missionTitle') }}</h4>
                </div>
                <p class="text-[10px] sm:text-xs text-zinc-400 leading-relaxed line-clamp-2 sm:line-clamp-none">{{ t('about.missionText') }}</p>
              </div>
            </div>

            <div class="pt-2 sm:pt-4 flex flex-wrap items-center gap-3 sm:gap-4">
              <button
                @click="isQuoteModalOpen = true"
                class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-display font-semibold text-xs uppercase tracking-wider shadow-lg shadow-red-600/30 transition-all"
              >
                {{ t('about.inquireBtn') }}
              </button>

              <a
                href="https://wa.me/97126344403?text=Hello%20Veneno%20Auto%20Care,%20I%20would%20like%20to%20inquire%20about%20your%20services."
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700/80 hover:border-emerald-500/50 text-zinc-200 hover:text-white text-xs font-bold transition-all shadow-md"
              >
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#25D366] fill-current" viewBox="0 0 24 24">
                  <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"/>
                </svg>
                <span>{{ t('hero.whatsappBtn') }}</span>
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>
      <!-- DYNAMIC CONTINUOUS ANIMATED LUXURY MARQUEE TICKER -->
    <section class="relative py-4 sm:py-5 bg-zinc-950/95 border-y border-zinc-900 overflow-hidden select-none">
      <!-- Ambient Edge Glow Gradients -->
      <div class="pointer-events-none absolute inset-y-0 left-0 w-16 sm:w-32 bg-gradient-to-r from-zinc-950 to-transparent z-10"></div>
      <div class="pointer-events-none absolute inset-y-0 right-0 w-16 sm:w-32 bg-gradient-to-l from-zinc-950 to-transparent z-10"></div>

      <!-- Lane 1: Credentials, Certified Badges & Global Standards (Moving Left) -->
      <div class="animate-marquee flex items-center gap-4 py-1">
        <div v-for="i in 2" :key="'lane1-' + i" class="flex items-center gap-4 shrink-0">
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-red-500/40 text-white font-mono text-xs font-bold uppercase tracking-wider">
            <ShieldCheck class="w-3.5 h-3.5 text-red-500 shrink-0" />
            <span>{{ t('ticker.item1') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs font-bold uppercase tracking-wider">
            <Gem class="w-3.5 h-3.5 text-zinc-300 shrink-0" />
            <span>{{ t('ticker.item2') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs uppercase tracking-wider">
            <MapPin class="w-3.5 h-3.5 text-red-500 shrink-0" />
            <span>{{ t('ticker.item3') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-red-950/40 border border-red-500/40 text-red-300 font-mono text-xs font-bold uppercase tracking-wider">
            <Award class="w-3.5 h-3.5 text-red-400 shrink-0" />
            <span>{{ t('ticker.item4') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs uppercase tracking-wider">
            <Sparkles class="w-3.5 h-3.5 text-red-500 shrink-0" />
            <span>{{ t('ticker.item5') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs font-bold uppercase tracking-wider">
            <BadgeCheck class="w-3.5 h-3.5 text-zinc-300 shrink-0" />
            <span>{{ t('ticker.item6') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs uppercase tracking-wider">
            <Zap class="w-3.5 h-3.5 text-red-500 shrink-0" />
            <span>{{ t('ticker.item7') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs uppercase tracking-wider">
            <ShieldCheck class="w-3.5 h-3.5 text-white shrink-0" />
            <span>{{ t('ticker.item8') }}</span>
          </span>

          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-700/80 text-zinc-200 font-mono text-xs uppercase tracking-wider">
            <Users class="w-3.5 h-3.5 text-red-400 shrink-0" />
            <span>{{ t('ticker.item9') }}</span>
          </span>
        </div>
      </div>

      <!-- Lane 2: 9 Core Treatments & Precision Services (Moving Right) -->
      <div class="animate-marquee-reverse flex items-center gap-3 pt-2">
        <div v-for="j in 2" :key="'lane2-' + j" class="flex items-center gap-3 shrink-0">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
            <span>{{ t('ticker.strip1') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>
            <span>{{ t('ticker.strip2') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
            <span>{{ t('ticker.strip3') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
            <span>{{ t('ticker.strip4') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
            <span>{{ t('ticker.strip5') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-zinc-500"></span>
            <span>{{ t('ticker.strip6') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
            <span>{{ t('ticker.strip7') }}</span>
          </span>

          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900/60 border border-zinc-800 text-zinc-300 font-mono text-[11px]">
            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
            <span>{{ t('ticker.strip8') }}</span>
          </span>
        </div>
      </div>
    </section>
    <!-- =========================================================================
         2. ADIHEX 2026 OFFICIAL EXHIBITION SPOTLIGHT SECTION
         ========================================================================= -->
    <section class="relative py-10 sm:py-16 bg-gradient-to-b from-[#070709] via-[#0d0d12] to-[#070709] border-b border-zinc-800/80 overflow-hidden">
      <!-- Ambient Lighting Effects -->
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[350px] bg-gradient-to-b from-red-600/15 via-amber-500/10 to-transparent blur-3xl pointer-events-none -z-10"></div>
      <div class="absolute bottom-0 right-0 w-[500px] h-[300px] bg-amber-600/10 blur-3xl pointer-events-none -z-10"></div>
      <div class="absolute inset-0 bg-[radial-gradient(#27272a_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>

      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Main Spotlight Container Card with Glowing Gold Border -->
        <div class="relative rounded-3xl sm:rounded-[36px] bg-gradient-to-b from-zinc-900/90 via-zinc-950/95 to-black border-2 border-amber-500/30 hover:border-amber-500/50 p-6 sm:p-10 shadow-2xl shadow-black/90 transition-all duration-300 overflow-hidden">
          
          <!-- Subtle Top Gold Accent Line -->
          <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
            
            <!-- Left / Main Column: Dual Brand, Titles & Value Pillars (Span 7) -->
            <div class="lg:col-span-7 space-y-5 text-center lg:text-start rtl:lg:text-right">
              
              <!-- Badges Row -->
              <div class="flex flex-wrap items-center justify-center lg:justify-start rtl:lg:justify-start gap-2.5">
                <!-- Official Partner Tag -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/15 border border-amber-500/40 text-amber-300 font-mono text-xs font-bold uppercase tracking-wider shadow-sm">
                  <Sparkles class="w-3.5 h-3.5 text-amber-400 animate-pulse" />
                  <span>{{ t('adihexSpotlight.badge') }}</span>
                </div>

                <!-- Venue Tag -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-zinc-900 border border-zinc-700/80 text-zinc-300 font-mono text-xs">
                  <MapPin class="w-3.5 h-3.5 text-red-500" />
                  <span>{{ t('adihexSpotlight.venueBadge') }}</span>
                </div>
              </div>

              <!-- Dual Brand Logos Banner -->
              <div class="flex items-center justify-center lg:justify-start rtl:lg:justify-start gap-3 sm:gap-4 pt-1">
                <div class="flex items-center gap-2.5 sm:gap-3 p-2.5 rounded-2xl bg-zinc-950/80 border border-zinc-800 shadow-lg">
                  <img src="/images/adihex/veneno-header-icon.png" alt="Veneno Auto Care" class="h-9 w-9 sm:h-11 sm:w-11 object-contain rounded-xl" />
                  <div class="text-start rtl:text-right pr-2 rtl:pr-0 rtl:pl-2">
                    <div class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider font-display">VENENO</div>
                    <div class="text-[10px] text-zinc-400 font-mono">Auto Care Center</div>
                  </div>
                </div>

                <span class="text-amber-500 font-bold text-lg sm:text-xl">×</span>

                <div class="flex items-center gap-2.5 sm:gap-3 p-2.5 rounded-2xl bg-zinc-950/80 border border-zinc-800 shadow-lg">
                  <img src="/images/adihex/adihex-header-icon.png" alt="ADIHEX 2026" class="h-9 w-9 sm:h-11 sm:w-11 object-contain" />
                  <div class="text-start rtl:text-right pr-2 rtl:pr-0 rtl:pl-2">
                    <div class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider font-display">ADIHEX 2026</div>
                    <div class="text-[10px] text-zinc-400 font-mono">ADNEC Abu Dhabi</div>
                  </div>
                </div>
              </div>

              <!-- Headings -->
              <div class="space-y-2">
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-display font-bold text-white uppercase tracking-tight leading-[1.2]">
                  {{ t('adihexSpotlight.titleLine1') }} <br />
                  <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-amber-200 to-red-500">
                    {{ t('adihexSpotlight.titleLine2') }}
                  </span>
                </h2>
                <p class="text-xs sm:text-sm text-zinc-300 font-light leading-relaxed max-w-2xl">
                  {{ t('adihexSpotlight.subtitle') }}
                </p>
              </div>

              <!-- 4 Feature Highlights Grid -->
              <div class="grid grid-cols-2 gap-2.5 sm:gap-3.5 pt-1">
                <div class="p-3 sm:p-3.5 rounded-2xl bg-zinc-950/70 border border-zinc-800/80 flex items-start gap-2.5 text-start rtl:text-right">
                  <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0 mt-0.5">
                    <Gift class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400" />
                  </div>
                  <div>
                    <div class="text-xs sm:text-sm font-bold text-white">{{ t('adihexSpotlight.feature1Title') }}</div>
                    <div class="text-[10px] sm:text-xs text-zinc-400 font-light mt-0.5 leading-snug">{{ t('adihexSpotlight.feature1Desc') }}</div>
                  </div>
                </div>

                <div class="p-3 sm:p-3.5 rounded-2xl bg-zinc-950/70 border border-zinc-800/80 flex items-start gap-2.5 text-start rtl:text-right">
                  <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-red-500/10 border border-red-500/30 flex items-center justify-center shrink-0 mt-0.5">
                    <Flame class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-red-400" />
                  </div>
                  <div>
                    <div class="text-xs sm:text-sm font-bold text-white">{{ t('adihexSpotlight.feature2Title') }}</div>
                    <div class="text-[10px] sm:text-xs text-zinc-400 font-light mt-0.5 leading-snug">{{ t('adihexSpotlight.feature2Desc') }}</div>
                  </div>
                </div>

                <div class="p-3 sm:p-3.5 rounded-2xl bg-zinc-950/70 border border-zinc-800/80 flex items-start gap-2.5 text-start rtl:text-right">
                  <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0 mt-0.5">
                    <ShieldCheck class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400" />
                  </div>
                  <div>
                    <div class="text-xs sm:text-sm font-bold text-white">{{ t('adihexSpotlight.feature3Title') }}</div>
                    <div class="text-[10px] sm:text-xs text-zinc-400 font-light mt-0.5 leading-snug">{{ t('adihexSpotlight.feature3Desc') }}</div>
                  </div>
                </div>

                <div class="p-3 sm:p-3.5 rounded-2xl bg-zinc-950/70 border border-zinc-800/80 flex items-start gap-2.5 text-start rtl:text-right">
                  <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center shrink-0 mt-0.5">
                    <CheckCircle2 class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-400" />
                  </div>
                  <div>
                    <div class="text-xs sm:text-sm font-bold text-white">{{ t('adihexSpotlight.feature4Title') }}</div>
                    <div class="text-[10px] sm:text-xs text-zinc-400 font-light mt-0.5 leading-snug">{{ t('adihexSpotlight.feature4Desc') }}</div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Right Column: Interactive Spin Preview Card & Live Countdown (Span 5) -->
            <div class="lg:col-span-5 flex flex-col items-center justify-center gap-4">
              
              <!-- Countdown Display Box -->
              <div class="w-full p-4 sm:p-5 rounded-2xl bg-zinc-950/90 border border-amber-500/30 shadow-xl text-center space-y-2.5">
                <div class="flex items-center justify-center gap-2 text-xs font-mono uppercase tracking-wider text-amber-400 font-bold">
                  <Clock class="w-3.5 h-3.5" />
                  <span>{{ t('adihexSpotlight.countdownLabel') }}</span>
                </div>

                <div class="grid grid-cols-4 gap-2">
                  <div class="p-2 sm:p-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-center">
                    <div class="text-lg sm:text-2xl font-bold font-mono text-amber-300">{{ countdown.days }}</div>
                    <div class="text-[9px] text-zinc-500 font-mono uppercase">{{ t('adihexSpotlight.days') }}</div>
                  </div>
                  <div class="p-2 sm:p-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-center">
                    <div class="text-lg sm:text-2xl font-bold font-mono text-amber-300">{{ countdown.hours }}</div>
                    <div class="text-[9px] text-zinc-500 font-mono uppercase">{{ t('adihexSpotlight.hours') }}</div>
                  </div>
                  <div class="p-2 sm:p-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-center">
                    <div class="text-lg sm:text-2xl font-bold font-mono text-amber-300">{{ countdown.minutes }}</div>
                    <div class="text-[9px] text-zinc-500 font-mono uppercase">{{ t('adihexSpotlight.mins') }}</div>
                  </div>
                  <div class="p-2 sm:p-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-center">
                    <div class="text-lg sm:text-2xl font-bold font-mono text-amber-300">{{ countdown.seconds }}</div>
                    <div class="text-[9px] text-zinc-500 font-mono uppercase">{{ t('adihexSpotlight.secs') }}</div>
                  </div>
                </div>
              </div>

              <!-- Interactive Portal Action Card -->
              <div class="w-full p-4 sm:p-6 rounded-2xl bg-gradient-to-b from-zinc-900 via-zinc-950 to-black border border-zinc-800 text-center space-y-3.5 shadow-xl">
                <div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto flex items-center justify-center">
                  <!-- Animated Pulse Rings -->
                  <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-amber-500 to-red-600 opacity-25 animate-ping"></div>
                  <div class="relative w-full h-full rounded-full bg-gradient-to-tr from-amber-500 via-red-600 to-amber-400 p-1 shadow-lg shadow-amber-500/20">
                    <div class="w-full h-full rounded-full bg-zinc-950 flex flex-col items-center justify-center">
                      <Trophy class="w-7 h-7 sm:w-9 sm:h-9 text-amber-400 animate-bounce" />
                    </div>
                  </div>
                </div>

                <div class="space-y-1">
                  <div class="text-sm sm:text-base font-bold text-white">{{ t('adihexSpotlight.ctaSpin') }}</div>
                  <div class="text-[10px] sm:text-[11px] text-zinc-400">100% Guaranteed Prizes & Instant SMS Voucher</div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex flex-col gap-2 pt-1">
                  <Link
                    :href="currentLocale === 'ar' ? '/ar/adihex' : '/adihex'"
                    class="w-full py-3 sm:py-3.5 px-6 rounded-xl bg-gradient-to-r from-red-600 via-amber-500 to-red-600 hover:from-red-500 hover:to-amber-400 text-black font-bold text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-amber-500/20 hover:shadow-amber-500/40 transition-all duration-300 flex items-center justify-center gap-2 cursor-pointer active:scale-95"
                  >
                    <Sparkles class="w-4 h-4 text-black fill-current" />
                    <span>{{ t('adihexSpotlight.ctaSpin') }}</span>
                    <ArrowUpRight class="w-4 h-4 text-black rtl:rotate-90" />
                  </Link>

                  <Link
                    :href="currentLocale === 'ar' ? '/ar/adihex' : '/adihex'"
                    class="w-full py-2.5 px-4 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 hover:border-amber-500/50 text-zinc-300 hover:text-white text-xs font-semibold transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                  >
                    <span>{{ t('adihexSpotlight.ctaExplore') }}</span>
                    <ChevronRight class="w-3.5 h-3.5 rtl:rotate-180 text-zinc-400" />
                  </Link>
                </div>

              </div>

            </div>

          </div>

        </div>

      </div>
    </section>
    <!-- 3. WHY CHOOSE VENENO: 7 PILLARS (Swipeable on Mobile) -->
    <section id="why-us" class="py-12 sm:py-24 bg-zinc-950/80 relative border-t border-zinc-900 overflow-hidden">
      <!-- Background Watermark Typography for Depth -->
      <div class="absolute top-1/2 left-0 right-0 -translate-y-1/2 flex justify-center pointer-events-none opacity-25 select-none overflow-hidden">
        <span class="watermark-text font-display">7 PILLARS OF CARE</span>
      </div>
      <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-red-600/10 rounded-full blur-3xl pointer-events-none animate-glow-pulse"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-16">
          <span class="text-xs font-mono uppercase tracking-widest text-red-500 font-bold">{{ t('whyUs.tag') }}</span>
          <h2 class="text-2xl sm:text-4xl font-display font-semibold uppercase tracking-wider text-white mt-1">
            {{ t('whyUs.title') }}
          </h2>
          <p class="text-xs sm:text-sm text-zinc-400 mt-1 sm:mt-2">
            {{ t('whyUs.subtitle') }}
          </p>
        </div>

        <!-- Swipeable Horizontal Track on Mobile / 4-Col Grid on Desktop -->
        <div class="flex sm:grid overflow-x-auto snap-x no-scrollbar pb-3 gap-3.5 sm:grid-cols-2 lg:grid-cols-4 -mx-4 px-4 sm:mx-0 sm:px-0">

          <!-- Pillar 1: Expertise -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <Users class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">01</span>
            </div>
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p1Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p1Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/40 to-transparent rounded-full group-hover:from-red-500/70 transition-all"></div>
          </div>

          <!-- Pillar 2: Quality Service -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <Award class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">02</span>
            </div>
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p2Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p2Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/40 to-transparent rounded-full group-hover:from-red-500/70 transition-all"></div>
          </div>

          <!-- Pillar 3: Customer Satisfaction -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <HeartHandshake class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">03</span>
            </div>
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p3Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p3Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/40 to-transparent rounded-full group-hover:from-red-500/70 transition-all"></div>
          </div>

          <!-- Pillar 4: Attention to Detail -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <Sparkles class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">04</span>
            </div>
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p4Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p4Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/40 to-transparent rounded-full group-hover:from-red-500/70 transition-all"></div>
          </div>

          <!-- Pillar 5: Best Prices -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <Zap class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">05</span>
            </div>
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p5Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p5Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/40 to-transparent rounded-full group-hover:from-red-500/70 transition-all"></div>
          </div>

          <!-- Pillar 6: Trusted Warranty -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <ShieldCheck class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">06</span>
            </div>
            <div>
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p6Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p6Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/40 to-transparent rounded-full group-hover:from-red-500/70 transition-all"></div>
          </div>

          <!-- Pillar 7: After Sales Support -->
          <div class="w-[260px] sm:w-auto shrink-0 snap-start lg:col-span-2 group glass-panel p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800/80 hover:border-red-500/40 hover:bg-zinc-900/60 transition-all duration-300 flex flex-col gap-2.5 sm:gap-4 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-600/10 rounded-full blur-3xl pointer-events-none group-hover:bg-red-600/20 transition-colors"></div>
            <div class="flex items-center gap-3 relative">
              <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-red-600/15 border border-red-500/20 flex items-center justify-center shrink-0 group-hover:bg-red-600/25 transition-colors">
                <BadgeCheck class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <span class="text-[10px] font-mono text-red-500 font-bold uppercase tracking-widest">07</span>
              <span class="ml-auto px-2.5 py-0.5 rounded-full bg-red-600/20 border border-red-500/30 text-red-400 text-[9px] font-mono uppercase tracking-wide">Flagship</span>
            </div>
            <div class="relative">
              <h3 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wide">{{ t('whyUs.p7Title') }}</h3>
              <p class="text-[11px] sm:text-xs text-zinc-400 mt-1 sm:mt-2 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ t('whyUs.p7Desc') }}</p>
            </div>
            <div class="mt-auto h-px bg-gradient-to-r from-red-600/60 via-red-500/30 to-transparent rounded-full group-hover:from-red-500 transition-all"></div>
          </div>

        </div>
      </div>
    </section>

    <!-- 4. OFFICIAL BRAND PARTNERS (3M & GYEON) & ISO CERTIFICATES (2x2 on Mobile) -->
    <section id="certificates" class="py-10 sm:py-20 bg-zinc-950 relative border-t border-zinc-900 overflow-hidden">
      <!-- Background Watermark -->
      <div class="absolute top-1/2 left-0 right-0 -translate-y-1/2 flex justify-center pointer-events-none opacity-20 select-none overflow-hidden">
        <span class="watermark-text font-display">GLOBAL CERTIFIED</span>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-12">
          <span class="text-xs font-mono uppercase tracking-widest text-red-500 font-bold">{{ t('brands.tag') }}</span>
          <h2 class="text-xl sm:text-3xl font-display font-semibold uppercase tracking-wider text-white mt-1">
            {{ t('brands.title') }}
          </h2>
          <p class="text-xs sm:text-sm text-zinc-400 mt-1 sm:mt-2">
            {{ t('brands.subtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
          <!-- 3M Official SVG Logo Card -->
          <div class="glass-panel p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 text-center flex flex-col items-center justify-between space-y-2 sm:space-y-4">
            <div class="h-10 sm:h-16 flex items-center justify-center">
              <svg class="h-8 sm:h-12 w-auto text-red-600 fill-current" role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <title>3M</title>
                <path fill="currentColor" d="M18.903 5.954L17.17 13.03l-1.739-7.076h-5.099v2.613C9.72 6.28 7.56 5.706 5.558 5.674 3.12 5.641.563 6.701.469 9.936h3.373c0-.977.747-1.536 1.588-1.523 1.032-.008 1.508.434 1.533 1.124-.036.597-.387 1.014-1.525 1.014H4.303V12.9h1.03c.584 0 1.399.319 1.431 1.155.04.995-.652 1.435-1.501 1.443-1.517-.053-1.763-1.225-1.763-2.23H0c.015.677-.151 5.091 5.337 5.059 2.629.025 4.464-1.085 5.003-2.613v2.342h3.455v-7.632l1.867 7.634h3.018l1.875-7.626v7.634H24V5.954h-5.097zm-8.561 7.06c-.429-.893-1.034-1.284-1.376-1.407.714-.319 1.09-.751 1.376-1.614v3.021z"/>
              </svg>
            </div>
            <div>
              <h4 class="text-[11px] sm:text-xs font-bold text-white uppercase tracking-wider">3M Pro Shop Dealer</h4>
              <p class="text-[9px] sm:text-[11px] text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-none">{{ t('brands.dealer3M') }}</p>
            </div>
            <span class="px-2.5 py-0.5 sm:py-1 rounded-full bg-red-600/20 text-red-400 border border-red-500/30 text-[9px] sm:text-[10px] font-mono">3M Authorized</span>
          </div>

          <!-- GYEON Official SVG Logo Card -->
          <div class="glass-panel p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 text-center flex flex-col items-center justify-between space-y-2 sm:space-y-4">
            <div class="h-10 sm:h-16 flex items-center justify-center px-2">
              <svg class="h-5 sm:h-7 w-auto text-white fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 135.377 45.757" aria-label="Gyeon Logo Sticky" role="img">
                <path fill="currentColor" d="M79.865,44.962a10.235,10.235,0,0,1-3.63-2.153,11.023,11.023,0,0,1-2.36-3.328,16.684,16.684,0,0,1-1.332-4.2,27.788,27.788,0,0,1-.4-4.869v-14.8Q72.145,0,84.871,0,97.625,0,97.624,15.613V30.195a28.607,28.607,0,0,1-.387,4.882,16.874,16.874,0,0,1-1.318,4.246,11.5,11.5,0,0,1-2.372,3.412,10.064,10.064,0,0,1-3.642,2.215,16.118,16.118,0,0,1-10.041.013Zm3-37.486a3.448,3.448,0,0,0-1.209,1.639,10.453,10.453,0,0,0-.546,2.055,14.464,14.464,0,0,0-.157,2.214V32.422q0,6.483,3.919,6.483,3.944,0,3.945-6.483V13.385q0-6.508-3.945-6.51A3.312,3.312,0,0,0,82.864,7.476ZM6.836,44.889a9.063,9.063,0,0,1-3.17-2.3,13.055,13.055,0,0,1-2.093-3.363,17.538,17.538,0,0,1-1.21-3.953A23.343,23.343,0,0,1,0,31.125V15.612Q0,0,12.848,0a14.948,14.948,0,0,1,4.828.721,10.194,10.194,0,0,1,3.544,1.994,10.208,10.208,0,0,1,2.347,3.132A15.6,15.6,0,0,1,24.9,9.885a26.037,26.037,0,0,1,.411,4.8v3.2H16.889V14.192q0-1.148-.059-2.019a12.758,12.758,0,0,0-.3-1.932,5.974,5.974,0,0,0-.654-1.749A3.374,3.374,0,0,0,14.688,7.34a3.51,3.51,0,0,0-1.84-.465q-4.04,0-4.04,6.51V32.4q0,6.485,4.04,6.484,4.017,0,4.018-6.729v-2.3H14.373l-2.291-6.386H25.31V44.9H19.624l-1.113-3.083a8.177,8.177,0,0,1-3.169,2.852,8.96,8.96,0,0,1-4.235,1.089A10.259,10.259,0,0,1,6.836,44.889ZM52.3,44.9V17.583L56.954.856H70.043L68.251,7.29H61.108V18.964h6.406l-1.742,6.263H61.108v13.24h8.967l-1.8,6.434Zm-17.765,0V30.341L26.355.856h8.3l4.066,19.355q.75-3.18,2.25-9.677T43.2.856h8.058l-8.2,29.486V44.9Zm84.818,0-9.532-24.444V44.9h-7.576V.832h6.34l7.187,17.334,10.962,26.713V44.9Zm-1.209-34.737V.832h8.591V31.1Zm12.1-7.022a2.568,2.568,0,1,1,5.136,0,2.568,2.568,0,1,1-5.136,0Zm.4,0a2.168,2.168,0,1,0,4.336,0,2.168,2.168,0,1,0-4.336,0Zm2.975,1.444a.172.172,0,0,1-.134-.094l-.56-1.121h-.6c-.027,0-.042.013-.042.028V4.464a.112.112,0,0,1-.12.121h-.2a.107.107,0,0,1-.12-.121V1.792a.107.107,0,0,1,.12-.121h1.053a.815.815,0,0,1,.908.85.762.762,0,0,1-.56.8l.56,1.133c.054.095,0,.136-.079.136Zm-1.335-2.5v.85c0,.027.015.027.042.027H133c.361,0,.507-.161.507-.444s-.146-.46-.507-.46h-.679C132.3,2.061,132.282,2.076,132.282,2.088Z"></path>
              </svg>
            </div>
            <div>
              <h4 class="text-[11px] sm:text-xs font-bold text-white uppercase tracking-wider">GYEON Certified</h4>
              <p class="text-[9px] sm:text-[11px] text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-none">{{ t('brands.gyeon') }}</p>
            </div>
            <span class="px-2.5 py-0.5 sm:py-1 rounded-full bg-zinc-800 text-zinc-300 border border-zinc-700 text-[9px] sm:text-[10px] font-mono">Nano Ceramic</span>
          </div>

          <!-- ISO 9001:2015 Card -->
          <div class="glass-panel p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 text-center flex flex-col items-center justify-between space-y-2 sm:space-y-4 hover:border-red-500/40 transition-all group">
            <div class="h-10 sm:h-16 flex items-center justify-center">
              <img
                src="/images/certifications/iso-9001.svg"
                alt="ISO 9001:2015 Quality Management Certified"
                class="h-9 sm:h-14 w-auto object-contain rounded-lg bg-white p-1 shadow-md group-hover:scale-105 transition-transform"
              />
            </div>
            <div>
              <h4 class="text-[11px] sm:text-xs font-bold text-white uppercase tracking-wider">ISO 9001:2015</h4>
              <p class="text-[9px] sm:text-[11px] text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-none">{{ t('brands.iso9001') }}</p>
            </div>
            <span class="px-2.5 py-0.5 sm:py-1 rounded-full bg-zinc-900 text-zinc-300 border border-zinc-800 text-[9px] sm:text-[10px] font-mono">Quality Mgmt</span>
          </div>

          <!-- ISO 45001:2018 Card -->
          <div class="glass-panel p-3.5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 text-center flex flex-col items-center justify-between space-y-2 sm:space-y-4 hover:border-red-500/40 transition-all group">
            <div class="h-10 sm:h-16 flex items-center justify-center">
              <img
                src="/images/certifications/iso-45001.png"
                alt="ISO 45001:2018 Health & Safety Certified"
                class="h-9 sm:h-14 w-auto object-contain rounded-lg bg-white p-1 shadow-md group-hover:scale-105 transition-transform"
              />
            </div>
            <div>
              <h4 class="text-[11px] sm:text-xs font-bold text-white uppercase tracking-wider">ISO 45001:2018</h4>
              <p class="text-[9px] sm:text-[11px] text-zinc-400 mt-0.5 sm:mt-1 line-clamp-1 sm:line-clamp-none">{{ t('brands.iso45001') }}</p>
            </div>
            <span class="px-2.5 py-0.5 sm:py-1 rounded-full bg-zinc-900 text-zinc-300 border border-zinc-800 text-[9px] sm:text-[10px] font-mono">Health & Safety</span>
          </div>
        </div>
      </div>
    </section>

    <!-- DYNAMIC IMPACT HIGHLIGHTS STRIP (Luxury Obsidian & Brand Crimson Accents) -->
    <div class="relative py-6 bg-zinc-950/80 border-t border-zinc-900 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
          <div class="glass-panel p-3.5 sm:p-4 rounded-2xl border border-zinc-800/80 hover:border-red-500/50 transition-all flex items-center gap-3 group bg-zinc-900/40 hover:bg-zinc-900/80">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-red-600/15 border border-red-500/30 text-red-500 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-red-600/25 transition-all">
              <ShieldCheck class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" />
            </div>
            <div>
              <div class="text-xs sm:text-sm font-bold text-white font-mono uppercase tracking-wide">10-Year 3M PPF</div>
              <div class="text-[10px] sm:text-[11px] text-zinc-400 font-mono">{{ currentLocale === 'ar' ? 'ضمان رسمي معتمد' : 'Authorized Warranty' }}</div>
            </div>
          </div>

          <div class="glass-panel p-3.5 sm:p-4 rounded-2xl border border-zinc-800/80 hover:border-red-500/50 transition-all flex items-center gap-3 group bg-zinc-900/40 hover:bg-zinc-900/80">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-red-600/15 border border-red-500/30 text-red-500 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-red-600/25 transition-all">
              <Car class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" />
            </div>
            <div>
              <div class="text-xs sm:text-sm font-bold text-white font-mono uppercase tracking-wide">5,000+ Supercars</div>
              <div class="text-[10px] sm:text-[11px] text-zinc-400 font-mono">{{ currentLocale === 'ar' ? 'ثقة أصحاب السيارات الفارهة' : 'Protected in UAE' }}</div>
            </div>
          </div>

          <div class="glass-panel p-3.5 sm:p-4 rounded-2xl border border-zinc-800/80 hover:border-red-500/50 transition-all flex items-center gap-3 group bg-zinc-900/40 hover:bg-zinc-900/80">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-red-600/15 border border-red-500/30 text-red-500 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-red-600/25 transition-all">
              <Gem class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" />
            </div>
            <div>
              <div class="text-xs sm:text-sm font-bold text-white font-mono uppercase tracking-wide">9H+ GYEON Quartz</div>
              <div class="text-[10px] sm:text-[11px] text-zinc-400 font-mono">{{ currentLocale === 'ar' ? 'سيراميك كيميائي نانو' : 'Ceramic Diamond' }}</div>
            </div>
          </div>

          <div class="glass-panel p-3.5 sm:p-4 rounded-2xl border border-zinc-800/80 hover:border-red-500/50 transition-all flex items-center gap-3 group bg-zinc-900/40 hover:bg-zinc-900/80">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-red-600/15 border border-red-500/30 text-red-500 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-red-600/25 transition-all">
              <MapPin class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" />
            </div>
            <div>
              <div class="text-xs sm:text-sm font-bold text-white font-mono uppercase tracking-wide">2 Abu Dhabi Hubs</div>
              <div class="text-[10px] sm:text-[11px] text-zinc-400 font-mono">{{ currentLocale === 'ar' ? 'مصفح & القناة' : 'Musaffah & Al Qana' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 5. BEFORE / AFTER INTERACTIVE SHOWCASE LAB -->
    <section id="lab" class="py-10 sm:py-20 bg-zinc-950 relative border-t border-zinc-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-6 sm:mb-12">
          <span class="text-xs font-mono uppercase tracking-widest text-red-500 font-bold">{{ t('lab.tag') }}</span>
          <h2 class="text-xl sm:text-3xl font-display font-semibold uppercase tracking-wider text-white mt-1">
            {{ t('lab.title') }}
          </h2>
          <p class="text-xs sm:text-sm text-zinc-400 mt-1 sm:mt-2">
            {{ t('lab.subtitle') }}
          </p>
        </div>

        <BeforeAfterSlider />
      </div>
    </section>

    <!-- 6. OFFICIAL 9 CORE SERVICES GRID (High-Conversion Cards) -->
    <section id="services" class="py-12 sm:py-24 bg-zinc-950 relative border-t border-zinc-900 overflow-hidden">
      <!-- Background Watermark -->
      <div class="absolute top-1/4 left-0 right-0 -translate-y-1/2 flex justify-center pointer-events-none opacity-20 select-none overflow-hidden">
        <span class="watermark-text font-display">MASTER CRAFTSMANSHIP</span>
      </div>
      <div class="absolute -top-32 -left-32 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none animate-glow-pulse"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-16 gap-4 sm:gap-6">
          <div>
            <span class="text-xs font-mono uppercase tracking-widest text-red-500 font-bold">{{ t('services.tag') }}</span>
            <h2 class="text-2xl sm:text-4xl font-display font-semibold uppercase tracking-wider text-white mt-1 sm:mt-2">
              {{ t('services.title') }}
            </h2>
            <p class="text-xs sm:text-sm text-zinc-400 mt-1 sm:mt-2 max-w-xl">
              {{ t('services.subtitle') }}
            </p>
          </div>

          <button
            @click="isQuoteModalOpen = true"
            class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl bg-red-600/20 text-red-400 hover:text-white border border-red-500/40 text-xs font-mono uppercase tracking-wider transition-all self-start md:self-auto"
          >
            <span>{{ t('services.catalogQuote') }}</span>
            <ChevronRight class="w-4 h-4 rtl:rotate-180" />
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-8">
          <div
            v-for="service in activeServices"
            :key="service.id"
            class="group relative glass-panel rounded-2xl sm:rounded-3xl overflow-hidden border border-zinc-800/80 hover:border-red-500/40 transition-all duration-300 hover:shadow-2xl hover:shadow-red-950/20 flex flex-col"
          >
            <!-- Service Image Banner (Clickable Link) -->
            <Link
              :href="getServiceUrl(service.slug)"
              class="block relative h-36 sm:h-52 overflow-hidden bg-zinc-900 cursor-pointer"
            >
              <img
                :src="service.image"
                :alt="service.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent"></div>
              
              <!-- Floating Price Badge On Top of Image on Mobile -->
              <div v-if="service.starting_price" class="absolute bottom-2.5 left-2.5 rtl:left-auto rtl:right-2.5 z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-zinc-950/90 backdrop-blur-md border border-red-500/40 text-red-400 font-mono font-bold text-xs shadow-lg">
                  <span class="text-[9px] uppercase text-zinc-400">{{ t('services.startingFrom') }}</span>
                  <span class="text-white">{{ service.starting_price }}</span>
                </span>
              </div>
            </Link>

            <!-- Content -->
            <div class="p-4 sm:p-6 flex-1 flex flex-col justify-between space-y-3 sm:space-y-4 bg-zinc-950/90">
              <div>
                <div class="flex items-center justify-between gap-2 mb-1.5 sm:mb-2">
                  <span class="text-[9px] sm:text-[10px] font-mono text-red-400 font-bold uppercase">{{ service.badge }}</span>
                  <span class="text-[9px] sm:text-[10px] text-zinc-400 font-mono">{{ service.warranty }}</span>
                </div>

                <!-- Service Title (Clickable Link) -->
                <Link :href="getServiceUrl(service.slug)" class="block group/title">
                  <h3 class="text-base sm:text-xl font-display font-semibold uppercase tracking-wide text-white group-hover/title:text-red-400 transition-colors">
                    {{ service.name }}
                  </h3>
                </Link>

                <p class="text-[11px] sm:text-xs text-zinc-400 mt-1.5 sm:mt-2 line-clamp-2 leading-relaxed">
                  {{ service.description }}
                </p>
              </div>

              <!-- Features checklist (2 on Mobile, 3 on Desktop) -->
              <div class="space-y-1 sm:space-y-1.5 pt-2 border-t border-zinc-800/80 text-xs">
                <div
                  v-for="(feat, idx) in (service.features || []).slice(0, 3)"
                  :key="idx"
                  class="flex items-center gap-2 text-zinc-300 text-[11px] sm:text-xs"
                  :class="{ 'hidden sm:flex': idx === 2 }"
                >
                  <CheckCircle2 class="w-3 sm:w-3.5 h-3 sm:h-3.5 text-red-500 shrink-0" />
                  <span class="line-clamp-1">{{ feat }}</span>
                </div>
              </div>

              <!-- Action CTAs -->
              <div class="pt-2.5 sm:pt-3 border-t border-zinc-800/80 flex items-center justify-between gap-2">
                <Link
                  :href="getServiceUrl(service.slug)"
                  class="text-[11px] sm:text-xs font-mono uppercase text-zinc-400 hover:text-white flex items-center gap-1 transition-colors py-1 group/btn"
                >
                  <span class="group-hover/btn:underline">{{ t('services.explore') }}</span>
                  <ArrowUpRight class="w-3.5 h-3.5 rtl:rotate-90 group-hover/btn:translate-x-0.5 group-hover/btn:-translate-y-0.5 transition-transform" />
                </Link>

                <button
                  @click="openQuoteWithService(service.name)"
                  class="px-3.5 sm:px-4 py-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 active:scale-95 text-white font-display font-semibold text-xs uppercase tracking-wider shadow-lg shadow-red-600/30 transition-all flex items-center gap-1.5 cursor-pointer"
                >
                  <Sparkles class="w-3.5 h-3.5" />
                  <span>{{ t('services.getQuote') }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 7. OUR WORKS: PHOTO & VIDEO GALLERY -->
    <WorksGallery @open-quote="isQuoteModalOpen = true" />

    <!-- 8. GET A QUOTE SECTION -->
    <QuoteSection />

    <!-- 9. CONTACT US & 2 ABU DHABI BRANCHES -->
    <section id="contact" class="py-12 sm:py-24 bg-zinc-950/90 relative border-t border-zinc-900 overflow-hidden">
      <!-- Background Watermark -->
      <div class="absolute bottom-6 left-0 right-0 flex justify-center pointer-events-none opacity-20 select-none overflow-hidden">
        <span class="watermark-text font-display">ABU DHABI • UAE</span>
      </div>
      <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none animate-glow-pulse"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-16">
          <span class="text-xs font-mono uppercase tracking-widest text-red-500 font-bold">{{ t('branches.tag') }}</span>
          <h2 class="text-2xl sm:text-4xl font-display font-semibold uppercase tracking-wider text-white mt-1">
            {{ t('branches.title') }}
          </h2>
          <p class="text-xs sm:text-sm text-zinc-400 mt-1 sm:mt-2">
            {{ t('branches.subtitle') }}
          </p>
        </div>

        <!-- 3 Contact Cards (Fully Clickable) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
          <!-- Card 1: Direct Landline & Hotline -->
          <a
            href="tel:+97126344403"
            class="group glass-panel p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 hover:border-red-500/50 hover:bg-zinc-900/90 flex flex-col items-center text-center cursor-pointer transition-all duration-300 shadow-xl hover:shadow-red-950/30 hover:-translate-y-1"
          >
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-red-600/20 text-red-400 border border-red-500/30 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 group-hover:bg-red-600/30 transition-all">
              <Phone class="w-6 h-6 sm:w-7 sm:h-7 text-red-400" />
            </div>
            <h4 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider group-hover:text-red-400 transition-colors">{{ t('branches.landlineTitle') }}</h4>
            <p class="text-[11px] sm:text-xs text-zinc-400 mt-1">{{ t('branches.landlineDesc') }}</p>
            <span dir="ltr" class="text-sm sm:text-base font-mono font-bold text-red-400 group-hover:underline mt-3 inline-block" style="direction: ltr !important; unicode-bidi: isolate;">
              +971 2 634 4403
            </span>
          </a>

          <!-- Card 2: Official WhatsApp Concierge -->
          <a
            href="https://wa.me/97126344403?text=Hello%20Veneno%20Auto%20Care,%20I%20would%20like%20to%20inquire%20about%20your%20services."
            target="_blank"
            rel="noopener noreferrer"
            class="group glass-panel p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 hover:border-emerald-500/50 hover:bg-zinc-900/90 flex flex-col items-center text-center cursor-pointer transition-all duration-300 shadow-xl hover:shadow-emerald-950/30 hover:-translate-y-1"
          >
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-zinc-900 border border-zinc-800 group-hover:border-emerald-500/40 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-all">
              <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-current text-[#25D366]" viewBox="0 0 24 24">
                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"/>
              </svg>
            </div>
            <h4 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider group-hover:text-emerald-400 transition-colors">{{ t('branches.whatsappTitle') }}</h4>
            <p class="text-[11px] sm:text-xs text-zinc-400 mt-1">{{ t('branches.whatsappDesc') }}</p>
            <span class="px-4 py-2 rounded-xl bg-zinc-900 group-hover:bg-emerald-600 border border-zinc-700/80 group-hover:border-emerald-500 text-zinc-200 group-hover:text-white font-bold text-xs mt-3 flex items-center gap-1.5 transition-all shadow-md">
              {{ t('hero.whatsappBtn') }}
            </span>
          </a>

          <!-- Card 3: Corporate Inquiries & Email -->
          <a
            href="mailto:info@veneno.ae"
            class="group glass-panel p-5 sm:p-6 rounded-2xl sm:rounded-3xl border border-zinc-800 hover:border-red-500/50 hover:bg-zinc-900/90 flex flex-col items-center text-center cursor-pointer transition-all duration-300 shadow-xl hover:shadow-red-950/30 hover:-translate-y-1"
          >
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-red-600/15 text-red-500 border border-red-500/30 flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 group-hover:bg-red-600/25 transition-all">
              <Mail class="w-6 h-6 sm:w-7 sm:h-7 text-red-500" />
            </div>
            <h4 class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider group-hover:text-red-400 transition-colors">{{ t('branches.emailTitle') }}</h4>
            <p class="text-[11px] sm:text-xs text-zinc-400 mt-1">{{ t('branches.emailDesc') }}</p>
            <span class="text-sm sm:text-base font-mono font-bold text-red-400 group-hover:underline mt-3">
              info@veneno.ae
            </span>
          </a>
        </div>

        <!-- 2 Branch Showcase Cards & Maps -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
          
          <!-- BRANCH 1: MUSAFFAH (MAIN BRANCH) -->
          <div class="glass-panel rounded-2xl sm:rounded-3xl border border-zinc-800/90 overflow-hidden shadow-2xl flex flex-col justify-between">
            <div class="p-5 sm:p-8 space-y-3 sm:space-y-4">
              <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full bg-red-600/20 text-red-400 border border-red-500/40 text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider">
                  {{ t('branches.mainBranchBadge') }}
                </span>
                <span class="text-[11px] sm:text-xs text-zinc-400 font-mono flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5 text-zinc-500" /> {{ t('branches.hours') }}
                </span>
              </div>

              <div>
                <h3 class="text-lg sm:text-2xl font-display font-semibold uppercase tracking-wide text-white">
                  {{ t('branches.musaffahTitle') }}
                </h3>
                <p class="text-xs sm:text-sm text-zinc-400 mt-1 flex items-start gap-2">
                  <MapPin class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                  <span>{{ t('branches.musaffahAddress') }}</span>
                </p>
              </div>

              <div class="pt-1 sm:pt-2 flex items-center gap-3">
                <a
                  href="https://www.google.com/maps/place/VENENO+AUTO+CARE+CENTER/data=!4m2!3m1!1s0x0:0x1caa0a70d352aff4"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-display text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-md"
                >
                  <span>{{ t('branches.openMaps') }}</span>
                  <ArrowUpRight class="w-3.5 h-3.5 rtl:rotate-90" />
                </a>
                <a
                  href="tel:+97126344403"
                  dir="ltr"
                  class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs font-semibold font-mono flex items-center gap-1.5"
                  style="direction: ltr !important;"
                >
                  <Phone class="w-3.5 h-3.5 text-red-500 shrink-0" />
                  <span>{{ currentLocale === 'ar' ? 'اتصل:' : 'Call:' }}</span>
                  <span dir="ltr" class="font-mono inline-block" style="direction: ltr !important; unicode-bidi: isolate;">02 634 4403</span>
                </a>
              </div>
            </div>

            <!-- Musaffah Map Embed -->
            <div class="h-48 sm:h-72 border-t border-zinc-800 relative bg-zinc-900">
              <iframe
                class="w-full h-full grayscale contrast-125 opacity-80"
                src="https://maps.google.com/maps?q=VENENO+AUTO+CARE+CENTER+Musaffah+Abu+Dhabi&t=&z=15&ie=UTF8&iwloc=&output=embed"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Veneno Auto Care Center Musaffah Main Branch Map"
              ></iframe>
            </div>
          </div>

          <!-- BRANCH 2: AL QANA (BRANCH) -->
          <div class="glass-panel rounded-2xl sm:rounded-3xl border border-zinc-800/90 overflow-hidden shadow-2xl flex flex-col justify-between">
            <div class="p-5 sm:p-8 space-y-3 sm:space-y-4">
              <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full bg-zinc-900 text-zinc-300 border border-zinc-700 text-[10px] sm:text-[11px] font-mono font-bold uppercase tracking-wider">
                  {{ t('branches.alQanaBadge') }}
                </span>
                <span class="text-[11px] sm:text-xs text-zinc-400 font-mono flex items-center gap-1">
                  <Clock class="w-3.5 h-3.5 text-zinc-500" /> {{ t('branches.hours') }}
                </span>
              </div>

              <div>
                <h3 class="text-lg sm:text-2xl font-display font-semibold uppercase tracking-wide text-white">
                  {{ t('branches.alQanaTitle') }}
                </h3>
                <p class="text-xs sm:text-sm text-zinc-400 mt-1 flex items-start gap-2">
                  <MapPin class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                  <span>{{ t('branches.alQanaAddress') }}</span>
                </p>
              </div>

              <div class="pt-1 sm:pt-2 flex items-center gap-3">
                <a
                  href="https://www.google.com/maps/place/VENENO+AUTO+CARE+CENTER+-+AL+QANA/@24.4036113,54.4927375,17z/data=!3m1!4b1!4m6!3m5!1s0x3e5e4147b5e1008f:0xc96a4752ec9c10c6!8m2!3d24.4036064!4d54.4953124"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-display text-xs uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-md"
                >
                  <span>{{ t('branches.openMaps') }}</span>
                  <ArrowUpRight class="w-3.5 h-3.5 rtl:rotate-90" />
                </a>
                <a
                  href="tel:+97126344403"
                  dir="ltr"
                  class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 text-zinc-200 text-xs font-semibold font-mono flex items-center gap-1.5"
                  style="direction: ltr !important;"
                >
                  <Phone class="w-3.5 h-3.5 text-red-500 shrink-0" />
                  <span>{{ currentLocale === 'ar' ? 'اتصل:' : 'Call:' }}</span>
                  <span dir="ltr" class="font-mono inline-block" style="direction: ltr !important; unicode-bidi: isolate;">02 634 4403</span>
                </a>
              </div>
            </div>

            <!-- Al Qana Map Embed -->
            <div class="h-48 sm:h-72 border-t border-zinc-800 relative bg-zinc-900">
              <iframe
                class="w-full h-full grayscale contrast-125 opacity-80"
                src="https://maps.google.com/maps?q=VENENO+AUTO+CARE+CENTER+AL+QANA+Abu+Dhabi&t=&z=16&ie=UTF8&iwloc=&output=embed"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Veneno Auto Care Center Al Qana Branch Map"
              ></iframe>
            </div>
          </div>

        </div>

      </div>
    </section>

    <!-- Footer -->
    <Footer :services="activeServices" @open-quote="isQuoteModalOpen = true" />

    <!-- Quote Modal -->
    <QuoteModal
      :is-open="isQuoteModalOpen"
      :preselected-service="selectedServiceName"
      @close="isQuoteModalOpen = false"
    />

    <!-- Floating WhatsApp Widget -->
    <WhatsAppWidget />
  </div>
</template>
