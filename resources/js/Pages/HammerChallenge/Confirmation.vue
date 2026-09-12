<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, CheckCircle2, MapPin } from 'lucide-vue-next';

const registration = ref(null);
const errorMessage = ref('');
const isLoading = ref(true);

onMounted(async () => {
  const token = new URLSearchParams(window.location.search).get('token');
  if (!token) {
    errorMessage.value = 'This confirmation link is incomplete.';
    isLoading.value = false;
    return;
  }

  try {
    const response = await window.axios.get(`/api/hammer-challenge/confirmation/${encodeURIComponent(token)}`);
    registration.value = response.data.registration;
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'This confirmation could not be found.';
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <Head title="Hammer Challenge Registration" />
  <div class="relative min-h-screen overflow-hidden bg-[#070709] px-4 py-10 text-zinc-100">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(239,68,68,0.18),transparent_38%),radial-gradient(circle_at_90%_100%,rgba(197,160,89,0.10),transparent_34%)]"></div>
    <main class="mx-auto flex min-h-[80vh] max-w-2xl items-center justify-center">
      <div v-if="isLoading" class="text-sm text-zinc-400">Loading confirmation...</div>
      <div v-else-if="errorMessage" class="w-full rounded-3xl border border-red-500/40 bg-red-950/30 p-8 text-center"><AlertCircle class="mx-auto h-10 w-10 text-red-400" /><p class="mt-4 text-red-200">{{ errorMessage }}</p><Link href="/hammer-challenge/register" class="mt-6 inline-flex items-center gap-2 text-sm text-white underline">Return to registration <ArrowRight class="h-4 w-4" /></Link></div>
      <section v-else class="relative w-full overflow-hidden rounded-3xl border border-red-500/40 bg-gradient-to-b from-red-950/70 via-zinc-950 to-black p-7 text-center shadow-2xl shadow-black/70 sm:p-12">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-lime-400 to-transparent"></div>
        <CheckCircle2 class="mx-auto h-16 w-16 text-lime-400" />
        <p class="mt-6 text-xs font-mono uppercase tracking-[0.25em] text-lime-400">Registration confirmed</p>
        <h1 class="mt-3 font-display text-4xl font-black uppercase text-white sm:text-5xl">You're Registered!</h1>
        <p class="mt-3 text-lg text-zinc-300">تم تسجيلك بنجاح! 🔥</p>
        <div class="mt-8 space-y-2 text-sm text-zinc-300"><p>Saturday, 12 September 2026</p><p class="flex justify-center gap-2"><MapPin class="h-4 w-4 text-red-400" /> Veneno Auto Care Center - Mussafah</p><p>Event Timing: 5:00 PM – 10:00 PM</p></div>
        <div class="mt-8 rounded-2xl border border-red-500/50 bg-gradient-to-b from-red-950/40 to-zinc-950 p-5 shadow-lg shadow-red-950/20"><p class="text-xs uppercase tracking-[0.2em] text-lime-400 font-bold">Registration number</p><p class="mt-3 text-4xl font-black tracking-[0.18em] text-white sm:text-5xl">{{ registration.registration_number }}</p><p class="mt-2 text-[10px] font-mono uppercase tracking-widest text-zinc-400">Keep this number for event check-in</p></div>
        <p class="mt-8 text-sm leading-relaxed text-zinc-300">Bring valid ID and suitable sports clothing and athletic footwear.</p>
        <p class="mt-5 text-lg font-bold text-white">See you at the challenge 💪🔥</p>
        <Link href="/" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-bold uppercase text-white hover:bg-red-500">Return to Homepage <ArrowRight class="h-4 w-4" /></Link>
      </section>
    </main>
  </div>
</template>
