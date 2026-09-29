<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import WhatsAppWidget from '@/Components/WhatsAppWidget.vue';
import {
  Calendar,
  MapPin,
  Clock,
  Trophy,
  Flame,
  Sparkles,
  ArrowUpRight,
  Search,
  Tag,
  Share2,
  ChevronRight,
  CheckCircle2,
  Star,
  Eye,
  X,
  ExternalLink,
  Newspaper,
  Award,
  Layers
} from 'lucide-vue-next';
import { useI18n } from '@/i18n';

const props = defineProps({
  locale: {
    type: String,
    default: 'en',
  },
  items: {
    type: Array,
    default: () => [],
  },
  featured: {
    type: Object,
    default: null,
  },
});

const { t, currentLocale } = useI18n();

const activeFilter = ref('all'); // 'all' | 'event' | 'news' | 'past'
const searchQuery = ref('');
const selectedItemModal = ref(null);
const copySuccess = ref(false);

const filteredItems = computed(() => {
  let list = props.items || [];

  if (activeFilter.value === 'event') {
    list = list.filter(i => i.type === 'event');
  } else if (activeFilter.value === 'news') {
    list = list.filter(i => i.type === 'news');
  } else if (activeFilter.value === 'past') {
    list = list.filter(i => i.is_past || (i.event_date && new Date(i.event_date) < new Date()));
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(i => {
      const title = (i.title || '').toLowerCase();
      const titleAr = (i.title_ar || '').toLowerCase();
      const summary = (i.summary || '').toLowerCase();
      const summaryAr = (i.summary_ar || '').toLowerCase();
      const location = (i.location || '').toLowerCase();
      const cat = (i.category || '').toLowerCase();
      return title.includes(q) || titleAr.includes(q) || summary.includes(q) || summaryAr.includes(q) || location.includes(q) || cat.includes(q);
    });
  }

  return list;
});

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString(currentLocale.value === 'ar' ? 'ar-AE' : 'en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};

const openModal = (item) => {
  selectedItemModal.value = item;
};

const closeModal = () => {
  selectedItemModal.value = null;
};

const shareItem = (item) => {
  const url = `${window.location.origin}/news-events/${item.slug}`;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url);
    copySuccess.value = true;
    setTimeout(() => {
      copySuccess.value = false;
    }, 2500);
  }
};
</script>

<template>
  <div class="min-h-screen bg-[#070709] text-zinc-100 font-sans selection:bg-red-600 selection:text-white">
    <Head>
      <title>{{ currentLocale === 'ar' ? 'الأخبار والفعاليات — مركز فينينو للعناية بالسيارات بأبوظبي' : 'News & Events — Veneno Auto Care Center Abu Dhabi' }}</title>
      <meta name="description" :content="currentLocale === 'ar' ? 'سجل فعاليات وتحديات وأخبار مركز فينينو للعناية بالسيارات في أبوظبي بما في ذلك تحدي المطرقة النهائي ومعرض أديهيكس وأحدث ابتكارات الحماية.' : 'Explore official highlights from our premier automotive gatherings, competitive challenges, international exhibitions, and elite auto care product innovations in Abu Dhabi, UAE.'" />
      <meta property="og:title" :content="currentLocale === 'ar' ? 'الأخبار والفعاليات — مركز فينينو للعناية بالسيارات' : 'News & Events — Veneno Auto Care Center'" />
      <meta property="og:image" content="/images/hammer/Hammer1.jpeg" />
    </Head>

    <!-- Global Navigation -->
    <Navbar />

    <main class="relative overflow-hidden">
      <!-- Ambient Lighting Blurs -->
      <div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 w-[850px] h-[450px] bg-gradient-to-b from-red-600/15 via-amber-500/10 to-transparent blur-3xl"></div>
      <div class="pointer-events-none absolute top-96 -left-40 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl"></div>

      <!-- HERO HEADER -->
      <section class="relative border-b border-zinc-800/80 pt-12 pb-14 sm:pt-16 sm:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <!-- Breadcrumb -->
          <div class="flex items-center gap-2 text-xs font-mono text-zinc-400 mb-6">
            <Link href="/" class="hover:text-white transition-colors">{{ t('nav.home') }}</Link>
            <ChevronRight class="w-3.5 h-3.5 text-zinc-600 rtl:rotate-180" />
            <span class="text-amber-400 font-bold">{{ currentLocale === 'ar' ? 'الأخبار والفعاليات' : 'News & Events' }}</span>
          </div>

          <!-- Hero Content -->
          <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-red-600/20 via-amber-500/15 to-transparent border border-amber-500/30 text-amber-300 text-xs font-mono font-bold uppercase tracking-wider mb-4">
              <Sparkles class="w-3.5 h-3.5 text-amber-400" />
              <span>{{ currentLocale === 'ar' ? 'سجل فعاليات وتحديات فينينو الرسمية' : 'Official Veneno Chronicles & Activations' }}</span>
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight text-white font-display leading-[1.1]">
              {{ currentLocale === 'ar' ? 'الأخبار' : 'News &' }}
              <span class="bg-gradient-to-r from-amber-400 via-amber-200 to-red-500 bg-clip-text text-transparent">
                {{ currentLocale === 'ar' ? 'والفعاليات' : 'Events' }}
              </span>
            </h1>

            <p class="mt-4 text-sm sm:text-base text-zinc-400 font-light leading-relaxed max-w-2xl">
              {{ currentLocale === 'ar'
                ? 'استكشف ملخص الفعاليات الرياضية وتحديات القوة، والمعارض الدولية في أبوظبي، وأحدث تدشينات أفلام حماية الطلاء 3M وتقنيات النانو سيراميك الفاخرة.'
                : 'Explore official highlights from our premier automotive gatherings, competitive strength challenges, ADNEC exhibitions, and elite protection product releases in Abu Dhabi, UAE.'
              }}
            </p>
          </div>

          <!-- Filter & Search Bar -->
          <div class="mt-10 flex flex-col md:flex-row md:items-center justify-between gap-4 p-2 rounded-2xl bg-zinc-950/80 border border-zinc-800 shadow-2xl backdrop-blur-xl">
            <!-- Filter Pills -->
            <div class="flex flex-wrap items-center gap-1.5 p-1">
              <button
                @click="activeFilter = 'all'"
                class="px-4 py-2 rounded-xl text-xs font-mono font-bold uppercase tracking-wider transition-all cursor-pointer"
                :class="activeFilter === 'all' ? 'bg-white text-zinc-950 shadow-md font-black' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'"
              >
                {{ currentLocale === 'ar' ? 'الكل' : 'All' }} ({{ items.length }})
              </button>

              <button
                @click="activeFilter = 'event'"
                class="px-4 py-2 rounded-xl text-xs font-mono font-bold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5"
                :class="activeFilter === 'event' ? 'bg-amber-500 text-zinc-950 shadow-md font-black' : 'text-zinc-400 hover:text-amber-300 hover:bg-zinc-900'"
              >
                <Flame class="w-3.5 h-3.5" />
                <span>{{ currentLocale === 'ar' ? 'الفعاليات والتحديات' : 'Events & Challenges' }}</span>
              </button>

              <button
                @click="activeFilter = 'news'"
                class="px-4 py-2 rounded-xl text-xs font-mono font-bold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5"
                :class="activeFilter === 'news' ? 'bg-blue-600 text-white shadow-md font-black' : 'text-zinc-400 hover:text-blue-300 hover:bg-zinc-900'"
              >
                <Newspaper class="w-3.5 h-3.5" />
                <span>{{ currentLocale === 'ar' ? 'الأخبار والبيانات' : 'News & Releases' }}</span>
              </button>

              <button
                @click="activeFilter = 'past'"
                class="px-4 py-2 rounded-xl text-xs font-mono font-bold uppercase tracking-wider transition-all cursor-pointer flex items-center gap-1.5"
                :class="activeFilter === 'past' ? 'bg-red-600 text-white shadow-md font-black' : 'text-zinc-400 hover:text-red-300 hover:bg-zinc-900'"
              >
                <Award class="w-3.5 h-3.5" />
                <span>{{ currentLocale === 'ar' ? 'أرشيف الفعاليات السابقة' : 'Past Events' }}</span>
              </button>
            </div>

            <!-- Search Field -->
            <div class="relative w-full md:w-72">
              <Search class="w-4 h-4 text-zinc-500 absolute left-3.5 top-1/2 -translate-y-1/2 rtl:left-auto rtl:right-3.5" />
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="currentLocale === 'ar' ? 'بحث في الفعاليات والأخبار...' : 'Search news & events...'"
                class="w-full pl-10 pr-4 rtl:pl-4 rtl:pr-10 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400 transition-colors"
              />
            </div>
          </div>
        </div>
      </section>

      <!-- FEATURED SPOTLIGHT: PREVIOUS EVENT / HAMMER CHALLENGE -->
      <section v-if="featured && activeFilter !== 'news'" class="py-10 sm:py-14 border-b border-zinc-800/80 bg-gradient-to-b from-zinc-950 via-[#0d0d12] to-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="relative overflow-hidden rounded-3xl border-2 border-amber-500/40 bg-gradient-to-b from-zinc-900/90 via-zinc-950/95 to-black p-6 sm:p-10 shadow-2xl shadow-black/90 group">
            
            <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>
            
            <div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">
              
              <!-- Left Info -->
              <div class="space-y-6 lg:col-span-7">
                <div class="flex flex-wrap items-center gap-2.5">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-600/20 border border-red-500/40 text-red-300 text-xs font-mono font-bold uppercase tracking-wider">
                    <Flame class="w-3.5 h-3.5 text-red-500" />
                    <span>{{ currentLocale === 'ar' ? (featured.badge_ar || 'فعالية مكتملة') : (featured.badge || 'Concluded Event') }}</span>
                  </span>

                  <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-400 text-xs font-mono">
                    <Tag class="w-3 h-3 text-amber-400" />
                    <span>{{ featured.category }}</span>
                  </span>

                  <span v-if="featured.event_date" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-zinc-900 border border-zinc-800 text-amber-300 text-xs font-mono">
                    <Calendar class="w-3.5 h-3.5 text-amber-400" />
                    <span>{{ formatDate(featured.event_date) }}</span>
                  </span>
                </div>

                <div>
                  <h2 class="font-display text-2xl sm:text-4xl lg:text-5xl font-black uppercase text-white leading-tight">
                    {{ currentLocale === 'ar' ? (featured.title_ar || featured.title) : featured.title }}
                  </h2>
                  <p class="mt-3 text-sm text-zinc-400 leading-relaxed font-light">
                    {{ currentLocale === 'ar' ? (featured.summary_ar || featured.summary) : featured.summary }}
                  </p>
                </div>

                <!-- Event Stats / Highlight Pills -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                  <div class="p-3.5 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-start gap-3">
                    <MapPin class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                    <div>
                      <span class="text-[10px] font-mono text-zinc-500 uppercase block">{{ currentLocale === 'ar' ? 'الموقع' : 'Venue Location' }}</span>
                      <strong class="text-xs text-zinc-200">{{ currentLocale === 'ar' ? (featured.location_ar || featured.location) : featured.location }}</strong>
                    </div>
                  </div>

                  <div v-if="featured.prize_podium" class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-3">
                    <Trophy class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" />
                    <div>
                      <span class="text-[10px] font-mono text-amber-300 uppercase block">{{ currentLocale === 'ar' ? 'الجائزة الكبرى' : 'Prize Podium' }}</span>
                      <strong class="text-xs text-white">{{ featured.prize_podium }}</strong>
                    </div>
                  </div>
                </div>

                <!-- CTAs -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                  <Link
                    :href="`/news-events/${featured.slug}`"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 hover:from-red-500 hover:to-amber-500 text-white font-mono text-xs font-black uppercase tracking-wider shadow-lg shadow-red-950/50 transition-all transform hover:-translate-y-0.5"
                  >
                    <span>{{ currentLocale === 'ar' ? 'قراءة التقرير الكامل' : 'Read Full Story' }}</span>
                    <ArrowUpRight class="w-4 h-4 rtl:rotate-90" />
                  </Link>

                  <button
                    @click="openModal(featured)"
                    class="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700/80 text-zinc-200 hover:text-white font-mono text-xs font-semibold transition-colors cursor-pointer"
                  >
                    {{ currentLocale === 'ar' ? 'نظرة سريعة 🔍' : 'Quick Preview 🔍' }}
                  </button>

                  <button
                    @click="shareItem(featured)"
                    class="p-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-zinc-400 hover:text-amber-400 transition-colors cursor-pointer"
                    title="Share Link"
                  >
                    <Share2 class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Right Media: Hammer Artwork / Event Visual -->
              <div class="lg:col-span-5">
                <div class="relative overflow-hidden rounded-2xl border border-amber-500/35 bg-zinc-950 p-3 shadow-2xl group/image">
                  <div class="relative overflow-hidden rounded-xl h-64 sm:h-80 w-full bg-zinc-900 flex items-center justify-center">
                    <img
                      :src="featured.image_url"
                      :alt="featured.title"
                      class="h-full w-full object-cover transition-transform duration-700 group-hover/image:scale-105"
                      loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    
                    <div v-if="featured.prize_podium" class="absolute bottom-3 left-3 right-3 p-3 rounded-xl bg-black/85 backdrop-blur-md border border-amber-500/30 flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <Trophy class="w-4 h-4 text-amber-400" />
                        <span class="text-xs font-bold text-white font-mono">{{ featured.prize_podium.split('+')[0] }}</span>
                      </div>
                      <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[10px] font-mono font-bold uppercase">1ST PRIZE</span>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>

      <!-- CARDS SHOWCASE GRID -->
      <section class="py-12 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <div class="flex items-center justify-between mb-8 pb-4 border-b border-zinc-800/80">
            <div>
              <h2 class="text-xl sm:text-2xl font-black uppercase text-white font-display flex items-center gap-2">
                <span>{{ currentLocale === 'ar' ? 'سجل الأخبار والفعاليات' : 'Chronicles & News Feed' }}</span>
                <span class="text-xs font-mono text-zinc-500">({{ filteredItems.length }})</span>
              </h2>
              <p class="text-xs text-zinc-400 mt-1 font-light">
                {{ currentLocale === 'ar' ? 'استعرض كافة الأحداث والتحديثات الصادرة عن مركز فينينو' : 'Browse through all activities, activations and updates from Veneno Auto Care' }}
              </p>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="filteredItems.length === 0" class="py-20 text-center rounded-3xl bg-zinc-950/60 border border-zinc-800/60">
            <Newspaper class="w-10 h-10 text-zinc-600 mx-auto mb-3" />
            <p class="text-base font-bold text-zinc-300">{{ currentLocale === 'ar' ? 'لم يتم العثور على نتائج' : 'No news or events found' }}</p>
            <p class="text-xs text-zinc-500 mt-1">{{ currentLocale === 'ar' ? 'يرجى تجربة فلتر آخر أو كلمة بحث مختلفة.' : 'Try adjusting your search query or switching active filter.' }}</p>
            <button
              @click="activeFilter = 'all'; searchQuery = ''"
              class="mt-4 px-4 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-xs font-mono text-zinc-300 hover:text-white transition-colors"
            >
              {{ currentLocale === 'ar' ? 'إعادة ضبط الفلاتر' : 'Reset Filters' }}
            </button>
          </div>

          <!-- Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <article
              v-for="item in filteredItems"
              :key="item.id"
              class="group flex flex-col overflow-hidden rounded-3xl bg-zinc-950/80 border border-zinc-800/90 hover:border-amber-500/50 transition-all duration-300 shadow-xl shadow-black/80 hover:shadow-amber-500/10 hover:-translate-y-1"
            >
              <!-- Card Image Header -->
              <div class="relative h-56 sm:h-60 w-full overflow-hidden bg-zinc-900">
                <img
                  :src="item.image_url"
                  :alt="item.title"
                  class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent"></div>

                <!-- Floating Top Badges -->
                <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between pointer-events-none">
                  <span
                    class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider backdrop-blur-md border shadow-md flex items-center gap-1.5"
                    :class="item.type === 'event' ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' : 'bg-blue-500/20 border-blue-500/50 text-blue-300'"
                  >
                    <Flame v-if="item.type === 'event'" class="w-3 h-3" />
                    <Newspaper v-else class="w-3 h-3" />
                    <span>{{ item.type === 'event' ? (currentLocale === 'ar' ? 'فعالية' : 'Event') : (currentLocale === 'ar' ? 'خبر' : 'News') }}</span>
                  </span>

                  <span v-if="item.badge" class="px-2.5 py-0.5 rounded-full bg-black/80 text-zinc-300 border border-zinc-700 text-[10px] font-mono">
                    {{ currentLocale === 'ar' ? (item.badge_ar || item.badge) : item.badge }}
                  </span>
                </div>

                <!-- Date / Location Bar at bottom of image -->
                <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-[11px] font-mono text-zinc-300">
                  <span v-if="item.event_date" class="flex items-center gap-1.5 bg-black/70 backdrop-blur-md px-2.5 py-1 rounded-lg border border-zinc-800">
                    <Calendar class="w-3 h-3 text-amber-400" />
                    <span>{{ formatDate(item.event_date) }}</span>
                  </span>
                  <span v-if="item.views_count" class="flex items-center gap-1 bg-black/70 backdrop-blur-md px-2 py-1 rounded-lg border border-zinc-800 text-zinc-400 text-[10px]">
                    <Eye class="w-3 h-3 text-zinc-500" />
                    <span>{{ item.views_count }}</span>
                  </span>
                </div>
              </div>

              <!-- Card Body -->
              <div class="flex flex-1 flex-col p-6 space-y-4">
                
                <div class="text-[11px] font-mono uppercase tracking-wider text-amber-400 flex items-center gap-1.5 font-bold">
                  <Tag class="w-3 h-3" />
                  <span>{{ item.category }}</span>
                </div>

                <h3 class="font-display text-lg sm:text-xl font-bold uppercase text-white group-hover:text-amber-300 transition-colors line-clamp-2 leading-snug">
                  {{ currentLocale === 'ar' ? (item.title_ar || item.title) : item.title }}
                </h3>

                <p class="text-xs text-zinc-400 font-light leading-relaxed line-clamp-3">
                  {{ currentLocale === 'ar' ? (item.summary_ar || item.summary) : item.summary }}
                </p>

                <!-- Venue / Podium Meta if present -->
                <div v-if="item.location || item.prize_podium" class="pt-2 border-t border-zinc-800/80 space-y-1.5 text-xs font-mono">
                  <div v-if="item.location" class="flex items-center gap-1.5 text-zinc-400 truncate">
                    <MapPin class="w-3.5 h-3.5 text-red-500 shrink-0" />
                    <span class="truncate">{{ currentLocale === 'ar' ? (item.location_ar || item.location) : item.location }}</span>
                  </div>

                  <div v-if="item.prize_podium" class="flex items-center gap-1.5 text-amber-300 font-bold truncate">
                    <Trophy class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                    <span class="truncate">{{ item.prize_podium }}</span>
                  </div>
                </div>

                <!-- Footer Card Actions -->
                <div class="mt-auto pt-4 border-t border-zinc-800 flex items-center justify-between">
                  <Link
                    :href="`/news-events/${item.slug}`"
                    class="inline-flex items-center gap-1.5 text-xs font-mono font-bold uppercase tracking-wider text-amber-400 hover:text-white transition-colors group-hover:translate-x-1 rtl:group-hover:-translate-x-1"
                  >
                    <span>{{ currentLocale === 'ar' ? 'عرض التفاصيل' : 'Read Article' }}</span>
                    <ArrowUpRight class="w-3.5 h-3.5 rtl:rotate-90" />
                  </Link>

                  <div class="flex items-center gap-2">
                    <button
                      @click="openModal(item)"
                      class="p-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white transition-colors cursor-pointer"
                      title="Quick Preview"
                    >
                      <Eye class="w-3.5 h-3.5" />
                    </button>
                    <button
                      @click="shareItem(item)"
                      class="p-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-amber-400 transition-colors cursor-pointer"
                      title="Share Article Link"
                    >
                      <Share2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>

              </div>
            </article>
          </div>

        </div>
      </section>
    </main>

    <!-- QUICK PREVIEW MODAL -->
    <div
      v-if="selectedItemModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200"
      @click.self="closeModal"
    >
      <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-zinc-950 border border-zinc-800 p-6 sm:p-8 shadow-2xl text-left rtl:text-right">
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-5 right-5 rtl:right-auto rtl:left-5 p-2 rounded-full bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white transition-colors cursor-pointer"
        >
          <X class="w-4 h-4" />
        </button>

        <div class="space-y-4">
          <div class="flex flex-wrap items-center gap-2">
            <span
              class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border"
              :class="selectedItemModal.type === 'event' ? 'bg-amber-500/20 border-amber-500/40 text-amber-300' : 'bg-blue-500/20 border-blue-500/40 text-blue-300'"
            >
              {{ selectedItemModal.type === 'event' ? 'Event' : 'News' }}
            </span>
            <span v-if="selectedItemModal.category" class="px-2.5 py-0.5 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-400 text-[10px] font-mono">
              {{ selectedItemModal.category }}
            </span>
            <span v-if="selectedItemModal.event_date" class="text-xs font-mono text-zinc-400">
              {{ formatDate(selectedItemModal.event_date) }}
            </span>
          </div>

          <h2 class="text-xl sm:text-2xl font-black uppercase text-white font-display">
            {{ currentLocale === 'ar' ? (selectedItemModal.title_ar || selectedItemModal.title) : selectedItemModal.title }}
          </h2>

          <div class="relative h-64 sm:h-72 w-full rounded-2xl overflow-hidden border border-zinc-800">
            <img :src="selectedItemModal.image_url" :alt="selectedItemModal.title" class="w-full h-full object-cover" />
          </div>

          <div v-if="selectedItemModal.location || selectedItemModal.prize_podium" class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div v-if="selectedItemModal.location" class="flex items-center gap-2 text-zinc-300 font-mono">
              <MapPin class="w-4 h-4 text-red-500 shrink-0" />
              <span>{{ currentLocale === 'ar' ? (selectedItemModal.location_ar || selectedItemModal.location) : selectedItemModal.location }}</span>
            </div>
            <div v-if="selectedItemModal.prize_podium" class="flex items-center gap-2 text-amber-300 font-mono font-bold">
              <Trophy class="w-4 h-4 text-amber-400 shrink-0" />
              <span>{{ selectedItemModal.prize_podium }}</span>
            </div>
          </div>

          <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed font-light">
            {{ currentLocale === 'ar' ? (selectedItemModal.content_ar || selectedItemModal.content) : selectedItemModal.content }}
          </p>

          <div class="pt-4 border-t border-zinc-800 flex items-center justify-between gap-3">
            <Link
              :href="`/news-events/${selectedItemModal.slug}`"
              class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-mono text-xs font-bold uppercase tracking-wider flex items-center gap-1.5"
            >
              <span>{{ currentLocale === 'ar' ? 'فتح الصفحة المخصصة' : 'Open Full Article' }}</span>
              <ArrowUpRight class="w-3.5 h-3.5 rtl:rotate-90" />
            </Link>

            <button
              @click="closeModal"
              class="px-4 py-2.5 rounded-xl bg-zinc-900 text-zinc-400 hover:text-white font-mono text-xs cursor-pointer"
            >
              {{ currentLocale === 'ar' ? 'إغلاق' : 'Close' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Copied Toast -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div v-if="copySuccess" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-xl bg-emerald-950 border border-emerald-500 text-emerald-200 text-xs font-mono flex items-center gap-2 shadow-2xl">
        <CheckCircle2 class="w-4 h-4 text-emerald-400" />
        <span>{{ currentLocale === 'ar' ? 'تم نسخ الرابط بنجاح!' : 'Article link copied to clipboard!' }}</span>
      </div>
    </Transition>

    <!-- Global Footer -->
    <Footer />

    <!-- WhatsApp Floating Concierge -->
    <WhatsAppWidget />
  </div>
</template>
