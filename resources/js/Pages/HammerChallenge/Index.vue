<script setup>
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { AlertCircle, ArrowRight, CheckCircle2, MapPin, Trophy } from 'lucide-vue-next';
import { useI18n } from '@/i18n';

const form = reactive({
  full_name: '',
  date_of_birth: '',
  mobile: '',
  email: '',
  emergency_contact_name: '',
  emergency_contact_number: '',
  terms_accepted: false,
});

const { t } = useI18n();

const errors = ref({});
const generalError = ref('');
const isSubmitting = ref(false);
const countdown = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
let countdownTimer = null;

const updateCountdown = () => {
  const target = new Date('2026-09-12T19:00:00+04:00').getTime();
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

const age = computed(() => {
  if (!form.date_of_birth) return null;
  const birthDate = new Date(`${form.date_of_birth}T00:00:00`);
  if (Number.isNaN(birthDate.getTime())) return null;

  const today = new Date();
  let years = today.getFullYear() - birthDate.getFullYear();
  if (today.getMonth() < birthDate.getMonth() || (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
    years -= 1;
  }
  return years;
});

const fieldError = (field) => errors.value[field]?.[0] || '';

const validate = () => {
  const nextErrors = {};
  const requiredFields = ['full_name', 'date_of_birth', 'mobile', 'email', 'emergency_contact_name', 'emergency_contact_number'];
  requiredFields.forEach((field) => {
    if (!String(form[field] || '').trim()) nextErrors[field] = [t('hammer.registration.required')];
  });

  if (form.email && !/^\S+@\S+\.\S+$/.test(form.email)) nextErrors.email = [t('hammer.registration.validEmail')];
  if (form.mobile && !/^\+?[0-9\s().-]{7,20}$/.test(form.mobile)) nextErrors.mobile = [t('hammer.registration.validMobile')];
  if (form.emergency_contact_number && !/^\+?[0-9\s().-]{7,20}$/.test(form.emergency_contact_number)) nextErrors.emergency_contact_number = [t('hammer.registration.validEmergency')];
  if (age.value !== null && age.value < 18) nextErrors.date_of_birth = [t('hammer.registration.minimumAge')];
  if (!form.terms_accepted) nextErrors.terms_accepted = [t('hammer.registration.requiredDeclaration')];

  errors.value = nextErrors;
  return Object.keys(nextErrors).length === 0;
};

const submit = async () => {
  generalError.value = '';
  if (!validate()) return;

  isSubmitting.value = true;
  try {
    const response = await window.axios.post('/api/hammer-challenge/register', { ...form });
    const token = response.data?.registration?.confirmation_token;
    if (!token) throw new Error('The server did not return a confirmation token.');
    window.location.assign(`/hammer-challenge/confirmation?token=${encodeURIComponent(token)}`);
  } catch (error) {
    errors.value = error.response?.data?.errors || {};
    generalError.value = error.response?.data?.message || error.message || t('hammer.registration.registrationFailed');
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Head :title="t('hammer.registration.pageTitle')" />

  <div class="min-h-screen overflow-x-hidden bg-[#070709] text-zinc-100 selection:bg-red-600 selection:text-white">
    <Navbar />

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
      <section class="relative overflow-hidden rounded-3xl border border-amber-500/30 bg-gradient-to-br from-[#111114] via-[#09090b] to-black shadow-2xl shadow-black/60">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-amber-400"></div>
        <div class="relative z-10 grid items-stretch lg:grid-cols-[1fr_0.82fr]">
          <div class="flex flex-col justify-center p-6 sm:p-10 lg:p-12">
            <div class="flex flex-wrap items-center gap-2"><div class="inline-flex items-center gap-2 rounded-full border border-red-500/40 bg-red-500/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-red-300">{{ t('hammer.eventSeries') }}</div><div class="rounded-full border border-zinc-700 bg-zinc-900 px-3 py-1.5 text-[10px] font-mono uppercase tracking-wider text-zinc-400">{{ t('hammer.registration.registrationOpen') }}</div></div>
            <h1 class="mt-5 max-w-3xl font-display text-3xl font-black uppercase leading-tight tracking-wide text-white sm:text-6xl">{{ t('hammer.title') }} <span class="text-amber-400">{{ t('hammer.final') }}</span></h1>
            <div class="mt-6 grid gap-3 text-sm text-zinc-300 sm:grid-cols-2 sm:text-base">
              <p><span class="mr-2 text-[10px] font-mono uppercase tracking-widest text-zinc-500">{{ t('hammer.registration.dateLabel') }}</span>{{ t('hammer.date') }}</p>
              <p class="flex items-start gap-2"><MapPin class="mt-0.5 h-4 w-4 shrink-0 text-red-400" /> {{ t('hammer.registration.location') }}</p>
              <p>{{ t('hammer.registration.checkInLabel') }} <strong class="text-white">7:30 PM</strong></p>
              <p>{{ t('hammer.registration.startsLabel') }} <strong class="text-white">8:00 PM</strong></p>
            </div>
            <div class="mt-7 flex flex-wrap items-center gap-3">
              <Link href="#registration-form" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white shadow-xl shadow-red-950/50 transition hover:brightness-110">{{ t('hammer.registration.registerNow') }} <ArrowRight class="h-4 w-4" /></Link>
              <span class="text-xs font-mono uppercase tracking-widest text-zinc-500">{{ t('hammer.registration.eventTagline') }}</span>
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
          <p class="text-[10px] font-mono uppercase tracking-[0.2em] text-amber-300">{{ t('hammer.registration.countdown') }}</p>
          <div class="mt-2 grid grid-cols-4 gap-2">
            <div v-for="unit in [{ key: 'days', label: 'Days' }, { key: 'hours', label: 'Hours' }, { key: 'minutes', label: 'Minutes' }, { key: 'seconds', label: 'Seconds' }]" :key="unit.key" class="rounded-xl border border-white/15 bg-black/65 p-2 text-center backdrop-blur-md sm:p-3"><p class="font-mono text-xl font-black text-white sm:text-2xl">{{ countdown[unit.key] }}</p><p class="text-[9px] uppercase tracking-wider text-zinc-400">{{ unit.label }}</p></div>
          </div>
        </div>
      </section>

  <section class="mt-5 max-w-xxl">
  <div
    class="group relative overflow-hidden rounded-3xl border border-amber-400/30
           bg-zinc-950/90 p-6 shadow-2xl shadow-amber-500/10
           backdrop-blur-xl transition-all duration-500
           hover:-translate-y-1 hover:border-amber-300/60
           hover:shadow-amber-500/20"
  >

    <!-- Ambient glow -->
    <div
      class="pointer-events-none absolute -right-16 -top-16 h-48 w-48
             rounded-full bg-amber-400/20 blur-3xl
             transition-all duration-700 group-hover:bg-amber-300/30"
    ></div>

    <div
      class="pointer-events-none absolute -bottom-20 -left-20 h-40 w-40
             rounded-full bg-yellow-500/10 blur-3xl"
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

        <!-- Trophy -->
        <div
          class="flex h-14 w-14 items-center justify-center rounded-2xl
                 border border-amber-400/30
                 bg-gradient-to-br from-amber-400/25 to-amber-700/10
                 shadow-lg shadow-amber-500/10"
        >
          <svg
            class="h-7 w-7 text-amber-300"
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
          <p class="text-[10px] font-black uppercase tracking-[0.25em] text-amber-300/80">
            {{ t('hammer.registration.firstPlace') }}
          </p>

          <p class="mt-1 text-sm font-semibold text-white/70">
            {{ t('hammer.registration.cashPrize') }}
          </p>
        </div>

      </div>

      <!-- Winner badge -->
      <div
        class="hidden rounded-full border border-amber-400/20
               bg-amber-400/10 px-3 py-1.5
               text-[10px] font-black uppercase tracking-widest
               text-amber-300 sm:block"
      >
        1st Place
      </div>
    </div>

    <!-- Prize -->
    <div class="relative mt-7">

      <div class="flex items-end gap-3">
        <span
          class="text-5xl font-black tracking-tight text-white
                 drop-shadow-[0_0_20px_rgba(251,191,36,0.15)]
                 sm:text-6xl"
        >
          AED 15,000
        </span>
      </div>

      <div class="mt-3 flex items-center gap-2">
        <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>

        <p class="text-xs font-medium tracking-wide text-zinc-400">
          {{ t('hammer.registration.cashPrize') }}
        </p>
      </div>
    </div>

    <!-- Bottom accent -->
    <div class="relative mt-6 h-px overflow-hidden bg-white/5">
      <div
        class="h-full w-1/3 bg-gradient-to-r
               from-transparent via-amber-400 to-transparent
               transition-all duration-700
               group-hover:w-full"
      ></div>
    </div>

    <!-- Footer -->
    <div class="relative mt-4 flex items-center justify-between">

      <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500">
        Grand Prize 
      </span>

      <div class="flex items-center gap-1.5">
        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400"></span>
        <span class="text-[10px] font-bold uppercase tracking-widest text-amber-300">
          Winner Reward
        </span>
      </div>

    </div>

  </div>
</section>

      <div id="registration-form" class="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_300px]">
        <form @submit.prevent="submit" novalidate class="space-y-6">
          <section class="rounded-3xl border border-zinc-800/90 bg-zinc-950/70 p-5 shadow-xl sm:p-8">
            <div class="border-b border-zinc-800 pb-5"><p class="text-xs font-mono uppercase tracking-widest text-red-400">{{ t('hammer.registration.participantDetails') }}</p><h2 class="mt-2 font-display text-2xl font-bold uppercase text-white">{{ t('hammer.registration.securePlace') }}</h2></div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
              <label class="field-label sm:col-span-2">{{ t('hammer.registration.fullName') }}<input v-model="form.full_name" type="text" autocomplete="name" class="field-input" /><span class="field-error">{{ fieldError('full_name') }}</span></label>
              <label class="field-label">{{ t('hammer.registration.dateOfBirth') }}<input v-model="form.date_of_birth" type="date" :max="new Date().toISOString().slice(0, 10)" class="field-input" /><span v-if="age !== null" class="text-xs font-normal text-zinc-500">{{ t('hammer.registration.calculatedAge', { age }) }}</span><span class="field-error">{{ fieldError('date_of_birth') }}</span></label>
              <label class="field-label">{{ t('hammer.registration.mobileNumber') }}<input v-model="form.mobile" type="tel" autocomplete="tel" placeholder="+971 50 123 4567" class="field-input" /><span class="field-error">{{ fieldError('mobile') }}</span></label>
              <label class="field-label sm:col-span-2">{{ t('hammer.registration.emailAddress') }}<input v-model="form.email" type="email" autocomplete="email" class="field-input" /><span class="field-error">{{ fieldError('email') }}</span></label>
              <label class="field-label">{{ t('hammer.registration.emergencyName') }}<input v-model="form.emergency_contact_name" type="text" autocomplete="name" class="field-input" /><span class="field-error">{{ fieldError('emergency_contact_name') }}</span></label>
              <label class="field-label">{{ t('hammer.registration.emergencyNumber') }}<input v-model="form.emergency_contact_number" type="tel" autocomplete="tel" class="field-input" /><span class="field-error">{{ fieldError('emergency_contact_number') }}</span></label>
            </div>
          </section>

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
                <span class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.healthReqTitle') }}</strong> {{ t('hammer.registration.healthDeclaration') }}</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-2 h-2 rounded-full bg-red-500 mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.safetyReqTitle') }}</strong> {{ t('hammer.registration.challengeDeclaration') }}</p>
              </div>
              <div class="flex items-start gap-3">
                <span class="w-2 h-2 rounded-full bg-amber-400 mt-2 shrink-0"></span>
                <p><strong class="text-white">{{ t('hammer.registration.mediaReqTitle') }}</strong> {{ t('hammer.registration.mediaDeclaration') }}</p>
              </div>
            </div>

            <!-- Single Acceptance Checkbox -->
            <label class="flex cursor-pointer items-start gap-3.5 rounded-2xl border border-amber-500/40 bg-zinc-900/80 p-4 sm:p-5 transition hover:border-amber-400 shadow-md">
              <input v-model="form.terms_accepted" type="checkbox" class="mt-1 h-5 w-5 shrink-0 accent-red-600 rounded cursor-pointer" />
              <div class="text-xs sm:text-sm leading-relaxed text-zinc-200">
                <span class="font-bold text-white block">{{ t('hammer.registration.singleAcceptance') }}</span>
                <div class="mt-1.5 text-xs">
                  <Link href="/hammer-challenge/terms" target="_blank" class="text-amber-400 hover:text-amber-300 underline underline-offset-2">
                    {{ t('hammer.registration.viewTerms') }}
                  </Link>
                </div>
                <span v-if="fieldError('terms_accepted')" class="field-error block mt-1">{{ fieldError('terms_accepted') }}</span>
              </div>
            </label>
          </section>

          <div v-if="generalError" class="flex items-start gap-3 rounded-2xl border border-red-500/40 bg-red-950/30 p-4 text-sm text-red-200"><AlertCircle class="h-5 w-5 shrink-0 text-red-400" /><span>{{ generalError }}</span></div>
          <button type="submit" :disabled="isSubmitting" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 px-5 py-4 text-sm font-black uppercase tracking-wider text-white shadow-xl shadow-red-950/50 transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"><span>{{ isSubmitting ? t('hammer.registration.submitting') : t('hammer.registration.submit') }}</span><ArrowRight class="h-4 w-4" /></button>
        </form>

        <aside class="space-y-4 lg:sticky lg:top-24">
          <div class="rounded-3xl border border-amber-500/30 bg-gradient-to-b from-amber-500/10 to-zinc-950 p-6"><Trophy class="h-8 w-8 text-amber-300" /><h2 class="mt-4 font-display text-xl font-bold uppercase text-white">{{ t('hammer.registration.firstPlace') }}</h2><p class="mt-2 text-3xl font-black text-amber-300">AED 15,000</p><p class="text-xs uppercase tracking-widest text-zinc-400">{{ t('hammer.registration.cashPrize') }}</p></div>
          <div class="rounded-3xl border border-zinc-800 bg-zinc-900/60 p-6 text-sm text-zinc-400"><div class="flex items-center gap-2 text-emerald-400"><CheckCircle2 class="h-4 w-4" /><span class="font-semibold text-zinc-200">{{ t('hammer.registration.beforeEvent') }}</span></div><p class="mt-3 leading-relaxed">{{ t('hammer.registration.beforeEventText') }}</p></div>
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
