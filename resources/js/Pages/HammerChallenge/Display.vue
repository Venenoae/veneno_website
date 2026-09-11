<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { Camera, Trophy, Sparkles, MapPin, QrCode, Smartphone, Ticket, Flame } from 'lucide-vue-next';

const props = defineProps({
  targetUrl: { type: String, default: 'https://veneno.ae/hammer-challenge/register' },
});

// Fullscreen State (Double click / double tap anywhere on screen)
const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(() => {});
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen().catch(() => {});
    }
  }
};

// Screen Wake Lock API (Keep kiosk display awake indefinitely)
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

// Countdown to Hammer Challenge Final (12 Sept 2026 20:00:00 GST)
const countdown = ref({ days: '00', hours: '00', minutes: '00', seconds: '00' });
let countdownTimer = null;

const calculateCountdown = () => {
  const now = new Date().getTime();
  const targetDate = new Date('2026-09-12T20:00:00+04:00').getTime();
  const diff = Math.max(0, targetDate - now);

  const pad = (n) => String(n).padStart(2, '0');
  countdown.value = {
    days: pad(Math.floor(diff / (1000 * 60 * 60 * 24))),
    hours: pad(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))),
    minutes: pad(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))),
    seconds: pad(Math.floor((diff % (1000 * 60)) / 1000)),
  };
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
    const emblemSize = 64;
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
        
        ctx.beginPath();
        if (typeof ctx.roundRect === 'function') {
          ctx.roundRect(x - 4, y - 4, emblemSize + 8, emblemSize + 8, emblemRadius + 3);
        } else {
          ctx.rect(x - 4, y - 4, emblemSize + 8, emblemSize + 8);
        }
        ctx.fillStyle = '#ffffff';
        ctx.fill();
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#c5a059';
        ctx.stroke();

        ctx.beginPath();
        if (typeof ctx.roundRect === 'function') {
          ctx.roundRect(x, y, emblemSize, emblemSize, emblemRadius);
        } else {
          ctx.rect(x, y, emblemSize, emblemSize);
        }
        ctx.clip();
        ctx.drawImage(emblemImg, x, y, emblemSize, emblemSize);
        ctx.restore();

        resolve();
      };
      emblemImg.onerror = resolve;
    });

    qrDataUrl.value = offscreenCanvas.toDataURL('image/png');
  } catch (err) {
    console.error('Failed to generate Luxury QR:', err);
  }
};

// 60 FPS Fiery Embers & Sparks Floating Canvas Particle System
const initParticles = () => {
  const canvas = bgCanvasRef.value;
  if (!canvas) return;
  const ctx = canvas.getContext('2d');

  const resize = () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  };
  resize();
  window.addEventListener('resize', resize);

  const particles = [];
  const count = 55;
  const colors = ['#ef4444', '#dc2626', '#f59e0b', '#c5a059', '#e5c07b', '#ffffff'];

  for (let i = 0; i < count; i++) {
    particles.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      radius: Math.random() * 2.5 + 0.8,
      color: colors[Math.floor(Math.random() * colors.length)],
      vx: (Math.random() - 0.5) * 0.6,
      vy: -(Math.random() * 1.2 + 0.4),
      alpha: Math.random() * 0.7 + 0.3,
      pulse: Math.random() * 0.03 + 0.01,
      glow: Math.random() > 0.4,
    });
  }

  const render = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    particles.forEach((p) => {
      p.y += p.vy;
      p.x += p.vx;
      p.alpha += Math.sin(Date.now() * 0.003 + p.radius) * 0.01;
      p.alpha = Math.max(0.15, Math.min(0.9, p.alpha));

      if (p.y < -10) {
        p.y = canvas.height + 10;
        p.x = Math.random() * canvas.width;
      }
      if (p.x < -10) p.x = canvas.width + 10;
      if (p.x > canvas.width + 10) p.x = -10;

      ctx.save();
      ctx.globalAlpha = p.alpha;
      ctx.fillStyle = p.color;

      if (p.glow) {
        ctx.shadowColor = p.color;
        ctx.shadowBlur = 12;
      }

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });

    bgAnimationId = requestAnimationFrame(render);
  };

  render();
};

onMounted(() => {
  generateLuxuryQR();
  initParticles();
  calculateCountdown();
  countdownTimer = setInterval(calculateCountdown, 1000);
  requestWakeLock();
});

onUnmounted(() => {
  if (bgAnimationId) cancelAnimationFrame(bgAnimationId);
  if (countdownTimer) clearInterval(countdownTimer);
  if (wakeLock) {
    try {
      wakeLock.release();
    } catch {}
  }
});
</script>

<template>
  <Head title="VENENO HAMMER CHALLENGE • Big Screen Audience Display" />

  <div
    @dblclick="toggleFullscreen"
    class="relative w-screen min-h-screen bg-[#060608] text-white font-sans overflow-x-hidden overflow-y-auto select-none flex flex-col justify-between items-center cursor-default p-4 sm:p-6 lg:p-8"
  >
    <!-- Background Spark Particle Canvas -->
    <canvas ref="bgCanvasRef" class="absolute inset-0 pointer-events-none z-0"></canvas>

    <!-- Luxury Ambient Aura Spotlights -->
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[500px] bg-gradient-to-b from-red-600/25 via-amber-500/10 to-transparent rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-gradient-to-t from-red-700/20 via-amber-600/10 to-transparent rounded-full blur-[120px] pointer-events-none"></div>

    <!-- ========================================== -->
    <!-- TOP HEADER: Event Title & Grand Prize     -->
    <!-- ========================================== -->
    <header class="relative z-10 w-full max-w-5xl text-center pt-2">
      <!-- Live Badge & Event Series -->
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-red-950/80 via-zinc-900 to-amber-950/80 border border-red-500/40 shadow-xl backdrop-blur-md mb-3">
        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
        <span class="w-2 h-2 rounded-full bg-red-500"></span>
        <span class="text-[11px] sm:text-xs font-mono font-black uppercase tracking-[0.25em] text-red-300">
          VENENO AUTO CARE • OFFICIAL EVENT
        </span>
        <span class="text-zinc-600">•</span>
        <span class="text-[11px] sm:text-xs font-bold text-amber-300">
          مصفح M37، أبوظبي
        </span>
      </div>

      <!-- Main Dual-Language Event Typography -->
      <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none text-white drop-shadow-[0_4px_30px_rgba(239,68,68,0.4)]">
        VENENO HAMMER <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500">CHALLENGE</span>
      </h1>
      <p class="text-lg sm:text-2xl font-black tracking-wide text-zinc-300 mt-2">
        تحدي مطرقة فينينو • <span class="text-amber-400">النهائي الكبير</span>
      </p>

      <!-- Grand Prize Golden Banner -->
      <div class="mt-4 inline-flex flex-wrap items-center justify-center gap-3 px-6 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500/20 via-red-600/20 to-amber-500/20 border border-amber-400/50 shadow-2xl backdrop-blur-xl">
        <Trophy class="w-6 h-6 text-amber-300 animate-bounce" />
        <div class="text-center">
          <span class="text-xs sm:text-sm font-mono font-bold uppercase tracking-widest text-amber-300 block">
            1ST PLACE GRAND PRIZE • الجائزة الكبرى للفاِئز
          </span>
          <span class="text-2xl sm:text-4xl font-black text-white tracking-tight drop-shadow-[0_0_20px_rgba(251,191,36,0.35)]">
            AED 15,000 <span class="text-amber-400 text-lg sm:text-2xl">CASH PRIZE</span>
          </span>
        </div>
        <Flame class="w-6 h-6 text-red-400" />
      </div>
    </header>

    <!-- ========================================== -->
    <!-- CENTER: Laser Scannable Audience QR Code   -->
    <!-- ========================================== -->
    <main class="relative z-10 my-6 flex flex-col items-center">
      
      <!-- High Impact Glow Box -->
      <div class="relative group">
        <!-- Laser Glow Aura Outer Frame -->
        <div class="absolute -inset-4 rounded-3xl bg-gradient-to-r from-red-600 via-amber-500 to-red-600 opacity-60 blur-xl group-hover:opacity-100 transition-opacity duration-500 animate-pulse"></div>
        
        <div class="relative p-5 sm:p-7 rounded-3xl bg-[#0e0e12]/95 border-2 border-amber-400/60 shadow-[0_0_60px_rgba(239,68,68,0.35)] backdrop-blur-2xl flex flex-col items-center">
          
          <!-- Laser Scan Line Sweep -->
          <div class="relative rounded-2xl overflow-hidden p-3 bg-white shadow-2xl">
            <div class="laser-scanner"></div>

            <img
              v-if="qrDataUrl"
              :src="qrDataUrl"
              alt="Scan to Register for Veneno Hammer Challenge"
              class="w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96 object-contain block relative z-10"
            />
            <div v-else class="w-64 h-64 sm:w-80 sm:h-80 flex items-center justify-center text-zinc-500 font-mono text-xs">
              Generating High Precision QR...
            </div>
          </div>

          <!-- Dual-Language Action Label -->
          <div class="mt-4 text-center">
            <div class="flex items-center justify-center gap-2 text-white font-black text-sm sm:text-lg tracking-wider uppercase">
              <Camera class="w-5 h-5 text-amber-400 animate-pulse" />
              <span>POINT YOUR PHONE CAMERA TO REGISTER</span>
            </div>
            <p class="text-xs sm:text-sm font-bold text-amber-400 mt-0.5">
              وجّه كاميرا هاتفك نحو الرمز للتسجيل الفوري
            </p>
          </div>
        </div>
      </div>

      <!-- 3-Step Simple Audience Guide -->
      <div class="mt-6 grid grid-cols-3 gap-2.5 sm:gap-4 max-w-2xl w-full text-center">
        <!-- Step 1 -->
        <div class="p-3 rounded-2xl bg-zinc-950/80 border border-zinc-800 shadow-md">
          <div class="w-8 h-8 mx-auto rounded-xl bg-red-600/20 text-red-400 border border-red-500/30 flex items-center justify-center mb-1.5">
            <Camera class="w-4 h-4" />
          </div>
          <div class="text-[11px] sm:text-xs font-bold text-white uppercase">1. Open Camera</div>
          <div class="text-[10px] text-zinc-400">افتح الكاميرا</div>
        </div>

        <!-- Step 2 -->
        <div class="p-3 rounded-2xl bg-zinc-950/80 border border-amber-500/40 bg-amber-500/5 shadow-md">
          <div class="w-8 h-8 mx-auto rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center mb-1.5">
            <Smartphone class="w-4 h-4" />
          </div>
          <div class="text-[11px] sm:text-xs font-bold text-amber-300 uppercase">2. Name & Phone</div>
          <div class="text-[10px] text-zinc-300">الاسم ورقم الهاتف</div>
        </div>

        <!-- Step 3 -->
        <div class="p-3 rounded-2xl bg-zinc-950/80 border border-zinc-800 shadow-md">
          <div class="w-8 h-8 mx-auto rounded-xl bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center mb-1.5">
            <Ticket class="w-4 h-4" />
          </div>
          <div class="text-[11px] sm:text-xs font-bold text-white uppercase">3. Get VIP Ticket</div>
          <div class="text-[10px] text-zinc-400">احصل على تذكرتك</div>
        </div>
      </div>
    </main>

    <!-- ========================================== -->
    <!-- BOTTOM FOOTER: Event Details & Countdown   -->
    <!-- ========================================== -->
    <footer class="relative z-10 w-full max-w-4xl flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-zinc-950/90 border border-zinc-800 shadow-2xl backdrop-blur-xl">
      <!-- Location & Timing -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-red-600/20 border border-red-500/40 text-red-400 flex items-center justify-center shrink-0">
          <MapPin class="w-5 h-5" />
        </div>
        <div class="text-left">
          <div class="text-xs sm:text-sm font-bold text-white flex items-center gap-2">
            <span>Saturday, 12 Sept 2026</span>
            <span class="px-2 py-0.5 rounded bg-zinc-800 text-[10px] font-mono text-amber-400">8:00 PM</span>
          </div>
          <div class="text-[11px] text-zinc-400">
            📍 Veneno Auto Care Center • Musaffah M37, Abu Dhabi
          </div>
        </div>
      </div>

      <!-- Synchronized Live Countdown -->
      <div class="flex items-center gap-1.5 font-mono text-center">
        <div v-for="unit in [
          { key: 'days', label: 'Days' },
          { key: 'hours', label: 'Hrs' },
          { key: 'minutes', label: 'Min' },
          { key: 'seconds', label: 'Sec' }
        ]" :key="unit.key" class="px-2.5 py-1.5 rounded-xl bg-black/80 border border-zinc-700/80 min-w-[50px]">
          <div class="text-base sm:text-lg font-black text-amber-300 leading-none">
            {{ countdown[unit.key] }}
          </div>
          <div class="text-[9px] uppercase tracking-wider text-zinc-500 mt-0.5">
            {{ unit.label }}
          </div>
        </div>
      </div>
    </footer>

    <!-- Fullscreen hint -->
    <div class="relative z-10 text-[10px] font-mono uppercase tracking-widest text-zinc-600 mt-2 text-center">
      Double-tap anywhere for borderless 4K kiosk fullscreen
    </div>
  </div>
</template>

<style scoped>
.laser-scanner {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, transparent, #ef4444, #f59e0b, #ffffff, #f59e0b, #ef4444, transparent);
  box-shadow: 0 0 16px 4px rgba(239, 68, 68, 0.8), 0 0 30px 8px rgba(245, 158, 11, 0.5);
  animation: laserSweep 3s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
  z-index: 20;
  pointer-events: none;
}

@keyframes laserSweep {
  0% {
    top: 2%;
    opacity: 0.9;
  }
  50% {
    opacity: 1;
  }
  100% {
    top: 96%;
    opacity: 0.9;
  }
}
</style>
