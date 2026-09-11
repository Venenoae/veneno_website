<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
  User, 
  Phone, 
  Mail, 
  Sparkles, 
  MapPin, 
  Clock, 
  Trophy, 
  CheckCircle2, 
  ArrowRight, 
  Loader2, 
  Ticket,
  Flame,
  ShieldCheck
} from 'lucide-vue-next';

const form = ref({
  full_name: '',
  mobile: '',
  email: '',
});

const isSubmitting = ref(false);
const errorMessage = ref('');

const formatPhoneInput = (e) => {
  let val = e.target.value;
  // If user types 05... keep it clean
  form.value.mobile = val;
};

const handleAudienceSubmit = async () => {
  errorMessage.value = '';
  
  if (!form.value.full_name || form.value.full_name.trim().length < 2) {
    errorMessage.value = 'Please enter your full name.';
    return;
  }

  if (!form.value.mobile || form.value.mobile.trim().length < 7) {
    errorMessage.value = 'Please enter a valid mobile phone number.';
    return;
  }

  isSubmitting.value = true;

  try {
    const response = await window.axios.post('/api/hammer-challenge/audience', {
      full_name: form.value.full_name.trim(),
      mobile: form.value.mobile.trim(),
      email: form.value.email ? form.value.email.trim() : null,
    });

    if (response.data.success) {
      const token = response.data.registration.confirmation_token;
      router.visit(`/hammer-challenge/audience/confirmation?token=${encodeURIComponent(token)}`);
    } else {
      errorMessage.value = response.data.message || 'Registration failed. Please try again.';
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      const firstErr = Object.values(error.response.data.errors)[0];
      errorMessage.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to submit audience registration. Please try again.';
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Head title="Audience & Visitor Pass • Veneno Hammer Challenge 2026" />

  <div class="min-h-screen bg-[#070709] text-zinc-100 font-sans selection:bg-red-600 selection:text-white relative overflow-x-hidden flex flex-col justify-between">
    <!-- Ambient Background Glow -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.22),transparent_45%),radial-gradient(circle_at_90%_100%,rgba(197,160,89,0.12),transparent_40%)]"></div>

    <!-- Header Navigation -->
    <header class="relative z-10 w-full max-w-4xl mx-auto px-4 pt-6 pb-4 flex items-center justify-between">
      <Link href="/" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500/20 to-red-600/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-black text-sm">
          V
        </div>
        <div>
          <span class="text-xs font-black tracking-widest text-white uppercase font-display">VENENO AUTO CARE</span>
          <span class="block text-[10px] text-zinc-400 font-mono">Musaffah M37, Abu Dhabi</span>
        </div>
      </Link>

      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-950/60 border border-red-600/40 text-red-300 text-xs font-bold font-mono">
        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
        <span>AUDIENCE PASS</span>
      </div>
    </header>

    <!-- Main Form Section -->
    <main class="relative z-10 flex-1 max-w-xl w-full mx-auto px-4 py-4 flex flex-col justify-center">
      <!-- Card Container -->
      <div class="relative rounded-3xl bg-zinc-950/90 border border-amber-500/35 p-6 sm:p-8 shadow-2xl shadow-black/90 backdrop-blur-xl">
        <!-- Top Accent Gold Line -->
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>

        <!-- Title & Subtitle -->
        <div class="text-center space-y-2 mb-6">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-mono font-bold uppercase">
            <Ticket class="w-3.5 h-3.5 text-amber-400" />
            <span>Free Audience & Spectator Pass</span>
          </div>
          <h1 class="text-3xl sm:text-4xl font-black uppercase text-white tracking-tight font-display">
            HAMMER CHALLENGE
          </h1>
          <p class="text-base sm:text-lg font-bold text-amber-300/90">
            تسجيل حضور الجمهور والزوار مجاناً
          </p>
          <p class="text-xs text-zinc-400 max-w-md mx-auto leading-relaxed">
            Register in seconds to claim your VIP digital entrance pass to watch the live showdown and cheer for the contenders!
          </p>
        </div>

        <!-- Quick Event Snapshot -->
        <div class="grid grid-cols-2 gap-2.5 p-3 rounded-2xl bg-zinc-900/70 border border-zinc-800 text-xs text-zinc-300 mb-6">
          <div class="flex items-center gap-2">
            <Clock class="w-4 h-4 text-red-400 shrink-0" />
            <div>
              <div class="text-[10px] text-zinc-500 uppercase font-mono">Check-in Time</div>
              <div class="font-bold text-zinc-200">7:30 PM • 12 Sep 2026</div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <MapPin class="w-4 h-4 text-emerald-400 shrink-0" />
            <div>
              <div class="text-[10px] text-zinc-500 uppercase font-mono">Location</div>
              <div class="font-bold text-zinc-200">Musaffah M37, Abu Dhabi</div>
            </div>
          </div>
        </div>

        <!-- Registration Form -->
        <form @submit.prevent="handleAudienceSubmit" class="space-y-4">
          <!-- Full Name -->
          <div>
            <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
              <span>Full Name *</span>
              <span class="text-zinc-500 text-[11px] font-normal">الاسم الكامل</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                <User class="w-4 h-4 text-amber-400/80" />
              </div>
              <input
                v-model="form.full_name"
                type="text"
                required
                placeholder="e.g. Rashid Al Mansoori"
                class="w-full pl-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none"
              />
            </div>
          </div>

          <!-- Mobile Number -->
          <div>
            <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
              <span>Mobile Phone Number *</span>
              <span class="text-zinc-500 text-[11px] font-normal">رقم الهاتف المتحرك</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                <Phone class="w-4 h-4 text-amber-400/80" />
              </div>
              <input
                v-model="form.mobile"
                @input="formatPhoneInput"
                type="tel"
                required
                placeholder="e.g. 050 123 4567 or +971..."
                class="w-full pl-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none font-mono"
              />
            </div>
            <p class="text-[11px] text-zinc-500 mt-1">Ticket confirmation will be linked to this number.</p>
          </div>

          <!-- Email (Optional) -->
          <div>
            <label class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
              <span>Email Address (Optional)</span>
              <span class="text-zinc-500 text-[11px] font-normal">البريد الإلكتروني (اختياري)</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                <Mail class="w-4 h-4 text-zinc-500" />
              </div>
              <input
                v-model="form.email"
                type="email"
                placeholder="name@example.com"
                class="w-full pl-10 pr-4 py-3 rounded-xl bg-zinc-900 border border-zinc-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-zinc-500 text-sm transition outline-none font-mono"
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
            class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-amber-500 hover:from-red-500 hover:to-amber-400 text-white font-black text-sm uppercase tracking-wider shadow-lg shadow-red-950/50 transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer active:scale-98 disabled:opacity-50"
          >
            <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
            <Ticket v-else class="w-4 h-4" />
            <span>{{ isSubmitting ? 'Issuing Your Ticket...' : 'Get Free Audience Ticket • احصل على التذكرة' }}</span>
          </button>
        </form>

        <!-- Divider & Contestant Switcher Notice -->
        <div class="mt-6 pt-5 border-t border-zinc-800 text-center">
          <p class="text-xs text-zinc-400">
            Want to compete for the <strong class="text-amber-300">AED 15,000 Cash Prize</strong>?
          </p>
          <Link
            href="/hammer-challenge/register"
            class="mt-2 inline-flex items-center gap-1.5 text-xs font-bold text-red-400 hover:text-red-300 underline transition"
          >
            <Flame class="w-3.5 h-3.5 text-red-500" />
            <span>Register as a Contestant (18+) • التسجيل كمتسابق</span>
            <ArrowRight class="w-3 h-3" />
          </Link>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full max-w-4xl mx-auto px-4 py-4 text-center text-xs text-zinc-500 font-mono">
      © {{ new Date().getFullYear() }} Veneno Auto Care Center. All Rights Reserved. • Musaffah M37, Abu Dhabi
    </footer>
  </div>
</template>
