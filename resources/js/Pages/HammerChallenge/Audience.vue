<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import HammerLanguageSwitcher from '@/Components/HammerLanguageSwitcher.vue';
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
  ExternalLink
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
    errorMessage.value = isRTL.value ? 'يرجى كتابة اسم حساب Google للتأكيد عند الدخول.' : 'Please enter your Google Review Account / Display Name for entrance verification.';
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
      const confirmPath = isRTL.value ? '/ar/hammer-challenge/audience/confirmation' : '/hammer-challenge/audience/confirmation';
      router.visit(`${confirmPath}?token=${encodeURIComponent(token)}`);
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
    <!-- Magma & Volcanic Red Ambience Glows (Banner Theme) -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.28),transparent_50%),radial-gradient(circle_at_85%_95%,rgba(163,230,53,0.10),transparent_40%)]"></div>
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_55%,rgba(239,68,68,0.12),transparent_40%)]"></div>

    <!-- Header Navigation with Main Website Language Dropdown -->
    <header class="relative z-50 w-full max-w-4xl mx-auto px-4 pt-6 pb-4 flex items-center justify-between gap-3">
      <Link href="/" class="flex items-center gap-3 group">
        <div class="w-10 h-10 rounded-xl overflow-hidden border border-red-500/50 shadow-lg shadow-red-950/50 group-hover:scale-105 transition bg-red-600 flex items-center justify-center p-1.5 ring-1 ring-red-400/30">
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

      <div class="flex items-center gap-3">
        <!-- Main Website Style Dropdown Language Switcher -->
        <HammerLanguageSwitcher />

        <!-- Event Tag Badge -->
        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-950/60 border border-red-500/40 text-red-300 text-xs font-bold font-mono shadow-sm">
          <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
          <span>{{ isRTL ? 'تصريح الجمهور' : 'AUDIENCE PASS' }}</span>
        </div>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="relative z-10 flex-1 max-w-xl w-full mx-auto px-4 py-4 flex flex-col justify-center">
      <!-- Luxury Magma Card with Veneno Red & Electric Lime Accents -->
      <div class="relative rounded-3xl bg-[#0c0c10]/95 border-2 border-red-600/40 p-6 sm:p-8 shadow-2xl shadow-black/95 backdrop-blur-xl transition-all duration-300">
        <!-- Top Accent Magma Line -->
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent"></div>

        <!-- Multi-Step Progress Tracker -->
        <div class="mb-6 pb-5 border-b border-zinc-800/80">
          <div class="flex items-center justify-between text-xs font-mono">
            <!-- Step 1 Indicator -->
            <button 
              type="button" 
              @click="goToStep1" 
              class="flex items-center gap-2 transition cursor-pointer text-left rtl:text-right"
              :class="currentStep === 1 ? 'text-white font-bold' : isUnlocked ? 'text-[#a3e635] hover:text-[#bef264]' : 'text-zinc-500'"
            >
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold border transition"
                :class="isUnlocked 
                  ? 'bg-[#a3e635]/20 border-[#a3e635] text-[#a3e635]' 
                  : currentStep === 1 
                    ? 'bg-red-600/20 border-red-500 text-red-400' 
                    : 'bg-zinc-900 border-zinc-700 text-zinc-500'"
              >
                <CheckCircle2 v-if="isUnlocked" class="w-3.5 h-3.5" />
                <span v-else>1</span>
              </div>
              <span class="font-bold">{{ isRTL ? 'تقييم Google' : 'Google Review' }}</span>
            </button>

            <!-- Connector Line -->
            <div class="flex-1 mx-3 h-0.5 rounded-full overflow-hidden bg-zinc-800">
              <div 
                class="h-full bg-gradient-to-r from-red-500 to-[#a3e635] transition-all duration-500"
                :style="{ width: currentStep === 2 ? '100%' : isUnlocked ? '50%' : '0%' }"
              ></div>
            </div>

            <!-- Step 2 Indicator -->
            <div 
              class="flex items-center gap-2"
              :class="currentStep === 2 ? 'text-white font-bold' : 'text-zinc-500'"
            >
              <div 
                class="w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold border transition"
                :class="currentStep === 2 
                  ? 'bg-red-600/20 border-red-500 text-red-400' 
                  : 'bg-zinc-900 border-zinc-700 text-zinc-500'"
              >
                <span>2</span>
              </div>
              <span>{{ isRTL ? 'بيانات التذكرة' : 'Visitor Pass' }}</span>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- STEP 1: GOOGLE REVIEW GATE                               -->
        <!-- ======================================================== -->
        <div v-if="currentStep === 1" class="space-y-6 animate-in fade-in duration-200">
          <!-- Dual Brand Header Logos (Official Veneno Red Emblem + Google G) -->
          <div class="flex items-center justify-center -space-x-3 rtl:space-x-reverse pt-1">
            <!-- Official Veneno Emblem Circle with Red/Neon Border -->
            <div class="relative z-10 w-16 h-16 rounded-full overflow-hidden border-2 border-red-500 shadow-xl shadow-red-950/70 flex items-center justify-center bg-[#ef4444] p-2 ring-2 ring-black/70">
              <img 
                src="/images/veneno-emblem.png" 
                alt="Veneno Official Emblem" 
                class="w-full h-full object-contain drop-shadow"
              />
            </div>
            <!-- Google Official Logo Circle with Red/Neon Border -->
            <div class="relative z-20 w-16 h-16 rounded-full bg-white border-2 border-red-500 shadow-xl shadow-black/70 flex items-center justify-center p-3 ring-2 ring-black/70">
              <svg viewBox="0 0 24 24" class="w-full h-full">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
              </svg>
            </div>
          </div>

          <!-- 5 Luxury Glowing Stars with Electric Lime drop-shadow -->
          <div class="flex items-center justify-center gap-2 text-amber-400">
            <Star 
              v-for="i in 5" 
              :key="i" 
              class="w-7 h-7 sm:w-8 sm:h-8 fill-amber-400 text-amber-300 drop-shadow-[0_0_12px_rgba(251,191,36,0.7)] transform transition-transform hover:scale-125 duration-150"
            />
          </div>

          <!-- Strict Monolingual Headline & Context -->
          <div class="text-center space-y-2">
            <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight font-display">
              {{ isRTL ? 'هل استمتعت بزيارتك لمركز فينينو للعناية بالسيارات؟' : 'Enjoyed your visit at Veneno Auto Care Center?' }}
            </h1>
            <p class="text-xs sm:text-sm text-zinc-300 max-w-md mx-auto leading-relaxed">
              {{ isRTL 
                ? 'شارك تجربتك بتقييم 5 نجوم على Google لفتح تذكرة حضور الجمهور مجاناً ومشاهدة المنافسات الحية والجوائز الكبرى!' 
                : 'Share your 5-star experience on Google — it only takes a moment to unlock your Free VIP Audience Entrance Pass!' 
              }}
            </p>
          </div>

          <!-- Instruction Steps Card with Electric Lime Accent -->
          <div class="rounded-2xl bg-[#121217] border border-zinc-800 p-4 space-y-2.5 text-xs text-zinc-300 shadow-inner">
            <div class="font-bold text-[#a3e635] text-[11px] uppercase tracking-wider font-mono flex items-center gap-1.5">
              <Sparkles class="w-3.5 h-3.5" />
              <span>{{ isRTL ? 'خطوات فتح التذكرة المجانية:' : 'Quick 2-Step Unlock:' }}</span>
            </div>
            
            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center font-bold text-[11px] shrink-0 font-mono">1</span>
              <p class="leading-relaxed">
                {{ isRTL 
                  ? 'اضغط على زر "اكتب تقييمك على Google" وضع 5 نجوم مع رأيك الكريم.' 
                  : 'Click "Write a Review on Google" below and leave your 5-star rating.' 
                }}
              </p>
            </div>

            <div class="flex items-start gap-2.5">
              <span class="w-5 h-5 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center font-bold text-[11px] shrink-0 font-mono">2</span>
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
          <div v-if="isUnlocking" class="p-3.5 rounded-2xl bg-red-950/40 border border-red-500/40 text-red-300 text-xs flex items-center gap-3 animate-pulse">
            <Loader2 class="w-4 h-4 animate-spin text-red-400 shrink-0" />
            <div>
              <div class="font-bold">{{ isRTL ? 'جاري التحقق من فتح خطوة التسجيل...' : 'Verifying review action...' }}</div>
              <div class="text-[11px] text-zinc-300">{{ isRTL ? 'يرجى وضع 5 نجوم في الصفحة المفتوحة' : 'Please submit your 5-star review in the newly opened tab' }}</div>
            </div>
          </div>

          <!-- Verified Unlocked State Alert (Electric Lime) -->
          <div v-else-if="isUnlocked" class="p-3.5 rounded-2xl bg-[#a3e635]/15 border border-[#a3e635]/40 text-[#a3e635] text-xs flex items-center gap-3">
            <CheckCircle2 class="w-5 h-5 text-[#a3e635] shrink-0" />
            <div class="flex-1">
              <div class="font-bold">{{ isRTL ? 'تم فتح التسجيل بنجاح! ✓' : 'Review Step Unlocked! ✓' }}</div>
              <div class="text-[11px] text-zinc-200">
                {{ isRTL ? 'اضغط على زر المتابعة بالأسفل لتعبئة بيانات تذكرتك.' : 'Click the button below to complete your visitor pass.' }}
              </div>
            </div>
          </div>

          <!-- Continue to Registration Step Button (Veneno Crimson Gradient) -->
          <div class="pt-2">
            <button
              type="button"
              :disabled="!isUnlocked"
              @click="goToStep2"
              class="w-full py-4 px-6 rounded-2xl font-black text-sm uppercase tracking-wider transition-all duration-300 flex items-center justify-center gap-2.5 cursor-pointer"
              :class="isUnlocked 
                ? 'bg-gradient-to-r from-red-600 via-red-500 to-red-600 hover:from-red-500 hover:to-red-400 text-white shadow-xl shadow-red-950/60 active:scale-98 ring-2 ring-red-400/40' 
                : 'bg-zinc-900 border border-zinc-800 text-zinc-600 cursor-not-allowed opacity-60'"
            >
              <Lock v-if="!isUnlocked" class="w-4 h-4 text-zinc-500" />
              <Unlock v-else class="w-4 h-4 text-white" />

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
          <!-- Back to Review Step Link & Verified Badge -->
          <div class="flex items-center justify-between">
            <button
              type="button"
              @click="goToStep1"
              class="inline-flex items-center gap-1.5 text-xs text-zinc-400 hover:text-red-400 font-mono transition cursor-pointer"
            >
              <ArrowLeft v-if="!isRTL" class="w-3.5 h-3.5" />
              <ArrowRight v-else class="w-3.5 h-3.5" />
              <span>{{ isRTL ? 'العودة لخطوة التقييم' : 'Back to Review Step' }}</span>
            </button>

            <!-- Verified Review Badge in Electric Lime -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#a3e635]/15 border border-[#a3e635]/40 text-[#a3e635] text-[11px] font-mono font-bold">
              <CheckCircle2 class="w-3.5 h-3.5" />
              <span>{{ isRTL ? 'التقييم معتمد' : 'Review Verified' }}</span>
            </div>
          </div>

          <!-- Title & Subtitle (Strict Monolingual) -->
          <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-600/10 border border-red-500/30 text-red-400 text-xs font-mono font-bold uppercase">
              <Ticket class="w-3.5 h-3.5" />
              <span>{{ isRTL ? 'تذكرة حضور الجمهور والزوار مجاناً' : 'Free Audience & Spectator Pass' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black uppercase text-white tracking-tight font-display">
              {{ isRTL ? 'تأكيد تذكرة الدخول' : 'HAMMER CHALLENGE PASS' }}
            </h1>
            <p class="text-xs text-zinc-400 max-w-md mx-auto leading-relaxed">
              {{ isRTL 
                ? 'أدخل بياناتك بالأسفل للحصول على بطاقة الدخول الرقمية لحضور المنافسات الحية!' 
                : 'Enter your details below to claim your digital entrance pass to watch the live showdown!' 
              }}
            </p>
          </div>

          <!-- Quick Event Snapshot (Strict Monolingual) -->
          <div class="grid grid-cols-2 gap-2.5 p-3 rounded-2xl bg-[#121217] border border-zinc-800 text-xs text-zinc-300">
            <div class="flex items-center gap-2">
              <Clock class="w-4 h-4 text-red-400 shrink-0" />
              <div>
                <div class="text-[10px] text-zinc-500 uppercase font-mono">{{ isRTL ? 'موعد الحضور' : 'Check-in Time' }}</div>
                <div class="font-bold text-zinc-200">{{ isRTL ? '7:30 مساءً • 12 سبتمبر 2026' : '7:30 PM • 12 Sep 2026' }}</div>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <MapPin class="w-4 h-4 text-[#a3e635] shrink-0" />
              <div>
                <div class="text-[10px] text-zinc-500 uppercase font-mono">{{ isRTL ? 'الموقع' : 'Location' }}</div>
                <div class="font-bold text-zinc-200">{{ isRTL ? 'مصفح M37، أبوظبي' : 'Musaffah M37, Abu Dhabi' }}</div>
              </div>
            </div>
          </div>

          <!-- Registration Form (Strict Monolingual: Zero mixed language) -->
          <form @submit.prevent="handleAudienceSubmit" class="space-y-4">
            <!-- Full Name -->
            <div>
              <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5">
                {{ isRTL ? 'الاسم الكامل *' : 'Full Name *' }}
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <User class="w-4 h-4 text-red-500" />
                </div>
                <input
                  v-model="form.full_name"
                  type="text"
                  required
                  :placeholder="isRTL ? 'مثال: راشد المنصوري' : 'e.g. Rashid Al Mansoori'"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-white placeholder-zinc-500 text-sm transition outline-none"
                />
              </div>
            </div>

            <!-- Mobile Number -->
            <div>
              <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5">
                {{ isRTL ? 'رقم الهاتف المتحرك *' : 'Mobile Phone Number *' }}
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <Phone class="w-4 h-4 text-red-500" />
                </div>
                <input
                  v-model="form.mobile"
                  @input="formatPhoneInput"
                  type="tel"
                  required
                  :placeholder="isRTL ? 'مثال: 0501234567 أو 971...' : 'e.g. 050 123 4567 or +971...'"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-white placeholder-zinc-500 text-sm transition outline-none font-mono"
                />
              </div>
              <p class="text-[11px] text-zinc-500 mt-1">
                {{ isRTL ? 'سيتم ربط تذكرة الدخول بهذا الرقم لتأكيد الحضور عند المدخل.' : 'Ticket confirmation pass will be linked to this phone number.' }}
              </p>
            </div>

            <!-- FIELD: Google Review Account / Display Name -->
            <div class="p-3.5 rounded-2xl bg-zinc-900/90 border border-red-500/40">
              <label class="block text-xs font-bold text-white uppercase tracking-wider mb-1.5 flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                  <svg viewBox="0 0 24 24" class="w-3.5 h-3.5 shrink-0">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                  </svg>
                  <span>{{ isRTL ? 'اسم حسابك على Google (لتأكيد التقييم) *' : 'Your Google Review Account / Display Name *' }}</span>
                </span>
                <span class="text-[#a3e635] text-[10px] font-mono uppercase font-bold">
                  {{ isRTL ? 'التحقق' : 'Verification' }}
                </span>
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
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-950 border border-zinc-700 focus:border-[#a3e635] focus:ring-2 focus:ring-[#a3e635]/20 text-white placeholder-zinc-500 text-sm transition outline-none"
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
              <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5">
                {{ isRTL ? 'البريد الإلكتروني (اختياري)' : 'Email Address (Optional)' }}
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-zinc-500">
                  <Mail class="w-4 h-4 text-zinc-500" />
                </div>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="name@example.com"
                  class="w-full pl-10 rtl:pl-4 rtl:pr-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 text-white placeholder-zinc-500 text-sm transition outline-none font-mono"
                />
              </div>
            </div>

            <!-- Error Alert -->
            <div v-if="errorMessage" class="p-3.5 rounded-xl bg-red-950/60 border border-red-500/50 text-red-300 text-xs flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
              <span>{{ errorMessage }}</span>
            </div>

            <!-- Submit Button (Pure Language) -->
            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-red-600 hover:from-red-500 hover:to-red-400 text-white font-black text-sm uppercase tracking-wider shadow-xl shadow-red-950/60 transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer active:scale-98 disabled:opacity-50"
            >
              <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <Ticket v-else class="w-4 h-4" />
              <span>
                {{ isSubmitting 
                  ? (isRTL ? 'جاري إصدار التذكرة...' : 'Issuing Your Ticket...') 
                  : (isRTL ? 'احصل على تذكرة الحضور المجانية' : 'Get Free Audience Ticket') 
                }}
              </span>
            </button>
          </form>

          <!-- Divider & Contestant Switcher Notice (Pure Language) -->
          <div class="mt-6 pt-5 border-t border-zinc-800 text-center">
            <p class="text-xs text-zinc-400">
              {{ isRTL ? 'هل ترغب في خوض المنافسة على جائزة ' : 'Want to compete for the ' }}
              <strong class="text-white font-bold">{{ isRTL ? '15,000 درهم كاش؟' : 'AED 15,000 Cash Prize?' }}</strong>
            </p>
            <Link
              :href="isRTL ? '/ar/hammer-challenge/register' : '/hammer-challenge/register'"
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
      {{ isRTL 
        ? `© ${new Date().getFullYear()} مركز فينينو للعناية بالسيارات. جميع الحقوق محفوظة. • مصفح M37، أبوظبي` 
        : `© ${new Date().getFullYear()} Veneno Auto Care Center. All Rights Reserved. • Musaffah M37, Abu Dhabi` 
      }}
    </footer>
  </div>
</template>
