<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import HammerLanguageSwitcher from '@/Components/HammerLanguageSwitcher.vue';
import { 
  CheckCircle2, 
  AlertCircle, 
  MapPin, 
  Clock, 
  Ticket, 
  Calendar, 
  ArrowRight, 
  ArrowLeft,
  Share2, 
  Sparkles,
  ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
  initialLocale: { type: String, default: 'ar' },
});

const { currentLocale, setLocale, isRTL } = useI18n();
const registration = ref(null);
const errorMessage = ref('');
const isLoading = ref(true);

onMounted(async () => {
  if (props.initialLocale && ['en', 'ar'].includes(props.initialLocale)) {
    setLocale(props.initialLocale);
  }

  const token = new URLSearchParams(window.location.search).get('token');
  if (!token) {
    errorMessage.value = isRTL.value ? 'رمز تأكيد التذكرة غير موجود.' : 'Ticket confirmation token is missing.';
    isLoading.value = false;
    return;
  }

  try {
    const response = await window.axios.get(`/api/hammer-challenge/audience/confirmation/${encodeURIComponent(token)}`);
    registration.value = response.data.registration;
  } catch (error) {
    errorMessage.value = error.response?.data?.message || (isRTL.value ? 'تعذر العثور على تذكرة الحضور.' : 'Audience confirmation pass could not be found.');
  } finally {
    isLoading.value = false;
  }
});

const openGoogleMaps = () => {
  window.open('https://maps.google.com/?q=Veneno+Auto+Care+Center+Musaffah+M37+Abu+Dhabi', '_blank');
};
</script>

<template>
  <Head :title="isRTL ? 'تذكرتك الرسمية • تحدي مطرقة فينينو' : 'Your Official Pass • Veneno Hammer Challenge'" />

  <div 
    :dir="isRTL ? 'rtl' : 'ltr'"
    class="relative min-h-screen overflow-x-hidden bg-[#070709] text-zinc-100 font-sans px-4 py-6 flex flex-col justify-between"
  >
    <!-- Magma & Volcanic Red Ambience Glows -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.28),transparent_50%),radial-gradient(circle_at_85%_95%,rgba(163,230,53,0.10),transparent_40%)]"></div>

    <!-- Header Navigation -->
    <header class="relative z-50 w-full max-w-2xl mx-auto flex items-center justify-between pb-4 gap-3">
      <Link href="/" class="flex items-center gap-3 group">
        <div class="w-10 h-10 rounded-xl overflow-hidden border border-red-500/50 bg-red-600 flex items-center justify-center p-1.5 shadow-lg shadow-red-950/50 group-hover:scale-105 transition">
          <img 
            src="/images/veneno-emblem.png" 
            alt="Veneno Auto Care" 
            class="w-full h-full object-contain rounded-lg"
          />
        </div>
        <div>
          <span class="text-xs font-black tracking-widest text-white uppercase font-display block">
            {{ isRTL ? 'مركز فينينو للعناية بالسيارات' : 'VENENO AUTO CARE' }}
          </span>
          <span class="block text-[10px] text-zinc-400 font-mono">
            {{ isRTL ? 'مصفح M37، أبوظبي' : 'Musaffah M37, Abu Dhabi' }}
          </span>
        </div>
      </Link>

      <!-- Main Website Style Dropdown Language Switcher -->
      <HammerLanguageSwitcher />
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 max-w-xl w-full mx-auto flex items-center justify-center py-4">
      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-12 text-zinc-400 text-sm">
        <div class="w-8 h-8 mx-auto mb-3 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
        {{ isRTL ? 'جاري تحميل التذكرة...' : 'Loading Audience Ticket...' }}
      </div>

      <!-- Error State -->
      <div v-else-if="errorMessage" class="w-full rounded-3xl border border-red-500/40 bg-red-950/40 p-8 text-center backdrop-blur-xl">
        <AlertCircle class="mx-auto h-12 w-12 text-red-400" />
        <h2 class="mt-4 text-lg font-bold text-white">{{ isRTL ? 'التذكرة غير موجودة' : 'Ticket Not Found' }}</h2>
        <p class="mt-2 text-sm text-red-200">{{ errorMessage }}</p>
        <Link 
          :href="isRTL ? '/ar/hammer-challenge/audience' : '/hammer-challenge/audience'" 
          class="mt-6 inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold uppercase text-white hover:bg-red-500 transition"
        >
          <span>{{ isRTL ? 'تسجيل تذكرة جديدة' : 'Register New Ticket' }}</span>
          <ArrowRight v-if="!isRTL" class="h-4 w-4" />
          <ArrowLeft v-else class="h-4 w-4" />
        </Link>
      </div>

      <!-- Digital Pass Card (Banner Theme) -->
      <section v-else class="relative w-full overflow-hidden rounded-3xl border-2 border-red-600/50 bg-gradient-to-b from-[#141014] via-[#0d0d12] to-[#08080a] p-6 sm:p-9 text-center shadow-2xl shadow-black/95">
        <!-- Red Accent Top Line -->
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-transparent via-red-500 to-transparent"></div>

        <!-- Success Icon (Electric Lime) -->
        <div class="w-16 h-16 rounded-2xl bg-[#a3e635]/15 border border-[#a3e635]/40 mx-auto flex items-center justify-center text-[#a3e635] shadow-inner">
          <CheckCircle2 class="w-9 h-9" />
        </div>

        <p class="mt-5 text-xs font-mono uppercase tracking-[0.25em] text-red-400 font-bold">
          {{ isRTL ? 'تصريح دخول رسمي للجمهور' : 'Official Event Pass' }}
        </p>
        
        <h1 class="mt-2 font-display text-3xl sm:text-4xl font-black uppercase text-white tracking-tight">
          {{ isRTL ? 'تم تأكيد تسجيلك!' : "You're Registered!" }}
        </h1>
        <p class="text-sm sm:text-base font-bold text-zinc-300 mt-1">
          {{ isRTL ? 'تم إصدار تذكرة الحضور المجانية بنجاح' : 'Your Free Audience VIP Pass is Confirmed' }}
        </p>

        <!-- Ticket Voucher Badge Box with Red & Electric Lime Border -->
        <div class="mt-6 rounded-2xl border-2 border-dashed border-red-500/60 bg-gradient-to-b from-red-950/20 via-zinc-950/90 to-black p-5 shadow-xl shadow-black/80">
          <div class="flex items-center justify-between text-xs font-mono pb-2 border-b border-zinc-800">
            <span class="flex items-center gap-1.5 text-white font-bold">
              <Ticket class="w-3.5 h-3.5 text-red-500" />
              <span>{{ isRTL ? 'تذكرة الجمهور' : 'Audience Pass' }}</span>
            </span>
            <span class="text-[#a3e635] font-bold font-mono">
              ● {{ isRTL ? 'فعال' : 'ACTIVE' }}
            </span>
          </div>

          <div class="py-4">
            <div class="text-[11px] uppercase tracking-[0.2em] text-zinc-400 font-mono">
              {{ isRTL ? 'رقم التذكرة' : 'Ticket Number' }}
            </div>
            <div class="mt-1 text-3xl sm:text-4xl font-black tracking-[0.15em] text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-white to-red-400 font-mono">
              {{ registration.ticket_number }}
            </div>
            <div class="text-sm font-bold text-white mt-1.5">{{ registration.full_name }}</div>
            
            <!-- Verified Google Review Account in Electric Lime -->
            <div v-if="registration.google_review_name" class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#a3e635]/15 border border-[#a3e635]/40 text-[#a3e635] text-[11px] font-mono">
              <span class="text-amber-400">★</span>
              <span>
                {{ isRTL ? 'تقييم Google: ' : 'Google Review: ' }}
                <strong class="text-white">{{ registration.google_review_name }}</strong>
              </span>
            </div>
          </div>

          <div class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider pt-2 border-t border-zinc-800">
            {{ isRTL ? 'يرجى إبراز هذه الشاشة عند مدخل الفعالية' : 'Show this digital pass at the venue entrance' }}
          </div>
        </div>

        <!-- Event Timing & Location (Strict Monolingual) -->
        <div class="mt-6 space-y-2.5 text-xs sm:text-sm text-zinc-300 bg-[#121217] border border-zinc-800/90 rounded-2xl p-4 text-left rtl:text-right">
          <div class="flex items-center gap-3">
            <Calendar class="w-4 h-4 text-red-400 shrink-0" />
            <div>
              <span class="text-zinc-500 block text-[10px] uppercase font-mono">{{ isRTL ? 'التاريخ' : 'Date' }}</span>
              <strong class="text-white">{{ isRTL ? 'السبت، 12 سبتمبر 2026' : 'Saturday, 12 September 2026' }}</strong>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <Clock class="w-4 h-4 text-red-400 shrink-0" />
            <div>
              <span class="text-zinc-500 block text-[10px] uppercase font-mono">{{ isRTL ? 'الجدول' : 'Schedule' }}</span>
              <span class="text-zinc-300">
                {{ isRTL 
                  ? 'استقبال الجمهور: 7:30 مساءً • انطلاق التحدي: 8:00 مساءً' 
                  : 'Audience Check-in: 7:30 PM • Challenge Starts: 8:00 PM' 
                }}
              </span>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <MapPin class="w-4 h-4 text-[#a3e635] shrink-0" />
            <div>
              <span class="text-zinc-500 block text-[10px] uppercase font-mono">{{ isRTL ? 'الموقع' : 'Venue' }}</span>
              <span class="text-zinc-300">
                {{ isRTL 
                  ? 'مركز فينينو للعناية بالسيارات، مصفح M37، أبوظبي' 
                  : 'Veneno Auto Care Center, Musaffah M37, Abu Dhabi' 
                }}
              </span>
            </div>
          </div>
        </div>

        <!-- Action Buttons (Strict Monolingual) -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <button
            type="button"
            @click="openGoogleMaps"
            class="flex-1 py-3 px-4 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer"
          >
            <MapPin class="w-4 h-4 text-[#a3e635]" />
            <span>{{ isRTL ? 'فتح خريطة الموقع' : 'Open Location Map' }}</span>
          </button>

          <Link
            href="/"
            class="flex-1 py-3 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs uppercase flex items-center justify-center gap-2 transition shadow-lg shadow-red-950/50"
          >
            <span>{{ isRTL ? 'العودة للرئيسية' : 'Return to Homepage' }}</span>
            <ArrowRight v-if="!isRTL" class="w-4 h-4" />
            <ArrowLeft v-else class="w-4 h-4" />
          </Link>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full max-w-2xl mx-auto text-center text-xs text-zinc-500 font-mono pt-4">
      {{ isRTL 
        ? `مركز فينينو للعناية بالسيارات • المنصة الرسمية لتحدي المطرقة ${new Date().getFullYear()}` 
        : `Veneno Auto Care Center • Official Hammer Challenge Platform ${new Date().getFullYear()}` 
      }}
    </footer>
  </div>
</template>
