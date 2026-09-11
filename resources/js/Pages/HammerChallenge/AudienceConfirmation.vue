<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  CheckCircle2, 
  AlertCircle, 
  MapPin, 
  Clock, 
  Ticket, 
  Calendar, 
  ArrowRight, 
  Share2, 
  Sparkles,
  Download
} from 'lucide-vue-next';

const registration = ref(null);
const errorMessage = ref('');
const isLoading = ref(true);

onMounted(async () => {
  const token = new URLSearchParams(window.location.search).get('token');
  if (!token) {
    errorMessage.value = 'Ticket confirmation token is missing.';
    isLoading.value = false;
    return;
  }

  try {
    const response = await window.axios.get(`/api/hammer-challenge/audience/confirmation/${encodeURIComponent(token)}`);
    registration.value = response.data.registration;
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Audience confirmation pass could not be found.';
  } finally {
    isLoading.value = false;
  }
});

const openGoogleMaps = () => {
  window.open('https://maps.google.com/?q=Veneno+Auto+Care+Center+Musaffah+M37+Abu+Dhabi', '_blank');
};
</script>

<template>
  <Head title="Your Audience Ticket • Veneno Hammer Challenge" />

  <div class="relative min-h-screen overflow-x-hidden bg-[#070709] text-zinc-100 font-sans px-4 py-8 flex flex-col justify-between">
    <!-- Ambient Background Glow -->
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.20),transparent_45%),radial-gradient(circle_at_90%_100%,rgba(197,160,89,0.12),transparent_40%)]"></div>

    <!-- Header -->
    <header class="relative z-10 w-full max-w-2xl mx-auto flex items-center justify-between pb-4">
      <Link href="/" class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500/20 to-red-600/20 border border-amber-500/40 flex items-center justify-center text-amber-400 font-black text-xs">
          V
        </div>
        <span class="text-xs font-black tracking-widest text-white uppercase font-display">VENENO AUTO CARE</span>
      </Link>

      <span class="text-[11px] font-mono text-zinc-400">Musaffah M37, Abu Dhabi</span>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 max-w-xl w-full mx-auto flex items-center justify-center py-4">
      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-12 text-zinc-400 text-sm">
        <div class="w-8 h-8 mx-auto mb-3 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></div>
        Loading Audience Ticket...
      </div>

      <!-- Error State -->
      <div v-else-if="errorMessage" class="w-full rounded-3xl border border-red-500/40 bg-red-950/30 p-8 text-center backdrop-blur-xl">
        <AlertCircle class="mx-auto h-12 w-12 text-red-400" />
        <h2 class="mt-4 text-lg font-bold text-white">Ticket Not Found</h2>
        <p class="mt-2 text-sm text-red-200">{{ errorMessage }}</p>
        <Link href="/hammer-challenge/audience" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold uppercase text-white hover:bg-red-500 transition">
          Register New Ticket <ArrowRight class="h-4 w-4" />
        </Link>
      </div>

      <!-- Digital Pass Card -->
      <section v-else class="relative w-full overflow-hidden rounded-3xl border border-amber-500/40 bg-gradient-to-b from-[#141014] via-[#0d0d12] to-[#08080a] p-6 sm:p-9 text-center shadow-2xl shadow-black/95">
        <!-- Gold Glow Top Line -->
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>

        <!-- Success Icon -->
        <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 mx-auto flex items-center justify-center text-emerald-400 shadow-inner">
          <CheckCircle2 class="w-9 h-9" />
        </div>

        <p class="mt-5 text-xs font-mono uppercase tracking-[0.25em] text-red-400">
          Official Event Pass • تصريح دخول رسمي
        </p>
        
        <h1 class="mt-2 font-display text-3xl sm:text-4xl font-black uppercase text-white tracking-tight">
          You're Registered!
        </h1>
        <p class="text-base sm:text-lg font-bold text-amber-300 mt-1">
          تم تأكيد تسجيل تذكرة الحضور بنجاح 🔥
        </p>

        <!-- Ticket Voucher Badge Box -->
        <div class="mt-6 rounded-2xl border-2 border-dashed border-amber-500/60 bg-gradient-to-b from-amber-500/15 via-zinc-900/90 to-black p-5 shadow-xl shadow-amber-950/20">
          <div class="flex items-center justify-between text-xs font-mono text-zinc-400 uppercase tracking-widest pb-2 border-b border-zinc-800">
            <span class="flex items-center gap-1.5 text-amber-400 font-bold">
              <Ticket class="w-3.5 h-3.5" /> Audience Pass
            </span>
            <span class="text-emerald-400 font-bold">● ACTIVE</span>
          </div>

          <div class="py-4">
            <div class="text-[11px] uppercase tracking-[0.2em] text-zinc-400 font-mono">Ticket Number</div>
            <div class="mt-1 text-3xl sm:text-4xl font-black tracking-[0.15em] text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-white to-amber-300 font-mono">
              {{ registration.ticket_number }}
            </div>
            <div class="text-xs font-bold text-zinc-200 mt-1.5">{{ registration.full_name }}</div>
          </div>

          <div class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider pt-2 border-t border-zinc-800">
            Show this pass or digital screen at the venue entrance
          </div>
        </div>

        <!-- Event Timing & Location -->
        <div class="mt-6 space-y-2.5 text-xs sm:text-sm text-zinc-300 bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-4 text-left">
          <div class="flex items-center gap-3">
            <Calendar class="w-4 h-4 text-amber-400 shrink-0" />
            <div>
              <span class="text-zinc-500 block text-[10px] uppercase font-mono">Date</span>
              <strong>Saturday, 12 September 2026</strong>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <Clock class="w-4 h-4 text-red-400 shrink-0" />
            <div>
              <span class="text-zinc-500 block text-[10px] uppercase font-mono">Schedule</span>
              <span>Audience Check-in: <strong>7:30 PM</strong> • Challenge Starts: <strong>8:00 PM</strong></span>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <MapPin class="w-4 h-4 text-emerald-400 shrink-0" />
            <div>
              <span class="text-zinc-500 block text-[10px] uppercase font-mono">Venue</span>
              <span>Veneno Auto Care Center, <strong>Musaffah M37, Abu Dhabi</strong></span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <button
            type="button"
            @click="openGoogleMaps"
            class="flex-1 py-3 px-4 rounded-xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-600 text-white font-bold text-xs flex items-center justify-center gap-2 transition cursor-pointer"
          >
            <MapPin class="w-4 h-4 text-emerald-400" />
            <span>Open Location Map</span>
          </button>

          <Link
            href="/"
            class="flex-1 py-3 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs uppercase flex items-center justify-center gap-2 transition"
          >
            <span>Return to Homepage</span>
            <ArrowRight class="w-4 h-4" />
          </Link>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full max-w-2xl mx-auto text-center text-xs text-zinc-500 font-mono pt-4">
      Veneno Auto Care Center • Official Hammer Challenge Platform
    </footer>
  </div>
</template>
