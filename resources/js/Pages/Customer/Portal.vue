<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BookingWizardModal from '@/Components/BookingWizardModal.vue';
import AIQuoteModal from '@/Components/AIQuoteModal.vue';
import LoginModal from '@/Components/LoginModal.vue';
import WhatsAppWidget from '@/Components/WhatsAppWidget.vue';
import {
  Car,
  ShieldCheck,
  Award,
  Clock,
  CheckCircle2,
  Calendar,
  Sparkles,
  Download,
  Printer,
  ChevronRight,
  Wrench,
  AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  bookings: Array,
  vehicles: Array,
  activeJob: Object,
  loyalty: Object,
});

const isBookingModalOpen = ref(false);
const isAiQuoteModalOpen = ref(false);
const isLoginModalOpen = ref(false);

const activeTab = ref('active-job'); // 'active-job', 'garage', 'history'

const stageLabels = {
  pending: 'Reservation Placed',
  confirmed: 'Studio Bay Booked',
  in_progress: 'Decon Foam Wash & Clay Prep',
  polishing: 'Stage 1-2 Paint Correction',
  curing: '9H Graphene Ceramic IR Cure',
  qc_ready: 'Quality Control 50-Point Pass',
  completed: 'Vehicle Ready for Handover',
};

const stagesOrder = ['confirmed', 'in_progress', 'polishing', 'curing', 'qc_ready', 'completed'];

const isStageCompleted = (stageName, currentStatus) => {
  const currentIdx = stagesOrder.indexOf(currentStatus);
  const stageIdx = stagesOrder.indexOf(stageName);
  return stageIdx <= currentIdx;
};
</script>

<template>
  <Head title="VIP Client Portal & Garage - Veneno Auto Care Center" />

  <div class="min-h-screen flex flex-col bg-zinc-950 text-zinc-100 font-sans selection:bg-red-600 selection:text-white">
    <Navbar
      @open-booking="isBookingModalOpen = true"
      @open-ai-quote="isAiQuoteModalOpen = true"
      @open-login="isLoginModalOpen = true"
    />

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <!-- VIP Client Profile Header -->
      <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-600 to-amber-600 p-0.5 shadow-xl">
            <img
              src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80"
              alt="Alex Sterling"
              class="w-full h-full object-cover rounded-[14px]"
            />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-2xl font-display uppercase tracking-wide text-white">Alex Sterling</h1>
              <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-mono font-bold uppercase">
                {{ loyalty?.tier || 'VIP Platinum' }}
              </span>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">alex@example.com • 2 Vehicles Registered in Garage</p>
          </div>
        </div>

        <!-- Loyalty Points Badge -->
        <div class="p-4 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center gap-4 text-xs font-mono">
          <div>
            <div class="text-[10px] text-zinc-500 uppercase">Loyalty Balance</div>
            <div class="text-2xl font-display italic text-amber-400">{{ loyalty?.points || 3450 }} PTS</div>
          </div>
          <div class="text-right border-l border-zinc-800 pl-4">
            <div class="text-[10px] text-zinc-500 uppercase">Next Reward</div>
            <div class="text-white font-bold">$200 Detailing Credit</div>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex border-b border-zinc-800 text-xs font-mono uppercase tracking-wider">
        <button
          @click="activeTab = 'active-job'"
          class="py-3 px-6 border-b-2 font-bold transition-colors"
          :class="activeTab === 'active-job' ? 'text-red-500 border-red-500' : 'text-zinc-400 border-transparent hover:text-zinc-200'"
        >
          Live Bay Job Tracker
        </button>
        <button
          @click="activeTab = 'garage'"
          class="py-3 px-6 border-b-2 font-bold transition-colors"
          :class="activeTab === 'garage' ? 'text-red-500 border-red-500' : 'text-zinc-400 border-transparent hover:text-zinc-200'"
        >
          My Vehicles Garage ({{ vehicles?.length || 2 }})
        </button>
        <button
          @click="activeTab = 'history'"
          class="py-3 px-6 border-b-2 font-bold transition-colors"
          :class="activeTab === 'history' ? 'text-red-500 border-red-500' : 'text-zinc-400 border-transparent hover:text-zinc-200'"
        >
          Service History & Receipts
        </button>
      </div>

      <!-- TAB 1: LIVE BAY STATUS TRACKER -->
      <div v-if="activeTab === 'active-job'" class="space-y-6">
        <div v-if="activeJob" class="glass-panel p-8 rounded-3xl border border-zinc-800 space-y-8">
          <!-- Job Title & Status Banner -->
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-zinc-800">
            <div>
              <div class="flex items-center gap-2 text-xs font-mono text-zinc-500 uppercase">
                <span>Active Booking Ref:</span>
                <span class="text-red-400 font-bold">{{ activeJob.booking_code }}</span>
              </div>
              <h2 class="text-2xl font-black text-white mt-1">
                {{ activeJob.vehicle_summary || '2024 Porsche 911 Turbo S' }}
              </h2>
              <div class="text-xs text-zinc-400 mt-1">
                Treatment: <span class="text-white font-semibold">{{ activeJob.service_package?.name || 'Diamond Ceramic Coating' }}</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <span class="px-3.5 py-1.5 rounded-xl bg-red-600/20 text-red-400 border border-red-500/30 text-xs font-mono font-bold uppercase flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                <span>{{ stageLabels[activeJob.status] || activeJob.status }}</span>
              </span>
            </div>
          </div>

          <!-- Live Bay Progress Stepper -->
          <div class="space-y-4">
            <h3 class="text-xs font-mono uppercase tracking-widest text-zinc-400 font-bold">
              Live Bay Milestone Timeline
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
              <div
                v-for="(st, idx) in stagesOrder"
                :key="st"
                class="p-4 rounded-2xl border transition-all text-left flex flex-col justify-between"
                :class="isStageCompleted(st, activeJob.status) ? 'bg-zinc-900/90 border-red-500/60 shadow-lg' : 'bg-zinc-950/60 border-zinc-800/60 opacity-50'"
              >
                <div class="flex items-center justify-between mb-3">
                  <span class="text-[10px] font-mono text-zinc-500">0{{ idx + 1 }}</span>
                  <CheckCircle2
                    class="w-4 h-4"
                    :class="isStageCompleted(st, activeJob.status) ? 'text-red-500' : 'text-zinc-700'"
                  />
                </div>
                <div class="text-xs font-bold text-white">{{ stageLabels[st] }}</div>
              </div>
            </div>
          </div>

          <!-- Technician Inspection Notes -->
          <div class="p-6 rounded-2xl bg-zinc-900/80 border border-zinc-800 space-y-3 text-xs">
            <div class="flex items-center justify-between text-zinc-400 font-mono text-[11px]">
              <span class="flex items-center gap-1.5">
                <Wrench class="w-4 h-4 text-amber-400" />
                <span>Assigned Master Detailer: Marcus Vance (Bay #3)</span>
              </span>
              <span class="text-emerald-400">IR Curing at 140°F in progress</span>
            </div>
            <p class="text-zinc-300 leading-relaxed">
              "Panel paint depth measured at 135μm. Dual compound jeweling eliminated 93% of wash holograms on hood and spoiler. 9H ceramic base layer applied smoothly with zero high spots."
            </p>
          </div>
        </div>

        <div v-else class="text-center py-16 glass-panel rounded-3xl border border-zinc-800 space-y-4">
          <Car class="w-12 h-12 mx-auto text-zinc-600" />
          <h3 class="text-lg font-bold text-white">No Active Jobs in Progress</h3>
          <p class="text-xs text-zinc-400">Reserve an appointment for ceramic coating or paint protection.</p>
          <button
            @click="isBookingModalOpen = true"
            class="px-6 py-3 rounded-xl bg-red-600 text-white font-bold text-xs uppercase"
          >
            Book New Service
          </button>
        </div>
      </div>

      <!-- TAB 2: GARAGE -->
      <div v-if="activeTab === 'garage'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div
          v-for="car in (vehicles?.length ? vehicles : [
            { make: 'Porsche', model: '911 Turbo S', year: 2024, color: 'Guards Red', license_plate: 'VEN911', type: 'coupe', vin: 'WP0AB2A99NS298311' },
            { make: 'Lamborghini', model: 'Urus Performante', year: 2023, color: 'Nero Nemesis Matte', license_plate: 'URUS88', type: 'suv', vin: 'ZPBUA1ZL3PLA01928' }
          ])"
          :key="car.license_plate"
          class="glass-panel p-6 rounded-3xl border border-zinc-800 space-y-4"
        >
          <div class="flex items-center justify-between">
            <div>
              <span class="text-[10px] font-mono uppercase text-red-400 font-bold">{{ car.type }}</span>
              <h3 class="text-xl font-black text-white">{{ car.year }} {{ car.make }} {{ car.model }}</h3>
            </div>
            <span class="px-3 py-1 rounded-xl bg-zinc-900 border border-zinc-700 text-xs font-mono font-bold text-white">
              {{ car.license_plate }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-3 text-xs font-mono text-zinc-400 pt-2 border-t border-zinc-800">
            <div>
              <span class="text-zinc-500 text-[10px] block">Color:</span>
              <span class="text-white">{{ car.color }}</span>
            </div>
            <div>
              <span class="text-zinc-500 text-[10px] block">Protection Status:</span>
              <span class="text-emerald-400">9H Ceramic Active (5-Yr)</span>
            </div>
          </div>

          <button
            @click="isBookingModalOpen = true"
            class="w-full py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-200 font-bold text-xs uppercase tracking-wider border border-zinc-800 transition-colors"
          >
            Schedule Maintenance Wash
          </button>
        </div>
      </div>

      <!-- TAB 3: SERVICE HISTORY & RECEIPTS -->
      <div v-if="activeTab === 'history'" class="glass-panel rounded-3xl border border-zinc-800 overflow-hidden">
        <div class="p-6 border-b border-zinc-800">
          <h3 class="text-lg font-bold text-white">Digital Service Records & Invoices</h3>
          <p class="text-xs text-zinc-400">Official CarFax-registered detailing history for your vehicles</p>
        </div>

        <div class="divide-y divide-zinc-800/80 text-xs font-mono">
          <div
            v-for="b in (bookings?.length ? bookings : [
              { booking_code: 'VEN-8492', vehicle_summary: '2024 Porsche 911 Turbo S', service_package: { name: 'Diamond Ceramic Coating' }, booking_date: '2026-08-10', total_amount: 1197.00, status: 'completed' }
            ])"
            :key="b.booking_code"
            class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-zinc-900/40 transition-colors"
          >
            <div>
              <div class="flex items-center gap-2 text-zinc-400">
                <span class="text-white font-bold text-sm">{{ b.booking_code }}</span>
                <span>•</span>
                <span>{{ b.booking_date }}</span>
              </div>
              <div class="text-zinc-200 text-sm font-semibold mt-1">{{ b.vehicle_summary }}</div>
              <div class="text-red-400 text-xs">{{ b.service_package?.name || 'Ceramic Coating' }}</div>
            </div>

            <div class="flex items-center gap-4">
              <div class="text-right">
                <div class="text-sm font-extrabold text-white">${{ Number(b.total_amount) }} USD</div>
                <div class="text-emerald-400 text-[10px] uppercase font-bold">Paid in Full</div>
              </div>

              <Link
                :href="route('bookings.confirmation', b.booking_code)"
                class="px-3.5 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-700 flex items-center gap-1.5 transition-colors"
              >
                <Printer class="w-3.5 h-3.5" />
                <span>Invoice</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />

    <BookingWizardModal
      :is-open="isBookingModalOpen"
      @close="isBookingModalOpen = false"
    />

    <AIQuoteModal
      :is-open="isAiQuoteModalOpen"
      @close="isAiQuoteModalOpen = false"
      @start-booking="isBookingModalOpen = true"
    />

    <LoginModal
      :is-open="isLoginModalOpen"
      @close="isLoginModalOpen = false"
    />

    <WhatsAppWidget />
  </div>
</template>
