<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { Camera, Sparkles, MapPin, Clock, Trophy, Flame } from 'lucide-vue-next';

const props = defineProps({
  targetUrl: { type: String, default: 'https://veneno.ae/hammer-challenge/audience' },
});

// Fullscreen State (Double click anywhere on screen)
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
    emblemImg.src = '/images/adihex/veneno-qr-emblem.png';

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

// 60 FPS Particle Canvas Engine (Champagne Gold & Crimson Embers)
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
      color: Math.random() > 0.4 ? '#c5a059' : '#ef4444',
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
});

onUnmounted(() => {
  if (bgAnimationId) cancelAnimationFrame(bgAnimationId);
  if (wakeLock) {
    wakeLock.release().catch(() => {});
    wakeLock = null;
  }
});
</script>

<template>
  <Head title="Veneno Hammer Challenge • Audience QR Display Screen" />

  <div
    @dblclick="toggleFullscreen"
    class="relative w-screen h-screen overflow-hidden bg-[#070709] text-white flex flex-col justify-between select-none font-sans cursor-pointer p-4 sm:p-8"
  >
    <!-- 60 FPS Particle Canvas Background -->
    <canvas ref="bgCanvasRef" class="absolute inset-0 pointer-events-none z-0"></canvas>

    <!-- Deep Ambient Glow Auras -->
    <div class="pointer-events-none absolute inset-0 z-0">
      <div class="absolute -top-[25%] left-1/2 -translate-x-1/2 w-[700px] h-[700px] rounded-full bg-gradient-to-b from-red-600/25 via-red-900/15 to-transparent blur-3xl"></div>
      <div class="absolute -bottom-[20%] left-1/2 -translate-x-1/2 w-[600px] h-[600px] rounded-full bg-gradient-to-t from-amber-500/20 via-amber-700/10 to-transparent blur-3xl"></div>
    </div>

    <!-- Top Header / Official Banner -->
    <header class="relative z-10 flex flex-col items-center text-center space-y-3 pt-2 sm:pt-4">
      <!-- Veneno Brand Signature Badge -->
      <div class="inline-flex items-center gap-3 px-5 py-2 rounded-full bg-zinc-900/80 border border-amber-500/40 shadow-xl shadow-black/80 backdrop-blur-md">
        <div class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></div>
        <span class="text-xs sm:text-sm font-bold tracking-[0.25em] uppercase text-amber-300 font-mono">
          VENENO AUTO CARE CENTER • ABU DHABI
        </span>
      </div>

      <!-- Main Exhibition Title -->
      <div class="space-y-1">
        <div class="flex items-center justify-center gap-2 text-red-500 font-black tracking-widest text-sm uppercase">
          <Flame class="w-5 h-5 text-red-500 animate-pulse" />
          <span>LIVE EVENT • AUDIENCE & VISITOR PASS</span>
          <Flame class="w-5 h-5 text-red-500 animate-pulse" />
        </div>
        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-white to-amber-300 drop-shadow-2xl font-display">
          HAMMER CHALLENGE
        </h1>
        <p class="text-xl sm:text-3xl font-bold text-zinc-200 tracking-wide">
          تحدي مطرقة فينينو • تصريح حضور الجمهور
        </p>
      </div>
    </header>

    <!-- Center Content: Ultra-Sharp Scannable QR Code -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center py-4 my-auto">
      <!-- QR Card Container -->
      <div class="relative group">
        <!-- Ambient Glowing Borders -->
        <div class="absolute -inset-1.5 bg-gradient-to-r from-red-600 via-amber-500 to-red-600 rounded-3xl blur-xl opacity-75 group-hover:opacity-100 transition duration-1000 animate-pulse"></div>

        <!-- Inner Frame -->
        <div class="relative rounded-3xl bg-zinc-950/95 border-2 border-amber-500/60 p-5 sm:p-7 shadow-2xl flex flex-col items-center">
          <!-- Scan Prompt Top Pill -->
          <div class="mb-4 inline-flex items-center gap-2 px-4 py-1.5 rounded-xl bg-red-950/70 border border-red-500/50 text-red-300 text-xs sm:text-sm font-bold uppercase tracking-wider shadow-inner">
            <Camera class="w-4 h-4 text-amber-400" />
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

          <!-- Pass Type Badge Below QR -->
          <div class="mt-4 text-center space-y-1">
            <div class="text-sm sm:text-base font-black tracking-wide text-white uppercase flex items-center justify-center gap-2">
              <Sparkles class="w-4 h-4 text-amber-400" />
              <span>FREE AUDIENCE & VISITOR TICKET</span>
              <Sparkles class="w-4 h-4 text-amber-400" />
            </div>
            <div class="text-xs sm:text-sm text-amber-300/90 font-medium">
              احصل على تذكرة الحضور وتصريح الدخول المجاني فوراً
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Bottom Footer: Event Details & Location Info -->
    <footer class="relative z-10 w-full max-w-4xl mx-auto pb-2 sm:pb-4 space-y-3">
      <!-- 3 Key Value Props -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#121216]/90 border border-zinc-800 backdrop-blur-md">
          <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0">
            <Trophy class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[10px] text-zinc-400 uppercase font-mono tracking-wider">Grand Prize</div>
            <div class="text-sm font-bold text-white">AED 15,000 Cash</div>
          </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#121216]/90 border border-zinc-800 backdrop-blur-md">
          <div class="w-10 h-10 rounded-xl bg-red-600/10 border border-red-600/30 flex items-center justify-center text-red-400 shrink-0">
            <Clock class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[10px] text-zinc-400 uppercase font-mono tracking-wider">Audience Check-in</div>
            <div class="text-sm font-bold text-white">7:30 PM • 12 Sep 2026</div>
          </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#121216]/90 border border-zinc-800 backdrop-blur-md">
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
            <MapPin class="w-5 h-5" />
          </div>
          <div>
            <div class="text-[10px] text-zinc-400 uppercase font-mono tracking-wider">Event Location</div>
            <div class="text-sm font-bold text-white">Musaffah M37, Abu Dhabi</div>
          </div>
        </div>
      </div>

      <!-- Kiosk Help Footer -->
      <div class="text-center text-[11px] text-zinc-400 font-mono">
        Double click / double tap anywhere for borderless Fullscreen • veneno.ae/hammer-challenge/audience
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
