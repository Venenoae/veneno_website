<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { 
  AlertCircle, 
  ArrowRight, 
  CheckCircle2, 
  MapPin, 
  Trophy, 
  Lock, 
  Ticket,
  Sparkles 
} from 'lucide-vue-next';
import { useI18n } from '@/i18n';

const { t, currentLocale } = useI18n();

const countdownUnits = computed(() => {
  const isAr = currentLocale.value === 'ar';
  return [
    { key: 'days', label: isAr ? 'أيام' : 'Days' },
    { key: 'hours', label: isAr ? 'ساعات' : 'Hours' },
    { key: 'minutes', label: isAr ? 'دقائق' : 'Minutes' },
    { key: 'seconds', label: isAr ? 'ثواني' : 'Seconds' },
  ];
});

const countdown = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
let countdownTimer = null;

const updateCountdown = () => {
  const target = new Date('2026-09-12T17:00:00+04:00').getTime();
  const remaining = Math.max(0, target - Date.now());
  const pad = (value) => String(value).padStart(2, '0');
  countdown.value = {
    days: pad(Math.floor(remaining / (1000 * 60 * 60 * 24))),
    hours: pad(Math.floor((remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))),
    minutes: pad(Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60))),
    seconds: pad(Math.floor((remaining % (1000 * 60)) / 1000)),
  };
};

onMounted(() => {
  updateCountdown();
  countdownTimer = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
  if (countdownTimer) clearInterval(countdownTimer);
});
</script>

<template>
  <Head :title="t('hammer.registration.pageTitle')" />

  <div class="min-h-screen overflow-x-hidden bg-[#070709] text-zinc-100 selection:bg-red-600 selection:text-white">
    <Navbar />

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
      <!-- Hero Section -->
      <section class="relative overflow-hidden rounded-3xl border border-red-500/30 bg-gradient-to-br from-[#111114] via-[#09090b] to-black shadow-2xl shadow-black/60">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-[#a3e635]"></div>
        <div class="relative z-10 grid items-stretch lg:grid-cols-[1fr_0.82fr]">
          <div class="flex flex-col justify-center p-6 sm:p-10 lg:p-12">
            <div class="flex flex-wrap items-center gap-2">
              <div class="inline-flex items-center gap-2 rounded-full border border-red-500/40 bg-red-500/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-red-300">
                {{ t('hammer.eventSeries') }}
              </div>
              <div class="inline-flex items-center gap-1.5 rounded-full border border-red-500/40 bg-red-950/70 px-3 py-1.5 text-[10px] font-mono uppercase tracking-wider text-red-400 font-bold">
                <Lock class="w-3 h-3" />
                <span>{{ t('hammer.registration.registrationClosed') }}</span>
              </div>
            </div>
            
            <h1 class="mt-5 max-w-3xl font-display text-3xl font-black uppercase leading-tight tracking-wide text-white sm:text-6xl">
              {{ t('hammer.title') }} <span class="text-[#a3e635]">{{ t('hammer.final') }}</span>
            </h1>
            
            <div class="mt-6 grid gap-3 text-sm text-zinc-300 sm:grid-cols-2 sm:text-base">
              <p><span class="mr-2 text-[10px] font-mono uppercase tracking-widest text-zinc-500">{{ t('hammer.registration.dateLabel') }}</span>{{ t('hammer.date') }}</p>
              <p class="flex items-start gap-2"><MapPin class="mt-0.5 h-4 w-4 shrink-0 text-red-400" /> {{ t('hammer.registration.location') }}</p>
              <p class="sm:col-span-2">{{ t('hammer.registration.checkInLabel') }} <strong class="text-white font-mono">5:00 PM – 10:00 PM</strong></p>
            </div>
            
            <div class="mt-7 flex flex-wrap items-center gap-3">
              <Link 
                :href="currentLocale === 'ar' ? '/ar/hammer-challenge/audience' : '/hammer-challenge/audience'" 
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-red-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white shadow-xl shadow-red-950/50 transition hover:brightness-110 active:scale-98"
              >
                <Ticket class="h-4 w-4" />
                <span>{{ t('hammer.registration.audiencePassCta') }}</span>
                <ArrowRight class="h-4 w-4 rtl:rotate-180" />
              </Link>
              <span class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-zinc-800 bg-zinc-900/80 text-xs font-mono font-bold text-zinc-400 uppercase tracking-wider">
                <Lock class="w-3.5 h-3.5 text-red-400" />
                <span>{{ t('hammer.registration.registrationClosed') }}</span>
              </span>
            </div>
          </div>
          
          <div class="flex min-h-[360px] items-center justify-center border-t border-zinc-800/80 p-5 sm:p-8 lg:border-l lg:border-t-0 lg:p-10">
            <picture class="block h-full w-full max-w-[420px]">
              <source media="(max-width: 639px)" srcset="/images/hammer/Hammer3.jpeg" />
              <img src="/images/hammer/Hammer1.jpeg" alt="Veneno Hammer Challenge hammer artwork" class="h-full max-h-[520px] w-full object-contain drop-shadow-[0_20px_45px_rgba(239,68,68,0.18)]" />
            </picture>
          </div>
        </div>
        
        <div class="relative z-10 border-t border-zinc-800/80 px-6 py-5 sm:px-10 lg:px-12">
          <p class="text-[10px] font-mono uppercase tracking-[0.2em] text-[#a3e635] font-bold">{{ t('hammer.registration.countdown') }}</p>
          <div class="mt-2 grid grid-cols-4 gap-2">
            <div v-for="unit in countdownUnits" :key="unit.key" class="rounded-xl border border-white/15 bg-black/65 p-2 text-center backdrop-blur-md sm:p-3">
              <p class="font-mono text-xl font-black text-white sm:text-2xl">{{ countdown[unit.key] }}</p>
              <p class="text-[9px] uppercase tracking-wider text-zinc-400">{{ unit.label }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Grand Prize Section -->
      <section class="mt-5 max-w-xxl">
        <div
          class="group relative overflow-hidden rounded-3xl border border-red-500/40
                 bg-zinc-950/90 p-6 shadow-2xl shadow-red-950/30
                 backdrop-blur-xl transition-all duration-500
                 hover:-translate-y-1 hover:border-red-400/60
                 hover:shadow-red-950/50"
        >
          <!-- Ambient glow -->
          <div
            class="pointer-events-none absolute -right-16 -top-16 h-48 w-48
                   rounded-full bg-red-600/20 blur-3xl
                   transition-all duration-700 group-hover:bg-red-500/30"
          ></div>

          <div
            class="pointer-events-none absolute -bottom-20 -left-20 h-40 w-40
                   rounded-full bg-[#a3e635]/10 blur-3xl"
          ></div>

          <!-- Shine effect -->
          <div
            class="pointer-events-none absolute inset-0 -translate-x-full
                   bg-gradient-to-r from-transparent via-white/10 to-transparent
                   transition-transform duration-1000 group-hover:translate-x-full"
          ></div>

          <!-- Header -->
          <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-4">
              <!-- Trophy in Electric Lime -->
              <div
                class="flex h-14 w-14 items-center justify-center rounded-2xl
                       border border-[#a3e635]/40
                       bg-gradient-to-br from-[#a3e635]/20 to-zinc-900
                       shadow-lg shadow-[#a3e635]/10"
              >
                <svg
                  class="h-7 w-7 text-[#a3e635]"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8 21h8m-4-4v4m-5-9a5 5 0 0010 0V4H7v8z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M7 6H4a1 1 0 00-1 1v1a4 4 0 004 4m10-6h3a1 1 0 011 1v1a4 4 0 01-4 4"
                  />
                </svg>
              </div>

              <div>
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#a3e635]">
                  {{ t('hammer.registration.firstPlace') }}
                </p>

                <p class="mt-1 text-sm font-semibold text-white/70">
                  {{ t('hammer.registration.cashPrize') }}
                </p>
              </div>
            </div>

            <!-- Winner badge -->
            <div
              class="hidden rounded-full border border-[#a3e635]/30
                     bg-[#a3e635]/15 px-3 py-1.5
                     text-[10px] font-black uppercase tracking-widest
                     text-[#a3e635] sm:block font-mono"
            >
              {{ t('hammer.registration.firstPlace') }}
            </div>
          </div>

          <!-- Prize -->
          <div class="relative mt-7">
            <div class="flex items-end gap-3">
              <span
                class="text-5xl font-black tracking-tight text-white
                       drop-shadow-[0_0_20px_rgba(239,68,68,0.25)]
                       sm:text-6xl"
              >
                AED 15,000
              </span>
            </div>

            <div class="mt-3 flex items-center gap-2">
              <span class="h-1.5 w-1.5 rounded-full bg-[#a3e635]"></span>
              <p class="text-xs font-medium tracking-wide text-zinc-300">
                {{ t('hammer.registration.cashPrize') }}
              </p>
            </div>
          </div>

          <!-- Bottom accent -->
          <div class="relative mt-6 h-px overflow-hidden bg-white/5">
            <div
              class="h-full w-1/3 bg-gradient-to-r
                     from-transparent via-red-500 to-transparent
                     transition-all duration-700
                     group-hover:w-full"
            ></div>
          </div>

          <!-- Footer -->
          <div class="relative mt-4 flex items-center justify-between">
            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">
              {{ currentLocale === 'ar' ? 'الجائزة الكبرى' : 'Grand Prize' }}
            </span>

            <div class="flex items-center gap-1.5">
              <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#a3e635]"></span>
              <span class="text-[10px] font-bold uppercase tracking-widest text-[#a3e635] font-mono">
                {{ currentLocale === 'ar' ? 'مكافأة الفائز' : 'Winner Reward' }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Event Details & Registration Closed Notice Showcase -->
      <div id="registration-form" class="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_300px]">
        <div class="space-y-6">
          <!-- Closed Notice Banner -->
          <section class="rounded-3xl border-2 border-red-500/40 bg-gradient-to-b from-red-950/30 via-zinc-950/90 to-black p-6 sm:p-8 shadow-2xl shadow-black/80 backdrop-blur-xl relative overflow-hidden">
            <div class="pointer-events-none absolute -right-20 -top-20 w-60 h-60 bg-red-600/15 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
              <div class="space-y-2.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-950/80 border border-red-500/50 text-red-400 text-xs font-mono font-bold uppercase tracking-wider">
                  <Lock class="w-3.5 h-3.5" />
                  <span>{{ t('hammer.registration.registrationClosed') }}</span>
                </div>
                <h2 class="font-display text-2xl sm:text-3xl font-black uppercase text-white tracking-tight">
                  {{ t('hammer.registration.closedNoticeTitle') }}
                </h2>
                <p class="text-sm text-zinc-300 leading-relaxed max-w-xl">
                  {{ t('hammer.registration.closedNoticeDesc') }}
                </p>
              </div>

              <Link 
                :href="currentLocale === 'ar' ? '/ar/hammer-challenge/audience' : '/hammer-challenge/audience'"
                class="shrink-0 px-6 py-4 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-red-600 hover:from-red-500 hover:to-red-400 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-xl shadow-red-950/60 transition flex items-center gap-2.5 active:scale-98"
              >
                <Ticket class="w-4 h-4" />
                <span>{{ t('hammer.registration.audiencePassCta') }}</span>
                <ArrowRight class="w-4 h-4 rtl:rotate-180" />
              </Link>
            </div>
          </section>

          <!-- Competition Declarations & Rules Overview -->
          <section class="rounded-3xl border border-zinc-800/90 bg-zinc-950/70 p-5 shadow-xl sm:p-8 space-y-5">
            <div class="border-b border-zinc-800 pb-4">
              <p class="text-xs font-mono uppercase tracking-widest text-red-400">{{ t('hammer.registration.declarations') }}</p>
              <h2 class="mt-1 font-display text-2xl font-bold uppercase text-white">{{ t('hammer.registration.agreement') }}</h2>
            </div>

            <!-- Bulleted Terms Summary -->
            <div class="rounded-2xl border border-zinc-800 bg-zinc-900/40 p-4 sm:p-5 space-y-3.5 text-xs sm:text-sm text-zinc-300 font-sans leading-relaxed">
              <div class="flex items-start gap-3">
                <span class="w-2 h-2 rounded-full bg-red-500 mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.ageReqTitle') }}</strong> {{ t('hammer.registration.ageDeclaration') }}</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-2 h-2 rounded-full bg-[#a3e635] mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.healthReqTitle') }}</strong> {{ t('hammer.registration.healthDeclaration') }}</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-2 h-2 rounded-full bg-red-500 mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.safetyReqTitle') }}</strong> {{ t('hammer.registration.challengeDeclaration') }}</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-2 h-2 rounded-full bg-[#a3e635] mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.mediaReqTitle') }}</strong> {{ t('hammer.registration.mediaDeclaration') }}</p>
              </div>
            </div>

            <div class="pt-2">
              <Link href="/hammer-challenge/terms" target="_blank" class="text-xs font-mono text-red-400 hover:text-red-300 underline underline-offset-4 flex items-center gap-1.5">
                <span>{{ t('hammer.registration.viewTerms') }} ➔</span>
              </Link>
            </div>
          </section>
        </div>

        <aside class="space-y-4 lg:sticky lg:top-24">
          <div class="rounded-3xl border border-red-500/30 bg-gradient-to-b from-red-600/15 via-zinc-900 to-zinc-950 p-6">
            <Trophy class="h-8 w-8 text-[#a3e635]" />
            <h2 class="mt-4 font-display text-xl font-bold uppercase text-white">{{ t('hammer.registration.firstPlace') }}</h2>
            <p class="mt-2 text-3xl font-black text-[#a3e635]">AED 15,000</p>
            <p class="text-xs uppercase tracking-widest text-zinc-400">{{ t('hammer.registration.cashPrize') }}</p>
          </div>
          <div class="rounded-3xl border border-zinc-800 bg-zinc-900/60 p-6 text-sm text-zinc-400">
            <div class="flex items-center gap-2 text-[#a3e635]">
              <CheckCircle2 class="h-4 w-4" />
              <span class="font-semibold text-zinc-200">{{ t('hammer.registration.beforeEvent') }}</span>
            </div>
            <p class="mt-3 leading-relaxed">{{ t('hammer.registration.beforeEventText') }}</p>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<style scoped>
.field-label { display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem; font-weight: 600; color: rgb(228 228 231); }
.field-input { width: 100%; border: 1px solid rgb(63 63 70); border-radius: 0.75rem; background: rgb(9 9 11); padding: 0.75rem 1rem; color: white; outline: none; transition: border-color 150ms, box-shadow 150ms; }
.field-input:focus { border-color: rgb(239 68 68); box-shadow: 0 0 0 1px rgb(239 68 68); }
.field-error { display: block; min-height: 1rem; margin-top: 0.25rem; color: rgb(248 113 113); font-size: 0.75rem; font-weight: 400; }
</style>
