<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { Camera, Sparkles, MapPin, Clock, Trophy, Flame, Maximize2, Minimize2 } from 'lucide-vue-next';

const props = defineProps({
  targetUrl: { type: String, default: 'https://veneno.ae/hammer-challenge/audience' },
});

// Fullscreen State with Dedicated Button
const isFullscreen = ref(false);
const updateFullscreenState = () => {
  isFullscreen.value = !!document.fullscreenElement;
};

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(() => {});
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen().catch(() => {});
    }
  }
};

// Screen Wake Lock API (Keep display awake indefinitely)
let wakeLock = null;
const requestWakeLock = async () => {
  try {
    if ('wakeLock' in navigator) {
      wakeLock = await navigator.wakeLock.request('screen');
    }
  } catch (err) {
    console.log('Wake Lock Error:', err);
  }
};

// State
const qrDataUrl = ref('');
const bgCanvasRef = ref(null);
let bgAnimationId = null;

// Generate Ultra-Sharp Luxury QR Code with Embedded Veneno Official Emblem
const generateLuxuryQR = async () => {
  const qrSize = 720;
  const offscreenCanvas = document.createElement('canvas');
  offscreenCanvas.width = qrSize;
  offscreenCanvas.height = qrSize;

  try {
    await QRCode.toCanvas(offscreenCanvas, props.targetUrl, {
      width: qrSize,
      margin: 3,
      color: {
        dark: '#000000',
        light: '#ffffff',
      },
      errorCorrectionLevel: 'H',
    });

    const ctx = offscreenCanvas.getContext('2d');
    const center = qrSize / 2;
    const emblemSize = 68;
    const emblemRadius = 14;
    const halfSize = emblemSize / 2;
    const x = center - halfSize;
    const y = center - halfSize;

    const emblemImg = new Image();
    emblemImg.crossOrigin = 'anonymous';
    emblemImg.src = '/images/veneno-emblem.png';

    await new Promise((resolve) => {
      emblemImg.onload = () => {
        ctx.save();
        ctx.shadowColor = 'rgba(0,0,0,0.45)';
        ctx.shadowBlur = 12;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 4;

        ctx.fillStyle = '#ffffff';
        ctx.beginPath();
        ctx.roundRect(x - 4, y - 4, emblemSize + 8, emblemSize + 8, emblemRadius + 2);
        ctx.fill();

        ctx.beginPath();
        ctx.roundRect(x, y, emblemSize, emblemSize, emblemRadius);
        ctx.clip();
        ctx.drawImage(emblemImg, x, y, emblemSize, emblemSize);
        ctx.restore();
        resolve();
      };
      emblemImg.onerror = () => {
        // High contrast fallback badge
        ctx.save();
        ctx.fillStyle = '#dc2626';
        ctx.beginPath();
        ctx.roundRect(x, y, emblemSize, emblemSize, emblemRadius);
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.font = '900 32px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('V', center, center);
        ctx.restore();
        resolve();
      };
    });

    qrDataUrl.value = offscreenCanvas.toDataURL('image/png');
  } catch (err) {
    console.error('QR Generation failed:', err);
  }
};

// 60 FPS Particle Canvas Engine (Veneno Red & Electric Lime Green Embers)
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

  const particleCount = 65;
  const particles = [];

  for (let i = 0; i < particleCount; i++) {
    particles.push({
      x: Math.random() * width,
      y: Math.random() * height,
      radius: Math.random() * 2.5 + 1.0,
      color: Math.random() > 0.5 ? '#ef4444' : (Math.random() > 0.5 ? '#a3e635' : '#ffffff'),
      alpha: Math.random() * 0.7 + 0.2,
      speedX: (Math.random() - 0.5) * 0.6,
      speedY: -Math.random() * 1.2 - 0.3,
      pulse: Math.random() * Math.PI,
    });
  }

  const render = () => {
    ctx.clearRect(0, 0, width, height);

    for (let p of particles) {
      p.pulse += 0.03;
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
      ctx.shadowBlur = 10;
      ctx.shadowColor = p.color;
      ctx.fill();
      ctx.restore();
    }

    bgAnimationId = requestAnimationFrame(render);
  };

  render();
};

onMounted(() => {
  generateLuxuryQR();
  initParticleCanvas();
  requestWakeLock();
  document.addEventListener('fullscreenchange', updateFullscreenState);
});

onUnmounted(() => {
  if (bgAnimationId) cancelAnimationFrame(bgAnimationId);
  if (wakeLock) {
    wakeLock.release().catch(() => {});
    wakeLock = null;
  }
  document.removeEventListener('fullscreenchange', updateFullscreenState);
});
</script>

<template>
  <Head title="Veneno Hammer Challenge • Audience QR Display Screen" />

  <div
    class="relative w-screen h-screen overflow-hidden bg-[#070709] text-white flex flex-col justify-between select-none font-sans p-4 sm:p-8"
  >
    <!-- 60 FPS Particle Canvas Background -->
    <canvas ref="bgCanvasRef" class="absolute inset-0 pointer-events-none z-0"></canvas>

    <!-- Deep Ambient Glow Auras -->
    <div class="pointer-events-none absolute inset-0 z-0">
      <div class="absolute -top-[25%] left-1/2 -translate-x-1/2 w-[700px] h-[700px] rounded-full bg-gradient-to-b from-red-600/25 via-red-900/15 to-transparent blur-3xl"></div>
      <div class="absolute -bottom-[20%] left-1/2 -translate-x-1/2 w-[600px] h-[600px] rounded-full bg-gradient-to-t from-lime-500/15 via-red-950/20 to-transparent blur-3xl"></div>
    </div>

    <!-- Top Header / Main Exhibition Title -->
    <header class="relative z-10 flex flex-col items-center text-center space-y-2 pt-2 sm:pt-4">
      <div class="space-y-1">
        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-white to-lime-400 drop-shadow-2xl font-display">
          HAMMER CHALLENGE
        </h1>
        <p class="text-xl sm:text-3xl font-bold text-zinc-200 tracking-wide font-display">
          تحدي مطرقة فينينو
        </p>
      </div>
    </header>

    <!-- Center Content: Ultra-Sharp Scannable QR Code -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center py-4 my-auto">
      <!-- QR Card Container -->
      <div class="relative group">
        <!-- Ambient Glowing Borders -->
        <div class="absolute -inset-1.5 bg-gradient-to-r from-red-600 via-lime-400 to-red-600 rounded-3xl blur-xl opacity-75 group-hover:opacity-100 transition duration-1000 animate-pulse"></div>

        <!-- Inner Frame -->
        <div class="relative rounded-3xl bg-zinc-950/95 border-2 border-red-500/60 p-5 sm:p-7 shadow-2xl flex flex-col items-center">
          <!-- Scan Prompt Top Pill -->
          <div class="mb-4 inline-flex items-center gap-2 px-4 py-1.5 rounded-xl bg-red-950/70 border border-red-500/50 text-red-200 text-xs sm:text-sm font-bold uppercase tracking-wider shadow-inner">
            <Camera class="w-4 h-4 text-lime-400" />
            <span>Open Camera to Scan • افتح الكاميرا للمسح</span>
          </div>

          <!-- The QR Code Image -->
          <div class="relative overflow-hidden rounded-2xl bg-white p-3 shadow-inner">
            <img
              v-if="qrDataUrl"
              :src="qrDataUrl"
              alt="Scan QR to Register as Audience"
              class="w-56 h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 object-contain rounded-xl"
            />
            <div v-else class="w-56 h-56 sm:w-72 sm:h-72 flex items-center justify-center text-zinc-500">
              Generating High-Res QR...
            </div>

            <!-- Laser Scanline Sweeping Across QR -->
            <div class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-red-500 to-transparent shadow-[0_0_12px_#ef4444] animate-laser pointer-events-none"></div>
          </div>

          <!-- Slogan & Call to Action Below QR -->
          <div class="mt-4 text-center space-y-1.5 max-w-sm px-2">
            <div class="inline-flex items-center justify-center gap-2 px-4 py-1.5 rounded-full bg-lime-500/15 border border-lime-400/40 text-lime-400 text-sm sm:text-base font-black tracking-wide uppercase shadow-lg shadow-lime-950/40">
              <Sparkles class="w-4 h-4 text-lime-400 animate-pulse" />
              <span>SCAN • REVIEW • WIN</span>
              <span class="text-xs opacity-60">|</span>
              <span class="font-bold">امسح • قيّم • اربح</span>
              <Sparkles class="w-4 h-4 text-lime-400 animate-pulse" />
            </div>
            <p class="text-xs sm:text-sm font-bold text-white leading-snug">
              امسح الكود، اكتب تقييمك على Google وادخل السحب على جوائز مميزة!
            </p>
            <p class="text-[11px] text-zinc-400 font-medium">
              Scan the code, write Google review & enter the Raffle to win exciting prizes!
            </p>
          </div>
        </div>
      </div>
    </main>

    <!-- Bottom Footer: Event Details & Location Info -->
    <footer class="relative z-10 w-full max-w-4xl mx-auto pb-2 sm:pb-4 space-y-3">
      <!-- 3 Key Value Props -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#121216]/90 border border-zinc-800 backdrop-blur-md">
          <div class="w-10 h-10 rounded-xl bg-lime-500/10 border border-lime-500/30 flex items-center justify-center text-lime-400 shrink-0">
            <Trophy class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[10px] text-zinc-400 uppercase font-mono tracking-wider">Audience Prizes • جوائز الجمهور</div>
            <div class="text-sm font-bold text-white">Exciting Gifts & Vouchers</div>
          </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#121216]/90 border border-zinc-800 backdrop-blur-md">
          <div class="w-10 h-10 rounded-xl bg-red-600/10 border border-red-600/30 flex items-center justify-center text-red-400 shrink-0">
            <Clock class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[10px] text-zinc-400 uppercase font-mono tracking-wider">Event Timing • توقيت الفعالية</div>
            <div class="text-sm font-bold text-white">5:00 PM – 10:00 PM • 12 Sep 2026</div>
          </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#121216]/90 border border-zinc-800 backdrop-blur-md">
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
            <MapPin class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[10px] text-zinc-400 uppercase font-mono tracking-wider">Event Location • موقع الفعالية</div>
            <div class="text-sm font-bold text-white">Musaffah M37, Abu Dhabi</div>
          </div>
        </div>
      </div>

      <!-- Kiosk Help Footer with Dedicated Fullscreen Button -->
      <div class="flex items-center justify-between gap-4 pt-1 px-1">
        <div class="text-left text-[11px] text-zinc-400 font-mono">
          Official Raffle Platform • veneno.ae/hammer-challenge/audience
        </div>

        <button
          type="button"
          @click="toggleFullscreen"
          class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 text-zinc-300 hover:text-white text-xs font-mono font-semibold transition cursor-pointer shadow-md hover:border-red-500/50"
          :title="isFullscreen ? 'Exit Fullscreen' : 'Enter Fullscreen'"
        >
          <component :is="isFullscreen ? Minimize2 : Maximize2" class="w-3.5 h-3.5 text-lime-400" />
          <span>{{ isFullscreen ? 'Exit Fullscreen' : 'Fullscreen' }}</span>
        </button>
      </div>
    </footer>
  </div>
</template>

<style scoped>
@keyframes laser-sweep {
  0% {
    top: 0%;
    opacity: 0;
  }
  15% {
    opacity: 1;
  }
  85% {
    opacity: 1;
  }
  100% {
    top: 100%;
    opacity: 0;
  }
}

.animate-laser {
  animation: laser-sweep 2.4s ease-in-out infinite;
}
</style>
