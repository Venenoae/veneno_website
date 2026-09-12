<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import confetti from 'canvas-confetti';
import { 
  Trophy, 
  Flame, 
  Sparkles, 
  Volume2, 
  VolumeX, 
  Maximize2, 
  Minimize2, 
  ArrowLeft, 
  RotateCcw, 
  CheckCircle2, 
  Users, 
  Ticket,
  Clock,
  X,
  Send
} from 'lucide-vue-next';

const props = defineProps({
  initialEligibleCount: { type: Number, default: 0 },
  recentWinners: { type: Array, default: () => [] },
});

// Sound Settings & Web Audio Synthesizer
const isMuted = ref(false);
let audioCtx = null;

const initAudio = () => {
  if (!audioCtx && typeof window !== 'undefined') {
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    if (AudioContext) audioCtx = new AudioContext();
  }
  if (audioCtx && audioCtx.state === 'suspended') {
    audioCtx.resume();
  }
};

const playTickSound = (isUrgent = false) => {
  if (isMuted.value) return;
  try {
    initAudio();
    if (!audioCtx) return;

    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.type = isUrgent ? 'triangle' : 'sine';
    osc.frequency.setValueAtTime(isUrgent ? 880 : 440, audioCtx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(isUrgent ? 1760 : 220, audioCtx.currentTime + 0.08);

    gain.gain.setValueAtTime(0.18, audioCtx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);

    osc.connect(gain);
    gain.connect(audioCtx.destination);
    osc.start();
    osc.stop(audioCtx.currentTime + 0.08);
  } catch (e) {
    // Audio context may be restricted
  }
};

const playReelTick = () => {
  if (isMuted.value) return;
  try {
    initAudio();
    if (!audioCtx) return;

    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(320, audioCtx.currentTime);
    gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.04);
    osc.connect(gain);
    gain.connect(audioCtx.destination);
    osc.start();
    osc.stop(audioCtx.currentTime + 0.04);
  } catch (e) {}
};

const playFanfare = () => {
  if (isMuted.value) return;
  try {
    initAudio();
    if (!audioCtx) return;

    const chords = [523.25, 659.25, 783.99, 1046.50]; // C Major
    chords.forEach((freq, idx) => {
      setTimeout(() => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.2);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 1.2);
      }, idx * 150);
    });
  } catch (e) {}
};

// Fullscreen Control
const isFullscreen = ref(false);
const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(() => {});
    isFullscreen.value = true;
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen().catch(() => {});
      isFullscreen.value = false;
    }
  }
};

// Participants & State
const participants = ref([]);
const winners = ref([...props.recentWinners]);
const eligibleCount = ref(props.initialEligibleCount);
const isLoading = ref(true);

// State: 'idle' | 'drawing' | 'revealed'
const drawState = ref('idle');
const countdownNumber = ref(10);
const currentSlotItem = ref({ full_name: '---', ticket_number: '---' });
const selectedWinner = ref(null);
const showWinnerModal = ref(false);

let countdownInterval = null;
let reelInterval = null;

// Fetch Live Participants from Server
const loadParticipants = async () => {
  isLoading.value = true;
  try {
    const res = await window.axios.get('/api/hammer-challenge/raffle/participants');
    if (res.data.success) {
      participants.value = res.data.participants || [];
      eligibleCount.value = res.data.count || 0;
      winners.value = res.data.winners || [];
    }
  } catch (err) {
    console.error('Failed to load raffle participants:', err);
  } finally {
    isLoading.value = false;
  }
};

// Fire Continuous Confetti Celebration
const triggerCelebrationConfetti = () => {
  const duration = 4.5 * 1000;
  const end = Date.now() + duration;
  const colors = ['#ef4444', '#a3e635', '#ffffff', '#eab308'];

  (function frame() {
    confetti({
      particleCount: 7,
      angle: 60,
      spread: 55,
      origin: { x: 0, y: 0.7 },
      colors: colors,
      zIndex: 99999,
    });
    confetti({
      particleCount: 7,
      angle: 120,
      spread: 55,
      origin: { x: 1, y: 0.7 },
      colors: colors,
      zIndex: 99999,
    });

    if (Date.now() < end) {
      requestAnimationFrame(frame);
    }
  })();
};

// Modal Winner SMS Dispatch State
const isSendingModalSms = ref(false);
const modalSmsSent = ref(false);

const sendModalWinnerSms = async () => {
  if (!selectedWinner.value || isSendingModalSms.value) return;
  isSendingModalSms.value = true;
  try {
    const res = await window.axios.post(`/api/hammer-challenge/raffle/${selectedWinner.value.id}/send-sms`);
    if (res.data.success) {
      modalSmsSent.value = true;
    } else {
      alert(res.data.message || 'Failed to dispatch SMS.');
    }
  } catch (err) {
    alert(err.response?.data?.message || 'Error dispatching SMS.');
  } finally {
    isSendingModalSms.value = false;
  }
};

// Start 10-Second Live Raffle Draw
const startRaffleDraw = async () => {
  initAudio();
  if (drawState.value === 'drawing') return;
  if (participants.value.length === 0) {
    await loadParticipants();
    if (participants.value.length === 0) {
      alert('No eligible audience registrations found to draw.');
      return;
    }
  }

  drawState.value = 'drawing';
  countdownNumber.value = 10;
  showWinnerModal.value = false;
  selectedWinner.value = null;
  modalSmsSent.value = false;

  // Pre-fetch or lock the winning participant from the server
  let determinedWinner = null;
  try {
    const res = await window.axios.post('/api/hammer-challenge/raffle/draw');
    if (res.data.success) {
      determinedWinner = res.data.winner;
      eligibleCount.value = res.data.remaining_count;
    }
  } catch (e) {
    // Fallback in-memory pick if offline
    const randomIdx = Math.floor(Math.random() * participants.value.length);
    determinedWinner = participants.value[randomIdx];
  }

  // 1. High-Speed Slot Machine Reel Ticker
  let speed = 40; // ms per tick
  let elapsed = 0;

  const runReel = () => {
    if (drawState.value !== 'drawing') return;
    const rand = participants.value[Math.floor(Math.random() * participants.value.length)];
    if (rand) {
      currentSlotItem.value = rand;
      playReelTick();
    }
    elapsed += speed;

    // Decelerate dramatically in final 3 seconds
    if (countdownNumber.value <= 3) {
      speed = Math.min(300, speed + 25);
    }

    reelInterval = setTimeout(runReel, speed);
  };
  runReel();

  // 2. 10-Second Countdown Timer
  playTickSound(false);

  countdownInterval = setInterval(() => {
    countdownNumber.value -= 1;

    if (countdownNumber.value > 3) {
      playTickSound(false);
    } else if (countdownNumber.value > 0) {
      playTickSound(true); // Urgent pitch beep
    } else {
      // Countdown completed (0s reached!)
      clearInterval(countdownInterval);
      clearTimeout(reelInterval);

      // Lock on the winner
      selectedWinner.value = determinedWinner || currentSlotItem.value;
      currentSlotItem.value = selectedWinner.value;

      // Add to local winners list
      if (!winners.value.some(w => w.id === selectedWinner.value.id)) {
        winners.value.unshift({
          id: selectedWinner.value.id,
          full_name: selectedWinner.value.full_name,
          ticket_number: selectedWinner.value.ticket_number,
          won_at: new Date().toLocaleTimeString(),
        });
      }

      // Remove from active local participants
      participants.value = participants.value.filter(p => p.id !== selectedWinner.value.id);

      drawState.value = 'revealed';
      playFanfare();
      triggerCelebrationConfetti();
      showWinnerModal.value = true;
    }
  }, 1000);
};

// Reset Winners (Admin utility)
const resetWinners = async () => {
  if (!confirm('Are you sure you want to reset all raffle winners so everyone can participate again?')) return;
  try {
    await window.axios.post('/api/hammer-challenge/raffle/reset');
    await loadParticipants();
    winners.value = [];
    selectedWinner.value = null;
    drawState.value = 'idle';
  } catch (e) {
    alert('Failed to reset winners.');
  }
};

// Background Particle Canvas
const bgCanvasRef = ref(null);
let bgAnimationId = null;

const initParticleCanvas = () => {
  const canvas = bgCanvasRef.value;
  if (!canvas) return;
  const ctx = canvas.getContext('2d');

  let width = (canvas.width = window.innerWidth);
  let height = (canvas.height = window.innerHeight);

  window.addEventListener('resize', () => {
    if (!canvas) return;
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
  });

  const particleCount = 75;
  const particles = [];

  for (let i = 0; i < particleCount; i++) {
    particles.push({
      x: Math.random() * width,
      y: Math.random() * height,
      radius: Math.random() * 2.8 + 1.2,
      color: Math.random() > 0.5 ? '#ef4444' : (Math.random() > 0.4 ? '#a3e635' : '#ffffff'),
      alpha: Math.random() * 0.7 + 0.25,
      speedX: (Math.random() - 0.5) * 0.7,
      speedY: -Math.random() * 1.4 - 0.4,
      pulse: Math.random() * Math.PI,
    });
  }

  const render = () => {
    ctx.clearRect(0, 0, width, height);

    for (let p of particles) {
      p.pulse += 0.04;
      p.y += p.speedY;
      p.x += p.speedX;

      if (p.y < -10) {
        p.y = height + 10;
        p.x = Math.random() * width;
      }
      if (p.x < -10) p.x = width + 10;
      if (p.x > width + 10) p.x = -10;

      const currentAlpha = p.alpha * (0.6 + 0.4 * Math.sin(p.pulse));

      ctx.save();
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
      ctx.fillStyle = p.color;
      ctx.globalAlpha = Math.max(0, currentAlpha);
      ctx.shadowBlur = 12;
      ctx.shadowColor = p.color;
      ctx.fill();
      ctx.restore();
    }

    bgAnimationId = requestAnimationFrame(render);
  };

  render();
};

onMounted(() => {
  loadParticipants();
  initParticleCanvas();
});

onUnmounted(() => {
  if (countdownInterval) clearInterval(countdownInterval);
  if (reelInterval) clearTimeout(reelInterval);
  if (bgAnimationId) cancelAnimationFrame(bgAnimationId);
});
</script>

<template>
  <Head title="Veneno Hammer Challenge • Live Audience Raffle Draw" />

  <div 
    class="relative min-h-screen w-screen overflow-x-hidden bg-[#070709] text-white select-none font-sans flex flex-col justify-between p-4 sm:p-8"
  >
    <!-- 60 FPS Particle Canvas Background -->
    <canvas ref="bgCanvasRef" class="absolute inset-0 pointer-events-none z-0"></canvas>

    <!-- Deep Ambient Glow Auras -->
    <div class="pointer-events-none absolute inset-0 z-0">
      <div class="absolute -top-[25%] left-1/2 -translate-x-1/2 w-[800px] h-[800px] rounded-full bg-gradient-to-b from-red-600/25 via-red-950/20 to-transparent blur-3xl"></div>
      <div class="absolute -bottom-[20%] left-1/2 -translate-x-1/2 w-[700px] h-[700px] rounded-full bg-gradient-to-t from-lime-500/15 via-red-950/20 to-transparent blur-3xl"></div>
    </div>

    <!-- Top Action Bar (Admin Controls) -->
    <header class="relative z-30 flex items-center justify-between gap-4">
      <!-- Back to Dashboard -->
      <Link 
        href="/dashboard"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-800 text-xs font-mono uppercase tracking-wider text-zinc-300 hover:text-white transition shadow-lg backdrop-blur-md"
      >
        <ArrowLeft class="w-3.5 h-3.5 text-red-500" />
        <span>Dashboard</span>
      </Link>

      <!-- Center Brand Tag -->
      <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-zinc-900/90 border border-red-500/40 shadow-xl backdrop-blur-md">
        <div class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></div>
        <span class="text-xs font-mono font-bold tracking-[0.25em] text-white uppercase">
          VENENO AUTO CARE • STAGE RAFFLE ENGINE
        </span>
      </div>

      <!-- Audio & Fullscreen Buttons -->
      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="isMuted = !isMuted"
          class="p-2.5 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 hover:text-white transition cursor-pointer shadow-md backdrop-blur-md"
          :title="isMuted ? 'Unmute Sound Effects' : 'Mute Sound Effects'"
        >
          <Volume2 v-if="!isMuted" class="w-4 h-4 text-[#a3e635]" />
          <VolumeX v-else class="w-4 h-4 text-zinc-500" />
        </button>

        <button
          type="button"
          @click="toggleFullscreen"
          class="p-2.5 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 hover:text-white transition cursor-pointer shadow-md backdrop-blur-md"
          :title="isFullscreen ? 'Exit Fullscreen' : 'Enter Borderless Fullscreen'"
        >
          <Minimize2 v-if="isFullscreen" class="w-4 h-4 text-white" />
          <Maximize2 v-else class="w-4 h-4 text-white" />
        </button>
      </div>
    </header>

    <!-- Main Stage Content -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center py-6 my-auto text-center">
      
      <!-- Top Title Block -->
      <div class="space-y-2 mb-6 sm:mb-8">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-950/70 border border-red-500/50 text-red-300 text-xs font-mono font-bold uppercase tracking-widest shadow-inner">
          <Flame class="w-4 h-4 text-red-500 animate-pulse" />
          <span>OFFICIAL AUDIENCE PRIZE RAFFLE</span>
          <Flame class="w-4 h-4 text-red-500 animate-pulse" />
        </div>

        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-white to-[#a3e635] drop-shadow-2xl font-display">
          LIVE RAFFLE DRAW
        </h1>
        <p class="text-xl sm:text-2xl font-bold text-zinc-300">
          سحب الجمهور المباشر • الفائز العشوائي
        </p>
      </div>

      <!-- Central Action / Reel Area -->
      <div class="relative w-full max-w-2xl mx-auto">
        <!-- Ambient Glowing Borders -->
        <div class="absolute -inset-2 bg-gradient-to-r from-red-600 via-[#a3e635] to-red-600 rounded-3xl blur-2xl opacity-60 transition duration-1000" :class="{ 'animate-pulse opacity-100': drawState === 'drawing' }"></div>

        <!-- Inner Stage Card -->
        <div class="relative rounded-3xl bg-[#0c0c10]/95 border-2 border-red-600/60 p-6 sm:p-10 shadow-2xl shadow-black flex flex-col items-center backdrop-blur-2xl">
          
          <!-- State 1: IDLE -->
          <div v-if="drawState === 'idle'" class="w-full space-y-6">
            <!-- Audience Count Badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl bg-zinc-900 border border-zinc-700 text-sm font-mono text-zinc-300 shadow-inner">
              <Users class="w-4 h-4 text-[#a3e635]" />
              <span>Eligible Visitors in Pot:</span>
              <strong class="text-white text-base">{{ eligibleCount }}</strong>
            </div>

            <!-- Big Start Draw Trigger -->
            <div class="py-4">
              <button
                type="button"
                :disabled="isLoading || eligibleCount === 0"
                @click="startRaffleDraw"
                class="w-full sm:w-auto px-10 py-6 rounded-3xl bg-gradient-to-r from-red-600 via-red-500 to-red-600 hover:from-red-500 hover:to-red-400 text-white font-black text-xl sm:text-2xl uppercase tracking-widest shadow-2xl shadow-red-950/80 transition-all duration-300 transform hover:scale-105 active:scale-95 cursor-pointer ring-4 ring-red-500/40 flex items-center justify-center gap-4 mx-auto disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <Flame class="w-7 h-7 text-white animate-pulse" />
                <span>START 10-SEC LIVE DRAW</span>
                <Flame class="w-7 h-7 text-white animate-pulse" />
              </button>
            </div>

            <p class="text-xs text-zinc-400 font-mono uppercase tracking-wider">
              10-Second Suspense Reel • 100% Random Algorithmic Selection
            </p>
          </div>

          <!-- State 2: 10-SECOND LIVE COUNTDOWN & SLOT REEL -->
          <div v-else-if="drawState === 'drawing'" class="w-full space-y-6 animate-in fade-in duration-300">
            
            <!-- Giant Circular Laser Countdown Timer -->
            <div class="relative w-44 h-44 sm:w-52 sm:h-52 mx-auto flex items-center justify-center">
              <svg class="w-full h-full transform -rotate-90">
                <circle
                  cx="50%"
                  cy="50%"
                  r="42%"
                  class="stroke-zinc-800"
                  stroke-width="10"
                  fill="none"
                />
                <circle
                  cx="50%"
                  cy="50%"
                  r="42%"
                  class="stroke-red-500 transition-all duration-1000 ease-linear"
                  stroke-width="10"
                  stroke-linecap="round"
                  fill="none"
                  :stroke-dasharray="264"
                  :stroke-dashoffset="264 - (264 * countdownNumber) / 10"
                />
              </svg>

              <!-- Central Giant Countdown Number -->
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span 
                  class="text-6xl sm:text-7xl font-black font-mono tracking-tight text-white drop-shadow-[0_0_20px_#ef4444]"
                  :class="{ 'text-[#a3e635] scale-125 transition-transform': countdownNumber <= 3 }"
                >
                  {{ countdownNumber }}
                </span>
                <span class="text-[10px] font-mono uppercase tracking-[0.2em] text-zinc-400">SECONDS</span>
              </div>
            </div>

            <!-- Fast Spinning Slot Machine Reel Box -->
            <div class="relative w-full overflow-hidden rounded-2xl bg-zinc-950 border-2 border-red-500/60 p-4 shadow-inner">
              <div class="text-[10px] uppercase font-mono tracking-widest text-[#a3e635] mb-1">
                SHUFFLING AUDIENCE PASSES...
              </div>

              <div class="h-16 flex flex-col items-center justify-center overflow-hidden">
                <div class="text-2xl sm:text-3xl font-black text-white uppercase tracking-wider font-display truncate max-w-full">
                  {{ currentSlotItem.full_name }}
                </div>
                <div class="text-sm font-mono font-black text-red-400 tracking-widest mt-1">
                  {{ currentSlotItem.ticket_number }}
                </div>
              </div>

              <!-- Scanning Line -->
              <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 bg-[#a3e635] shadow-[0_0_12px_#a3e635] pointer-events-none opacity-80"></div>
            </div>
          </div>

          <!-- State 3: REVEALED IN CARD (Also opens Big Modal) -->
          <div v-else-if="drawState === 'revealed'" class="w-full space-y-5">
            <div class="w-16 h-16 rounded-full bg-[#a3e635]/20 border-2 border-[#a3e635] mx-auto flex items-center justify-center text-[#a3e635] shadow-lg shadow-lime-950/60">
              <Trophy class="w-8 h-8" />
            </div>

            <div>
              <div class="text-xs font-mono uppercase tracking-[0.25em] text-[#a3e635] font-bold">
                WINNER SELECTED!
              </div>
              <h2 class="text-3xl sm:text-5xl font-black uppercase text-white font-display mt-2">
                {{ selectedWinner?.full_name }}
              </h2>
              <div class="inline-block mt-3 px-5 py-2 rounded-xl bg-red-950/80 border border-red-500/60 text-red-300 text-lg font-mono font-black tracking-widest">
                {{ selectedWinner?.ticket_number }}
              </div>
            </div>

            <div class="pt-3 flex flex-wrap items-center justify-center gap-3">
              <button
                type="button"
                @click="showWinnerModal = true"
                class="px-6 py-3 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-xs uppercase font-mono tracking-wider transition cursor-pointer"
              >
                Reopen Winner Banner
              </button>

              <button
                type="button"
                @click="startRaffleDraw"
                class="px-8 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white font-black text-xs uppercase tracking-wider transition shadow-lg shadow-red-950/60 cursor-pointer"
              >
                Draw Next Winner ➔
              </button>
            </div>
          </div>

        </div>
      </div>
    </main>

    <!-- Bottom Footer: Recent Winners Log & Reset -->
    <footer class="relative z-20 w-full max-w-4xl mx-auto pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-400 font-mono">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-[#a3e635]"></span>
        <span>Past Winners: <strong class="text-white">{{ winners.length }}</strong></span>
      </div>

      <div class="flex items-center gap-4">
        <button
          v-if="winners.length > 0"
          type="button"
          @click="resetWinners"
          class="text-zinc-500 hover:text-red-400 text-[11px] underline transition cursor-pointer"
        >
          Reset Winners Pot
        </button>
        <span class="text-zinc-600">Veneno Auto Care Center • Abu Dhabi</span>
      </div>
    </footer>

    <!-- ================================================================= -->
    <!-- FANCY BIG MINIMAL WINNER MODAL (Requested Specification)          -->
    <!-- ================================================================= -->
    <div 
      v-if="showWinnerModal && selectedWinner"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/90 backdrop-blur-2xl animate-in fade-in duration-300"
    >
      <!-- Halo Glow Background -->
      <div class="pointer-events-none absolute w-[700px] h-[700px] rounded-full bg-gradient-to-r from-red-600/30 via-[#a3e635]/20 to-red-600/30 blur-3xl animate-pulse"></div>

      <!-- The Big Winner Card -->
      <div class="relative w-full max-w-3xl overflow-hidden rounded-3xl border-4 border-red-500/80 bg-gradient-to-b from-[#141014] via-[#0b0b0e] to-black p-8 sm:p-14 text-center shadow-2xl shadow-black ring-4 ring-black/80">
        
        <!-- Close Button -->
        <button
          type="button"
          @click="showWinnerModal = false"
          class="absolute top-6 right-6 p-3 rounded-full bg-zinc-900/80 hover:bg-zinc-800 text-zinc-400 hover:text-white border border-zinc-700 transition cursor-pointer"
        >
          <X class="w-6 h-6" />
        </button>

        <!-- Official Veneno Emblem in Big Modal -->
        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl overflow-hidden border-2 border-red-500 shadow-2xl shadow-red-950/80 mx-auto flex items-center justify-center bg-[#ef4444] p-3 mb-6">
          <img 
            src="/images/veneno-emblem.png" 
            alt="Veneno Official Emblem" 
            class="w-full h-full object-contain drop-shadow"
          />
        </div>

        <!-- Winner Subtitle Banner -->
        <div class="inline-flex items-center gap-2 px-5 py-1.5 rounded-full bg-red-950/80 border border-red-500 text-red-300 text-xs sm:text-sm font-mono font-bold uppercase tracking-[0.25em] mb-4 shadow-inner">
          <Sparkles class="w-4 h-4 text-[#a3e635]" />
          <span>CONGRATULATIONS • تهانينا للفائز</span>
          <Sparkles class="w-4 h-4 text-[#a3e635]" />
        </div>

        <!-- MINIMAL DETAIL 1: WINNER FULL NAME -->
        <div class="my-6">
          <h2 class="text-4xl sm:text-6xl md:text-7xl font-black uppercase tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-white to-amber-300 font-display drop-shadow-2xl leading-tight">
            {{ selectedWinner.full_name }}
          </h2>
        </div>

        <!-- MINIMAL DETAIL 2: WINNER TICKET NUMBER -->
        <div class="my-6 inline-block">
          <div class="rounded-2xl border-2 border-dashed border-[#a3e635]/80 bg-zinc-950/90 px-8 py-4 sm:px-12 sm:py-5 shadow-2xl shadow-lime-950/40">
            <div class="text-xs uppercase tracking-[0.25em] text-[#a3e635] font-mono font-bold mb-1">
              TICKET NUMBER • رقم التذكرة
            </div>
            <div class="text-3xl sm:text-5xl md:text-6xl font-black tracking-[0.18em] text-white font-mono drop-shadow-[0_0_15px_rgba(163,230,53,0.7)]">
              {{ selectedWinner.ticket_number }}
            </div>
          </div>
        </div>

        <!-- Action Controls Inside Modal -->
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 border-t border-zinc-800">
          <button
            type="button"
            :disabled="isSendingModalSms || modalSmsSent"
            @click="sendModalWinnerSms"
            class="w-full sm:w-auto px-8 py-4 rounded-2xl font-black text-sm uppercase tracking-wider transition shadow-xl flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-75"
            :class="modalSmsSent 
              ? 'bg-emerald-950 border border-emerald-500 text-emerald-300' 
              : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 text-white shadow-emerald-950/80'"
          >
            <CheckCircle2 v-if="modalSmsSent" class="w-4 h-4 text-emerald-400" />
            <Send v-else class="w-4 h-4" />
            <span>{{ modalSmsSent ? 'Winner SMS Delivered ✓' : (isSendingModalSms ? 'Dispatching SMS...' : 'Dispatch Winner SMS 📱') }}</span>
          </button>

          <button
            type="button"
            @click="showWinnerModal = false"
            class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-zinc-800 hover:bg-zinc-700 text-white font-bold text-sm uppercase tracking-wider transition cursor-pointer"
          >
            Close Modal
          </button>

          <button
            type="button"
            @click="showWinnerModal = false; startRaffleDraw();"
            class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-red-600 hover:from-red-500 hover:to-red-400 text-white font-black text-sm uppercase tracking-wider transition shadow-xl shadow-red-950/80 cursor-pointer flex items-center justify-center gap-2"
          >
            <RotateCcw class="w-4 h-4" />
            <span>Draw Next Winner</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>
