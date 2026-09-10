<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Scale } from 'lucide-vue-next';
</script>

<template>
  <Head title="Hammer Challenge Terms and Conditions" />
  <div class="min-h-screen bg-[#070709] px-4 py-10 text-zinc-100">
    <main class="mx-auto max-w-3xl">
      <Link href="/hammer-challenge/register" class="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-white"><ArrowLeft class="h-4 w-4" /> Back to registration</Link>
      <section class="mt-8 rounded-3xl border border-zinc-800 bg-zinc-900/70 p-6 shadow-2xl sm:p-10">
        <div class="flex items-center gap-3"><Scale class="h-7 w-7 text-amber-400" /><p class="text-xs font-mono uppercase tracking-widest text-amber-300">Veneno Hammer Challenge</p></div>
        <h1 class="mt-4 font-display text-3xl font-black uppercase text-white">Terms & Conditions | الشروط والأحكام</h1>
        <div class="mt-6 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-5 text-sm leading-relaxed text-amber-100"><strong>Placeholder:</strong> The final official Terms & Conditions will be added here when provided. No legal wording is being inferred or invented.</div>
      </section>
    </main>
  </div>
</template>
