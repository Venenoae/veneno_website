<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { CheckCircle2, Calendar, Printer, Car, ShieldCheck, ArrowRight } from 'lucide-vue-next';

const props = defineProps({
  booking: Object,
});

const printReceipt = () => {
  window.print();
};
</script>

<template>
  <Head :title="`Booking Confirmed ${booking.booking_code} - Veneno Auto Care Center`" />

  <div class="min-h-screen flex flex-col bg-zinc-950 text-zinc-100 font-sans">
    <Navbar />

    <main class="flex-1 max-w-3xl mx-auto px-4 py-16 text-center space-y-8">
      <div class="w-20 h-20 mx-auto rounded-full bg-emerald-600/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center shadow-2xl shadow-emerald-900/50">
        <CheckCircle2 class="w-10 h-10" />
      </div>

      <div>
        <span class="text-xs font-mono uppercase tracking-widest text-emerald-400 font-bold">RESERVATION SECURED</span>
        <h1 class="text-3xl sm:text-4xl font-display uppercase tracking-wide text-white mt-1">Booking {{ booking.booking_code }} Confirmed</h1>
        <p class="text-xs text-zinc-400 mt-2">
          Your studio appointment is confirmed at our Al Quoz Detailing Studio.
        </p>
      </div>

      <!-- Printable Receipt Card -->
      <div class="glass-panel p-8 rounded-3xl border border-zinc-800 text-left space-y-6 font-mono text-xs shadow-2xl">
        <div class="flex items-center justify-between pb-4 border-b border-zinc-800">
          <div>
            <div class="text-base font-bold text-white">VENENO AUTO CARE CENTER RECEIPT</div>
            <div class="text-zinc-500 text-[10px]">Invoice Ref: {{ booking.payment?.receipt_number || 'VEN-REC-9021' }}</div>
          </div>
          <button
            @click="printReceipt"
            class="px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-zinc-700 flex items-center gap-1.5 text-xs transition-colors"
          >
            <Printer class="w-3.5 h-3.5" />
            <span>Print Receipt</span>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-zinc-500 text-[10px] uppercase">Client Name</div>
            <div class="text-white font-bold text-sm">{{ booking.customer_name }}</div>
            <div class="text-zinc-400 text-xs">{{ booking.customer_email }}</div>
          </div>
          <div>
            <div class="text-zinc-500 text-[10px] uppercase">Vehicle</div>
            <div class="text-white font-bold text-sm">{{ booking.vehicle_summary || 'Porsche 911 Turbo S' }}</div>
            <div class="text-zinc-400 text-xs">Plate: {{ booking.vehicle?.license_plate || 'VEN911' }}</div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-zinc-800">
          <div>
            <div class="text-zinc-500 text-[10px] uppercase">Service Package</div>
            <div class="text-red-400 font-bold">{{ booking.service_package?.name || 'Diamond Ceramic Coating' }}</div>
          </div>
          <div>
            <div class="text-zinc-500 text-[10px] uppercase">Scheduled Time</div>
            <div class="text-white font-bold">{{ booking.booking_date }} @ {{ booking.time_slot }}</div>
          </div>
        </div>

        <div class="pt-4 border-t border-zinc-800 flex justify-between items-center text-sm">
          <span class="text-zinc-400">Total Investment:</span>
          <span class="text-white font-bold">${{ Number(booking.total_amount) }} USD</span>
        </div>

        <div class="flex justify-between items-center text-sm font-bold text-emerald-400">
          <span>Stripe Deposit Authorized:</span>
          <span>${{ Number(booking.deposit_amount) }} USD</span>
        </div>
      </div>

      <div class="flex items-center justify-center gap-4">
        <Link
          :href="route('customer.portal')"
          class="px-6 py-3.5 rounded-2xl bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs uppercase tracking-wider border border-zinc-800 transition-colors flex items-center gap-2"
        >
          <Car class="w-4 h-4 text-emerald-400" />
          <span>View in VIP Garage</span>
        </Link>

        <Link
          :href="route('home')"
          class="px-6 py-3.5 rounded-2xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-red-600/30 transition-all flex items-center gap-2"
        >
          <span>Return to Homepage</span>
          <ArrowRight class="w-4 h-4" />
        </Link>
      </div>
    </main>

    <Footer />
  </div>
</template>
