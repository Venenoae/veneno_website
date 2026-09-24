<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X, Lock, Mail, Eye, EyeOff, Loader2, ShieldCheck } from 'lucide-vue-next';

const props = defineProps({
  isOpen: Boolean,
});

const emit = defineEmits(['close']);

const showPassword = ref(false);

const loginForm = useForm({
  email: '',
  password: '',
  remember: true,
});

const handleLogin = () => {
  loginForm.post(route('login'), {
    onSuccess: () => emit('close'),
  });
};
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
    <div class="w-full max-w-md bg-[#121216]/95 border-2 border-[#c5a059]/40 shadow-[0_0_60px_rgba(0,0,0,0.8)] rounded-3xl p-6 sm:p-8 relative animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Close Button -->
      <button
        type="button"
        @click="emit('close')"
        class="absolute top-5 right-5 p-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white transition-colors cursor-pointer"
      >
        <X class="w-5 h-5" />
      </button>

      <!-- Header -->
      <div class="text-center mb-6 pt-1">
        <img 
          src="/images/logo.png" 
          alt="Veneno Auto Care" 
          title="Veneno Auto Care" 
          class="h-12 w-auto mx-auto object-contain mb-3" 
        />
        <h2 class="text-base font-black tracking-wider uppercase text-white font-mono">
          Client & Staff Portal
        </h2>
        <p class="text-xs text-zinc-400 mt-1">Sign in with your authorized credentials to access your portal</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-4 text-xs">
        <div>
          <label class="block text-zinc-300 font-bold mb-1.5 uppercase tracking-wider font-mono text-[11px]">Email Address</label>
          <div class="relative">
            <Mail class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3.5" />
            <input
              v-model="loginForm.email"
              type="email"
              required
              autocomplete="email"
              placeholder="name@veneno.ae"
              class="w-full pl-10 pr-3.5 py-3 rounded-2xl bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-600 focus:outline-none focus:border-[#c5a059] focus:ring-1 focus:ring-[#c5a059] transition-all text-sm"
            />
          </div>
          <div v-if="loginForm.errors.email" class="text-red-400 text-xs font-medium mt-1.5 pl-1">
            {{ loginForm.errors.email }}
          </div>
        </div>

        <div>
          <label class="block text-zinc-300 font-bold mb-1.5 uppercase tracking-wider font-mono text-[11px]">Password</label>
          <div class="relative">
            <Lock class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3.5" />
            <input
              v-model="loginForm.password"
              :type="showPassword ? 'text' : 'password'"
              required
              autocomplete="current-password"
              placeholder="••••••••••••"
              class="w-full pl-10 pr-11 py-3 rounded-2xl bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-600 focus:outline-none focus:border-[#c5a059] focus:ring-1 focus:ring-[#c5a059] transition-all text-sm"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-500 hover:text-zinc-300 transition-colors cursor-pointer"
            >
              <EyeOff v-if="showPassword" class="w-4 h-4" />
              <Eye v-else class="w-4 h-4" />
            </button>
          </div>
          <div v-if="loginForm.errors.password" class="text-red-400 text-xs font-medium mt-1.5 pl-1">
            {{ loginForm.errors.password }}
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input
              v-model="loginForm.remember"
              type="checkbox"
              class="w-4 h-4 rounded border-zinc-700 bg-zinc-900 text-red-600 focus:ring-red-500 focus:ring-offset-0"
            />
            <span class="text-xs text-zinc-400 hover:text-zinc-300">Keep me signed in</span>
          </label>
        </div>

        <button
          type="submit"
          :disabled="loginForm.processing"
          class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-red-600 via-[#c5a059] to-red-600 hover:brightness-110 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-950/50 flex items-center justify-center gap-2 transition-all disabled:opacity-50 cursor-pointer"
        >
          <Loader2 v-if="loginForm.processing" class="w-4 h-4 animate-spin" />
          <span>Authenticate & Access Portal</span>
        </button>
      </form>

      <!-- Footer Info -->
      <div class="pt-5 mt-4 border-t border-zinc-800/80 flex items-center justify-between text-[11px] font-mono text-zinc-500">
        <span>Veneno Auto Care Center</span>
        <div class="flex items-center gap-1 text-[#c5a059]">
          <ShieldCheck class="w-3.5 h-3.5" />
          <span>256-bit Encrypted</span>
        </div>
      </div>

    </div>
  </div>
</template>
