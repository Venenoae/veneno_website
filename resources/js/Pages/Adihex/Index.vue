<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import confetti from 'canvas-confetti';
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';
import TermsModal from '@/Components/Adihex/TermsModal.vue';
import {
  Check,
  ChevronRight,
  ChevronLeft,
  ChevronDown,
  Copy,
  Download,
  Flame,
  Gift,
  Lock,
  MapPin,
  MessageCircle,
  Phone,
  Shield,
  Sparkles,
  Star,
  Trophy,
  Volume2,
  VolumeX,
  Zap,
  ArrowRight,
  ArrowLeft,
  Crown,
  AlertCircle,
  Globe
} from 'lucide-vue-next';

const props = defineProps({
  initialLocale: { type: String, default: 'en' },
  prizes: { type: Array, default: () => [] },
  packages: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({ displaySpinCount: 184, remainingPlatinumSlots: 2, isExpired: false }) },
  stripePublicKey: { type: String, default: '' },
});

// State Management
const currentStep = ref(1); // 1: Lang, 2: Reg, 3: Spin, 4: Prize, 5: Offers, 6: Pay, 7: Done
const currentLocale = ref(props.initialLocale || 'en');
const isMuted = ref(false);
const isSubmitting = ref(false);
const copySuccess = ref(false);
const isSpinning = ref(false);

// International Phone Code Setup
const countryCode = ref('+971');
const countryCodes = [
  { code: '+971', nameEn: 'UAE', nameAr: 'الإمارات', sample: '50 123 4567' },
  { code: '+966', nameEn: 'KSA', nameAr: 'السعودية', sample: '50 123 4567' },
  { code: '+974', nameEn: 'Qatar', nameAr: 'قطر', sample: '33 123 456' },
  { code: '+965', nameEn: 'Kuwait', nameAr: 'الكويت', sample: '91 234 567' },
  { code: '+968', nameEn: 'Oman', nameAr: 'عُمان', sample: '91 234 567' },
  { code: '+973', nameEn: 'Bahrain', nameAr: 'البحرين', sample: '39 123 456' },
  { code: '+44', nameEn: 'UK', nameAr: 'بريطانيا', sample: '7911 123456' },
  { code: '+1', nameEn: 'USA/CA', nameAr: 'أمريكا', sample: '202 555 0123' },
  { code: '+49', nameEn: 'Germany', nameAr: 'ألمانيا', sample: '151 23456789' },
  { code: '+33', nameEn: 'France', nameAr: 'فرنسا', sample: '6 12 34 56 78' },
  { code: '+39', nameEn: 'Italy', nameAr: 'إيطاليا', sample: '312 345 6789' },
  { code: '+7', nameEn: 'Russia', nameAr: 'روسيا', sample: '912 345 6789' },
  { code: '+91', nameEn: 'India', nameAr: 'الهند', sample: '98765 43210' },
  { code: '+92', nameEn: 'Pakistan', nameAr: 'باكستان', sample: '301 2345678' },
  { code: '+20', nameEn: 'Egypt', nameAr: 'مصر', sample: '10 1234 5678' },
  { code: '+962', nameEn: 'Jordan', nameAr: 'الأردن', sample: '7 9012 3456' },
  { code: '+961', nameEn: 'Lebanon', nameAr: 'لبنان', sample: '70 123 456' },
  { code: '+212', nameEn: 'Morocco', nameAr: 'المغرب', sample: '6 12 34 56 78' },
  { code: '+216', nameEn: 'Tunisia', nameAr: 'تونس', sample: '20 123 456' },
  { code: '+213', nameEn: 'Algeria', nameAr: 'الجزائر', sample: '5 50 12 34 56' },
  { code: '+964', nameEn: 'Iraq', nameAr: 'العراق', sample: '770 123 4567' },
  { code: '+963', nameEn: 'Syria', nameAr: 'سوريا', sample: '944 123 456' },
  { code: '+90', nameEn: 'Turkey', nameAr: 'تركيا', sample: '532 123 4567' },
  { code: '+86', nameEn: 'China', nameAr: 'الصين', sample: '138 0013 8000' },
  { code: '+81', nameEn: 'Japan', nameAr: 'اليابان', sample: '90 1234 5678' },
  { code: '+82', nameEn: 'S.Korea', nameAr: 'كوريا', sample: '10 1234 5678' },
  { code: '+61', nameEn: 'Australia', nameAr: 'أستراليا', sample: '412 345 678' },
  { code: '+41', nameEn: 'Switzerland', nameAr: 'سويسرا', sample: '78 123 45 67' },
  { code: '+34', nameEn: 'Spain', nameAr: 'إسبانيا', sample: '612 34 56 78' },
];

const currentPhonePlaceholder = computed(() => {
  const found = countryCodes.find(c => c.code === countryCode.value);
  return found?.sample || '50 123 4567';
});

// Canvas Wheel Reference
const wheelCanvas = ref(null);
let wheelCtx = null;
let currentRotation = 0;
let animationFrameId = null;

// Lead Registration State
const registrationForm = ref({
  name: '',
  phone: '',
  email: '',
  service_intent: ['PPF Protection', 'Ceramic Coating'],
});

const serviceOptions = [
  { id: 'PPF Protection', label_en: 'PPF Protection', label_ar: 'حماية PPF', icon: Shield },
  { id: 'Ceramic Coating', label_en: 'Ceramic Coating', label_ar: 'نانو سيراميك', icon: Sparkles },
  { id: 'Window Tinting', label_en: 'Window Tinting', label_ar: 'تظليل عازل', icon: Flame },
  { id: 'Detailing', label_en: 'Detailing', label_ar: 'تلميع ساطع', icon: Star },
  { id: 'Peelable Paint', label_en: 'Peelable Paint', label_ar: 'جلاد مطاطي', icon: Zap },
  { id: 'PDR Dent Repair', label_en: 'PDR Dent Repair', label_ar: 'شفط الصدمات PDR', icon: Trophy },
];

// Result & Won State
const leadId = ref(null);
const winningPrizeIndex = ref(0);
const wonPrize = ref(null);
const voucherCode = ref('VEN-ADIHEX-8492');
const voucherExpiresAt = ref('2026-12-05');
const whatsappUrl = ref('https://wa.me/97126344403');
const selectedPackage = ref(null);
const depositPaid = ref(false);
const liveSpinCount = ref(props.stats?.displaySpinCount || 184);
const alreadyParticipatedModal = ref(false);
const alreadyParticipatedData = ref(null);
const showTermsModal = ref(false);
const termsAccepted = ref(false);
const depositTermsAccepted = ref(false);

const onTermsAcceptedFromModal = () => {
  if (currentStep.value === 2) {
    termsAccepted.value = true;
  }
  if (currentStep.value === 6) {
    depositTermsAccepted.value = true;
  }
};

// Countdown State for ADIHEX Campaign (Ends in 6 SEPT 2026 10:00pm GST)
const countdown = ref({
  days: '00',
  hours: '00',
  minutes: '00',
  seconds: '00',
});
let countdownTimer = null;

const calculateCountdown = () => {
  const now = new Date().getTime();
  // 6 September 2026 22:00:00 GST (UAE +04:00)
  const targetDate = new Date('2026-09-06T22:00:00+04:00').getTime();
  const diff = Math.max(0, targetDate - now);

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  const pad = (n) => String(n).padStart(2, '0');
  countdown.value = {
    days: pad(days),
    hours: pad(hours),
    minutes: pad(minutes),
    seconds: pad(seconds),
  };
};

// Audio Synthesis for Wheel Ticker & Win Celebration
class SoundEngine {
  constructor() {
    this.ctx = null;
  }
  init() {
    if (!this.ctx && typeof window !== 'undefined') {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (AudioCtx) this.ctx = new AudioCtx();
    }
  }
  playTick() {
    if (isMuted.value || !this.ctx) return;
    try {
      if (this.ctx.state === 'suspended') this.ctx.resume();
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(480, this.ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(120, this.ctx.currentTime + 0.03);
      gain.gain.setValueAtTime(0.25, this.ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, this.ctx.currentTime + 0.03);
      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start();
      osc.stop(this.ctx.currentTime + 0.035);
    } catch (e) {}
  }
  playWin() {
    if (isMuted.value || !this.ctx) return;
    try {
      if (this.ctx.state === 'suspended') this.ctx.resume();
      const notes = [523.25, 659.25, 783.99, 1046.50]; // C5, E5, G5, C6 Fanfare
      notes.forEach((freq, idx) => {
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, this.ctx.currentTime + idx * 0.1);
        gain.gain.setValueAtTime(0.3, this.ctx.currentTime + idx * 0.1);
        gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + idx * 0.1 + 0.4);
        osc.connect(gain);
        gain.connect(this.ctx.destination);
        osc.start(this.ctx.currentTime + idx * 0.1);
        osc.stop(this.ctx.currentTime + idx * 0.1 + 0.45);
      });
    } catch (e) {}
  }
}

const sounds = new SoundEngine();

// Dictionary for Full Arabic & English i18n
const t = computed(() => {
  const isAr = currentLocale.value === 'ar';
  return {
    isAr,
    dir: isAr ? 'rtl' : 'ltr',
    fontClass: isAr ? 'font-arabic' : 'font-sans',
    langToggle: isAr ? 'English 🇬🇧' : 'العربية 🇦🇪',
    progress: isAr ? `الخطوة ${currentStep.value} من 7` : `STEP ${currentStep.value} OF 7`,
    adnecTag: isAr ? 'أديهيكس 2026 • أدنيك أبوظبي' : 'ADIHEX 2026 • ADNEC',

    // Step 1
    s1_tag: isAr ? 'مرحباً بكم في جناح مركز فينينو للعناية بالسيارات' : 'WELCOME TO VENENO AUTO CARE CENTER × ADIHEX 2026',
    s1_title1: isAr ? 'امسح.' : 'SCAN.',
    s1_title2: isAr ? 'أدِر.' : 'SPIN.',
    s1_title3: isAr ? 'اربح' : 'WIN',
    s1_desc: isAr
      ? 'اختر لغتك المفضلة لبدء تجربة السحب الفوري وحصريات معرض الصيد والفروسية.'
      : 'Select your preferred language to begin your exclusive ADIHEX giveaway experience.',
    s1_en_title: 'English',
    s1_en_sub: 'Continue in English',
    s1_ar_title: 'العربية',
    s1_ar_sub: 'المتابعة باللغة العربية (RTL)',

    // Step 2
    s2_tag: isAr ? 'الخطوة 2: التسجيل السريع' : 'STEP 2: FAST REGISTRATION',
    s2_title: isAr ? 'سجّل وأدِر عجلة الحظ' : 'Register to Unlock Spin',
    s2_desc: isAr
      ? 'أدخل بياناتك لربط جائزتك وضمان استلام كود القسيمة الفوري.'
      : 'Enter details to unlock your lucky spin and secure your instant voucher code.',
    fullName: isAr ? 'الاسم الكامل *' : 'FULL NAME *',
    namePlaceholder: isAr ? 'سلطان المنصوري' : 'Sultan Al Mansoori',
    mobile: isAr ? 'رقم الهاتف *' : 'MOBILE NUMBER *',
    email: isAr ? 'البريد الإلكتروني (اختياري)' : 'EMAIL (OPTIONAL)',
    emailPlaceholder: isAr ? 'sultan@example.ae' : 'sultan@example.ae',
    serviceIntentTitle: isAr ? 'الخدمات التي تهتم بها (اختر ما يناسبك):' : 'INTERESTED SERVICES (SELECT):',
    btnSpinNow: isAr ? 'أدِر العجلة الآن ➔' : 'SPIN THE WHEEL NOW ➔',
    antiGamingText: isAr ? '🔒 لفة واحدة مضمونة لكل رقم هاتف خلال 24 ساعة' : '🔒 Single-spin guarantee per mobile number within 24 hours',
    termsAgree1: isAr ? 'أوافق على' : 'I agree to the',
    termsLink: isAr ? 'الشروط والأحكام' : 'Terms & Conditions',
    termsAgree2: isAr ? 'واستلام كود القسيمة عبر رسالة SMS على هاتفي.' : 'and to receive my official voucher code via SMS.',
    depositTermsAgree1: isAr ? 'أوافق على' : 'I agree to the',
    depositTermsLink: isAr ? 'شروط الحجز والعربون' : 'Deposit & Reservation Terms',
    depositTermsAgree2: isAr
      ? '(صلاحية 60 يوماً، يُخصم 100%، وقابل للاسترداد خلال 30 يوماً من تاريخ الطلب الخطي).'
      : '(60-day validity, 100% deductible, refundable within 30 days of written request).',

    // Step 3
    s3_title: isAr ? 'عجلة الحظ الفاخرة' : 'Veneno Luxury Spin Wheel',
    s3_desc: isAr ? 'اضغط على زر الدوران في المنتصف لاكتشاف هديتك الحصرية!' : 'Tap the center Spin button to discover your guaranteed show prize!',
    btnSpinWheel: isAr ? 'دوران!' : 'SPIN!',
    spinning: isAr ? 'جاري السحب...' : 'SPINNING...',

    // Step 4
    s4_mabrook: isAr ? 'ألف مبروك! لقد فزت بـ' : 'MABROOK! YOU WON',
    s4_secured_title: isAr ? 'تم تأمين وتسجيل كود جائزتك' : 'Voucher Code Secured & Registered',
    s4_secured_desc: isAr
      ? 'تم ربط كود قسيمتك الرسمي برقم هاتفك. استكشف باقات المعرض الحصرية في الخطوة التالية لتأكيد وتنزيل قسيمتك. يُرجى إبراز الرسالة النصية (SMS) في هاتفك لاستلام جائزتك.'
      : 'Your official voucher code is registered to your mobile number. Explore exclusive show packages next to confirm and download your Voucher. Present the SMS in Your Phone to Collect your prize.',
    btnSeeOffers: isAr ? 'استكشف عروض المعرض الحصرية ➔' : 'SEE EXCLUSIVE ADIHEX OFFERS ➔',

    // Step 5
    s5_tag: isAr ? 'عروض حصرية لزوار أديهيكس 2026' : 'EXCLUSIVE ADIHEX 2026 SHOW PACKAGES',
    s5_title: isAr ? 'احجز باقتك بعربون 500 درهم فقط' : 'Lock Show Deals for AED 500 Deposit',
    s5_desc: isAr
      ? 'عربون 500 درهم يُخصم 100% بالكامل من فاتورتك النهائية عند الحضور خلال 60 يوماً.'
      : 'AED 500 deposit is 100% deductible from your final invoice upon service at Veneno within 60 days.',
    reserveFor50: isAr ? 'احجز بـ 500 درهم' : 'Reserve for AED 500',
    deductibleNoteBadge: isAr ? '✓ يُخصم 100% من الفاتورة' : '✓ 100% Deductible',
    btnSkipPackages: isAr ? 'لا شكراً، أرغب فقط بالمطالبة بجائزتي المجانية ➔' : 'No thanks, continue with my free prize only ➔',

    // Step 6
    s6_title: isAr ? 'تأكيد الحجز والدفع الآمن' : 'Secure Show Reservation',
    s6_locking: isAr ? 'تثبيت عرض' : 'Lock in',
    depositDue: isAr ? 'مبلغ العربون المستحق:' : 'Reservation Deposit:',
    deductibleNote: isAr ? '✓ يُخصم 100% بالكامل من الفاتورة النهائية عند الخدمة' : '✓ 100% Deductible from final invoice upon service',
    payApplePay: isAr ? 'الدفع السريع عبر Apple Pay' : 'Pay with Apple Pay',
    orCard: isAr ? 'أو الدفع ببطاقة الائتمان / الخصم' : 'OR PAY WITH CREDIT / DEBIT CARD',
    cardNum: isAr ? 'رقم البطاقة' : 'Card Number',
    cardExp: isAr ? 'الشهر / السنة' : 'MM / YY',
    cardCvc: isAr ? 'رمز الأمان CVC' : 'CVC',
    btnConfirmPay50: isAr ? 'تأكيد ودفع 500 درهم ➔' : 'CONFIRM & PAY AED 500 ➔',
    btnBackOffers: isAr ? '← تغيير الباقة' : '← Change Package',
    stripeSecureNote: isAr ? 'دفع إلكتروني مشفر 256-bit مدعوم عبر Stripe' : '256-Bit SSL Encrypted • Powered by Stripe Payments',

    // Step 7
    s7_title1: isAr ? 'تم تأكيد طلبك!' : 'All Set!',
    s7_title2: isAr ? 'مبروك!' : 'Mabrook!',
    s7_desc: isAr
      ? 'تم تأكيد تفاصيل جائزتك وحجزك وإرسالها إلى هاتفك. يُرجى إبراز الرسالة النصية (SMS) في هاتفك لاستلام جائزتك.'
      : 'Your prize and reservation details have been confirmed and sent to your phone. Present the SMS in Your Phone to Collect your prize.',
    summary_name: isAr ? 'اسم العميل:' : 'Customer Name:',
    summary_phone: isAr ? 'رقم الهاتف:' : 'Mobile Number:',
    summary_prize: isAr ? 'الجائزة الرابحة:' : 'Won Prize:',
    summary_code: isAr ? 'كود القسيمة:' : 'Voucher Code:',
    summary_pkg: isAr ? 'باقة المعرض المحجوزة:' : 'Show Package:',
    summary_paid: isAr ? 'العربون المدفوع:' : 'Deposit Paid:',
    summary_valid: isAr ? 'صلاحية العرض:' : 'Validity Window:',
    validity_90_days: isAr ? '60 يوماً (ساري لمدة 60 يوماً)' : '60 Days (Valid for 60 days)',
    btnWhatsApp: isAr ? 'تواصل معنا عبر واتساب' : 'Open WhatsApp Concierge',
    btnDownloadPdf: isAr ? 'تحميل بطاقة الجائزة والوصل (PDF)' : 'Download Receipt & Pass (PDF)',
    btnDirections: isAr ? 'الموقع الجغرافي للمركز (مصفح M37) 📍' : 'Directions to Workshop (Musaffah M37) 📍',
    btnVisitWebsite: isAr ? 'زيارة موقع فينينو الرسمي (Veneno.ae) ➔' : 'Visit Official Veneno Website (Veneno.ae) ➔',

    // Live Pill
    liveSpunText: isAr ? 'زائر سحبوا اليوم في المعرض' : 'visitors have spun today at ADIHEX',
    platinumRemainingText: isAr ? 'متبقي فقط باقتين بلاتينيوم اليوم' : 'ONLY 2 PLATINUM SLOTS REMAINING TODAY',

    // Expired
    expiredTitle: isAr ? 'اختتام فعاليات حملة أديهيكس 2026' : 'ADIHEX 2026 Campaign Concluded',
    expiredDesc: isAr
      ? 'شكراً لزيارتكم جناح فينينو في أديهيكس 2026. للاستفسارات والحجوزات يرجى زيارة موقعنا الرئيسي.'
      : 'Thank you for visiting Veneno Auto Care Center at ADIHEX 2026. For inquiries and bookings, visit our main website.',
    visitVeneno: isAr ? 'زيارة موقع فينينو ➔' : 'Visit Veneno.ae ➔',
  };
});

// Toggle Service Intent
const toggleService = (srvId) => {
  const idx = registrationForm.value.service_intent.indexOf(srvId);
  if (idx > -1) {
    registrationForm.value.service_intent.splice(idx, 1);
  } else {
    registrationForm.value.service_intent.push(srvId);
  }
};

// Switch Language
const setLanguage = (lang) => {
  currentLocale.value = lang;
  sounds.init();
  if (currentStep.value === 3) {
    nextTick(() => {
      drawWheel(currentRotation);
    });
  }
};

const selectLanguageAndAdvance = (lang) => {
  setLanguage(lang);
  currentStep.value = 2;
};

// Submit Registration & Trigger Spin Step
const handleRegistration = async () => {
  sounds.init();
  if (!registrationForm.value.name.trim()) {
    alert(currentLocale.value === 'ar' ? 'يرجى كتابة الاسم الكامل' : 'Please enter your full name');
    return;
  }
  
  const rawNumber = (registrationForm.value.phone || '').trim().replace(/^0+/, '');
  if (!rawNumber || rawNumber.length < 6) {
    alert(currentLocale.value === 'ar' ? 'يرجى كتابة رقم هاتف صحيح' : 'Please enter a valid mobile number');
    return;
  }

  const fullPhone = `${countryCode.value} ${rawNumber}`;

  isSubmitting.value = true;
  try {
    const res = await window.axios.post('/api/adihex/spin', {
      name: registrationForm.value.name,
      phone: fullPhone,
      email: registrationForm.value.email || null,
      service_intent: registrationForm.value.service_intent,
      locale: currentLocale.value,
    });

    if (res.data.success) {
      leadId.value = res.data.lead_id;
      winningPrizeIndex.value = res.data.winning_prize_index;
      wonPrize.value = res.data.won_prize;
      voucherCode.value = res.data.voucher_code;
      voucherExpiresAt.value = res.data.voucher_expires_at || '2026-12-05';
      if (res.data.whatsapp_url) {
        whatsappUrl.value = res.data.whatsapp_url;
      }

      currentStep.value = 3;
      await nextTick();
      initWheel();
    }
  } catch (error) {
    console.error('Registration failed:', error);
    if (error.response?.status === 422 && error.response?.data?.already_participated) {
      alreadyParticipatedData.value = error.response.data;
      alreadyParticipatedModal.value = true;
      return;
    }
    // Graceful fallback for offline / mock
    leadId.value = 999;
    winningPrizeIndex.value = 3; // Free Diamond Car Wash
    wonPrize.value = props.prizes[3] || {
      id: 'wash_diamond',
      label_en: 'Free Diamond Car Wash',
      label_ar: 'غسيل دايموند مجاني',
      value_en: 'Worth AED 250 • Complimentary',
      value_ar: 'بقيمة 250 درهم • مجاناً',
    };
    voucherCode.value = 'VEN-ADIHEX-' + Math.floor(1000 + Math.random() * 9000);
    currentStep.value = 3;
    await nextTick();
    initWheel();
  } finally {
    isSubmitting.value = false;
  }
};

// ==========================================
// 60 FPS HTML5 Canvas Physics Wheel Engine
// ==========================================
const initWheel = () => {
  if (!wheelCanvas.value) return;
  const canvas = wheelCanvas.value;
  wheelCtx = canvas.getContext('2d');
  
  // High DPI Canvas Scaling
  const dpr = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  canvas.width = rect.width * dpr;
  canvas.height = rect.height * dpr;
  wheelCtx.scale(dpr, dpr);

  drawWheel(currentRotation);
};

const formatSegmentLines = (label) => {
  const customMap = {
    'بوليش وتلميع مجاني': ['بوليش وتلميع', 'مجاني'],
    'خصم 20% على التظليل': ['خصم %20', 'على التظليل'],
    'قسيمة 100 درهم': ['قسيمة', '100 درهم'],
    'غسيل دايموند مجاني': ['غسيل دايموند', 'مجاني'],
    'غسيل سلايم مجاني': ['غسيل سلايم', 'مجاني'],
    'خصم 20% على جميع الخدمات': ['خصم %20', 'جميع الخدمات'],
    'خصم 20% على باقة Platinum': ['خصم %20', 'باقة Platinum'],
    'Free Polish & Detailing': ['Free Polish', '& Detailing'],
    '20% Off Window Tinting': ['20% Off', 'Window Tinting'],
    'AED 100 Gift Voucher': ['AED 100', 'Gift Voucher'],
    'Free Diamond Car Wash': ['Free Diamond', 'Car Wash'],
    'Free Slime Wash': ['Free Slime', 'Wash'],
    '20% Off All Services': ['20% Off', 'All Services'],
    '20% Off Platinum Package': ['20% Off', 'Platinum Pkg'],
  };
  if (customMap[label]) return customMap[label];
  const words = label.split(' ');
  if (words.length <= 1) return [label, ''];
  const mid = Math.ceil(words.length / 2);
  return [words.slice(0, mid).join(' '), words.slice(mid).join(' ')];
};

const drawWheel = (rotationAngle) => {
  if (!wheelCtx || !wheelCanvas.value) return;
  const canvas = wheelCanvas.value;
  const rect = canvas.getBoundingClientRect();
  const width = rect.width;
  const height = rect.height;
  const centerX = width / 2;
  const centerY = height / 2;
  const radius = Math.min(centerX, centerY) - 14;

  wheelCtx.clearRect(0, 0, width, height);

  const numSegments = props.prizes.length || 7;
  const segmentAngle = (2 * Math.PI) / numSegments;

  wheelCtx.save();
  wheelCtx.translate(centerX, centerY);
  wheelCtx.rotate(rotationAngle);

  // Draw Segments
  for (let i = 0; i < numSegments; i++) {
    const startAngle = i * segmentAngle;
    const endAngle = startAngle + segmentAngle;
    const prize = props.prizes[i];

    wheelCtx.beginPath();
    wheelCtx.moveTo(0, 0);
    wheelCtx.arc(0, 0, radius, startAngle, endAngle);
    wheelCtx.closePath();

    // Alternate Luxury Gradient Styling
    const segGradient = wheelCtx.createRadialGradient(0, 0, 10, 0, 0, radius);
    if (prize.id === 'platinum_20') {
      segGradient.addColorStop(0, '#ff1a24');
      segGradient.addColorStop(1, '#990000');
    } else if (i % 3 === 0) {
      segGradient.addColorStop(0, '#aa1111');
      segGradient.addColorStop(1, '#660000');
    } else if (i % 3 === 1) {
      segGradient.addColorStop(0, '#2d2d2d');
      segGradient.addColorStop(1, '#111111');
    } else {
      segGradient.addColorStop(0, '#e5c07b');
      segGradient.addColorStop(1, '#9a7b38');
    }

    wheelCtx.fillStyle = segGradient;
    wheelCtx.fill();

    // Segment Border
    wheelCtx.lineWidth = 1.5;
    wheelCtx.strokeStyle = 'rgba(212, 175, 55, 0.4)';
    wheelCtx.stroke();

    // Segment Text - Horizontal Orientation
    wheelCtx.save();
    wheelCtx.rotate(startAngle + segmentAngle / 2);
    
    // Position text in the middle of the sector band
    const textRadius = radius * 0.63;
    wheelCtx.translate(textRadius, 0);
    
    // Rotate 90 degrees so text runs horizontally across the segment
    wheelCtx.rotate(Math.PI / 2);
    
    wheelCtx.textAlign = 'center';
    wheelCtx.textBaseline = 'middle';
    wheelCtx.fillStyle = (i % 3 === 2 && prize.id !== 'platinum_20') ? '#0a0a0a' : '#ffffff';
    wheelCtx.font = currentLocale.value === 'ar' 
      ? 'bold 11.5px Cairo, sans-serif' 
      : 'bold 11.5px Outfit, sans-serif';
    wheelCtx.shadowColor = 'rgba(0,0,0,0.9)';
    wheelCtx.shadowBlur = 4;

    const rawLabel = currentLocale.value === 'ar' ? prize.label_ar : prize.label_en;
    const [line1, line2] = formatSegmentLines(rawLabel);

    if (line2) {
      wheelCtx.fillText(line1, 0, -7.5);
      wheelCtx.fillText(line2, 0, 7.5);
    } else {
      wheelCtx.fillText(line1, 0, 0);
    }

    wheelCtx.restore();

    // Segment outer bulb indicator on the gold rim
    wheelCtx.save();
    wheelCtx.rotate(startAngle + segmentAngle / 2);
    wheelCtx.beginPath();
    wheelCtx.arc(radius - 8, 0, 3.5, 0, 2 * Math.PI);
    wheelCtx.fillStyle = '#fef08a';
    wheelCtx.shadowColor = '#fef08a';
    wheelCtx.shadowBlur = 6;
    wheelCtx.fill();
    wheelCtx.restore();
  }

  // Draw Outer Gold Rim
  wheelCtx.beginPath();
  wheelCtx.arc(0, 0, radius, 0, 2 * Math.PI);
  wheelCtx.lineWidth = 7;
  wheelCtx.strokeStyle = '#c5a059';
  wheelCtx.stroke();

  // Draw Center Hub
  wheelCtx.beginPath();
  wheelCtx.arc(0, 0, 42, 0, 2 * Math.PI);
  const hubGrad = wheelCtx.createRadialGradient(0, 0, 5, 0, 0, 42);
  hubGrad.addColorStop(0, '#fef08a');
  hubGrad.addColorStop(0.5, '#c5a059');
  hubGrad.addColorStop(1, '#785920');
  wheelCtx.fillStyle = hubGrad;
  wheelCtx.fill();
  wheelCtx.lineWidth = 3;
  wheelCtx.strokeStyle = '#ffffff';
  wheelCtx.stroke();

  wheelCtx.restore();
};

// Trigger Spin with Smooth Cubic-Bezier Deceleration
const triggerSpin = () => {
  if (isSpinning.value) return;
  sounds.init();
  isSpinning.value = true;

  const numSegments = props.prizes.length || 7;
  const segmentAngle = (2 * Math.PI) / numSegments;
  
  // The arrow is at top (-PI/2 or 3PI/2)
  // Target rotation brings winning segment to the top arrow
  const targetIndex = winningPrizeIndex.value;
  // Calculate offset so segment center aligns with top pointer (-Math.PI / 2)
  const segmentCenter = targetIndex * segmentAngle + segmentAngle / 2;
  const targetAngle = (3 * Math.PI / 2) - segmentCenter;

  const fullRotations = 6 * (2 * Math.PI); // 6 full exciting spins
  const totalAngle = fullRotations + targetAngle;

  const startTime = performance.now();
  const duration = 5200; // 5.2s natural casino wheel feel
  const startAngle = currentRotation % (2 * Math.PI);
  let lastTickSegment = -1;

  // Custom Cubic-Bezier OutQuart Easing for ultra-realistic physics
  const easeOutQuart = (x) => 1 - Math.pow(1 - x, 4);

  const animate = (currentTime) => {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easedProgress = easeOutQuart(progress);

    currentRotation = startAngle + (totalAngle - startAngle) * easedProgress;
    drawWheel(currentRotation);

    // Audio Ticker Pass Trigger
    const normalizedAngle = (currentRotation % (2 * Math.PI) + 2 * Math.PI) % (2 * Math.PI);
    const activeSeg = Math.floor(normalizedAngle / segmentAngle);
    if (activeSeg !== lastTickSegment) {
      sounds.playTick();
      lastTickSegment = activeSeg;
    }

    if (progress < 1) {
      animationFrameId = requestAnimationFrame(animate);
    } else {
      isSpinning.value = false;
      sounds.playWin();
      triggerConfetti();
      setTimeout(() => {
        currentStep.value = 4;
      }, 1000);
    }
  };

  animationFrameId = requestAnimationFrame(animate);
};

// Celebration Confetti Explosion
const triggerConfetti = () => {
  try {
    confetti({
      particleCount: 100,
      spread: 70,
      origin: { y: 0.6 },
      colors: ['#dc2626', '#c5a059', '#ffffff', '#e50914', '#ffd700'],
      zIndex: 9999,
    });
  } catch (e) {}
};

// Copy Voucher Code
const copyVoucherCode = async () => {
  try {
    await navigator.clipboard.writeText(voucherCode.value);
    copySuccess.value = true;
    setTimeout(() => {
      copySuccess.value = false;
    }, 2500);
  } catch (e) {
    copySuccess.value = true;
  }
};

// Step 5: Choose Package
const selectPackage = (pkg) => {
  selectedPackage.value = pkg;
  currentStep.value = 6;
  nextTick(() => {
    initStripePayment(pkg);
  });
};

// Step 5 Skip: Skip Reservation, Claim Free Spin Prize
const skipToConfirmation = async () => {
  sounds.init();
  isSubmitting.value = true;
  try {
    if (leadId.value) {
      await window.axios.post('/api/adihex/reserve', {
        lead_id: leadId.value,
        package_id: 'skip',
        action: 'skip',
      });
    }
  } catch (e) {
    console.warn('Skip reservation notice:', e);
  } finally {
    isSubmitting.value = false;
  }
  selectedPackage.value = null;
  depositPaid.value = false;
  currentStep.value = 7;
  window.scrollTo({ top: 0, behavior: 'smooth' });
  triggerConfetti();
};

// ==========================================
// Stripe Integration for AED 50.00 Deposit
// ==========================================
let stripeInstance = null;
let stripeElements = null;
let stripePaymentElement = null;
const isStripeLoading = ref(false);
const stripeErrorMessage = ref('');

const loadStripeScript = () => {
  return new Promise((resolve, reject) => {
    if (window.Stripe) {
      resolve(window.Stripe);
      return;
    }
    const script = document.createElement('script');
    script.src = 'https://js.stripe.com/v3/';
    script.onload = () => resolve(window.Stripe);
    script.onerror = reject;
    document.head.appendChild(script);
  });
};

const initStripePayment = async (pkg) => {
  isStripeLoading.value = true;
  stripeErrorMessage.value = '';
  try {
    const StripeSDK = await loadStripeScript();
    
    // Request PaymentIntent from backend for 50 AED
    const res = await window.axios.post('/api/adihex/payment-intent', {
      lead_id: leadId.value,
      package_id: pkg ? pkg.id : (selectedPackage.value?.id || 'golden'),
    });

    if (!res.data.success || !res.data.clientSecret) {
      throw new Error(res.data.message || 'Failed to initialize payment');
    }

    const pubKey = res.data.publishableKey || props.stripePublicKey;
    stripeInstance = StripeSDK(pubKey);

    const appearance = {
      theme: 'night',
      variables: {
        colorPrimary: '#ef4444',
        colorBackground: '#18181b',
        colorText: '#ffffff',
        colorDanger: '#ef4444',
        fontFamily: 'Outfit, Cairo, system-ui, sans-serif',
        borderRadius: '12px',
        spacingUnit: '4px',
      },
      rules: {
        '.Input': {
          border: '1px solid #27272a',
          backgroundColor: '#09090b',
        },
        '.Input:focus': {
          border: '1px solid #ef4444',
          boxShadow: '0 0 0 1px #ef4444',
        },
      },
    };

    stripeElements = stripeInstance.elements({
      clientSecret: res.data.clientSecret,
      appearance,
      locale: currentLocale.value === 'ar' ? 'ar' : 'en',
    });

    stripePaymentElement = stripeElements.create('payment', {
      layout: 'tabs',
    });

    await nextTick();
    const container = document.getElementById('stripe-payment-element-mount');
    if (container) {
      container.innerHTML = '';
      stripePaymentElement.mount(container);
    }
  } catch (err) {
    console.error('Stripe init error:', err);
    stripeErrorMessage.value = err.message || 'Unable to load payment gateway.';
  } finally {
    isStripeLoading.value = false;
  }
};

const handleStripeSubmit = async () => {
  sounds.init();
  if (!stripeInstance || !stripeElements) {
    return;
  }

  isSubmitting.value = true;
  stripeErrorMessage.value = '';

  try {
    const result = await stripeInstance.confirmPayment({
      elements: stripeElements,
      confirmParams: {
        return_url: window.location.origin + '/adihex',
      },
      redirect: 'if_required',
    });

    if (result.error) {
      stripeErrorMessage.value = result.error.message || 'Payment could not be authorized.';
      isSubmitting.value = false;
      return;
    }

    if (result.paymentIntent && (result.paymentIntent.status === 'succeeded' || result.paymentIntent.status === 'processing')) {
      const reserveRes = await window.axios.post('/api/adihex/reserve', {
        lead_id: leadId.value,
        package_id: selectedPackage.value ? selectedPackage.value.id : 'golden',
        action: 'pay',
        payment_method: 'Stripe',
        stripe_payment_id: result.paymentIntent.id,
      });

      if (reserveRes.data.whatsapp_url) {
        whatsappUrl.value = reserveRes.data.whatsapp_url;
      }

      depositPaid.value = true;
      currentStep.value = 7;
      triggerConfetti();
    }
  } catch (err) {
    console.error('Payment confirmation error:', err);
    stripeErrorMessage.value = err.message || 'Payment authorization failed.';
  } finally {
    isSubmitting.value = false;
  }
};

// ==========================================
// Ultra-Sharp Native HTML5 2D Canvas PDF Engine
// ==========================================
const isDownloadingPdf = ref(false);

const downloadPrizeCertificate = () => {
  if (isDownloadingPdf.value) return;
  isDownloadingPdf.value = true;

  try {
    const isAr = (currentLocale.value === 'ar');
    const width = 1240;
    const height = 1754; // A6 @ 300 DPI
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');

    // 1. Dark Carbon Background
    ctx.fillStyle = '#0d0d0f';
    ctx.fillRect(0, 0, width, height);

    // 2. Outer Luxury Gold Border
    ctx.strokeStyle = '#c5a059';
    ctx.lineWidth = 14;
    ctx.strokeRect(40, 40, width - 80, height - 80);

    // 3. Inner Gold Border
    ctx.lineWidth = 4;
    ctx.strokeRect(60, 60, width - 120, height - 120);

    // 4. Header Red Brand Name
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillStyle = '#ef4444';
    ctx.font = isAr ? '900 50px "Cairo", "Segoe UI", sans-serif' : '900 50px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'مركز فينينو للعناية بالسيارات' : 'VENENO AUTO CARE CENTER', width / 2, 170);

    // 5. Header Gold Subtitle
    ctx.fillStyle = '#c5a059';
    ctx.font = isAr ? 'bold 28px "Cairo", "Segoe UI", sans-serif' : 'bold 28px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'قسيمة الفائز الرسمية' : 'OFFICIAL WINNER VOUCHER', width / 2, 235);

    // 6. Gold Separator Line
    ctx.strokeStyle = '#c5a059';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(250, 275);
    ctx.lineTo(width - 250, 275);
    ctx.stroke();

    // 7. Customer Info Section
    ctx.fillStyle = '#a1a1aa';
    ctx.font = isAr ? '22px "Cairo", "Segoe UI", sans-serif' : '22px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'بيانات العميل' : 'CUSTOMER / CLIENT', width / 2, 340);

    ctx.fillStyle = '#ffffff';
    ctx.font = isAr ? 'bold 42px "Cairo", "Segoe UI", sans-serif' : 'bold 42px "Outfit", sans-serif';
    const clientName = registrationForm.value.name || (isAr ? 'عميل مميز' : 'VIP Client');
    ctx.fillText(clientName, width / 2, 400);

    ctx.fillStyle = '#d4d4d8';
    ctx.font = '28px monospace';
    const clientPhone = registrationForm.value.phone || '+971 50 123 4567';
    ctx.fillText(clientPhone, width / 2, 455);

    // Helper to draw rounded rectangle
    const drawRoundRect = (x, y, w, h, radius, fill, stroke, strokeColor) => {
      ctx.beginPath();
      if (typeof ctx.roundRect === 'function') {
        ctx.roundRect(x, y, w, h, radius);
      } else {
        ctx.rect(x, y, w, h);
      }
      if (fill) {
        ctx.fillStyle = fill;
        ctx.fill();
      }
      if (stroke) {
        ctx.strokeStyle = strokeColor || '#3f3f46';
        ctx.lineWidth = 2;
        ctx.stroke();
      }
    };

    // 8. Won Prize Card Box
    drawRoundRect(140, 520, width - 280, 250, 24, '#1f1f23', true, '#3f3f46');

    ctx.fillStyle = '#c5a059';
    ctx.font = isAr ? 'bold 26px "Cairo", "Segoe UI", sans-serif' : 'bold 26px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'مبروك! لقد فزت بـ' : 'CONGRATULATIONS! YOU WON', width / 2, 580);

    ctx.fillStyle = '#f59e0b';
    ctx.font = isAr ? '900 40px "Cairo", "Segoe UI", sans-serif' : '900 40px "Outfit", sans-serif';
    const wonPrizeTitle = wonPrize.value ? (isAr ? wonPrize.value.label_ar : wonPrize.value.label_en) : (isAr ? 'خصم 10% على التظليل' : '10% Off Window Tinting');
    ctx.fillText(wonPrizeTitle, width / 2, 655);

    ctx.fillStyle = '#a1a1aa';
    ctx.font = isAr ? '22px "Cairo", "Segoe UI", sans-serif' : '22px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'هدية حصرية لزوار جناح فينينو في أديهيكس 2026' : 'Exclusive Reward • Veneno Auto Care Center ADIHEX 2026', width / 2, 720);

    // 9. Official Voucher Code Box
    drawRoundRect(180, 810, width - 360, 200, 20, '#881337', true, '#ef4444');

    ctx.fillStyle = '#fecaca';
    ctx.font = isAr ? 'bold 24px "Cairo", "Segoe UI", sans-serif' : 'bold 24px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'كود القسيمة الرسمي' : 'OFFICIAL VOUCHER CODE', width / 2, 865);

    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 54px monospace';
    ctx.fillText(voucherCode.value, width / 2, 940);

    // 10. Package or Validity Section
    if (selectedPackage.value && depositPaid.value) {
      drawRoundRect(140, 1050, width - 280, 240, 20, '#18181b', true, '#27272a');

      ctx.fillStyle = '#c5a059';
      ctx.font = isAr ? 'bold 24px "Cairo", "Segoe UI", sans-serif' : 'bold 24px "Outfit", sans-serif';
      ctx.fillText(isAr ? 'باقة المعرض المحجوزة' : 'RESERVED SHOW PACKAGE', width / 2, 1105);

      ctx.fillStyle = '#ffffff';
      ctx.font = isAr ? 'bold 34px "Cairo", "Segoe UI", sans-serif' : 'bold 34px "Outfit", sans-serif';
      const pkgTitle = isAr ? selectedPackage.value.name_ar : selectedPackage.value.name_en;
      ctx.fillText(pkgTitle, width / 2, 1165);

      ctx.fillStyle = '#10b981';
      ctx.font = isAr ? 'bold 26px "Cairo", "Segoe UI", sans-serif' : 'bold 26px "Outfit", sans-serif';
      ctx.fillText(isAr ? 'عربون 500 درهم مدفوع (يُخصم 100% من الفاتورة)' : 'AED 500 Deposit Paid (100% Deductible)', width / 2, 1230);
    } else {
      ctx.fillStyle = '#d4d4d8';
      ctx.font = isAr ? '26px "Cairo", "Segoe UI", sans-serif' : '26px "Outfit", sans-serif';
      ctx.fillText(isAr ? 'صلاحية القسيمة: 60 يوماً من تاريخ الإصدار' : 'Validity: 60 Days from issue date', width / 2, 1090);

      ctx.fillStyle = '#71717a';
      ctx.font = isAr ? '22px "Cairo", "Segoe UI", sans-serif' : '22px "Outfit", sans-serif';
      ctx.fillText(isAr ? 'قابلة للاستخدام لدى مركز فينينو للعناية بالسيارات' : 'Redeemable at Veneno Auto Care Workshop Facility', width / 2, 1140);
    }

    // 11. Footer Section
    ctx.strokeStyle = '#27272a';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(180, 1380);
    ctx.lineTo(width - 180, 1380);
    ctx.stroke();

    ctx.fillStyle = '#71717a';
    ctx.font = isAr ? '20px "Cairo", "Segoe UI", sans-serif' : '20px "Outfit", sans-serif';
    ctx.fillText(isAr ? 'مركز فينينو للعناية بالسيارات — أبوظبي، مصفح M37' : 'Veneno Auto Care Center — Musaffah M43, Abu Dhabi, UAE', width / 2, 1440);
    ctx.fillText(isAr ? 'واتساب: 026344403 • الموقع: veneno.ae' : 'WhatsApp Concierge: +971 2 634 4403 • Website: veneno.ae', width / 2, 1485);

    ctx.font = '16px monospace';
    ctx.fillStyle = '#52525b';
    ctx.fillText('AUTH ID: ' + Math.random().toString(36).substring(2, 10).toUpperCase(), width / 2, 1540);

    // 12. Convert Canvas to A6 jsPDF
    const imgData = canvas.toDataURL('image/png');
    const doc = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: [105, 148], // A6
    });

    doc.addImage(imgData, 'PNG', 0, 0, 105, 148);
    const filename = isAr
      ? `قسيمة_فينينو_أديهيكس_${voucherCode.value}.pdf`
      : `VENENO_WINNER_VOUCHER_${voucherCode.value}.pdf`;
    doc.save(filename);
  } catch (err) {
    console.error('PDF generation error:', err);
  } finally {
    isDownloadingPdf.value = false;
  }
};

onMounted(() => {
  sounds.init();
  calculateCountdown();
  countdownTimer = setInterval(calculateCountdown, 1000);
});

onUnmounted(() => {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
  }
  if (countdownTimer) {
    clearInterval(countdownTimer);
  }
});
</script>

<template>
  <Head>
    <title>SCAN. SPIN. WIN. — Veneno Auto Care Center × ADIHEX 2026</title>
    <meta name="description" content="Exclusive ADIHEX 2026 digital activation and lead-to-reservation funnel for Veneno Auto Care Center. Scan at booth, spin the wheel, win luxury prizes.">
  </Head>

  <div
    :dir="t.dir"
    :class="[t.fontClass, 'min-h-screen bg-zinc-950 text-zinc-100 flex flex-col items-center relative overflow-x-hidden selection:bg-red-600 selection:text-white']"
  >
    <!-- Step 1 Ambient Video Background -->
    <div v-if="currentStep === 1" class="fixed inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute inset-0 bg-black/35 z-10"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent z-10"></div>
      
      <iframe
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120vw] h-[68vw] min-h-[110vh] min-w-[195vh] opacity-80 pointer-events-none border-0"
        src="https://www.youtube-nocookie.com/embed/I4cZjZX3S00?autoplay=1&mute=1&controls=0&loop=1&playlist=I4cZjZX3S00&playsinline=1&rel=0&showinfo=0&iv_load_policy=3&disablekb=1&modestbranding=1"
        title="Veneno ADIHEX Background"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      ></iframe>
    </div>

    <!-- Ambient Luxury Glow Backdrop (Steps 2 to 7) -->
    <div v-else class="fixed inset-0 pointer-events-none z-0">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-[450px] bg-gradient-to-b from-red-600/15 via-amber-500/5 to-transparent blur-3xl"></div>
      <div class="absolute bottom-0 right-0 w-96 h-96 bg-red-950/20 blur-3xl rounded-full"></div>
    </div>

    <!-- Expired Campaign Overlay Guard -->
    <div
      v-if="props.stats?.isExpired"
      class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center animate-in fade-in"
    >
      <div class="text-6xl mb-4">🏁</div>
      <h2 class="text-2xl font-bold text-amber-400 mb-2">{{ t.expiredTitle }}</h2>
      <p class="text-sm text-zinc-400 max-w-md mb-6 leading-relaxed">{{ t.expiredDesc }}</p>
      <a href="https://veneno.ae" class="px-6 py-3 rounded-xl bg-red-600 text-white font-bold text-sm shadow-xl shadow-red-600/30">
        {{ t.visitVeneno }}
      </a>
    </div>

    <!-- Main Container Frame (Responsive Tablet - Mobile Phone) -->
    <div :class="[currentStep === 1 ? 'min-h-[100dvh] h-[100dvh] py-2 px-3 sm:py-5 sm:px-6 overflow-hidden' : 'min-h-screen pt-6 pb-20 sm:pt-8 sm:pb-24 px-4 sm:px-8', 'w-full max-w-lg sm:max-w-xl md:max-w-2xl flex flex-col justify-between z-10 transition-all']">
      
      <!-- Top Branding Header (Visible on Steps 2 to 7) -->
      <header v-if="currentStep > 1" class="flex items-center justify-between py-2 sm:py-2.5 border-b border-zinc-800/80 mb-4 sm:mb-5 animate-in fade-in duration-300">
        <div class="flex items-center gap-2.5 sm:gap-3">
          <img src="/images/adihex/veneno-header-icon.png" alt="Veneno" class="h-12 w-12 sm:h-[60px] sm:w-[60px] object-contain drop-shadow-md rounded-xl sm:rounded-2xl" />
          <span class="text-zinc-700 text-lg sm:text-xl font-light select-none">|</span>
          <img src="/images/adihex/adihex-header-icon.png" alt="ADIHEX 2026" class="h-12 w-12 sm:h-[60px] sm:w-[60px] object-contain drop-shadow-md" />
        </div>

        <div class="flex items-center gap-2">
          <!-- Language Switcher with Name before the SVG Flag -->
          <button
            type="button"
            @click="setLanguage(currentLocale === 'en' ? 'ar' : 'en')"
            class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl sm:rounded-2xl bg-zinc-900/90 border border-zinc-800 hover:border-zinc-700 active:scale-95 transition-all shadow-sm flex items-center gap-2 cursor-pointer group"
            :title="currentLocale === 'en' ? 'العربية' : 'English'"
          >
            <span class="text-xs sm:text-sm font-bold text-zinc-200 group-hover:text-white transition-colors" :class="currentLocale === 'en' ? 'font-arabic' : 'font-sans'">
              {{ currentLocale === 'en' ? 'العربية' : 'English' }}
            </span>

            <!-- Show UAE Flag if in English to switch to Arabic -->
            <svg v-if="currentLocale === 'en'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 drop-shadow-sm" viewBox="0 0 256 256">
              <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                <path d="M 2.57 30 l 84.859 0 C 81.254 12.534 64.611 0.015 45.033 0 l -0.068 0 C 25.388 0.015 8.745 12.534 2.57 30 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,111,46); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 87.429 60 L 2.57 60 C 8.749 77.476 25.408 90 45 90 S 81.25 77.476 87.429 60 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 87.429 60 C 89.088 55.307 90 50.261 90 45 c 0 -5.261 -0.911 -10.307 -2.571 -15 L 2.57 30 C 0.911 34.693 0 39.739 0 45 c 0 5.261 0.912 10.307 2.571 15 L 87.429 60 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(243,244,245); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 30 87.429 l 0 -84.858 C 12.524 8.75 0 25.408 0 45 S 12.524 81.25 30 87.429 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(247,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
              </g>
            </svg>

            <!-- Show UK Flag if in Arabic to switch to English -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 drop-shadow-sm" viewBox="0 0 256 256">
              <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                <path d="M 88.35 57.052 c 0.034 -0.123 0.076 -0.243 0.109 -0.367 l -0.004 -0.002 C 89.457 52.957 90 49.043 90 45 c 0 -4.033 -0.54 -7.938 -1.538 -11.657 l 0.004 -0.002 c -0.039 -0.146 -0.088 -0.289 -0.128 -0.434 c -0.137 -0.492 -0.28 -0.982 -0.434 -1.468 c -0.081 -0.257 -0.167 -0.512 -0.253 -0.768 c -0.073 -0.217 -0.139 -0.437 -0.215 -0.653 h -0.015 c -1.645 -4.653 -4.021 -8.96 -7.01 -12.768 L 59.997 27.458 V 2.57 c -4.368 -1.544 -9.046 -2.427 -13.915 -2.542 h -2.164 c -4.868 0.115 -9.545 0.998 -13.913 2.541 v 24.889 L 9.589 17.249 c -2.989 3.809 -5.366 8.116 -7.01 12.769 H 2.564 c -0.076 0.216 -0.143 0.436 -0.216 0.653 c -0.086 0.255 -0.172 0.509 -0.253 0.765 c -0.154 0.486 -0.297 0.977 -0.434 1.47 c -0.04 0.145 -0.089 0.287 -0.128 0.432 l 0.004 0.002 C 0.54 37.061 0 40.966 0 45 c 0 4.043 0.543 7.957 1.545 11.684 l -0.004 0.002 c 0.033 0.123 0.074 0.242 0.108 0.365 c 0.146 0.524 0.298 1.046 0.462 1.562 c 0.075 0.236 0.154 0.47 0.233 0.705 c 0.077 0.231 0.148 0.464 0.229 0.693 H 2.59 c 1.647 4.651 4.025 8.955 7.016 12.761 l 20.4 -10.2 v 24.86 C 34.697 89.089 39.741 90 45 90 c 5.26 0 10.305 -0.911 14.997 -2.57 V 62.572 l 20.398 10.199 c 2.991 -3.806 5.368 -8.11 7.015 -12.76 h 0.015 c 0.081 -0.229 0.152 -0.463 0.23 -0.694 c 0.079 -0.234 0.158 -0.468 0.233 -0.704 C 88.052 58.096 88.205 57.575 88.35 57.052 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(243,244,245); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 53.999 0.902 c -2.565 -0.521 -5.213 -0.81 -7.917 -0.874 h -2.164 c -2.703 0.064 -5.35 0.354 -7.914 0.874 v 35.116 H 0.899 C 0.311 38.92 0 41.924 0 45 c 0 3.087 0.312 6.1 0.904 9.012 h 35.1 v 35.087 C 38.911 89.689 41.919 90 45 90 c 3.082 0 6.091 -0.311 8.999 -0.902 V 54.012 h 35.097 C 89.688 51.1 90 48.087 90 45 c 0 -3.076 -0.311 -6.08 -0.899 -8.983 H 53.999 V 0.902 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 85.242 65.135 c 0.829 -1.653 1.56 -3.363 2.184 -5.125 H 74.993 L 85.242 65.135 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 82.216 19.701 L 61.581 30.019 h 13.412 l 10.261 -5.131 C 84.353 23.088 83.341 21.354 82.216 19.701 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 4.747 24.887 c -0.829 1.655 -1.559 3.368 -2.182 5.132 H 15.01 L 4.747 24.887 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 7.8 70.321 L 28.422 60.01 H 15.01 L 4.758 65.136 C 5.661 66.936 6.674 68.67 7.8 70.321 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 9.589 17.249 l 20.416 10.208 v -3.99 V 2.584 C 21.874 5.458 14.813 10.593 9.589 17.249 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 59.997 2.585 v 22.302 v 2.57 L 80.411 17.25 C 75.188 10.594 68.128 5.459 59.997 2.585 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 30.006 72.77 V 62.572 l -20.4 10.2 c 5.222 6.646 12.276 11.774 20.4 14.646 V 72.77 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                <path d="M 59.997 62.572 v 9.296 v 15.548 c 8.123 -2.872 15.176 -8 20.398 -14.646 L 59.997 62.572 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
              </g>
            </svg>
          </button>
        </div>
      </header>

      <!-- Wizard Step Progress Bar (Visible on Steps 2 to 7, text removed) -->
      <div v-if="currentStep > 1" class="mb-4 sm:mb-5 animate-in fade-in duration-300">
        <div class="w-full h-1.5 rounded-full bg-zinc-900 overflow-hidden border border-zinc-800/80 shadow-inner">
          <div
            class="h-full bg-gradient-to-r from-red-600 via-red-500 to-amber-600 transition-all duration-500 rounded-full"
            :style="{ width: `${((currentStep - 1) / 6) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- ==========================================================
           STEP 1: LANDING & LANGUAGE SELECTION (RESPONSIVE NO-SCROLL LAYOUT)
           ========================================================== -->
      <div v-if="currentStep === 1" class="flex-1 flex flex-col justify-between items-center text-center w-full h-full my-auto py-1 sm:py-3 animate-in fade-in zoom-in-95 duration-400">
        
        <!-- Tier 1: Big & Clear Logos (Same Width): Veneno Top, ADIHEX Below -->
        <div class="flex flex-col items-center justify-center gap-2 xs:gap-2.5 sm:gap-4 w-full pt-1">
          <!-- Veneno White Text Logo -->
          <div class="w-full flex items-center justify-center">
            <img
              src="/images/adihex/veneno-brand-logo.png"
              alt="Veneno Auto Care Center"
              class="w-full max-w-[190px] xs:max-w-[220px] sm:max-w-[260px] md:max-w-[290px] h-auto object-contain drop-shadow-[0_4px_20px_rgba(255,255,255,0.15)]"
            />
          </div>

          <!-- ADIHEX Logo -->
          <div class="w-full flex items-center justify-center">
            <img
              src="/images/adihex/adihex-brand-logo.png"
              alt="ADIHEX 2026 Abu Dhabi"
              class="w-full max-w-[190px] xs:max-w-[220px] sm:max-w-[260px] md:max-w-[290px] h-auto object-contain drop-shadow-[0_4px_20px_rgba(255,255,255,0.15)]"
            />
          </div>
        </div>

        <!-- Tier 2: Luxury Handwriting Welcome Text & Language Selection -->
        <div class="space-y-1.5 xs:space-y-2 sm:space-y-3 max-w-xl mx-auto px-2 my-auto">
          <div class="font-script text-lg xs:text-xl sm:text-2xl md:text-3xl text-white font-normal tracking-wide drop-shadow-[0_2px_10px_rgba(255,255,255,0.3)] leading-tight">
            Welcome to Veneno Auto Care Center
          </div>

          <!-- SCAN. SPIN. WIN -->
          <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight uppercase leading-none">
            SCAN. <span class="text-red-500">SPIN.</span> <span class="text-amber-400">WIN</span>
          </h1>

          <!-- Subtitle Description -->
          <p class="text-[11px] xs:text-xs sm:text-sm text-zinc-300 max-w-[280px] xs:max-w-xs sm:max-w-md mx-auto leading-snug font-medium">
            Select your preferred language to begin your exclusive ADIHEX giveaway experience.
          </p>

          <!-- Language Buttons Beside Each Other (Compact & Elegant) -->
          <div class="grid grid-cols-2 gap-2.5 xs:gap-3 sm:gap-4 w-full max-w-[310px] xs:max-w-[340px] sm:max-w-[420px] mx-auto pt-1 sm:pt-2">
            <!-- English Button -->
            <button
              type="button"
              @click="selectLanguageAndAdvance('en')"
              class="group relative overflow-hidden py-2.5 px-3 sm:py-3.5 sm:px-4 rounded-xl sm:rounded-2xl bg-gradient-to-b from-zinc-900/95 via-zinc-900/90 to-zinc-950/95 hover:from-zinc-800 hover:to-zinc-900 border border-zinc-800/90 hover:border-red-500/80 active:scale-[0.97] transition-all duration-300 shadow-xl shadow-black/80 flex items-center justify-center gap-2 sm:gap-2.5 cursor-pointer backdrop-blur-md"
            >
              <div class="absolute inset-0 bg-gradient-to-tr from-red-600/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <!-- UK Flag SVG -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 flex-shrink-0 drop-shadow-md transition-transform group-hover:scale-105" viewBox="0 0 256 256">
                <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                  <path d="M 88.35 57.052 c 0.034 -0.123 0.076 -0.243 0.109 -0.367 l -0.004 -0.002 C 89.457 52.957 90 49.043 90 45 c 0 -4.033 -0.54 -7.938 -1.538 -11.657 l 0.004 -0.002 c -0.039 -0.146 -0.088 -0.289 -0.128 -0.434 c -0.137 -0.492 -0.28 -0.982 -0.434 -1.468 c -0.081 -0.257 -0.167 -0.512 -0.253 -0.768 c -0.073 -0.217 -0.139 -0.437 -0.215 -0.653 h -0.015 c -1.645 -4.653 -4.021 -8.96 -7.01 -12.768 L 59.997 27.458 V 2.57 c -4.368 -1.544 -9.046 -2.427 -13.915 -2.542 h -2.164 c -4.868 0.115 -9.545 0.998 -13.913 2.541 v 24.889 L 9.589 17.249 c -2.989 3.809 -5.366 8.116 -7.01 12.769 H 2.564 c -0.076 0.216 -0.143 0.436 -0.216 0.653 c -0.086 0.255 -0.172 0.509 -0.253 0.765 c -0.154 0.486 -0.297 0.977 -0.434 1.47 c -0.04 0.145 -0.089 0.287 -0.128 0.432 l 0.004 0.002 C 0.54 37.061 0 40.966 0 45 c 0 4.043 0.543 7.957 1.545 11.684 l -0.004 0.002 c 0.033 0.123 0.074 0.242 0.108 0.365 c 0.146 0.524 0.298 1.046 0.462 1.562 c 0.075 0.236 0.154 0.47 0.233 0.705 c 0.077 0.231 0.148 0.464 0.229 0.693 H 2.59 c 1.647 4.651 4.025 8.955 7.016 12.761 l 20.4 -10.2 v 24.86 C 34.697 89.089 39.741 90 45 90 c 5.26 0 10.305 -0.911 14.997 -2.57 V 62.572 l 20.398 10.199 c 2.991 -3.806 5.368 -8.11 7.015 -12.76 h 0.015 c 0.081 -0.229 0.152 -0.463 0.23 -0.694 c 0.079 -0.234 0.158 -0.468 0.233 -0.704 C 88.052 58.096 88.205 57.575 88.35 57.052 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(243,244,245); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 53.999 0.902 c -2.565 -0.521 -5.213 -0.81 -7.917 -0.874 h -2.164 c -2.703 0.064 -5.35 0.354 -7.914 0.874 v 35.116 H 0.899 C 0.311 38.92 0 41.924 0 45 c 0 3.087 0.312 6.1 0.904 9.012 h 35.1 v 35.087 C 38.911 89.689 41.919 90 45 90 c 3.082 0 6.091 -0.311 8.999 -0.902 V 54.012 h 35.097 C 89.688 51.1 90 48.087 90 45 c 0 -3.076 -0.311 -6.08 -0.899 -8.983 H 53.999 V 0.902 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 85.242 65.135 c 0.829 -1.653 1.56 -3.363 2.184 -5.125 H 74.993 L 85.242 65.135 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 82.216 19.701 L 61.581 30.019 h 13.412 l 10.261 -5.131 C 84.353 23.088 83.341 21.354 82.216 19.701 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 4.747 24.887 c -0.829 1.655 -1.559 3.368 -2.182 5.132 H 15.01 L 4.747 24.887 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 7.8 70.321 L 28.422 60.01 H 15.01 L 4.758 65.136 C 5.661 66.936 6.674 68.67 7.8 70.321 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(204,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 9.589 17.249 l 20.416 10.208 v -3.99 V 2.584 C 21.874 5.458 14.813 10.593 9.589 17.249 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 59.997 2.585 v 22.302 v 2.57 L 80.411 17.25 C 75.188 10.594 68.128 5.459 59.997 2.585 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 30.006 72.77 V 62.572 l -20.4 10.2 c 5.222 6.646 12.276 11.774 20.4 14.646 V 72.77 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 59.997 62.572 v 9.296 v 15.548 c 8.123 -2.872 15.176 -8 20.398 -14.646 L 59.997 62.572 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,102); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                </g>
              </svg>
              <span class="text-sm sm:text-base font-bold text-white group-hover:text-red-400 transition-colors tracking-wide">English</span>
            </button>

            <!-- Arabic Button -->
            <button
              type="button"
              @click="selectLanguageAndAdvance('ar')"
              class="group relative overflow-hidden py-2.5 px-3 sm:py-3.5 sm:px-4 rounded-xl sm:rounded-2xl bg-gradient-to-b from-zinc-900/95 via-zinc-900/90 to-zinc-950/95 hover:from-zinc-800 hover:to-zinc-900 border border-zinc-800/90 hover:border-amber-500/80 active:scale-[0.97] transition-all duration-300 shadow-xl shadow-black/80 flex items-center justify-center gap-2 sm:gap-2.5 cursor-pointer backdrop-blur-md"
            >
              <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <!-- UAE Flag SVG -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 flex-shrink-0 drop-shadow-md transition-transform group-hover:scale-105" viewBox="0 0 256 256">
                <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                  <path d="M 2.57 30 l 84.859 0 C 81.254 12.534 64.611 0.015 45.033 0 l -0.068 0 C 25.388 0.015 8.745 12.534 2.57 30 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,111,46); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 87.429 60 L 2.57 60 C 8.749 77.476 25.408 90 45 90 S 81.25 77.476 87.429 60 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 87.429 60 C 89.088 55.307 90 50.261 90 45 c 0 -5.261 -0.911 -10.307 -2.571 -15 L 2.57 30 C 0.911 34.693 0 39.739 0 45 c 0 5.261 0.912 10.307 2.571 15 L 87.429 60 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(243,244,245); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                  <path d="M 30 87.429 l 0 -84.858 C 12.524 8.75 0 25.408 0 45 S 12.524 81.25 30 87.429 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(247,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                </g>
              </svg>
              <span class="text-sm sm:text-base font-bold text-white font-arabic group-hover:text-amber-400 transition-colors tracking-wide">العربية</span>
            </button>
          </div>
        </div>

        <!-- Tier 3: In-Flow Countdown & Live Urgency Card (Never overlaps) -->
        <div class="w-full flex flex-col items-center gap-1.5 sm:gap-2 pt-1 pb-1">
          <!-- Flip Countdown Card -->
          <div class="w-full max-w-[290px] xs:max-w-[320px] sm:max-w-[350px] px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-zinc-950/90 border border-zinc-800/90 backdrop-blur-xl shadow-xl flex flex-col items-center">
            <div class="text-[9px] xs:text-[10px] sm:text-[11px] font-bold text-zinc-300 uppercase tracking-widest mb-1 sm:mb-1.5 flex items-center gap-1.5 font-mono">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
              <span>{{ currentLocale === 'ar' ? 'ينتهي في 6 سبتمبر 2026 • 10:00 مساءً' : 'ENDS IN 6 SEPT 2026 • 10:00 PM' }}</span>
            </div>

            <div class="grid grid-cols-4 gap-1.5 sm:gap-2 w-full">
              <!-- Days -->
              <div class="flex flex-col items-center">
                <div class="w-full h-8 xs:h-9 sm:h-11 rounded-lg bg-gradient-to-b from-[#2a2a2e] to-[#141416] border border-zinc-700/80 shadow-md relative flex items-center justify-center overflow-hidden">
                  <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-[1px] bg-black/90"></div>
                  <span class="text-sm xs:text-base sm:text-xl font-black text-zinc-100 font-mono z-10">{{ countdown.days }}</span>
                </div>
                <span class="text-[7px] xs:text-[8px] sm:text-[9px] font-bold text-amber-400 uppercase tracking-wider mt-0.5">{{ currentLocale === 'ar' ? 'أيام' : 'DAYS' }}</span>
              </div>

              <!-- Hours -->
              <div class="flex flex-col items-center">
                <div class="w-full h-8 xs:h-9 sm:h-11 rounded-lg bg-gradient-to-b from-[#2a2a2e] to-[#141416] border border-zinc-700/80 shadow-md relative flex items-center justify-center overflow-hidden">
                  <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-[1px] bg-black/90"></div>
                  <span class="text-sm xs:text-base sm:text-xl font-black text-zinc-100 font-mono z-10">{{ countdown.hours }}</span>
                </div>
                <span class="text-[7px] xs:text-[8px] sm:text-[9px] font-bold text-amber-400 uppercase tracking-wider mt-0.5">{{ currentLocale === 'ar' ? 'ساعات' : 'HOURS' }}</span>
              </div>

              <!-- Minutes -->
              <div class="flex flex-col items-center">
                <div class="w-full h-8 xs:h-9 sm:h-11 rounded-lg bg-gradient-to-b from-[#2a2a2e] to-[#141416] border border-zinc-700/80 shadow-md relative flex items-center justify-center overflow-hidden">
                  <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-[1px] bg-black/90"></div>
                  <span class="text-sm xs:text-base sm:text-xl font-black text-zinc-100 font-mono z-10">{{ countdown.minutes }}</span>
                </div>
                <span class="text-[7px] xs:text-[8px] sm:text-[9px] font-bold text-amber-400 uppercase tracking-wider mt-0.5">{{ currentLocale === 'ar' ? 'دقائق' : 'MINS' }}</span>
              </div>

              <!-- Seconds -->
              <div class="flex flex-col items-center">
                <div class="w-full h-8 xs:h-9 sm:h-11 rounded-lg bg-gradient-to-b from-[#2a2a2e] to-[#141416] border border-zinc-700/80 shadow-md relative flex items-center justify-center overflow-hidden">
                  <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-[1px] bg-black/90"></div>
                  <span class="text-sm xs:text-base sm:text-xl font-black text-amber-400 font-mono z-10">{{ countdown.seconds }}</span>
                </div>
                <span class="text-[7px] xs:text-[8px] sm:text-[9px] font-bold text-amber-400 uppercase tracking-wider mt-0.5">{{ currentLocale === 'ar' ? 'ثواني' : 'SECS' }}</span>
              </div>
            </div>
          </div>

          <!-- Live Urgency Pill -->
          <div class="px-2.5 py-0.5 rounded-full bg-zinc-900/90 border border-zinc-800/90 backdrop-blur-md shadow-lg flex items-center gap-1.5 text-[9.5px] xs:text-[10px] sm:text-[11px] text-zinc-300 font-mono">
            <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-ping"></div>
            <span>🔴 Live: <strong class="text-amber-400 font-bold">{{ liveSpinCount }}</strong> {{ t.liveSpunText }}</span>
          </div>
        </div>

      </div>

      <!-- ==========================================================
           STEP 2: FAST REGISTRATION & LEAD CAPTURE
           ========================================================== -->
      <div v-if="currentStep === 2" class="flex-1 flex flex-col justify-center space-y-5 animate-in fade-in slide-in-from-bottom-3 duration-300">
        <div class="space-y-1">
          <h2 class="text-2xl font-black text-white">{{ t.s2_title }}</h2>
        </div>

        <form @submit.prevent="handleRegistration" class="space-y-3.5">
          <!-- Full Name -->
          <div>
            <label class="block text-[11px] font-mono uppercase text-zinc-400 mb-1 font-semibold">{{ t.fullName }}</label>
            <input
              v-model="registrationForm.name"
              type="text"
              required
              :placeholder="t.namePlaceholder"
              class="w-full px-4 py-3 rounded-2xl bg-zinc-900/90 border border-zinc-800 text-white placeholder-zinc-600 focus:outline-none focus:border-red-500 text-sm font-medium shadow-inner"
            />
          </div>

          <!-- Mobile Phone with International Code Dropdown -->
          <div>
            <label class="block text-[11px] font-mono uppercase text-zinc-400 mb-1 font-semibold">{{ t.mobile }}</label>
            <div class="flex rounded-2xl bg-zinc-900/90 border border-zinc-800 focus-within:border-red-500 transition-colors shadow-inner overflow-hidden" dir="ltr">
              <!-- International Dial Code Dropdown -->
              <div class="relative flex items-center bg-zinc-800/60 border-r border-zinc-800 flex-shrink-0">
                <select
                  v-model="countryCode"
                  class="appearance-none bg-transparent pl-3 pr-7 py-3 text-xs sm:text-sm font-mono font-medium text-white focus:outline-none cursor-pointer"
                >
                  <option
                    v-for="c in countryCodes"
                    :key="c.code"
                    :value="c.code"
                    class="bg-zinc-900 text-white font-mono"
                  >
                    {{ c.code }} ({{ currentLocale === 'ar' ? c.nameAr : c.nameEn }})
                  </option>
                </select>
                <ChevronDown class="w-3.5 h-3.5 text-zinc-400 absolute right-2 pointer-events-none" />
              </div>

              <!-- Phone Number Input with UAE Default Sample Placeholder -->
              <input
                v-model="registrationForm.phone"
                type="tel"
                required
                :placeholder="currentPhonePlaceholder"
                class="w-full px-3.5 py-3 bg-transparent text-white placeholder-zinc-600 focus:outline-none text-sm font-mono tracking-wider"
              />
            </div>
          </div>

          <!-- Email (Optional) -->
          <div>
            <label class="block text-[11px] font-mono uppercase text-zinc-400 mb-1 font-semibold">{{ t.email }}</label>
            <input
              v-model="registrationForm.email"
              type="email"
              :placeholder="t.emailPlaceholder"
              class="w-full px-4 py-2.5 rounded-2xl bg-zinc-900/90 border border-zinc-800 text-white placeholder-zinc-600 focus:outline-none focus:border-red-500 text-xs shadow-inner"
            />
          </div>

          <!-- Service Intent Multi-Select -->
          <div class="pt-1">
            <label class="block text-[11px] font-mono uppercase text-zinc-400 mb-2 font-semibold">{{ t.serviceIntentTitle }}</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                v-for="srv in serviceOptions"
                :key="srv.id"
                type="button"
                @click="toggleService(srv.id)"
                :class="[
                  'px-3 py-2 rounded-xl border text-xs font-semibold flex items-center gap-1.5 transition-all text-left',
                  registrationForm.service_intent.includes(srv.id)
                    ? 'bg-red-950/40 border-red-500 text-white shadow-md shadow-red-950/30'
                    : 'bg-zinc-900/60 border-zinc-800 text-zinc-400 hover:border-zinc-700'
                ]"
              >
                <component :is="srv.icon" :class="['w-3.5 h-3.5', registrationForm.service_intent.includes(srv.id) ? 'text-red-400' : 'text-zinc-500']" />
                <span class="truncate">{{ currentLocale === 'ar' ? srv.label_ar : srv.label_en }}</span>
              </button>
            </div>
          </div>

          <!-- Terms & Conditions Consent Checkbox (Step 2) -->
          <div class="pt-1 flex items-start gap-2.5 text-start">
            <input
              id="terms-checkbox"
              v-model="termsAccepted"
              type="checkbox"
              class="mt-0.5 w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-amber-500 focus:ring-amber-500/30 focus:ring-offset-0 cursor-pointer shrink-0 accent-amber-500"
            />
            <label for="terms-checkbox" class="text-[11px] sm:text-xs text-zinc-300 leading-snug cursor-pointer select-none">
              <span>{{ t.termsAgree1 }} </span>
              <button
                type="button"
                @click.stop="showTermsModal = true"
                class="text-amber-400 hover:text-amber-300 underline underline-offset-2 font-bold cursor-pointer inline transition-colors"
              >
                {{ t.termsLink }}
              </button>
              <span> {{ t.termsAgree2 }}</span>
            </label>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isSubmitting || !termsAccepted"
            class="w-full mt-2 py-3.5 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 hover:from-red-500 hover:to-amber-500 text-white font-bold text-sm uppercase tracking-wider shadow-xl shadow-red-600/30 flex items-center justify-center gap-2 transition-all active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
          >
            <span v-if="!isSubmitting">{{ t.btnSpinNow }}</span>
            <span v-else class="animate-pulse">{{ t.spinning }}</span>
          </button>
        </form>
      </div>

      <!-- ==========================================================
           STEP 3: 60 FPS CANVAS SPIN WHEEL
           ========================================================== -->
      <div v-if="currentStep === 3" class="flex-1 flex flex-col items-center justify-center space-y-4 animate-in fade-in zoom-in-95 duration-300">
        <div class="text-center space-y-1">
          <h2 class="text-xl sm:text-2xl font-black text-white">{{ t.s3_title }}</h2>
          <p class="text-xs text-zinc-400 max-w-xs mx-auto">{{ t.s3_desc }}</p>
        </div>

        <!-- Wheel Canvas Relative Container (Enlarged from 310px to 400px) -->
        <div class="relative w-[360px] h-[360px] sm:w-[400px] sm:h-[400px] max-w-[94vw] max-h-[94vw] aspect-square flex items-center justify-center my-3">
          <!-- Outer Ambient Glow Ring -->
          <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-red-600/35 via-amber-500/25 to-transparent blur-2xl"></div>

          <!-- Top Pointer Arrow -->
          <div class="absolute -top-3.5 sm:-top-4 left-1/2 -translate-x-1/2 z-30 flex flex-col items-center pointer-events-none drop-shadow-[0_4px_10px_rgba(0,0,0,0.9)]">
            <div class="w-0 h-0 border-l-[14px] border-l-transparent border-r-[14px] border-r-transparent border-t-[26px] border-t-amber-400"></div>
          </div>

          <!-- HTML5 Canvas -->
          <canvas
            ref="wheelCanvas"
            class="w-full h-full rounded-full cursor-pointer z-10 drop-shadow-2xl"
            @click="triggerSpin"
          ></canvas>

          <!-- Center Interactive Spin Button -->
          <button
            type="button"
            @click="triggerSpin"
            :disabled="isSpinning"
            class="absolute z-20 w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-amber-400 via-amber-600 to-amber-700 text-zinc-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-2xl flex flex-col items-center justify-center border-2 border-white transition-transform active:scale-90 hover:scale-105 disabled:pointer-events-none cursor-pointer"
          >
            <Sparkles class="w-4 h-4 sm:w-5 sm:h-5 text-zinc-900 mb-0.5 animate-bounce" />
            <span>{{ isSpinning ? t.spinning : t.btnSpinWheel }}</span>
          </button>
        </div>

        <!-- Tap to Spin Helper -->
        <p class="text-[11px] font-mono text-zinc-500 uppercase tracking-widest animate-pulse">
          ⚡ TAP WHEEL OR CENTER BUTTON TO SPIN
        </p>
      </div>

      <!-- ==========================================================
           STEP 4: INSTANT PRIZE REVEAL & SECURED NOTICE
           ========================================================== -->
      <div v-if="currentStep === 4" class="flex-1 flex flex-col justify-center space-y-5 sm:space-y-6 animate-in fade-in zoom-in-95 duration-300 text-center max-w-md mx-auto w-full">
        
        <!-- Celebration Badge -->
        <div class="relative mx-auto">
          <div class="absolute inset-0 rounded-full bg-amber-500/30 blur-xl animate-pulse"></div>
          <div class="relative w-20 h-20 rounded-full bg-gradient-to-tr from-amber-600/30 via-zinc-900 to-amber-400/20 border-2 border-amber-400/60 text-amber-400 flex items-center justify-center shadow-2xl shadow-amber-950/60">
            <Gift class="w-9 h-9 animate-bounce" />
          </div>
        </div>

        <!-- Won Prize Announcement Card -->
        <div class="space-y-2">
          <span class="text-xs font-mono uppercase tracking-widest text-amber-400 font-bold">🎉 {{ t.s4_mabrook }}</span>
          <div class="p-4 sm:p-5 rounded-2xl bg-zinc-900/90 border border-zinc-800/90 shadow-xl space-y-1.5">
            <h2 class="text-2xl sm:text-3xl font-black text-white">
              {{ wonPrize ? (currentLocale === 'ar' ? wonPrize.label_ar : wonPrize.label_en) : 'Free Diamond Car Wash' }}
            </h2>
            <p
              v-if="wonPrize && (currentLocale === 'ar' ? wonPrize.value_ar : wonPrize.value_en)"
              class="text-xs sm:text-sm font-medium text-amber-300"
            >
              {{ currentLocale === 'ar' ? wonPrize.value_ar : wonPrize.value_en }}
            </p>
          </div>
        </div>

        <!-- Secured Voucher Reassurance Notice (No code displayed now) -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-br from-zinc-900/90 via-zinc-900/70 to-red-950/20 border border-zinc-800 text-center space-y-1.5 shadow-md">
          <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-zinc-200">
            <Lock class="w-3.5 h-3.5 text-amber-400" />
            <span>{{ t.s4_secured_title }}</span>
          </div>
          <p class="text-[11px] sm:text-xs text-zinc-400 leading-relaxed max-w-xs mx-auto">
            {{ t.s4_secured_desc }}
          </p>
        </div>

        <!-- Primary Next Step CTA Button (Style matching Step 2 Form button) -->
        <div class="pt-1 w-full">
          <button
            type="button"
            @click="currentStep = 5"
            class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 hover:from-red-500 hover:to-amber-500 text-white font-bold text-sm uppercase tracking-wider shadow-xl shadow-red-600/30 flex items-center justify-center gap-2 transition-all active:scale-[0.98] cursor-pointer"
          >
            <span>{{ t.btnSeeOffers }}</span>
          </button>
        </div>
      </div>

      <!-- ==========================================================
           STEP 5: EXCLUSIVE ADIHEX SHOW PACKAGES
           ========================================================== -->
      <div v-if="currentStep === 5" class="flex-1 flex flex-col space-y-5 animate-in fade-in slide-in-from-bottom-3 duration-300 w-full py-1 pb-16 sm:pb-20">
        <!-- Step Header -->
        <div class="space-y-1.5 text-center sm:text-start">
          <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ t.s5_title }}</h2>
          <p class="text-xs sm:text-sm text-zinc-400 leading-relaxed max-w-xl">{{ t.s5_desc }}</p>
        </div>

        <!-- Package Cards List (Natural Page Flow, No Nested Scroll Container) -->
        <div class="space-y-4 w-full">
          <div
            v-for="pkg in props.packages"
            :key="pkg.id"
            class="group overflow-hidden rounded-3xl bg-gradient-to-b from-zinc-900/95 via-zinc-900/90 to-zinc-950 border border-zinc-800/90 hover:border-amber-500/60 transition-all duration-300 shadow-2xl hover:shadow-black/70 flex flex-col relative"
          >
            <!-- Top Image Banner with Luxury Overlays (Top Badges Removed) -->
            <div class="relative w-full h-40 xs:h-44 sm:h-52 overflow-hidden bg-zinc-950">
              <img
                :src="pkg.image"
                :alt="currentLocale === 'ar' ? pkg.name_ar : pkg.name_en"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                loading="lazy"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-black/30"></div>

              <!-- Price & Savings Float (Vertically Centered Alignment) -->
              <div class="absolute bottom-2.5 inset-x-3 flex items-center justify-between gap-2 z-10">
                <div class="px-3 py-1.5 rounded-xl bg-black/85 backdrop-blur-md border border-zinc-700/80 shadow-md flex items-center gap-2">
                  <span class="text-base sm:text-lg font-black text-amber-400 font-mono leading-none">AED {{ Number(pkg.promo_price).toLocaleString() }}</span>
                  <span class="text-[11px] sm:text-xs line-through text-zinc-400 font-mono leading-none">AED {{ Number(pkg.original_price).toLocaleString() }}</span>
                </div>

                <!-- Savings Tag (Vertically Aligned with Price Tag) -->
                <div class="px-3 py-1.5 rounded-xl bg-red-600/95 backdrop-blur-md text-white text-[10.5px] sm:text-[11px] font-mono font-bold shadow-md leading-none flex items-center shrink-0">
                  {{ currentLocale === 'ar' ? `وفر ${Number(pkg.original_price - pkg.promo_price).toLocaleString()} د.إ` : `Save AED ${Number(pkg.original_price - pkg.promo_price).toLocaleString()}` }}
                </div>
              </div>
            </div>

            <!-- Card Body Content -->
            <div class="p-3.5 sm:p-4.5 flex flex-col justify-between space-y-3">
              <div class="space-y-1">
                <h3 class="text-base sm:text-lg font-black text-white tracking-tight leading-snug">
                  {{ currentLocale === 'ar' ? pkg.name_ar : pkg.name_en }}
                </h3>
                <p class="text-xs text-zinc-300 leading-relaxed font-normal">
                  {{ currentLocale === 'ar' ? pkg.desc_ar : pkg.desc_en }}
                </p>
              </div>

              <!-- Deposit CTA Button -->
              <button
                type="button"
                @click="selectPackage(pkg)"
                class="w-full py-3 px-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 hover:from-red-500 hover:to-amber-500 active:scale-[0.98] text-white font-bold text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-red-950/50 flex items-center justify-center gap-2 transition-all cursor-pointer group-hover:brightness-105"
              >
                <span>{{ t.reserveFor50 }}</span>
                <ChevronRight class="w-4 h-4 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================================
           STEP 6: AED 50 RESERVATION & STRIPE CHECKOUT
           ========================================================== -->
      <div v-if="currentStep === 6" class="flex-1 flex flex-col justify-center space-y-4 animate-in fade-in zoom-in-95 duration-300 max-w-md mx-auto w-full">
        <div class="space-y-1 text-center">
          <h2 class="text-xl sm:text-2xl font-black text-white">{{ t.s6_title }}</h2>
          <p class="text-xs text-zinc-400">
            {{ t.s6_locking }} <strong class="text-amber-400">{{ selectedPackage ? (currentLocale === 'ar' ? selectedPackage.name_ar : selectedPackage.name_en) : 'Package' }}</strong>
          </p>
        </div>

        <!-- Deposit Summary Card -->
        <div class="p-4 rounded-2xl bg-zinc-900/90 border border-zinc-800 space-y-2 shadow-lg">
          <div class="flex items-center justify-between">
            <span class="text-xs text-zinc-400">{{ t.depositDue }}</span>
            <span class="text-xl font-black text-emerald-400 font-mono">AED 500.00</span>
          </div>
          <p class="text-[10.5px] text-zinc-400 border-t border-zinc-800 pt-1.5 leading-snug">
            {{ t.deductibleNote }}
          </p>
        </div>

        <!-- Stripe Payment Element Mount -->
        <div class="rounded-2xl bg-zinc-900/90 border border-zinc-800 p-3.5 sm:p-4 shadow-xl space-y-3">
          <div v-if="isStripeLoading" class="flex flex-col items-center justify-center py-8 text-zinc-400 space-y-2">
            <div class="w-6 h-6 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-xs font-mono">{{ currentLocale === 'ar' ? 'جاري تحميل بوابة الدفع الآمن...' : 'Loading Secure Stripe Gateway...' }}</span>
          </div>

          <div id="stripe-payment-element-mount" :class="{ 'hidden': isStripeLoading }"></div>
        </div>

        <!-- Stripe Error Alert -->
        <div v-if="stripeErrorMessage" class="p-3 rounded-xl bg-red-950/80 border border-red-800 text-red-300 text-xs flex items-center gap-2">
          <AlertCircle class="w-4 h-4 shrink-0 text-red-400" />
          <span>{{ stripeErrorMessage }}</span>
        </div>

        <!-- Deposit Terms & Conditions Consent Checkbox (Step 6) -->
        <div class="p-3 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-start gap-2.5 text-start">
          <input
            id="deposit-terms-checkbox"
            v-model="depositTermsAccepted"
            type="checkbox"
            class="mt-0.5 w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-amber-500 focus:ring-amber-500/30 focus:ring-offset-0 cursor-pointer shrink-0 accent-amber-500"
          />
          <label for="deposit-terms-checkbox" class="text-[11px] sm:text-xs text-zinc-300 leading-snug cursor-pointer select-none">
            <span>{{ t.depositTermsAgree1 }} </span>
            <button
              type="button"
              @click.stop="showTermsModal = true"
              class="text-amber-400 hover:text-amber-300 underline underline-offset-2 font-bold cursor-pointer inline transition-colors"
            >
              {{ t.depositTermsLink }}
            </button>
            <span> {{ t.depositTermsAgree2 }}</span>
          </label>
        </div>

        <!-- Confirm and Pay Button (Styled like Step 2 form button) -->
        <button
          type="button"
          @click="handleStripeSubmit"
          :disabled="isSubmitting || isStripeLoading || !depositTermsAccepted"
          class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 hover:from-red-500 hover:to-amber-500 text-white font-bold text-sm uppercase tracking-wider shadow-xl shadow-red-600/30 flex items-center justify-center gap-2 transition-all active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
        >
          <span v-if="!isSubmitting">{{ t.btnConfirmPay50 }}</span>
          <span v-else class="flex items-center gap-2 animate-pulse">
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <span>{{ currentLocale === 'ar' ? 'جاري تأكيد الدفع...' : 'Processing Payment...' }}</span>
          </span>
        </button>

        <!-- Back to Offers Link -->
        <button
          type="button"
          @click="currentStep = 5"
          class="w-full text-center text-xs text-zinc-500 hover:text-zinc-300 transition-colors py-1 cursor-pointer"
        >
          {{ t.btnBackOffers }}
        </button>

        <div class="text-center text-[10px] text-zinc-500 flex items-center justify-center gap-1.5 pt-1">
          <Lock class="w-3 h-3 text-emerald-400" />
          <span>{{ t.stripeSecureNote }}</span>
        </div>
      </div>

      <!-- ==========================================================
           STEP 7: CONFIRMATION & MULTI-CHANNEL DELIVERY
           ========================================================== -->
      <div v-if="currentStep === 7" class="flex-1 flex flex-col justify-center space-y-4 animate-in fade-in zoom-in-95 duration-300">
        <div class="text-center space-y-1">
          <div class="w-14 h-14 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center mx-auto shadow-xl shadow-emerald-950">
            <Check class="w-7 h-7" />
          </div>
          <h2 class="text-2xl font-black text-white mt-2">
            {{ t.s7_title1 }} <span class="text-amber-400">{{ t.s7_title2 }}</span>
          </h2>
          <p class="text-xs text-zinc-400 leading-relaxed">{{ t.s7_desc }}</p>
        </div>

        <!-- Confirmation Pass Card -->
        <div class="p-4 rounded-2xl bg-zinc-900/90 border border-zinc-800 space-y-2.5 text-xs font-mono shadow-xl">
          <div class="flex items-center justify-between text-zinc-400">
            <span>{{ t.summary_name }}</span>
            <span class="text-white font-bold">{{ registrationForm.name || (currentLocale === 'ar' ? 'ضيف مميز' : 'VIP Guest') }}</span>
          </div>

          <div class="flex items-center justify-between text-zinc-400">
            <span>{{ t.summary_phone }}</span>
            <span class="text-white font-mono">{{ registrationForm.phone }}</span>
          </div>

          <div class="flex items-center justify-between text-zinc-400">
            <span>{{ t.summary_prize }}</span>
            <span class="text-amber-400 font-bold">{{ wonPrize ? (currentLocale === 'ar' ? wonPrize.label_ar : wonPrize.label_en) : (currentLocale === 'ar' ? 'غسيل دايموند مجاني' : 'Free Diamond Car Wash') }}</span>
          </div>

          <div class="flex items-center justify-between text-zinc-400 border-t border-zinc-800 pt-2">
            <span>{{ t.summary_code }}</span>
            <span class="text-red-400 font-bold font-mono tracking-wider">{{ voucherCode }}</span>
          </div>

          <div v-if="selectedPackage && depositPaid" class="flex items-center justify-between text-zinc-400">
            <span>{{ t.summary_pkg }}</span>
            <span class="text-amber-300 font-bold truncate max-w-[180px]">{{ currentLocale === 'ar' ? selectedPackage.name_ar : selectedPackage.name_en }}</span>
          </div>

          <div v-if="depositPaid" class="flex items-center justify-between text-zinc-400">
            <span>{{ t.summary_paid }}</span>
            <span class="text-emerald-400 font-bold">AED 500.00 ({{ currentLocale === 'ar' ? 'يُخصم بالكامل' : '100% Deductible' }})</span>
          </div>

          <div class="flex items-center justify-between text-zinc-400 border-t border-zinc-800 pt-2 text-[10px]">
            <span>{{ t.summary_valid }}</span>
            <span class="text-zinc-300">{{ t.validity_90_days }}</span>
          </div>
        </div>

        <!-- 1-Tap WhatsApp Concierge CTA -->
        <a
          :href="whatsappUrl"
          target="_blank"
          class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2.5 shadow-lg shadow-emerald-950 transition-all active:scale-[0.98]"
        >
          <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"></path></svg>
          <span>{{ t.btnWhatsApp }}</span>
        </a>

        <!-- Download Luxury Pass PDF -->
        <button
          type="button"
          @click="downloadPrizeCertificate"
          :disabled="isDownloadingPdf"
          class="w-full py-3 rounded-2xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 text-amber-300 font-bold text-xs flex items-center justify-center gap-2 transition-colors shadow-md disabled:opacity-50 cursor-pointer"
        >
          <div v-if="isDownloadingPdf" class="w-3.5 h-3.5 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></div>
          <Download v-else class="w-4 h-4" />
          <span>{{ isDownloadingPdf ? (currentLocale === 'ar' ? 'جاري تحضير ملف PDF...' : 'Generating PDF Pass...') : t.btnDownloadPdf }}</span>
        </button>

        <!-- Google Maps Location -->
        <a
          href="https://maps.google.com/?q=Veneno+Auto+Care+Musaffah+M37+Abu+Dhabi"
          target="_blank"
          class="w-full py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-zinc-400 hover:text-white text-xs flex items-center justify-center gap-1.5 transition-colors"
        >
          <MapPin class="w-3.5 h-3.5 text-red-500" />
          <span>{{ t.btnDirections }}</span>
        </a>

        <!-- Official Veneno Website Link -->
        <a
          href="/"
          class="w-full py-2.5 rounded-xl bg-gradient-to-r from-zinc-900 via-zinc-900 to-zinc-950 hover:border-amber-500/50 border border-zinc-800 text-zinc-300 hover:text-amber-400 text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-md"
        >
          <Globe class="w-3.5 h-3.5 text-amber-400" />
          <span>{{ t.btnVisitWebsite }}</span>
        </a>
      </div>

    </div>

    <!-- Persistent Floating Live Urgency Container (Step 2 only) -->
    <div v-if="currentStep === 2" class="fixed bottom-2.5 sm:bottom-4 z-30 flex flex-col items-center gap-2 px-3 w-full max-w-sm sm:max-w-md pointer-events-none">
      <div class="pointer-events-auto px-3.5 py-1 rounded-full bg-zinc-900/90 border border-zinc-800/90 backdrop-blur-md shadow-xl flex items-center gap-2 text-[10.5px] sm:text-[11px] text-zinc-300 font-mono">
        <div class="w-2 h-2 rounded-full bg-red-500 animate-ping"></div>
        <span>🔴 Live: <strong class="text-amber-400 font-bold">{{ liveSpinCount }}</strong> {{ t.liveSpunText }}</span>
      </div>
    </div>

    <!-- Persistent Fixed Bottom Footer for Step 5 (Skip to Free Prize Only - 25px Bottom Margin) -->
    <div
      v-if="currentStep === 5"
      class="fixed bottom-[25px] inset-x-0 z-50 flex items-center justify-center px-4 pointer-events-none"
    >
      <div class="pointer-events-auto w-full max-w-sm sm:max-w-md bg-zinc-950/90 backdrop-blur-md border border-zinc-800/90 rounded-2xl py-2.5 px-4 flex items-center justify-center shadow-[0_8px_30px_rgba(0,0,0,0.85)]">
        <button
          type="button"
          @click="skipToConfirmation"
          :disabled="isSubmitting"
          class="w-full text-xs sm:text-sm font-semibold text-zinc-300 hover:text-white active:text-amber-300 transition-all underline underline-offset-4 decoration-zinc-600 hover:decoration-amber-400 cursor-pointer active:scale-95 text-center py-1 disabled:opacity-50"
        >
          <span>{{ t.btnSkipPackages }}</span>
        </button>
      </div>
    </div>

    <!-- Already Participated Blocking Alert Modal -->
    <div
      v-if="alreadyParticipatedModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md animate-in fade-in duration-300"
    >
      <div
        class="w-full max-w-sm sm:max-w-md bg-gradient-to-b from-[#1c1c20] to-[#0c0c0e] border border-red-500/40 rounded-3xl p-5 sm:p-6 shadow-2xl shadow-red-950/80 text-center space-y-4 relative overflow-hidden"
        :dir="currentLocale === 'ar' ? 'rtl' : 'ltr'"
      >
        <!-- Ambient Red Top Accent -->
        <div class="absolute -top-12 inset-x-0 h-24 bg-red-600/20 blur-2xl pointer-events-none"></div>

        <!-- Warning Icon -->
        <div class="w-16 h-16 rounded-full bg-red-950/80 border border-red-500/50 text-red-500 flex items-center justify-center mx-auto shadow-lg shadow-red-950">
          <AlertCircle class="w-8 h-8" />
        </div>

        <!-- Title -->
        <div class="space-y-1">
          <span class="text-[10.5px] font-mono font-bold uppercase tracking-widest text-red-400">
            {{ currentLocale === 'ar' ? 'تنبيه المشاركة' : 'PARTICIPATION NOTICE' }}
          </span>
          <h3 class="text-xl sm:text-2xl font-black text-white">
            {{ currentLocale === 'ar' ? alreadyParticipatedData?.title_ar || 'هذا الرقم مسجل مسبقاً' : alreadyParticipatedData?.title_en || 'Phone Number Already Registered' }}
          </h3>
        </div>

        <!-- Explanatory Box & SMS Proof Requirement -->
        <div class="p-4 rounded-2xl bg-zinc-950/80 border border-zinc-800/80 text-start space-y-2.5">
          <p class="text-xs text-zinc-300 leading-relaxed font-medium">
            {{ currentLocale === 'ar'
              ? 'لقد شاركت مسبقاً في سحب أديهيكس 2026. يحق لكل زائر فرصة مشاركة واحدة فقط عبر رقم هاتفه.'
              : 'You have already participated in the ADIHEX 2026 spin. Each visitor is eligible for 1 chance only per phone number.' }}
          </p>

          <div class="p-3 rounded-xl bg-red-950/40 border border-red-500/30 text-red-200 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-amber-300">
              <span>📱</span>
              <span>{{ currentLocale === 'ar' ? 'إثبات استلام الجائزة عبر SMS:' : 'Proof of Prize Claim (SMS):' }}</span>
            </div>
            <p class="text-[11px] leading-relaxed text-zinc-300">
              {{ currentLocale === 'ar'
                ? 'تم إرسال كود القسيمة وتفاصيل الجائزة في رسالة SMS نصية إلى هاتفك. يُرجى إبراز رسالة الـ SMS عند زيارة مركز فينينو للعناية بالسيارات لاستلام جائزتك.'
                : 'Your official voucher code and prize details were sent via SMS to your mobile phone. Please present your SMS message at Veneno Auto Care facility to claim your reward.' }}
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2 pt-1">
          <!-- WhatsApp Support -->
          <a
            v-if="alreadyParticipatedData?.whatsapp_url"
            :href="alreadyParticipatedData.whatsapp_url"
            target="_blank"
            class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2.5 shadow-lg shadow-emerald-950 transition-all cursor-pointer"
          >
            <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"/></svg>
            <span>{{ currentLocale === 'ar' ? 'تواصل مع خدمة العملاء عبر واتساب' : 'Contact Concierge on WhatsApp' }}</span>
          </a>

          <!-- Close Modal -->
          <button
            type="button"
            @click="alreadyParticipatedModal = false"
            class="w-full py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 border border-zinc-700 text-zinc-400 hover:text-white text-xs font-semibold transition-colors cursor-pointer"
          >
            {{ currentLocale === 'ar' ? 'إغلاق' : 'Close' }}
          </button>
        </div>

      </div>
    </div>

    <!-- Bilingual Terms & Conditions Modal -->
    <TermsModal
      :show="showTermsModal"
      :locale="currentLocale"
      @close="showTermsModal = false"
      @accept="onTermsAcceptedFromModal"
    />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cairo:wght@400;600;700;800;900&family=Great+Vibes&family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

.font-arabic {
  font-family: 'Cairo', sans-serif;
}

.font-script {
  font-family: 'Great Vibes', 'Alex Brush', cursive;
}
</style>
