<script setup>
import { ref } from 'vue';
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
  ArrowLeft,
  ArrowUpRight,
  Share2,
  CheckCircle2,
  Tag,
  Newspaper,
  ChevronRight,
  Phone,
  MessageCircle,
  Eye,
  ExternalLink
} from 'lucide-vue-next';
import { useI18n } from '@/i18n';

const props = defineProps({
  locale: {
    type: String,
    default: 'en',
  },
  item: {
    type: Object,
    required: true,
  },
  related: {
    type: Array,
    default: () => [],
  },
});

const { t, currentLocale } = useI18n();
const copySuccess = ref(false);

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString(currentLocale.value === 'ar' ? 'ar-AE' : 'en-GB', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const copyArticleLink = () => {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(window.location.href);
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
      <title>{{ (currentLocale === 'ar' ? (item.title_ar || item.title) : item.title) + ' — Veneno Auto Care' }}</title>
      <meta name="description" :content="currentLocale === 'ar' ? (item.summary_ar || item.summary) : item.summary" />
      <meta property="og:title" :content="currentLocale === 'ar' ? (item.title_ar || item.title) : item.title" />
      <meta property="og:description" :content="currentLocale === 'ar' ? (item.summary_ar || item.summary) : item.summary" />
      <meta property="og:image" :content="item.image_url" />
    </Head>

    <Navbar />

    <main class="relative overflow-hidden pt-10 pb-20 sm:pt-14 sm:pb-28">
      <!-- Ambient Lighting -->
      <div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-red-600/15 via-amber-500/10 to-transparent blur-3xl"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Breadcrumbs & Back -->
        <div class="flex items-center justify-between gap-4 mb-8">
          <Link
            href="/news-events"
            class="inline-flex items-center gap-2 text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 hover:text-white transition-colors"
          >
            <ArrowLeft class="w-4 h-4 rtl:rotate-180" />
            <span>{{ currentLocale === 'ar' ? 'العودة للأخبار والفعاليات' : 'Back to News & Events' }}</span>
          </Link>

          <button
            @click="copyArticleLink"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-xs font-mono text-zinc-300 hover:text-amber-400 transition-colors cursor-pointer"
          >
            <Share2 class="w-3.5 h-3.5" />
            <span>{{ currentLocale === 'ar' ? 'مشاركة' : 'Share' }}</span>
          </button>
        </div>

        <!-- Main Layout: 2 Columns (Article Body + Sidebar) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
          
          <!-- Article Left Column (Span 8) -->
          <article class="lg:col-span-8 space-y-8">
            
            <!-- Badges -->
            <div class="flex flex-wrap items-center gap-2.5">
              <span
                class="px-3.5 py-1.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider border shadow-md flex items-center gap-1.5"
                :class="item.type === 'event' ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' : 'bg-blue-500/20 border-blue-500/50 text-blue-300'"
              >
                <Flame v-if="item.type === 'event'" class="w-3.5 h-3.5" />
                <Newspaper v-else class="w-3.5 h-3.5" />
                <span>{{ item.type === 'event' ? (currentLocale === 'ar' ? 'فعالية رسمية' : 'Official Event') : (currentLocale === 'ar' ? 'بيان صحفي' : 'Official Press') }}</span>
              </span>

              <span v-if="item.badge" class="px-3 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-mono">
                {{ currentLocale === 'ar' ? (item.badge_ar || item.badge) : item.badge }}
              </span>

              <span v-if="item.category" class="px-3 py-1.5 rounded-full bg-zinc-900/80 border border-zinc-800 text-zinc-400 text-xs font-mono flex items-center gap-1">
                <Tag class="w-3 h-3 text-amber-400" />
                <span>{{ item.category }}</span>
              </span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl sm:text-5xl font-black uppercase text-white font-display tracking-tight leading-[1.15]">
              {{ currentLocale === 'ar' ? (item.title_ar || item.title) : item.title }}
            </h1>

            <!-- Summary Lead -->
            <p class="text-base sm:text-lg text-zinc-300 leading-relaxed font-light border-l-2 rtl:border-l-0 rtl:border-r-2 border-red-500 pl-4 rtl:pl-0 rtl:pr-4 py-1">
              {{ currentLocale === 'ar' ? (item.summary_ar || item.summary) : item.summary }}
            </p>

            <!-- Featured Image Visual -->
            <div class="relative overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-950 shadow-2xl">
              <img
                :src="item.image_url"
                :alt="item.title"
                class="w-full max-h-[500px] object-cover"
              />
              <div v-if="item.prize_podium" class="absolute bottom-4 left-4 right-4 p-4 rounded-2xl bg-black/85 backdrop-blur-md border border-amber-500/40 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <Trophy class="w-5 h-5 text-amber-400" />
                  <span class="text-sm font-bold text-white font-mono">{{ item.prize_podium }}</span>
                </div>
                <span class="px-2.5 py-1 rounded bg-amber-500/20 text-amber-300 text-xs font-mono font-bold uppercase">PODIUM</span>
              </div>
            </div>

            <!-- Full Content Story -->
            <div class="prose prose-invert max-w-none text-zinc-300 leading-relaxed space-y-6 text-sm sm:text-base">
              <p class="whitespace-pre-line leading-relaxed font-light">
                {{ currentLocale === 'ar' ? (item.content_ar || item.content) : item.content }}
              </p>
            </div>

            <!-- Gallery Images if any -->
            <div v-if="item.gallery_images && item.gallery_images.length > 1" class="pt-6 border-t border-zinc-800 space-y-4">
              <h3 class="text-sm font-mono font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                <span>{{ currentLocale === 'ar' ? 'معرض صور الفعالية' : 'Event Photo Gallery' }}</span>
                <span class="text-xs text-zinc-500">({{ item.gallery_images.length }} photos)</span>
              </h3>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div
                  v-for="(img, idx) in item.gallery_images"
                  :key="idx"
                  class="relative h-36 sm:h-44 rounded-2xl overflow-hidden border border-zinc-800 bg-zinc-900 group cursor-pointer"
                >
                  <img :src="img" :alt="`${item.title} photo ${idx + 1}`" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                </div>
              </div>
            </div>

          </article>

          <!-- Sidebar Right Column (Span 4) -->
          <aside class="lg:col-span-4 space-y-6">
            
            <!-- Event / Article Facts Card -->
            <div class="rounded-3xl bg-zinc-950/90 border border-zinc-800 p-6 space-y-5 shadow-2xl">
              <h3 class="text-xs font-mono font-bold uppercase tracking-widest text-amber-400 border-b border-zinc-800 pb-3 flex items-center gap-2">
                <Sparkles class="w-4 h-4" />
                <span>{{ currentLocale === 'ar' ? 'بيانات الفعالية والتقرير' : 'Event & Story Specifications' }}</span>
              </h3>

              <div class="space-y-4 text-xs">
                
                <!-- Date -->
                <div v-if="item.event_date" class="flex items-start gap-3">
                  <div class="p-2 rounded-xl bg-zinc-900 border border-zinc-800 text-amber-400 shrink-0">
                    <Calendar class="w-4 h-4" />
                  </div>
                  <div>
                    <span class="text-[10px] font-mono text-zinc-500 uppercase block">{{ currentLocale === 'ar' ? 'التاريخ' : 'Date' }}</span>
                    <strong class="text-zinc-200 font-mono">{{ formatDate(item.event_date) }}</strong>
                  </div>
                </div>

                <!-- Timing -->
                <div v-if="item.event_time" class="flex items-start gap-3">
                  <div class="p-2 rounded-xl bg-zinc-900 border border-zinc-800 text-amber-400 shrink-0">
                    <Clock class="w-4 h-4" />
                  </div>
                  <div>
                    <span class="text-[10px] font-mono text-zinc-500 uppercase block">{{ currentLocale === 'ar' ? 'التوقيت' : 'Time' }}</span>
                    <strong class="text-zinc-200 font-mono">{{ item.event_time }}</strong>
                  </div>
                </div>

                <!-- Location -->
                <div v-if="item.location" class="flex items-start gap-3">
                  <div class="p-2 rounded-xl bg-zinc-900 border border-zinc-800 text-red-500 shrink-0">
                    <MapPin class="w-4 h-4" />
                  </div>
                  <div>
                    <span class="text-[10px] font-mono text-zinc-500 uppercase block">{{ currentLocale === 'ar' ? 'الموقع' : 'Location' }}</span>
                    <strong class="text-zinc-200">{{ currentLocale === 'ar' ? (item.location_ar || item.location) : item.location }}</strong>
                  </div>
                </div>

                <!-- Prize Podium -->
                <div v-if="item.prize_podium" class="flex items-start gap-3">
                  <div class="p-2 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 shrink-0">
                    <Trophy class="w-4 h-4" />
                  </div>
                  <div>
                    <span class="text-[10px] font-mono text-amber-300 uppercase block">{{ currentLocale === 'ar' ? 'الجوائز' : 'Prize / Stakes' }}</span>
                    <strong class="text-white">{{ item.prize_podium }}</strong>
                  </div>
                </div>

              </div>

              <!-- Workshop Google Maps Link -->
              <a
                href="https://maps.google.com/maps?q=VENENO+AUTO+CARE+CENTER+Musaffah+Abu+Dhabi"
                target="_blank"
                rel="noopener noreferrer"
                class="w-full py-2.5 px-3.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 hover:text-white text-xs font-mono flex items-center justify-between transition-colors cursor-pointer"
              >
                <span>{{ currentLocale === 'ar' ? 'فتح موقع المركز على الخريطة' : 'Open Venue in Google Maps' }}</span>
                <ExternalLink class="w-3.5 h-3.5 text-red-400 rtl:rotate-90" />
              </a>
            </div>

            <!-- Concierge Inquiry Card -->
            <div class="rounded-3xl bg-gradient-to-b from-zinc-900 to-black border border-zinc-800 p-6 space-y-4 shadow-xl">
              <h3 class="text-sm font-bold text-white uppercase font-display flex items-center gap-2">
                <MessageCircle class="w-4 h-4 text-emerald-400" />
                <span>{{ currentLocale === 'ar' ? 'استفسر عن الفعاليات والخدمات' : 'Direct VIP Concierge' }}</span>
              </h3>
              <p class="text-xs text-zinc-400 leading-relaxed font-light">
                {{ currentLocale === 'ar'
                  ? 'تواصل مباشرة مع إدارة فينينو لحجز باقات الرعاية أو الاستفسار عن بطولات وتحديات السيارات القادمة.'
                  : 'Contact our management desk directly to inquire about upcoming competitions, exhibition passes, or booking bespoke vehicle care.'
                }}
              </p>
              
              <div class="space-y-2 pt-2">
                <a
                  :href="`https://wa.me/97126344403?text=${encodeURIComponent(`Hello Veneno Auto Care, I am inquiring regarding the story: ${item.title}`)}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-mono text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-emerald-950 transition-all"
                >
                  <MessageCircle class="w-4 h-4" />
                  <span>{{ currentLocale === 'ar' ? 'محادثة واتساب مباشرة' : 'WhatsApp Concierge' }}</span>
                </a>

                <a
                  href="tel:+97126344403"
                  class="w-full py-3 px-4 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-200 hover:text-white border border-zinc-800 font-mono text-xs font-semibold flex items-center justify-center gap-2 transition-colors"
                >
                  <Phone class="w-4 h-4 text-red-400" />
                  <span>02 634 4403</span>
                </a>
              </div>
            </div>

          </aside>

        </div>

        <!-- Related Stories / Other Events -->
        <section v-if="related && related.length > 0" class="mt-16 pt-12 border-t border-zinc-800">
          <h2 class="text-xl font-bold uppercase text-white font-display mb-6">
            {{ currentLocale === 'ar' ? 'أخبار وفعاليات ذات صلة' : 'Related Stories & Highlights' }}
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Link
              v-for="rel in related"
              :key="rel.id"
              :href="`/news-events/${rel.slug}`"
              class="group block p-4 rounded-2xl bg-zinc-950/80 border border-zinc-800 hover:border-amber-500/50 transition-all"
            >
              <div class="h-40 rounded-xl overflow-hidden bg-zinc-900 mb-3">
                <img :src="rel.image_url" :alt="rel.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              </div>
              <span class="text-[10px] font-mono text-amber-400 uppercase font-bold">{{ rel.category }}</span>
              <h4 class="text-sm font-bold text-white group-hover:text-amber-300 transition-colors line-clamp-2 mt-1">
                {{ currentLocale === 'ar' ? (rel.title_ar || rel.title) : rel.title }}
              </h4>
            </Link>
          </div>
        </section>

      </div>
    </main>

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

    <Footer />
    <WhatsAppWidget />
  </div>
</template>
