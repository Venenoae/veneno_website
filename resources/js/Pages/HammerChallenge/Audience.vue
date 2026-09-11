<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import { 
  User, 
  Phone, 
  Mail, 
  Sparkles, 
  MapPin, 
  Clock, 
  CheckCircle2, 
  ArrowRight, 
  ArrowLeft,
  Loader2, 
  Ticket,
  Flame,
  ShieldCheck,
  Star,
  Lock,
  Unlock,
  ExternalLink,
  ChevronRight,
  Globe
} from 'lucide-vue-next';

const props = defineProps({
  initialLocale: { type: String, default: 'ar' },
});

const { currentLocale, setLocale, isRTL } = useI18n();

onMounted(() => {
  if (props.initialLocale && ['en', 'ar'].includes(props.initialLocale)) {
    setLocale(props.initialLocale);
  }
  // Check if previously unlocked in this session
  if (typeof window !== 'undefined' && sessionStorage.getItem('veneno_review_unlocked') === 'true') {
    isUnlocked.value = true;
    isReviewClicked.value = true;
  }
});

// Step state: 1 = Google Review Gate, 2 = Registration Form
const currentStep = ref(1);

// Review gate unlock states
const isReviewClicked = ref(false);
const isUnlocking = ref(false);
const isUnlocked = ref(false);

const GOOGLE_REVIEW_URL = 'https://g.page/r/CfSvUtNwCqocEAE/review';

const form = ref({
  full_name: '',
  mobile: '',
  google_review_name: '',
  email: '',
});

const isSubmitting = ref(false);
const errorMessage = ref('');

// Switch language
const toggleLocale = (lang) => {
  setLocale(lang);
};

// Handle opening Google Review
const handleOpenReview = () => {
  window.open(GOOGLE_REVIEW_URL, '_blank', 'noopener,noreferrer');
  isReviewClicked.value = true;
  isUnlocking.value = true;
  
  // 1.5s verification unlock animation
  setTimeout(() => {
    isUnlocking.value = false;
    isUnlocked.value = true;
    if (typeof window !== 'undefined') {
      sessionStorage.setItem('veneno_review_unlocked', 'true');
    }
  }, 1600);
};

// Proceed to Step 2
const goToStep2 = () => {
  if (!isUnlocked.value) return;
  currentStep.value = 2;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Return to Step 1
const goToStep1 = () => {
  currentStep.value = 1;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const formatPhoneInput = (e) => {
  form.value.mobile = e.target.value;
};

const handleAudienceSubmit = async () => {
  errorMessage.value = '';
  
  if (!form.value.full_name || form.value.full_name.trim().length < 2) {
    errorMessage.value = isRTL.value ? 'يرجى كتابة الاسم الكامل.' : 'Please enter your full name.';
    return;
  }

  if (!form.value.mobile || form.value.mobile.trim().length < 7) {
    errorMessage.value = isRTL.value ? 'يرجى كتابة رقم هاتف متحرك صحيح.' : 'Please enter a valid mobile phone number.';
    return;
  }

  if (!form.value.google_review_name || form.value.google_review_name.trim().length < 2) {
    errorMessage.value = isRTL.value ? 'يرجى كتابة اسم حساب Google الذي قيّمت به للتحقق عند الدخول.' : 'Please enter your Google Review Account / Display Name for entrance verification.';
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await window.axios.post('/api/hammer-challenge/audience', {
      full_name: form.value.full_name.trim(),
      mobile: form.value.mobile.trim(),
      google_review_name: form.value.google_review_name.trim(),
      email: form.value.email ? form.value.email.trim() : null,
    });

    if (response.data.success) {
      const token = response.data.registration.confirmation_token;
      router.visit(`/hammer-challenge/audience/confirmation?token=${encodeURIComponent(token)}`);
    } else {
      errorMessage.value = response.data.message || (isRTL.value ? 'فشل التسجيل. يرجى المحاولة مرة أخرى.' : 'Registration failed. Please try again.');
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const firstErr = Object.values(error.response.data.errors)[0];
      errorMessage.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      errorMessage.value = error.response?.data?.message || (isRTL.value ? 'فشل تقديم طلب التسجيل. يرجى المحاولة مجدداً.' : 'Failed to submit audience registration. Please try again.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Head :title="isRTL ? 'تصريح حضور الجمهور والزوار • تحدي فينينو 2026' : 'Audience & Visitor Pass • Veneno Hammer Challenge 2026'" />

  <div 
    :dir="isRTL ? 'rtl' : 'ltr'" 
    class="min-h-screen bg-[#070709] text-zinc-100 font-sans selection:bg-red-600 selection:text-white relative overflow-x-hidden flex flex-col justify-between"
  >
    <!-- Ambient Luxury Background Glows -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.22),transparent_48%),radial-gradient(circle_at_90%_100%,rgba(197,160,89,0.14),transparent_42%)]"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_50%,rgba(234,179,8,0.06),transparent_35%)]"></div>

    <!-- Header Navigation -->
    <header class="relative z-10 w-full max-w-4xl mx-auto px-4 pt-6 pb-4 flex items-center justify-between gap-2">
      <Link href="/" class="flex items-center gap-2.5 sm:gap-3 group">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/20 to-red-600/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-black text-sm group-hover:scale-105 transition shadow-lg shadow-amber-950/20">
          V
        </div>
        <div>
          <span class="text-xs font-black tracking-widest text-white uppercase font-display block">VENENO AUTO CARE</span>
          <span class="block text-[10px] text-zinc-400 font-mono">Musaffah M37, Abu Dhabi</span>
        </div>
      </Link>

      <div class="flex items-center gap-2 sm:gap-3">
        <!-- Language Switcher -->
        <div class="inline-flex items-center p-0.5 rounded-xl bg-zinc-900/90 border border-zinc-700/80 text-xs font-bold font-mono shadow-inner">
          <button
            type="button"
            @click="toggleLocale('ar')"
            :class="isRTL ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-black shadow-md font-black' : 'text-zinc-400 hover:text-white'"
            class="px-2.5 py-1 rounded-lg transition-all text-[11px] cursor-pointer"
          >
            العربية
          </button>
          <button
            type="button"
            @click="toggleLocale('en')"
            :class="!isRTL ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-black shadow-md font-black' : 'text-zinc-400 hover:text-white'"
            class="px-2.5 py-1 rounded-lg transition-all text-[11px] cursor-pointer"
          >
            EN
          </button>
        </div>

        <!-- Audience Badge -->
        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-950/60 border border-red-600/40 text-red-300 text-xs font-bold font-mono">
          <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
          <span>{{ isRTL ? 'تصريح الجمهور' : 'AUDIENCE PASS' }}</span>
        </div>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="relative z-10 flex-1 max-w-xl w-full mx-auto px-4 py-4 flex flex-col justify-center">
      <!-- Luxury Card -->
      <div class="relative rounded-3xl bg-zinc-950/90 border border-amber-500/35 p-6 sm:p-8 shadow-2xl shadow-black/95 backdrop-blur-xl transition-all duration-300">
        <!-- Top Accent Gold Glow Line -->
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>

        <!-- Multi-Step Progress Tracker -->
        <div class="mb-6 pb-5 border-b border-zinc-800/80">
          <div class="flex items-center justify-between text-xs font-mono">
            <!-- Step 1 Indicator -->
            <button 
              type="button" 
              @click="goToStep1" 
              class="flex items-center gap-2 transition cursor-pointer text-left rtl:text-right"
              :class="currentStep === 1 ? 'text-amber-400 font-bold' : isUnlocked ? 'text-emerald-400 hover:text-emerald-300' : 'text-zinc-500'"
            >
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold border transition"
                :class="isUnlocked ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400' : currentStep === 1 ? 'bg-amber-500/20 border-amber-400 text-amber-300' : 'bg-zinc-900 border-zinc-700 text-zinc-500'"
              >
                <CheckCircle2 v-if="isUnlocked" class="w-3.5 h-3.5" />
                <span v-else>1</span>
              </div>
              <span class="hidden sm:inline">{{ isRTL ? 'تقييم Google' : 'Google Review' }}</span>
            </button>

            <!-- Connector Line -->
            <div class="flex-1 mx-3 h-0.5 rounded-full overflow-hidden bg-zinc-800">
              <div 
                class="h-full bg-gradient-to-r from-amber-500 to-emerald-400 transition-all duration-500"
                :style="{ width: currentStep === 2 ? '100%' : isUnlocked ? '50%' : '0%' }"
              ></div>
            </div>

            <!-- Step 2 Indicator -->
            <div 
              class="flex items-center gap-2"
              :class="currentStep === 2 ? 'text-amber-400 font-bold' : 'text-zinc-500'"
            >
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold border transition"
                :class="currentStep === 2 ? 'bg-amber-500/20 border-amber-400 text-amber-300' : 'bg-zinc-900 border-zinc-700 text-zinc-500'"
              >
                <span>2</span>
              </div>
              <span class="hidden sm:inline">{{ isRTL ? 'بيانات التذكرة' : 'Visitor Pass' }}</span>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- STEP 1: GOOGLE REVIEW GATE                               -->
        <!-- ======================================================== -->
        <div v-if="currentStep === 1" class="space-y-6 animate-in fade-in duration-200">
          <!-- Dual Brand Header Logos (Veneno Red + Google G) -->
          <div class="flex items-center justify-center -space-x-3 rtl:space-x-reverse pt-1">
            <!-- Veneno Emblem Circle -->
            <div class="relative z-10 w-14 h-14 rounded-full bg-gradient-to-br from-red-600 via-red-700 to-red-950 border-2 border-amber-400/80 shadow-xl shadow-red-950/60 flex items-center justify-center">
              <span class="text-amber-300 font-black text-xl font-display tracking-tight">V</span>
            </div>
            <!-- Google Official Logo Circle -->
            <div class="relative z-20 w-14 h-14 rounded-full bg-white border-2 border-amber-400/80 shadow-xl shadow-black/60 flex items-center justify-center p-2.5">
              <svg viewBox="0 0 24 24" class="w-8 h-8">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
              </svg>
            </div>
          </div>

          <!-- 5 Luxury Glowing Stars -->
          <div class="flex items-center justify-center gap-1.5 text-amber-400">
            <Star 
              v-for="i in 5" 
              :key="i" 
              class="w-7 h-7 sm:w-8 sm:h-8 fill-amber-400 text-amber-300 drop-shadow-[0_0_10px_rgba(251,191,36,0.65)] transform transition-transform hover:scale-125 duration-150"
            />
          </div>

          <!-- Headline & Context -->
          <div class="text-center space-y-2">
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight font-display">
              {{ isRTL ? 'هل استمتعت بزيارتك لمركز فينينو للعناية بالسيارات؟' : 'Enjoyed your visit at Veneno Auto Care Center?' }}
            </h1>
            <p class="text-xs sm:text-sm text-zinc-300 max-w-md mx-auto leading-relaxed">
              {{ isRTL 
                ? 'شارك تجربتك بتقييم 5 نجوم على Google لفتح تذكرة دخول الجمهور مجاناً لحضور منافسات تحدي المطرقة والجوائز الكبرى!' 
                : 'Share your 5-star experience on Google — it only takes a moment to unlock your Free VIP Audience Entrance Pass!' 
              }}
            </p>
          </div>

          <!-- Instruction Steps Card -->
          <div class="rounded-2xl bg-zinc-900/70 border border-zinc-800/90 p-4 space-y-2.5 text-xs text-zinc-300 shadow-inner">
            <div class="font-bold text-amber-400 text-[11px] uppercase tracking-wider font-mono flex items-center gap-1.5">
              <Sparkles class="w-3.5 h-3.5" />
              <span>{{ isRTL ? 'خطوات فتح التذكرة المجانية:' : 'Quick 2-Step Unlock:' }}</span>
            </div>
            
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-[11px] shrink-0 font-mono">1</span>
              <p class="leading-relaxed">
                {{ isRTL 
                  ? 'اضغط على زر "اكتب تقييمك على Google" وضع 5 نجوم مع رأيك.' 
                  : 'Click "Write a Review on Google" below and leave your 5-star rating.' 
                }}
              </p>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-[11px] shrink-0 font-mono">2</span>
              <p class="leading-relaxed">
                {{ isRTL 
                  ? 'ارجع إلى هذه الصفحة واضغط "المتابعة إلى التسجيل" لإصدار تذكرتك الرقمية فوراً.' 
                  : 'Return here and click "Continue to Registration" to generate your VIP pass.' 
                }}
              </p>
            </div>
          </div>

          <!-- Action Button: Write a Review on Google -->
          <div>
            <button
              type="button"
              @click="handleOpenReview"
              class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-sm uppercase tracking-wider shadow-xl shadow-blue-950/60 transition-all duration-200 flex items-center justify-center gap-3 cursor-pointer active:scale-98"
            >
              <!-- Google G Icon in Button -->
              <div class="w-5 h-5 rounded-full bg-white flex items-center justify-center p-0.5 shrink-0 shadow-sm">
                <svg viewBox="0 0 24 24" class="w-3.5 h-3.5">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                </svg>
              </div>
              <span>{{ isRTL ? 'اكتب تقييمك على Google (5 نجوم)' : 'Write a Review on Google (5-Star)' }}</span>
              <ExternalLink class="w-4 h-4 opacity-80" />
            </button>
          </div>

          <!-- Unlocking / Verification Status Feedback -->
          <div v-if="isUnlocking" class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs flex items-center gap-3 animate-pulse">
            <Loader2 class="w-4 h-4 animate-spin text-amber-400 shrink-0" />
            <div>
              <div class="font-bold">{{ isRTL ? 'جاري التحقق من فتح خطوة التسجيل...' : 'Verifying review action...' }}</div>
              <div class="text-[11px] text-amber-200/80">{{ isRTL ? 'يرجى وضع 5 نجوم في الصفحة المفتوحة' : 'Please submit your 5-star review in the newly opened tab' }}</div>
            </div>
          </div>

          <!-- Verified Unlocked State Alert -->
          <div v-else-if="isUnlocked" class="p-3.5 rounded-2xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-xs flex items-center gap-3">
            <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0" />
            <div class="flex-1">
              <div class="font-bold">{{ isRTL ? 'تم فتح التسجيل بنجاح! ✓' : 'Review Step Unlocked! ✓' }}</div>
              <div class="text-[11px] text-emerald-200/80">
                {{ isRTL ? 'اضغط على زر المتابعة بالأسفل لتعبئة بيانات تذكرتك.' : 'Click the button below to complete your visitor pass.' }}
              </div>
            </div>
          </div>

          <!-- Continue to Registration Step Button -->
          <div class="pt-2">
            <button
              type="button"
              :disabled="!isUnlocked"
              @click="goToStep2"
              class="w-full py-4 px-6 rounded-2xl font-black text-sm uppercase tracking-wider transition-all duration-300 flex items-center justify-center gap-2.5 cursor-pointer"
              :class="isUnlocked 
                ? 'bg-gradient-to-r from-red-600 via-amber-500 to-amber-400 hover:from-red-500 hover:to-amber-300 text-black shadow-xl shadow-amber-950/40 active:scale-98 ring-2 ring-amber-400/40 animate-bounce-subtle' 
                : 'bg-zinc-900 border border-zinc-800 text-zinc-600 cursor-not-allowed opacity-60'"
            >
              <Lock v-if="!isUnlocked" class="w-4 h-4 text-zinc-500" />
              <Unlock v-else class="w-4 h-4 text-black" />

              <span>
                {{ isUnlocked 
                  ? (isRTL ? 'المتابعة إلى التسجيل ➔' : 'Continue to Registration ➔') 
                  : (isRTL ? 'مغلق • اضغط زر التقييم أعلاه أولاً' : 'Locked • Write Google Review First') 
                }}
              </span>
            </button>
            
            <p v-if="!isUnlocked" class="text-[11px] text-center text-zinc-500 mt-2">
              {{ isRTL ? 'يجب النقر على زر التقييم على Google أولاً لفتح التسجيل.' : 'Clicking the Google Review button unlocks the registration form.' }}
            </p>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- STEP 2: REGISTRATION FORM                                -->
        <!-- ======================================================== -->
        <div v-else class="space-y-6 animate-in fade-in duration-200">
          <!-- Back to Review Step Link -->
          <div class="flex items-center justify-between">
            <button
              type="button"
              @click="goToStep1"
              class="inline-flex items-center gap-1.5 text-xs text-zinc-400 hover:text-amber-400 font-mono transition cursor-pointer"
            >
              <ArrowLeft v-if="!isRTL" class="w-3.5 h-3.5" />
              <ArrowRight v-else class="w-3.5 h-3.5" />
              <span>{{ isRTL ? 'العودة لخطوة التقييم' : 'Back to Review Step' }}</span>
            </button>

            <!-- Verified Review Badge -->
            <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[11px] font-mono">
              <CheckCircle2 class="w-3 h-3" />
              <span>{{ isRTL ? 'التقييم معتمد' : 'Review Verified' }}</span>
            </div>
          </div>

          <!-- Title & Subtitle -->
          <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-mono font-bold uppercase">
              <Ticket class="w-3.5 h-3.5 text-amber-400" />
              <span>{{ isRTL ? 'تذكرة الجمهور والزوار مجاناً' : 'Free Audience & Spectator Pass' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black uppercase text-white tracking-tight font-display">
              {{ isRTL ? 'تأكيد تذكرة الدخول' : 'HAMMER CHALLENGE PASS' }}
            </h1>
            <p class="text-xs text-zinc-400 max-w-md mx-auto leading-relaxed">
              {{ isRTL 
                ? 'أدخل بياناتك بالأسفل للحصول على بطاقة الدخول الرقمية VIP لحضور العرض والمنافسات!' 
                : 'Enter your details below to claim your VIP digital entrance pass to watch the live showdown!' 
              }}
            </p>
          </div>

          <!-- Quick Event Snapshot -->
          <div class="grid grid-cols-2 gap-2.5 p-3 rounded-2xl bg-zinc-900/70 border border-zinc-800 text-xs text-zinc-300">
            <div class="flex items-center gap-2">
              <Clock class="w-4 h-4 text-red-400 shrink-0" />
              <div>
                <div class="text-[10px] text-zinc-500 uppercase font-mono">{{ isRTL ? 'موعد الحضور' : 'Check-in Time' }}</div>
                <div class="font-bold text-zinc-200">7:30 PM • 12 Sep 2026</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <MapPin class="w-4 h-4 text-emerald-400 shrink-0" />
              <div>
                <div class="text-[10px] text-zinc-500 uppercase font-mono">{{ isRTL ? 'الموقع' : 'Location' }}</div>
                <div class="font-bold text-zinc-200">Musaffah M37, Abu Dhabi</div>
              </div>
            </div>
          </div>

          <!-- Registration Form -->
          <form @submit.prevent="handleAudienceSubmit" class="space-y-4">
            <!-- Full Name -->
            <div>
              <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span>{{ isRTL ? 'الاسم الكامل *' : 'Full Name *' }}</span>
                <span class="text-zinc-500 text-[11px] font-normal">{{ isRTL ? 'Full Name' : 'الاسم الكامل' }}</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <User class="w-4 h-4 text-amber-400/80" />
                </div>
                <input
                  v-model="form.full_name"
                  type="text"
                  required
                  :placeholder="isRTL ? 'مثال: راشد المنصوري' : 'e.g. Rashid Al Mansoori'"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none"
                />
              </div>
            </div>

            <!-- Mobile Number -->
            <div>
              <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span>{{ isRTL ? 'رقم الهاتف المتحرك *' : 'Mobile Phone Number *' }}</span>
                <span class="text-zinc-500 text-[11px] font-normal">{{ isRTL ? 'Mobile Number' : 'رقم الهاتف' }}</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <Phone class="w-4 h-4 text-amber-400/80" />
                </div>
                <input
                  v-model="form.mobile"
                  @input="formatPhoneInput"
                  type="tel"
                  required
                  :placeholder="isRTL ? '050 123 4567 أو 971...' : 'e.g. 050 123 4567 or +971...'"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none font-mono"
                />
              </div>
              <p class="text-[11px] text-zinc-500 mt-1">
                {{ isRTL ? 'سيتم ربط تذكرة الدخول بهذا الرقم لتأكيد الحضور عند المدخل.' : 'Ticket confirmation pass will be linked to this phone number.' }}
              </p>
            </div>

            <!-- NEW FIELD: Google Review Account / Display Name -->
            <div class="p-3.5 rounded-2xl bg-amber-500/5 border border-amber-500/25">
              <label class="block text-xs font-bold text-amber-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                  <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 shrink-0">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                  </svg>
                  <span>{{ isRTL ? 'اسم حسابك على Google (لتأكيد التقييم) *' : 'Your Google Review Account / Display Name *' }}</span>
                </span>
                <span class="text-amber-400/70 text-[10px] font-mono uppercase">Verification</span>
              </label>

              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <Star class="w-4 h-4 text-amber-400" />
                </div>
                <input
                  v-model="form.google_review_name"
                  type="text"
                  required
                  :placeholder="isRTL ? 'مثال: راشد المنصوري (الاسم الظاهر في حساب Google)' : 'e.g. Rashid Al Mansoori (As shown on Google)'"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-amber-500/40 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none"
                />
              </div>
              <p class="text-[11px] text-zinc-400 mt-1.5 leading-relaxed">
                {{ isRTL 
                  ? 'يرجى كتابة الاسم المسجل به في تقييم Google ليتمكن فريق الاستقبال من مطابقة تقييمك بـ 5 نجوم عند البوابة.' 
                  : 'Used by booth staff at the event entrance to quickly verify your 5-star Google review.' 
                }}
              </p>
            </div>

            <!-- Email (Optional) -->
            <div>
              <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span>{{ isRTL ? 'البريد الإلكتروني (اختياري)' : 'Email Address (Optional)' }}</span>
                <span class="text-zinc-500 text-[11px] font-normal">{{ isRTL ? 'Email' : 'البريد الإلكتروني' }}</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <Mail class="w-4 h-4 text-zinc-500" />
                </div>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="name@example.com"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none font-mono"
                />
              </div>
            </div>

            <!-- Error Alert -->
            <div v-if="errorMessage" class="p-3.5 rounded-xl bg-red-950/60 border border-red-500/50 text-red-300 text-xs flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
              <span>{{ errorMessage }}</span>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-amber-500 hover:from-red-500 hover:to-amber-400 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-red-950/50 transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer active:scale-98 disabled:opacity-50"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <Ticket v-else class="w-4 h-4" />
              <span>
                {{ isSubmitting 
                  ? (isRTL ? 'جاري إصدار التذكرة...' : 'Issuing Your Ticket...') 
                  : (isRTL ? 'احصل على تذكرة الحضور المجانية • تأكيد' : 'Get Free Audience Ticket • Issue Pass') 
                }}
              </span>
            </button>
          </form>

          <!-- Divider & Contestant Switcher Notice -->
          <div class="mt-6 pt-5 border-t border-zinc-800 text-center">
            <p class="text-xs text-zinc-400">
              {{ isRTL ? 'هل ترغب في خوض المنافسة على جائزة ' : 'Want to compete for the ' }}
              <strong class="text-amber-300">{{ isRTL ? '15,000 درهم كاش؟' : 'AED 15,000 Cash Prize?' }}</strong>
            </p>
            <Link
              href="/hammer-challenge/register"
              class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-red-400 hover:text-red-300 underline transition"
            >
              <Flame class="w-3.5 h-3.5 text-red-500" />
              <span>{{ isRTL ? 'التسجيل كمتسابق في التحدي (18+)' : 'Register as a Contestant (18+)' }}</span>
              <ArrowRight v-if="!isRTL" class="w-3 h-3" />
              <ArrowLeft v-else class="w-3 h-3" />
            </Link>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full max-w-4xl mx-auto px-4 py-4 text-center text-xs text-zinc-500 font-mono">
      © {{ new Date().getFullYear() }} Veneno Auto Care Center. All Rights Reserved. • Musaffah M37, Abu Dhabi
    </footer>
  </div>
</template>

<style scoped>
@keyframes bounceSubtle {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}
.animate-bounce-subtle {
  animation: bounceSubtle 2.2s infinite ease-in-out;
}
</style>
