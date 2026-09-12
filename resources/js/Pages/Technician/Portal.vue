<script setup>
import { ref } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import {
  Wrench,
  Car,
  Clock,
  CheckCircle2,
  AlertCircle,
  Sparkles,
  ArrowRight,
  ChevronRight,
  ShieldAlert,
  Droplets,
  Disc,
  LayoutDashboard
} from 'lucide-vue-next';

const props = defineProps({
  bookings: Array,
  myJobs: Array,
});

const isUpdating = ref(false);
const activeJobDetail = ref(null);

const stages = [
  { id: 'confirmed', label: 'Bay Booked', color: 'border-blue-500/50 text-blue-400' },
  { id: 'in_progress', label: 'Decon Wash & Prep', color: 'border-cyan-500/50 text-cyan-400' },
  { id: 'polishing', label: 'Paint Correction', color: 'border-amber-500/50 text-amber-400' },
  { id: 'curing', label: 'Ceramic / PPF Cure', color: 'border-red-500/50 text-red-400' },
  { id: 'qc_ready', label: '50-Point QC Ready', color: 'border-purple-500/50 text-purple-400' },
  { id: 'completed', label: 'Handover Completed', color: 'border-emerald-500/50 text-emerald-400' },
];

const updateJobStage = (booking, newStage) => {
  router.post(route('technician.bookings.stage', booking.id), {
    stage: newStage,
    notes: `Technician transitioned job to ${newStage.replace('_', ' ').toUpperCase()}`,
  });
};
</script>

<template>
  <Head title="Technician Bay Board - Veneno Auto Care Center" />

  <div class="min-h-screen flex flex-col bg-zinc-950 text-zinc-100 font-sans selection:bg-red-600 selection:text-white">
    <Navbar />

    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <!-- Bay Dashboard Header -->
      <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center shadow-lg">
            <Wrench class="w-7 h-7" />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-2xl font-display uppercase tracking-wide text-white">Technician Bay Floor</h1>
              <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-mono font-bold uppercase">
                Bay 1-4 Active
              </span>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">Live shop floor workflow manager • Stage transitions update customer live tracker in real-time</p>
          </div>
        </div>

        <Link
          :href="route('dashboard')"
          class="px-5 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 text-xs font-mono font-bold uppercase flex items-center gap-2"
        >
          <LayoutDashboard class="w-4 h-4 text-red-500" />
          <span>Switch to Admin CRM</span>
        </Link>
      </div>

      <!-- KANBAN BAY PIPELINE -->
      <div class="space-y-4">
        <h3 class="text-xs font-mono uppercase tracking-widest text-zinc-400 font-bold">
          Active Bay Stage Pipeline
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div
            v-for="st in stages"
            :key="st.id"
            class="glass-panel rounded-2xl p-4 border border-zinc-800/80 flex flex-col min-h-[450px]"
          >
            <!-- Column Header -->
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800/80 mb-3">
              <span class="text-xs font-bold text-white font-mono uppercase tracking-wider">{{ st.label }}</span>
              <span class="w-5 h-5 rounded-full bg-zinc-900 text-zinc-400 text-[10px] font-mono font-bold flex items-center justify-center border border-zinc-800">
                {{ bookings.filter(b => b.status === st.id).length }}
              </span>
            </div>

            <!-- Job Cards in Column -->
            <div class="space-y-3 flex-1 overflow-y-auto pr-0.5">
              <div
                v-for="job in bookings.filter(b => b.status === st.id)"
                :key="job.id"
                class="p-4 rounded-xl bg-zinc-900/90 border border-zinc-800 hover:border-red-500/40 transition-all space-y-3 shadow-md"
              >
                <div>
                  <div class="flex items-center justify-between text-[10px] font-mono text-zinc-500">
                    <span class="text-red-400 font-bold">{{ job.booking_code }}</span>
                    <span>{{ job.time_slot }}</span>
                  </div>
                  <h4 class="text-xs font-bold text-white mt-1 line-clamp-1">{{ job.vehicle_summary || 'Porsche 911' }}</h4>
                  <div class="text-[11px] text-zinc-400 line-clamp-1">{{ job.service_package?.name || 'Ceramic Coating' }}</div>
                </div>

                <!-- Addons tags -->
                <div v-if="job.addons?.length" class="flex flex-wrap gap-1">
                  <span
                    v-for="a in job.addons"
                    :key="a.id"
                    class="text-[9px] px-1.5 py-0.5 rounded bg-zinc-950 text-zinc-400 border border-zinc-800"
                  >
                    {{ a.name }}
                  </span>
                </div>

                <!-- Stage Transition Actions -->
                <div class="pt-2 border-t border-zinc-800/80 flex items-center justify-between text-[10px]">
                  <select
                    :value="job.status"
                    @change="updateJobStage(job, $event.target.value)"
                    class="w-full px-2 py-1.5 rounded-lg bg-zinc-950 border border-zinc-700 text-zinc-200 text-[11px] focus:outline-none focus:border-red-500"
                  >
                    <option v-for="s in stages" :key="s.id" :value="s.id">
                      → Move: {{ s.label }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Empty state in column -->
              <div
                v-if="!bookings.filter(b => b.status === st.id).length"
                class="h-32 flex items-center justify-center text-[11px] text-zinc-600 font-mono text-center"
              >
                No vehicles in this bay stage
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
