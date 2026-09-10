<script setup>
import { ref, computed } from 'vue';
import { Head, router, Link, usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import {
  LayoutDashboard,
  DollarSign,
  TrendingUp,
  Users,
  MessageSquare,
  Sparkles,
  Send,
  ShieldCheck,
  CheckCircle2,
  AlertCircle,
  Car,
  Printer,
  ChevronRight,
  ChevronDown,
  Filter,
  Search,
  Clock,
  MapPin,
  ExternalLink,
  X,
  Phone,
  Trash2,
  Tv,
  Gift,
  Crown,
  Trophy,
  Check,
  RefreshCw,
  KeyRound,
  LogOut,
  UserCog,
  Lock,
  Eye,
  EyeOff,
  User,
  ShieldAlert,
  UserCheck
} from 'lucide-vue-next';

const props = defineProps({
  adihexLeads: { type: Array, default: () => [] },
  adihexStats: { type: Object, default: () => ({}) },
  inquiries: { type: Array, default: () => [] },
  inquiryStats: { type: Object, default: () => ({}) },
  hammerRegistrations: { type: Array, default: () => [] },
  users: { type: Array, default: () => [] },
});

const currentTab = ref('adihex'); // 'adihex' | 'hammer' | 'inquiries'

// ==========================================
// ADIHEX 2026 CRM Filters & Search
// ==========================================
const adihexTierFilter = ref('all');
const adihexStatusFilter = ref('all');
const adihexSearch = ref('');
const redeemCodeInput = ref('');
const redeemMessage = ref(null);
const isRedeeming = ref(false);

const filteredAdihexLeads = computed(() => {
  let list = props.adihexLeads || [];
  
  if (adihexTierFilter.value !== 'all') {
    list = list.filter(l => l.lead_tier === adihexTierFilter.value);
  }

  if (adihexStatusFilter.value !== 'all') {
    if (adihexStatusFilter.value === 'paid_deposit') {
      list = list.filter(l => l.deposit_status === 'paid');
    } else if (adihexStatusFilter.value === 'unredeemed') {
      list = list.filter(l => !l.is_redeemed);
    } else if (adihexStatusFilter.value === 'redeemed') {
      list = list.filter(l => l.is_redeemed);
    }
  }

  if (adihexSearch.value.trim()) {
    const q = adihexSearch.value.toLowerCase();
    list = list.filter(l =>
      (l.name && l.name.toLowerCase().includes(q)) ||
      (l.phone && l.phone.toLowerCase().includes(q)) ||
      (l.voucher_code && l.voucher_code.toLowerCase().includes(q)) ||
      (l.won_prize_label && l.won_prize_label.toLowerCase().includes(q)) ||
      (l.selected_package_name && l.selected_package_name.toLowerCase().includes(q))
    );
  }

  return list;
});

const handleRedeemVoucher = async () => {
  if (!redeemCodeInput.value.trim()) return;
  isRedeeming.value = true;
  redeemMessage.value = null;
  try {
    const res = await window.axios.post(route('api.adihex.redeem'), {
      voucher_code: redeemCodeInput.value.trim(),
    });
    redeemMessage.value = { type: 'success', text: res.data.message };
    redeemCodeInput.value = '';
    router.reload({ only: ['adihexLeads', 'adihexStats'] });
  } catch (err) {
    redeemMessage.value = {
      type: 'error',
      text: err.response?.data?.message || 'Failed to redeem voucher code.',
    };
  } finally {
    isRedeeming.value = false;
  }
};

// ==========================================
// ADIHEX WhatsApp 1-Click Outreach Modal
// ==========================================
const isWhatsAppModalOpen = ref(false);
const selectedLeadForWhatsApp = ref(null);
const customWhatsAppMessage = ref('');
const activeWhatsAppTemplate = ref('confirmation');

const openWhatsAppModal = (lead) => {
  selectedLeadForWhatsApp.value = lead;
  updateWhatsAppTemplate('confirmation');
  isWhatsAppModalOpen.value = true;
};

const updateWhatsAppTemplate = (templateType) => {
  activeWhatsAppTemplate.value = templateType;
  const lead = selectedLeadForWhatsApp.value;
  if (!lead) return;

  const isAr = (lead.locale === 'ar');
  const code = lead.voucher_code;
  const prize = lead.won_prize_label;
  const hasPackage = (lead.deposit_status === 'paid' && lead.selected_package_name);

  if (templateType === 'confirmation') {
    if (isAr) {
      customWhatsAppMessage.value = hasPackage
        ? `مرحباً ${lead.name}، شكراً لزيارتك جناح فينينو للعناية بالسيارات في معرض أديهيكس 2026!\nنؤكد استلام عربون 500 درهم وحجز باقة "${lead.selected_package_name}".\nكود القسيمة: ${code}\nجائزتك: ${prize}\nيسعدنا التنسيق معك لتحديد موعد حضور سيارتك لمركزنا في مصفح M37، أبوظبي.`
        : `Hello ${lead.name}, thank you for visiting Veneno Auto Care at ADIHEX 2026!\nWe confirm your AED 500 deposit for "${lead.selected_package_name}".\nVoucher Code: ${code}\nWon Prize: ${prize}\nWhen would you like to schedule your workshop visit in Musaffah M37, Abu Dhabi?`;
    } else {
      customWhatsAppMessage.value = hasPackage
        ? `Hello ${lead.name}, thank you for visiting Veneno Auto Care at ADIHEX 2026!\nWe confirm your AED 500 deposit for "${lead.selected_package_name}".\nVoucher Code: ${code}\nWon Prize: ${prize}\nWhen would you like to schedule your workshop visit in Musaffah M37, Abu Dhabi?`
        : `Hello ${lead.name}, thank you for visiting Veneno Auto Care at ADIHEX 2026!\nWe confirm your won prize: "${prize}".\nVoucher Code: ${code}\nValid for 60 days. When would you like to bring your vehicle in?`;
    }
  } else if (templateType === 'appointment') {
    if (isAr) {
      customWhatsAppMessage.value = `أهلاً بك ${lead.name}، نود التنسيق معك بخصوص موعد زيارتك لمركز فينينو للعناية بالسيارات لاستخدام قسيمتك (${code}).\nيرجى تزويدنا بنوع السيارة واليوم المناسب لك لنقوم بتجهيز مسار الخدمة الخاص بك.`;
    } else {
      customWhatsAppMessage.value = `Dear ${lead.name}, we would like to coordinate your upcoming appointment at Veneno Auto Care for voucher (${code}).\nPlease share your vehicle model and preferred day so our team can prepare your dedicated service bay.`;
    }
  } else if (templateType === 'exclusive_offer') {
    if (isAr) {
      customWhatsAppMessage.value = `مرحباً ${lead.name}، يسر فريق فينينو تقديم ترقية حصرية خاصة لزوار أديهيكس على باقات حماية النانو سيراميك وPPF بجانب جائزتك (${prize}). هل ترغب بالحصول على استشارة فنية مخصصة لسيارتك؟`;
    } else {
      customWhatsAppMessage.value = `Hello ${lead.name}, Veneno Auto Care is pleased to offer you an exclusive ADIHEX upgrade on our Nano-Ceramic and PPF protection packages alongside your prize (${prize}). Would you like a complimentary consultation for your car?`;
    }
  }
};

const getWhatsAppDirectUrl = computed(() => {
  if (!selectedLeadForWhatsApp.value) return '#';
  let phone = (selectedLeadForWhatsApp.value.phone || '').replace(/[^\d]/g, '');
  if (phone.startsWith('05')) phone = '971' + phone.substring(1);
  else if (phone.startsWith('5') && phone.length === 9) phone = '971' + phone;
  return `https://wa.me/${phone}?text=${encodeURIComponent(customWhatsAppMessage.value)}`;
});

// ==========================================
// Website Quote Inquiries Filters & Search
// ==========================================
const inquiryStatusFilter = ref('all');
const inquirySearch = ref('');

const filteredInquiries = computed(() => {
  let list = props.inquiries || [];
  
  if (inquiryStatusFilter.value !== 'all') {
    list = list.filter(i => i.status === inquiryStatusFilter.value);
  }
  
  if (inquirySearch.value.trim()) {
    const q = inquirySearch.value.toLowerCase();
    list = list.filter(i =>
      (i.customer_name && i.customer_name.toLowerCase().includes(q)) ||
      (i.phone && i.phone.toLowerCase().includes(q)) ||
      (i.service_requested && i.service_requested.toLowerCase().includes(q)) ||
      (i.vehicle_details && i.vehicle_details.toLowerCase().includes(q)) ||
      (i.message_text && i.message_text.toLowerCase().includes(q))
    );
  }
  
  return list;
});

const handleUpdateInquiry = (inquiryId, status) => {
  router.patch(route('dashboard.inquiries.update', inquiryId), { status }, {
    preserveScroll: true,
  });
};

const handleDeleteInquiry = (inquiryId) => {
  if (confirm('Are you sure you want to delete this quote lead record?')) {
    router.delete(route('dashboard.inquiries.destroy', inquiryId), {
      preserveScroll: true,
    });
  }
};

const hammerSearch = ref('');
const hammerStatusFilter = ref('all');

const filteredHammerRegistrations = computed(() => {
  let list = props.hammerRegistrations || [];

  if (hammerStatusFilter.value !== 'all') {
    list = list.filter(registration => registration.status === hammerStatusFilter.value);
  }

  const query = hammerSearch.value.trim().toLowerCase();
  if (query) {
    list = list.filter(registration =>
      [registration.registration_number, registration.full_name, registration.mobile, registration.email]
        .some(value => value && value.toLowerCase().includes(query))
    );
  }

  return list;
});

const updateHammerStatus = (registrationId, status) => {
  router.patch(route('dashboard.hammer-challenge.status', registrationId), { status }, { preserveScroll: true });
};

// ==========================================
// Admin Profile, Password & Logout Management
// ==========================================
const page = usePage();
const authUser = computed(() => page.props.auth?.user || {});

const isProfileMenuOpen = ref(false);
const isSecurityModalOpen = ref(false);
const securityModalTab = ref('my_password'); // 'my_password' | 'staff_accounts'

// My Password State
const myPassword = ref('');
const myPasswordConfirm = ref('');
const showMyPassword = ref(false);
const myPasswordProcessing = ref(false);
const myPasswordError = ref(null);
const myPasswordSuccess = ref(null);

const handleChangeMyPassword = () => {
  myPasswordError.value = null;
  myPasswordSuccess.value = null;

  if (!myPassword.value) {
    myPasswordError.value = 'Please enter a new password.';
    return;
  }
  if (myPassword.value.length < 6) {
    myPasswordError.value = 'Password must be at least 6 characters.';
    return;
  }
  if (myPassword.value !== myPasswordConfirm.value) {
    myPasswordError.value = 'Passwords do not match.';
    return;
  }

  myPasswordProcessing.value = true;
  router.post(
    route('dashboard.password.change'),
    {
      password: myPassword.value,
      password_confirmation: myPasswordConfirm.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        myPassword.value = '';
        myPasswordConfirm.value = '';
        myPasswordSuccess.value = 'Your password has been changed successfully!';
        myPasswordProcessing.value = false;
      },
      onError: (errors) => {
        myPasswordError.value = errors.password || Object.values(errors)[0] || 'Failed to update password.';
        myPasswordProcessing.value = false;
      },
      onFinish: () => {
        myPasswordProcessing.value = false;
      },
    }
  );
};

// Staff Password Reset State
const staffSearch = ref('');
const selectedStaffUser = ref(null);
const staffNewPassword = ref('');
const staffNewPasswordConfirm = ref('');
const showStaffPassword = ref(false);
const staffResetProcessing = ref(false);
const staffResetError = ref(null);
const staffResetSuccess = ref(null);

const filteredStaffUsers = computed(() => {
  let list = props.users || [];
  if (staffSearch.value.trim()) {
    const q = staffSearch.value.toLowerCase();
    list = list.filter(
      (u) =>
        (u.name && u.name.toLowerCase().includes(q)) ||
        (u.email && u.email.toLowerCase().includes(q)) ||
        (u.role && u.role.toLowerCase().includes(q))
    );
  }
  return list;
});

const openStaffPasswordReset = (user) => {
  selectedStaffUser.value = user;
  staffNewPassword.value = '';
  staffNewPasswordConfirm.value = '';
  staffResetError.value = null;
  staffResetSuccess.value = null;
};

const handleResetStaffPassword = () => {
  if (!selectedStaffUser.value) return;
  staffResetError.value = null;
  staffResetSuccess.value = null;

  if (!staffNewPassword.value) {
    staffResetError.value = 'Please enter a new password for this staff account.';
    return;
  }
  if (staffNewPassword.value.length < 6) {
    staffResetError.value = 'Password must be at least 6 characters.';
    return;
  }
  if (staffNewPassword.value !== staffNewPasswordConfirm.value) {
    staffResetError.value = 'Passwords do not match.';
    return;
  }

  staffResetProcessing.value = true;
  router.post(
    route('dashboard.users.reset-password', selectedStaffUser.value.id),
    {
      password: staffNewPassword.value,
      password_confirmation: staffNewPasswordConfirm.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        staffResetSuccess.value = `Password for ${selectedStaffUser.value.name} (${selectedStaffUser.value.email}) has been successfully updated!`;
        staffNewPassword.value = '';
        staffNewPasswordConfirm.value = '';
        staffResetProcessing.value = false;
      },
      onError: (errors) => {
        staffResetError.value = errors.password || Object.values(errors)[0] || 'Failed to reset password.';
        staffResetProcessing.value = false;
      },
      onFinish: () => {
        staffResetProcessing.value = false;
      },
    }
  );
};

const handleLogout = () => {
  if (confirm('Are you sure you want to log out of the Admin Dashboard?')) {
    router.post(route('logout'));
  }
};
</script>

<template>
  <Head title="ADIHEX 2026 & Leads Command Center - Veneno Auto Care" />

  <div class="min-h-screen flex flex-col bg-[#070709] text-zinc-100 font-sans selection:bg-red-600 selection:text-white">
    <Navbar />

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      
      <!-- Top Command Header -->
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-zinc-950 via-[#121216] to-zinc-950 border border-zinc-800/90 p-6 sm:p-8 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="flex items-start sm:items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500/20 to-red-600/20 border border-amber-500/40 text-amber-400 flex items-center justify-center shrink-0 shadow-inner">
              <Sparkles class="w-7 h-7 text-amber-400" />
            </div>
            <div>
              <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                  ADIHEX 2026 & Leads Hub
                </h1>
                <span class="px-3 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 text-[11px] font-mono font-bold uppercase tracking-wider flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                  Live Telemetry
                </span>
              </div>
              <p class="text-xs sm:text-sm text-zinc-400 mt-1">
                ADNEC Hall 4 Booth Activation • Real-time visitor spins, show package deposits, instant voucher redemptions, and website inquiries
              </p>
            </div>
          </div>

          <!-- Quick Actions & Admin Profile Dropdown -->
          <div class="flex flex-wrap items-center gap-2.5">
            <a
              :href="route('dashboard.adihex.export')"
              class="px-4 py-2.5 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 text-zinc-200 hover:text-white text-xs font-mono font-bold flex items-center gap-2 transition-all shadow-sm"
              title="Export all ADIHEX leads as CSV"
            >
              <Printer class="w-3.5 h-3.5 text-amber-400" />
              <span class="hidden sm:inline">Export CSV</span>
            </a>

            <a
              href="/adihex/display"
              target="_blank"
              class="px-4 py-2.5 rounded-xl bg-zinc-900/90 hover:bg-zinc-800 border border-zinc-700/80 text-zinc-200 hover:text-white text-xs font-mono font-bold flex items-center gap-2 transition-all shadow-sm"
              title="Open 22-Inch Portrait Kiosk Display"
            >
              <Tv class="w-3.5 h-3.5 text-red-400" />
              <span class="hidden sm:inline">22" Display</span>
            </a>

            <a
              href="/adihex"
              target="_blank"
              class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-600 via-red-500 to-amber-600 hover:brightness-110 text-white text-xs font-mono font-bold uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-red-950/60 transition-transform active:scale-95"
            >
              <span>Booth App</span>
              <ExternalLink class="w-3.5 h-3.5" />
            </a>

            <!-- Admin Profile Chip & Menu -->
            <div class="relative">
              <button
                type="button"
                @click="isProfileMenuOpen = !isProfileMenuOpen"
                class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-zinc-900/95 hover:bg-zinc-800 border border-amber-500/50 hover:border-amber-400 text-zinc-100 transition-all shadow-md group cursor-pointer"
                title="Admin Profile & Security"
              >
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-500/30 to-red-600/30 border border-amber-500/60 flex items-center justify-center text-amber-300 font-bold text-xs uppercase shadow-inner">
                  {{ authUser.name ? authUser.name.charAt(0) : 'A' }}
                </div>
                <div class="text-left hidden md:block">
                  <div class="text-xs font-bold text-white flex items-center gap-1.5 leading-tight">
                    <span>{{ authUser.name || 'Admin' }}</span>
                    <span class="px-1.5 py-0.2 rounded bg-red-600/25 text-red-300 border border-red-500/40 text-[9px] font-mono uppercase font-bold">
                      {{ authUser.role || 'SUPER_ADMIN' }}
                    </span>
                  </div>
                </div>
                <ChevronDown class="w-3.5 h-3.5 text-zinc-400 transition-transform duration-200" :class="{ 'rotate-180': isProfileMenuOpen }" />
              </button>

              <!-- Profile Dropdown Menu -->
              <div
                v-if="isProfileMenuOpen"
                class="absolute right-0 mt-2 w-72 rounded-2xl bg-[#101014]/98 backdrop-blur-2xl border border-zinc-700/80 p-2 shadow-2xl shadow-black/95 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
              >
                <div class="px-3.5 py-3 border-b border-zinc-800/90 mb-1.5 bg-zinc-900/50 rounded-xl">
                  <div class="text-xs font-bold text-white flex items-center justify-between">
                    <span>{{ authUser.name || 'Administrator' }}</span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 font-mono font-bold uppercase">
                      {{ authUser.role || 'SUPER_ADMIN' }}
                    </span>
                  </div>
                  <div class="text-[11px] text-zinc-400 font-mono truncate mt-0.5">{{ authUser.email || 'admin@venenoautocare.com' }}</div>
                </div>

                <button
                  type="button"
                  @click="isProfileMenuOpen = false; isSecurityModalOpen = true; securityModalTab = 'my_password'"
                  class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-200 hover:text-white hover:bg-zinc-800/90 transition-colors text-left cursor-pointer group"
                >
                  <KeyRound class="w-4 h-4 text-amber-400 group-hover:scale-110 transition-transform" />
                  <div>
                    <div>Change My Password</div>
                    <div class="text-[10px] text-zinc-500 font-normal">Update current admin login key</div>
                  </div>
                </button>

                <button
                  type="button"
                  @click="isProfileMenuOpen = false; isSecurityModalOpen = true; securityModalTab = 'staff_accounts'"
                  class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-zinc-200 hover:text-white hover:bg-zinc-800/90 transition-colors text-left cursor-pointer group"
                >
                  <UserCog class="w-4 h-4 text-blue-400 group-hover:scale-110 transition-transform" />
                  <div>
                    <div>Staff Accounts & Passwords</div>
                    <div class="text-[10px] text-zinc-500 font-normal">Reset passwords for team members</div>
                  </div>
                </button>

                <div class="border-t border-zinc-800/80 my-1.5"></div>

                <button
                  type="button"
                  @click="isProfileMenuOpen = false; handleLogout()"
                  class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-red-400 hover:text-red-200 hover:bg-red-950/50 border border-transparent hover:border-red-800/50 transition-colors text-left cursor-pointer group"
                >
                  <LogOut class="w-4 h-4 text-red-400 group-hover:scale-110 transition-transform" />
                  <span>Log Out of Dashboard</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPI Metrics Overview -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 font-mono text-xs">
        <!-- Total Spins -->
        <div class="bg-zinc-950/80 p-4 rounded-2xl border border-zinc-800/80 space-y-1.5 hover:border-zinc-700 transition-colors shadow-sm">
          <div class="flex items-center justify-between text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">
            <span>Booth Spins</span>
            <Users class="w-3.5 h-3.5 text-blue-400" />
          </div>
          <div class="text-2xl font-black text-white">{{ adihexStats?.totalSpins || adihexLeads.length }}</div>
          <div class="text-[10px] text-zinc-500 truncate">Verified UAE visitors</div>
        </div>

        <!-- Paid VIP Reservations -->
        <div class="bg-zinc-950/80 p-4 rounded-2xl border border-emerald-900/40 bg-emerald-950/10 space-y-1.5 hover:border-emerald-800/60 transition-colors shadow-sm">
          <div class="flex items-center justify-between text-[10px] text-emerald-400 uppercase tracking-wider font-semibold">
            <span>VIP Reservations</span>
            <Crown class="w-3.5 h-3.5 text-amber-400" />
          </div>
          <div class="text-2xl font-black text-emerald-400">{{ adihexStats?.paidReservations || 0 }}</div>
          <div class="text-[10px] text-emerald-500">AED 500 deposits paid</div>
        </div>

        <!-- Deposit Cashflow -->
        <div class="bg-zinc-950/80 p-4 rounded-2xl border border-zinc-800/80 space-y-1.5 hover:border-zinc-700 transition-colors shadow-sm">
          <div class="flex items-center justify-between text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">
            <span>Deposit Cashflow</span>
            <DollarSign class="w-3.5 h-3.5 text-emerald-400" />
          </div>
          <div class="text-2xl font-black text-white">AED {{ Number(adihexStats?.depositCashflow || 0).toLocaleString() }}</div>
          <div class="text-[10px] text-zinc-500">100% upfront captured</div>
        </div>

        <!-- Pipeline Value -->
        <div class="bg-zinc-950/80 p-4 rounded-2xl border border-amber-900/40 bg-amber-950/10 space-y-1.5 hover:border-amber-800/60 transition-colors shadow-sm">
          <div class="flex items-center justify-between text-[10px] text-amber-400 uppercase tracking-wider font-semibold">
            <span>Show Pipeline</span>
            <TrendingUp class="w-3.5 h-3.5 text-amber-400" />
          </div>
          <div class="text-2xl font-black text-amber-400">AED {{ Number(adihexStats?.pipelineRevenue || 0).toLocaleString() }}</div>
          <div class="text-[10px] text-zinc-500">Booked package value</div>
        </div>

        <!-- Redemptions -->
        <div class="bg-zinc-950/80 p-4 rounded-2xl border border-zinc-800/80 space-y-1.5 hover:border-zinc-700 transition-colors shadow-sm">
          <div class="flex items-center justify-between text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">
            <span>Vouchers Redeemed</span>
            <CheckCircle2 class="w-3.5 h-3.5 text-purple-400" />
          </div>
          <div class="text-2xl font-black text-purple-400">{{ adihexStats?.redeemedCount || 0 }}</div>
          <div class="text-[10px] text-zinc-500">{{ adihexStats?.activeCount || 0 }} Active unredeemed</div>
        </div>

        <!-- Website Quote Leads -->
        <div class="bg-zinc-950/80 p-4 rounded-2xl border border-zinc-800/80 space-y-1.5 hover:border-zinc-700 transition-colors shadow-sm">
          <div class="flex items-center justify-between text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">
            <span>Website Quotes</span>
            <MessageSquare class="w-3.5 h-3.5 text-red-400" />
          </div>
          <div class="text-2xl font-black text-red-400">{{ inquiryStats?.total || inquiries.length }}</div>
          <div class="text-[10px] text-zinc-500">{{ inquiryStats?.new || 0 }} New inquiries</div>
        </div>
      </div>

      <!-- Fast Voucher Validator Engine -->
      <div class="bg-zinc-950/90 p-5 rounded-3xl border border-zinc-800/90 shadow-xl space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center border border-amber-500/30">
              <Gift class="w-4 h-4" />
            </div>
            <div>
              <h3 class="text-xs font-mono font-bold text-white uppercase tracking-wider">Fast Voucher Validation & Pass Check-in</h3>
              <p class="text-[11px] text-zinc-400">Validate and mark customer ADIHEX voucher passes when visiting the workshop</p>
            </div>
          </div>
          <span class="text-[10px] font-mono text-zinc-500">Format: VEN-ADIHEX-XXXX</span>
        </div>

        <form @submit.prevent="handleRedeemVoucher" class="flex flex-col sm:flex-row gap-2.5">
          <div class="relative flex-1">
            <input
              v-model="redeemCodeInput"
              type="text"
              required
              placeholder="e.g. VEN-ADIHEX-8492"
              class="w-full px-4 py-2.5 rounded-xl bg-zinc-900 border border-zinc-800 text-white font-mono uppercase text-xs focus:outline-none focus:border-amber-400 placeholder-zinc-600 transition-colors"
            />
          </div>
          <button
            type="submit"
            :disabled="isRedeeming"
            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-mono font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-all disabled:opacity-50 shadow-md cursor-pointer"
          >
            <CheckCircle2 class="w-4 h-4" />
            <span>{{ isRedeeming ? 'Validating...' : 'Validate & Redeem Voucher' }}</span>
          </button>
        </form>

        <div v-if="redeemMessage" :class="['p-3 rounded-xl text-xs font-mono flex items-center gap-2 animate-in fade-in duration-200', redeemMessage.type === 'success' ? 'bg-emerald-950/60 border border-emerald-700/60 text-emerald-200' : 'bg-red-950/60 border border-red-700/60 text-red-200']">
          <CheckCircle2 v-if="redeemMessage.type === 'success'" class="w-4 h-4 text-emerald-400 shrink-0" />
          <AlertCircle v-else class="w-4 h-4 text-red-400 shrink-0" />
          <span>{{ redeemMessage.text }}</span>
        </div>
      </div>

      <!-- Navigation Tabs (Only ADIHEX & Website Quote Leads) -->
      <div class="flex items-center gap-2 border-b border-zinc-800/80 text-xs font-mono uppercase tracking-wider pb-px">
        <button
          @click="currentTab = 'adihex'"
          class="py-3 px-5 border-b-2 font-bold transition-all flex items-center gap-2 cursor-pointer"
          :class="currentTab === 'adihex' ? 'text-amber-400 border-amber-400 bg-amber-500/5 rounded-t-xl' : 'text-zinc-400 border-transparent hover:text-zinc-200'"
        >
          <Sparkles class="w-3.5 h-3.5" />
          <span>ADIHEX 2026 Leads ({{ adihexLeads?.length || 0 }})</span>
        </button>

        <button
          @click="currentTab = 'inquiries'"
          class="py-3 px-5 border-b-2 font-bold transition-all flex items-center gap-2 cursor-pointer"
          :class="currentTab === 'inquiries' ? 'text-red-500 border-red-500 bg-red-600/5 rounded-t-xl' : 'text-zinc-400 border-transparent hover:text-zinc-200'"
        >
          <MessageSquare class="w-3.5 h-3.5" />
          <span>Website Quote Inquiries ({{ inquiries?.length || 0 }})</span>
        </button>

        <button
          @click="currentTab = 'hammer'"
          class="py-3 px-5 border-b-2 font-bold transition-all flex items-center gap-2 cursor-pointer"
          :class="currentTab === 'hammer' ? 'text-red-400 border-red-400 bg-red-500/5 rounded-t-xl' : 'text-zinc-400 border-transparent hover:text-zinc-200'"
        >
          <Trophy class="w-3.5 h-3.5" />
          <span>Hammer Challenge ({{ hammerRegistrations?.length || 0 }})</span>
        </button>
      </div>

      <!-- ========================================================= -->
      <!-- TAB 1: ADIHEX 2026 LEADS                                  -->
      <!-- ========================================================= -->
      <div v-if="currentTab === 'adihex'" class="space-y-6 animate-in fade-in duration-200">
        
        <!-- Filter Bar & Search Controls -->
        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
          <div class="flex flex-wrap items-center gap-2">
            <!-- Tier Filter -->
            <div class="flex items-center gap-1 bg-zinc-950 p-1 rounded-xl border border-zinc-800 text-xs font-mono">
              <button
                v-for="tier in [
                  { id: 'all', label: `All (${adihexLeads.length})` },
                  { id: 'VIP_RESERVED', label: `👑 VIP Reserved (${adihexStats?.vipReservedCount || 0})` },
                  { id: 'HIGH_INTENT_PPF', label: `🛡️ High Intent (${adihexStats?.highIntentCount || 0})` },
                  { id: 'SPIN_PRIZE', label: `🎁 Spin Prize (${adihexStats?.spinPrizeCount || 0})` },
                ]"
                :key="tier.id"
                @click="adihexTierFilter = tier.id"
                :class="[
                  'px-3 py-1.5 rounded-lg transition-colors font-medium cursor-pointer',
                  adihexTierFilter === tier.id ? 'bg-zinc-800 text-white font-bold shadow-sm' : 'text-zinc-400 hover:text-zinc-200'
                ]"
              >
                {{ tier.label }}
              </button>
            </div>

            <!-- Status Filter -->
            <select
              v-model="adihexStatusFilter"
              class="px-3 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-xs font-mono text-zinc-300 focus:outline-none focus:border-amber-400"
            >
              <option value="all">All Status</option>
              <option value="paid_deposit">Paid Deposit (AED 500)</option>
              <option value="unredeemed">Active (Unredeemed)</option>
              <option value="redeemed">Redeemed Passes</option>
            </select>
          </div>

          <!-- Instant Search Input -->
          <div class="relative w-full md:w-80">
            <Search class="w-4 h-4 text-zinc-500 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              v-model="adihexSearch"
              type="text"
              placeholder="Search visitor, phone, code, package..."
              class="w-full pl-10 pr-4 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 text-xs font-mono focus:outline-none focus:border-amber-400 transition-colors"
            />
          </div>
        </div>

        <!-- ADIHEX Leads Table -->
        <div class="rounded-3xl border border-zinc-800/90 bg-zinc-950/70 overflow-hidden shadow-2xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono">
              <thead class="bg-zinc-900/90 text-zinc-400 uppercase tracking-wider text-[10px] border-b border-zinc-800">
                <tr>
                  <th class="py-3.5 px-4">Visitor / Contact</th>
                  <th class="py-3.5 px-4">Lead Classification</th>
                  <th class="py-3.5 px-4">Won Prize & Code</th>
                  <th class="py-3.5 px-4">Show Package & Deposit</th>
                  <th class="py-3.5 px-4">Service Intent</th>
                  <th class="py-3.5 px-4">Voucher Status</th>
                  <th class="py-3.5 px-4 text-right">Concierge Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                <tr
                  v-for="lead in filteredAdihexLeads"
                  :key="lead.id"
                  class="hover:bg-zinc-900/40 transition-colors"
                >
                  <!-- Contact Info -->
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-white text-sm flex items-center gap-1.5">
                      <span>{{ lead.name }}</span>
                      <span v-if="lead.locale === 'ar'" class="text-[9px] px-1.5 py-0.2 rounded bg-zinc-800 text-amber-400 font-bold">AR</span>
                      <span v-else class="text-[9px] px-1.5 py-0.2 rounded bg-zinc-800 text-zinc-400 font-bold">EN</span>
                    </div>
                    <div class="text-zinc-400 text-xs mt-0.5 font-mono">
                      {{ lead.phone }}
                    </div>
                    <div v-if="lead.email" class="text-zinc-500 text-[10px] truncate max-w-[160px]">{{ lead.email }}</div>
                    <div class="text-[10px] text-zinc-600 mt-0.5">
                      {{ new Date(lead.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }) }}
                    </div>
                  </td>

                  <!-- Lead Classification -->
                  <td class="py-3.5 px-4">
                    <span
                      v-if="lead.lead_tier === 'VIP_RESERVED'"
                      class="px-2.5 py-1 rounded-lg bg-amber-950/80 text-amber-300 border border-amber-700/60 text-[10px] font-bold uppercase inline-flex items-center gap-1 shadow-sm"
                    >
                      <Crown class="w-3 h-3 text-amber-400" />
                      <span>VIP RESERVED</span>
                    </span>
                    <span
                      v-else-if="lead.lead_tier === 'HIGH_INTENT_PPF'"
                      class="px-2.5 py-1 rounded-lg bg-blue-950/80 text-blue-300 border border-blue-700/60 text-[10px] font-bold uppercase inline-flex items-center gap-1 shadow-sm"
                    >
                      <ShieldCheck class="w-3 h-3 text-blue-400" />
                      <span>HIGH INTENT</span>
                    </span>
                    <span
                      v-else
                      class="px-2.5 py-1 rounded-lg bg-zinc-900 text-zinc-400 border border-zinc-700 text-[10px] font-bold uppercase inline-flex items-center gap-1"
                    >
                      <Gift class="w-3 h-3 text-zinc-500" />
                      <span>SPIN PRIZE</span>
                    </span>
                  </td>

                  <!-- Won Prize & Code -->
                  <td class="py-3.5 px-4">
                    <div class="text-white font-semibold flex items-center gap-1">
                      <span>{{ lead.won_prize_label }}</span>
                    </div>
                    <div class="text-red-400 font-mono text-xs font-bold tracking-wider mt-0.5">{{ lead.voucher_code }}</div>
                    <div class="text-[10px] text-zinc-500">Exp: {{ lead.voucher_expires_at ? lead.voucher_expires_at.split('T')[0] : '2026-12-05' }}</div>
                  </td>

                  <!-- Show Package & Deposit -->
                  <td class="py-3.5 px-4">
                    <div v-if="lead.selected_package_name">
                      <div class="text-white font-semibold">{{ lead.selected_package_name }}</div>
                      <div v-if="lead.deposit_status === 'paid'" class="text-emerald-400 text-xs font-bold flex items-center gap-1 mt-0.5">
                        <CheckCircle2 class="w-3.5 h-3.5" />
                        <span>AED {{ Number(lead.deposit_amount || 500) }} Paid (VIP)</span>
                      </div>
                      <div v-else class="text-zinc-500 text-[10px]">Deposit Pending</div>
                    </div>
                    <div v-else class="text-zinc-500 text-xs italic">Free Spin Only</div>
                  </td>

                  <!-- Service Intent -->
                  <td class="py-3.5 px-4">
                    <div class="flex flex-wrap gap-1 max-w-xs">
                      <span
                        v-for="(svc, idx) in (Array.isArray(lead.service_intent) ? lead.service_intent : [])"
                        :key="idx"
                        class="px-2 py-0.5 rounded bg-zinc-900 border border-zinc-800 text-[10px] text-zinc-300"
                      >
                        {{ svc }}
                      </span>
                      <span v-if="!lead.service_intent || lead.service_intent.length === 0" class="text-zinc-500 text-xs">-</span>
                    </div>
                  </td>

                  <!-- Voucher Status -->
                  <td class="py-3.5 px-4">
                    <span
                      v-if="lead.is_redeemed"
                      class="px-2.5 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800 text-[10px] font-bold uppercase inline-flex items-center gap-1"
                    >
                      <CheckCircle2 class="w-3 h-3 text-emerald-400" />
                      <span>Redeemed</span>
                    </span>
                    <span
                      v-else
                      class="px-2.5 py-0.5 rounded-full bg-zinc-900 text-zinc-400 border border-zinc-800 text-[10px] font-bold uppercase inline-flex items-center gap-1"
                    >
                      <Clock class="w-3 h-3 text-amber-500" />
                      <span>Active</span>
                    </span>
                  </td>

                  <!-- Concierge Action -->
                  <td class="py-3.5 px-4 text-right">
                    <div class="inline-flex items-center gap-1.5 justify-end">
                      <button
                        type="button"
                        @click="openWhatsAppModal(lead)"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-950 hover:bg-emerald-900 text-emerald-300 border border-emerald-800/70 text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer"
                        title="Open WhatsApp Concierge Modal"
                      >
                        <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"></path></svg>
                        <span>WhatsApp</span>
                      </button>

                      <a
                        :href="`tel:${lead.phone}`"
                        class="p-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white border border-zinc-800 transition-colors"
                        title="Direct Call"
                      >
                        <Phone class="w-3.5 h-3.5 text-amber-400" />
                      </a>
                    </div>
                  </td>
                </tr>

                <tr v-if="filteredAdihexLeads.length === 0">
                  <td colspan="7" class="py-12 text-center text-zinc-500 font-mono">
                    No ADIHEX leads matching your filter criteria.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 3: HAMMER CHALLENGE PARTICIPANTS -->
      <div v-if="currentTab === 'hammer'" class="space-y-6 animate-in fade-in duration-200">
        <div class="rounded-3xl border border-zinc-800/90 bg-zinc-950/70 p-6 shadow-2xl space-y-6">
          <div class="flex flex-col gap-4 border-b border-zinc-800/80 pb-6 md:flex-row md:items-center md:justify-between">
            <div><h3 class="text-lg font-bold uppercase text-white font-display">Hammer Challenge Participants</h3><p class="mt-1 text-xs text-zinc-400">Registered participants for 12 September 2026.</p></div>
            <a :href="route('dashboard.hammer-challenge.export')" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold uppercase text-white hover:bg-emerald-500"><Printer class="w-3.5 h-3.5" /> Export to Excel</a>
          </div>
          <div class="flex flex-col gap-3 md:flex-row">
            <div class="relative flex-1"><Search class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-500" /><input v-model="hammerSearch" type="search" placeholder="Search name, registration number, mobile, email..." class="w-full rounded-xl border border-zinc-800 bg-zinc-900 py-2.5 pl-10 pr-4 text-xs text-white placeholder-zinc-500 focus:border-red-500 focus:outline-none" /></div>
            <select v-model="hammerStatusFilter" class="rounded-xl border border-zinc-800 bg-zinc-900 px-3 py-2.5 text-xs text-zinc-300 focus:border-red-500 focus:outline-none"><option value="all">All Statuses</option><option value="registered">Registered</option><option value="checked_in">Checked-in</option><option value="participated">Participated</option><option value="disqualified">Disqualified</option><option value="finished">Finished</option></select>
          </div>
          <div class="overflow-x-auto rounded-2xl border border-zinc-800"><table class="w-full text-left text-xs font-mono"><thead class="bg-zinc-900 text-[10px] uppercase text-zinc-400"><tr><th class="p-3">Participant</th><th class="p-3">Contact</th><th class="p-3">Emergency Contact</th><th class="p-3">Registered</th><th class="p-3">Status</th></tr></thead><tbody class="divide-y divide-zinc-800/80"><tr v-for="registration in filteredHammerRegistrations" :key="registration.id" class="hover:bg-zinc-900/50"><td class="p-3"><div class="font-bold text-white">{{ registration.full_name }}</div><div class="mt-1 text-red-400">{{ registration.registration_number }}</div><div class="mt-1 text-zinc-500">DOB {{ registration.date_of_birth }} ({{ registration.age }})</div></td><td class="p-3"><div class="text-zinc-200">{{ registration.mobile }}</div><div class="mt-1 text-zinc-500">{{ registration.email }}</div></td><td class="p-3"><div class="text-zinc-200">{{ registration.emergency_contact_name }}</div><div class="mt-1 text-zinc-500">{{ registration.emergency_contact_number }}</div></td><td class="p-3 text-zinc-400">{{ new Date(registration.created_at).toLocaleString('en-GB') }}</td><td class="p-3"><select :value="registration.status" @change="updateHammerStatus(registration.id, $event.target.value)" class="rounded-lg border border-zinc-700 bg-zinc-950 px-2 py-2 text-[11px] text-zinc-200"><option value="registered">Registered</option><option value="checked_in">Checked-in</option><option value="participated">Participated</option><option value="disqualified">Disqualified</option><option value="finished">Finished</option></select></td></tr><tr v-if="filteredHammerRegistrations.length === 0"><td colspan="5" class="p-10 text-center text-zinc-500">No Hammer Challenge registrations found.</td></tr></tbody></table></div>
        </div>
      </div>

      <!-- ========================================================= -->
      <!-- TAB 2: WEBSITE QUOTE LEADS & INQUIRIES                    -->
      <!-- ========================================================= -->
      <div v-if="currentTab === 'inquiries'" class="space-y-6 animate-in fade-in duration-200">
        <div class="rounded-3xl border border-zinc-800/90 bg-zinc-950/70 p-6 space-y-6 shadow-2xl">
          
          <!-- Header & Search Controls -->
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800/80 pb-6">
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-lg font-bold text-white uppercase font-display">Website Quote Inquiries</h3>
                <span class="px-2.5 py-0.5 rounded-full bg-red-600/20 text-red-400 border border-red-500/30 text-xs font-mono font-bold">
                  {{ filteredInquiries.length }} Leads
                </span>
              </div>
              <p class="text-xs text-zinc-400 mt-1">Instant quote requests submitted directly via veneno.ae</p>
            </div>

            <!-- Search Input -->
            <div class="relative w-full md:w-80">
              <Search class="w-4 h-4 text-zinc-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                v-model="inquirySearch"
                type="text"
                placeholder="Search name, phone, service..."
                class="w-full pl-10 pr-4 py-2 rounded-xl bg-zinc-900 border border-zinc-800 text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-red-500"
              />
            </div>
          </div>

          <!-- Status Filter Tabs -->
          <div class="flex flex-wrap items-center gap-2">
            <button
              @click="inquiryStatusFilter = 'all'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold uppercase transition-all cursor-pointer"
              :class="inquiryStatusFilter === 'all' ? 'bg-white text-black shadow-lg' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'"
            >
              All Inquiries ({{ inquiries?.length || 0 }})
            </button>
            <button
              @click="inquiryStatusFilter = 'new'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold uppercase transition-all flex items-center gap-1.5 cursor-pointer"
              :class="inquiryStatusFilter === 'new' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'"
            >
              <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
              <span>New ({{ inquiries?.filter(i => i.status === 'new').length || 0 }})</span>
            </button>
            <button
              @click="inquiryStatusFilter = 'contacted'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold uppercase transition-all cursor-pointer"
              :class="inquiryStatusFilter === 'contacted' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/30' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'"
            >
              Contacted ({{ inquiries?.filter(i => i.status === 'contacted').length || 0 }})
            </button>
            <button
              @click="inquiryStatusFilter = 'booked'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold uppercase transition-all cursor-pointer"
              :class="inquiryStatusFilter === 'booked' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'"
            >
              Converted / Booked ({{ inquiries?.filter(i => i.status === 'booked').length || 0 }})
            </button>
            <button
              @click="inquiryStatusFilter = 'lost'"
              class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold uppercase transition-all cursor-pointer"
              :class="inquiryStatusFilter === 'lost' ? 'bg-zinc-700 text-white' : 'bg-zinc-900 text-zinc-400 hover:text-white border border-zinc-800'"
            >
              Lost / Closed ({{ inquiries?.filter(i => i.status === 'lost').length || 0 }})
            </button>
          </div>

          <!-- Inquiries List -->
          <div v-if="filteredInquiries.length === 0" class="text-center py-12 text-zinc-500 font-mono text-xs">
            No quote leads found matching your criteria.
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="inq in filteredInquiries"
              :key="inq.id"
              class="p-5 rounded-2xl bg-zinc-900/70 border border-zinc-800/80 hover:border-zinc-700 transition-all flex flex-col lg:flex-row lg:items-center justify-between gap-5 group"
            >
              <!-- Info -->
              <div class="space-y-2 flex-1">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="text-base font-bold text-white">{{ inq.customer_name }}</span>
                  <span class="text-xs font-mono text-emerald-400 font-bold">{{ inq.phone }}</span>
                  
                  <span
                    class="text-[10px] font-mono px-2.5 py-0.5 rounded-full font-bold uppercase"
                    :class="{
                      'bg-blue-950 text-blue-400 border border-blue-800/60': inq.status === 'new',
                      'bg-amber-950 text-amber-400 border border-amber-800/60': inq.status === 'contacted',
                      'bg-emerald-950 text-emerald-400 border border-emerald-800/60': inq.status === 'booked',
                      'bg-zinc-800 text-zinc-400 border border-zinc-700': inq.status === 'lost',
                    }"
                  >
                    {{ inq.status }}
                  </span>

                  <span class="text-[10px] text-zinc-500 font-mono flex items-center gap-1 ml-auto lg:ml-0">
                    <Clock class="w-3 h-3" />
                    <span>#{{ inq.id }} • {{ new Date(inq.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</span>
                  </span>
                </div>

                <div class="text-xs flex flex-wrap items-center gap-x-4 gap-y-1 text-zinc-300">
                  <div class="flex items-center gap-1.5">
                    <span class="text-zinc-500 font-mono uppercase text-[10px]">Service:</span>
                    <span class="text-red-400 font-bold">{{ inq.service_requested }}</span>
                  </div>

                  <div v-if="inq.vehicle_details" class="flex items-center gap-1.5">
                    <MapPin class="w-3.5 h-3.5 text-zinc-500 shrink-0" />
                    <span class="text-zinc-300">{{ inq.vehicle_details }}</span>
                  </div>
                </div>

                <div v-if="inq.message_text" class="p-2.5 rounded-xl bg-zinc-950/60 border border-zinc-800/60 text-zinc-300 text-xs font-sans leading-relaxed whitespace-pre-line">
                  {{ inq.message_text }}
                </div>
              </div>

              <!-- Actions Hub -->
              <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-zinc-800/60">
                <!-- Status Dropdown -->
                <select
                  :value="inq.status"
                  @change="handleUpdateInquiry(inq.id, $event.target.value)"
                  class="px-3 py-2 rounded-xl bg-zinc-950 border border-zinc-700 text-zinc-200 text-xs font-mono focus:outline-none focus:border-red-500 cursor-pointer"
                >
                  <option value="new">🔵 Mark as New</option>
                  <option value="contacted">🟡 Contacted</option>
                  <option value="booked">🟢 Booked / Converted</option>
                  <option value="lost">⚪ Closed / Lost</option>
                </select>

                <!-- WhatsApp -->
                <a
                  :href="`https://wa.me/${inq.phone.replace(/[^0-9]/g, '')}?text=${encodeURIComponent('Hello ' + inq.customer_name + ', thank you for contacting Veneno Auto Care regarding your quote request for ' + inq.service_requested + '. How may we assist you?')}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold text-xs flex items-center gap-1.5 shadow-md transition-all"
                  title="WhatsApp Customer"
                >
                  <Send class="w-3.5 h-3.5" />
                  <span>WhatsApp</span>
                </a>

                <!-- Call -->
                <a
                  :href="`tel:${inq.phone}`"
                  class="p-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-200 hover:text-white border border-zinc-700 transition-colors"
                  title="Call Customer"
                >
                  <Phone class="w-4 h-4 text-red-400" />
                </a>

                <!-- Delete Lead -->
                <button
                  @click="handleDeleteInquiry(inq.id)"
                  class="p-2 rounded-xl bg-zinc-900 hover:bg-red-950/80 text-zinc-500 hover:text-red-400 border border-zinc-800 hover:border-red-500/50 transition-colors cursor-pointer"
                  title="Delete Lead Record"
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ADIHEX WhatsApp Outreach Modal -->
    <div
      v-if="isWhatsAppModalOpen && selectedLeadForWhatsApp"
      class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 animate-in fade-in duration-200"
    >
      <div class="w-full max-w-lg rounded-3xl border border-zinc-700 bg-zinc-950 p-6 shadow-2xl space-y-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-zinc-800 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center border border-emerald-500/30">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"></path></svg>
            </div>
            <div>
              <h3 class="text-sm font-black text-white">Direct WhatsApp Concierge</h3>
              <p class="text-[11px] text-zinc-400 font-mono">Recipient: {{ selectedLeadForWhatsApp.name }} ({{ selectedLeadForWhatsApp.phone }})</p>
            </div>
          </div>
          <button
            type="button"
            @click="isWhatsAppModalOpen = false"
            class="w-8 h-8 rounded-full bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Lead Summary Card -->
        <div class="p-3.5 rounded-2xl bg-zinc-900/90 border border-zinc-800 text-xs font-mono grid grid-cols-2 gap-2">
          <div>
            <span class="text-zinc-500 text-[10px] block uppercase">Won Prize</span>
            <span class="text-amber-400 font-bold truncate block">{{ selectedLeadForWhatsApp.won_prize_label }}</span>
          </div>
          <div>
            <span class="text-zinc-500 text-[10px] block uppercase">Voucher Code</span>
            <span class="text-red-400 font-bold block">{{ selectedLeadForWhatsApp.voucher_code }}</span>
          </div>
          <div v-if="selectedLeadForWhatsApp.selected_package_name">
            <span class="text-zinc-500 text-[10px] block uppercase">Show Package</span>
            <span class="text-white font-bold truncate block">{{ selectedLeadForWhatsApp.selected_package_name }}</span>
          </div>
          <div>
            <span class="text-zinc-500 text-[10px] block uppercase">Deposit / Tier</span>
            <span :class="selectedLeadForWhatsApp.deposit_status === 'paid' ? 'text-emerald-400 font-bold' : 'text-zinc-400'">
              {{ selectedLeadForWhatsApp.deposit_status === 'paid' ? 'AED 500 Paid (VIP)' : 'Free Spin' }}
            </span>
          </div>
        </div>

        <!-- Preset Template Switcher -->
        <div class="space-y-1.5">
          <label class="text-[11px] font-mono font-bold text-zinc-300 uppercase tracking-wider block">Message Template</label>
          <div class="grid grid-cols-3 gap-1.5 text-xs font-mono">
            <button
              type="button"
              @click="updateWhatsAppTemplate('confirmation')"
              :class="['p-2 rounded-xl border text-center transition-all font-semibold cursor-pointer', activeWhatsAppTemplate === 'confirmation' ? 'bg-emerald-950 border-emerald-600 text-emerald-300 shadow-md' : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:text-white']"
            >
              Pass Confirmation
            </button>
            <button
              type="button"
              @click="updateWhatsAppTemplate('appointment')"
              :class="['p-2 rounded-xl border text-center transition-all font-semibold cursor-pointer', activeWhatsAppTemplate === 'appointment' ? 'bg-emerald-950 border-emerald-600 text-emerald-300 shadow-md' : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:text-white']"
            >
              Book Bay Date
            </button>
            <button
              type="button"
              @click="updateWhatsAppTemplate('exclusive_offer')"
              :class="['p-2 rounded-xl border text-center transition-all font-semibold cursor-pointer', activeWhatsAppTemplate === 'exclusive_offer' ? 'bg-emerald-950 border-emerald-600 text-emerald-300 shadow-md' : 'bg-zinc-900 border-zinc-800 text-zinc-400 hover:text-white']"
            >
              VIP Upgrade Deal
            </button>
          </div>
        </div>

        <!-- Editable Message Content -->
        <div class="space-y-1.5">
          <div class="flex items-center justify-between text-[11px] font-mono text-zinc-400">
            <span>Message Content (Editable)</span>
            <span :class="selectedLeadForWhatsApp.locale === 'ar' ? 'text-amber-400 font-bold' : 'text-zinc-500'">
              {{ selectedLeadForWhatsApp.locale === 'ar' ? 'Arabic (العربية)' : 'English' }}
            </span>
          </div>
          <textarea
            v-model="customWhatsAppMessage"
            rows="5"
            :dir="selectedLeadForWhatsApp.locale === 'ar' ? 'rtl' : 'ltr'"
            class="w-full p-3.5 rounded-2xl bg-zinc-900 border border-zinc-800 text-zinc-100 text-xs font-mono leading-relaxed focus:outline-none focus:border-emerald-500 transition-colors resize-none"
          ></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 pt-2">
          <a
            :href="getWhatsAppDirectUrl"
            target="_blank"
            @click="isWhatsAppModalOpen = false"
            class="flex-1 py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-emerald-950 transition-transform active:scale-98"
          >
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.4 8.87 7.65C8.65 7.89 8.02 8.48 8.02 9.7C8.02 10.92 8.91 12.09 9.03 12.25C9.16 12.42 10.74 14.97 13.25 15.96C15.34 16.79 15.76 16.62 16.22 16.58C16.67 16.54 17.69 15.98 17.9 15.38C18.11 14.78 18.11 14.27 18.05 14.16C17.99 14.05 17.82 13.99 17.57 13.86C17.32 13.74 16.08 13.13 15.85 13.04C15.62 12.96 15.46 12.92 15.29 13.16C15.12 13.41 14.64 13.99 14.5 14.16C14.35 14.32 14.21 14.34 13.96 14.22C13.71 14.09 12.91 13.83 11.96 12.98C11.22 12.32 10.72 11.51 10.58 11.26C10.43 11.01 10.56 10.88 10.69 10.75C10.8 10.64 10.94 10.46 11.06 10.31C11.19 10.17 11.23 10.06 11.31 9.9C11.39 9.73 11.35 9.59 11.29 9.46C11.23 9.34 10.72 8.08 10.51 7.58C10.31 7.09 10.1 7.16 9.94 7.15C9.79 7.14 9.66 7.34 9.53 7.34Z"></path></svg>
            <span>Launch WhatsApp</span>
          </a>
          <button
            type="button"
            @click="isWhatsAppModalOpen = false"
            class="py-3 px-4 rounded-2xl bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white font-mono text-xs transition-colors cursor-pointer"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- Admin Security & Password Management Modal -->
    <!-- ========================================== -->
    <div
      v-if="isSecurityModalOpen"
      class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 animate-in fade-in duration-200"
    >
      <div class="w-full max-w-2xl rounded-3xl border border-zinc-700/90 bg-[#0d0d12] p-6 sm:p-7 shadow-2xl shadow-black/95 space-y-5 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500/20 to-red-600/20 text-amber-400 flex items-center justify-center border border-amber-500/40 shrink-0">
              <KeyRound class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-base font-black text-white tracking-tight">Security & Password Control</h3>
              <p class="text-xs text-zinc-400 font-mono">Manage Admin credentials & staff account access</p>
            </div>
          </div>
          <button
            type="button"
            @click="isSecurityModalOpen = false; selectedStaffUser = null"
            class="w-8 h-8 rounded-full bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <!-- Tab Selector -->
        <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-zinc-950 border border-zinc-800">
          <button
            type="button"
            @click="securityModalTab = 'my_password'"
            class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer"
            :class="securityModalTab === 'my_password' ? 'bg-gradient-to-r from-amber-500/20 to-amber-600/20 text-amber-300 border border-amber-500/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'"
          >
            <KeyRound class="w-3.5 h-3.5" />
            <span>My Password</span>
          </button>

          <button
            type="button"
            @click="securityModalTab = 'staff_accounts'"
            class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer"
            :class="securityModalTab === 'staff_accounts' ? 'bg-gradient-to-r from-blue-500/20 to-blue-600/20 text-blue-300 border border-blue-500/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-zinc-900'"
          >
            <UserCog class="w-3.5 h-3.5" />
            <span>Staff Accounts ({{ users.length }})</span>
          </button>
        </div>

        <!-- ============================== -->
        <!-- TAB 1: Change My Password      -->
        <!-- ============================== -->
        <div v-if="securityModalTab === 'my_password'" class="space-y-4 pt-1">
          <!-- Current Account Context Box -->
          <div class="p-4 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-300 font-bold text-xs uppercase">
                {{ authUser.name ? authUser.name.charAt(0) : 'A' }}
              </div>
              <div>
                <div class="text-xs font-bold text-white">{{ authUser.name || 'Current Admin' }}</div>
                <div class="text-[11px] text-zinc-400 font-mono">{{ authUser.email }}</div>
              </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-red-600/20 text-red-300 border border-red-500/30 text-[10px] font-mono uppercase font-bold">
              {{ authUser.role || 'SUPER_ADMIN' }}
            </span>
          </div>

          <!-- Alert feedback -->
          <div v-if="myPasswordSuccess" class="p-3.5 rounded-2xl bg-emerald-950/80 border border-emerald-600/60 text-emerald-300 text-xs flex items-center gap-2">
            <CheckCircle2 class="w-4 h-4 shrink-0 text-emerald-400" />
            <span>{{ myPasswordSuccess }}</span>
          </div>

          <div v-if="myPasswordError" class="p-3.5 rounded-2xl bg-red-950/80 border border-red-600/60 text-red-300 text-xs flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-red-400" />
            <span>{{ myPasswordError }}</span>
          </div>

          <!-- Password Inputs -->
          <form @submit.prevent="handleChangeMyPassword" class="space-y-4">
            <div class="space-y-1.5">
              <label class="text-xs font-mono text-zinc-300 flex items-center justify-between">
                <span>New Password</span>
                <span class="text-[10px] text-zinc-500">Min. 6 characters</span>
              </label>
              <div class="relative">
                <input
                  :type="showMyPassword ? 'text' : 'password'"
                  v-model="myPassword"
                  placeholder="Enter new strong password"
                  class="w-full pl-10 pr-10 py-3 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-mono focus:outline-none focus:border-amber-500 transition-colors"
                  required
                />
                <Lock class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3.5" />
                <button
                  type="button"
                  @click="showMyPassword = !showMyPassword"
                  class="absolute right-3.5 top-3.5 text-zinc-500 hover:text-zinc-300 cursor-pointer"
                >
                  <EyeOff v-if="showMyPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="text-xs font-mono text-zinc-300">Confirm New Password</label>
              <div class="relative">
                <input
                  :type="showMyPassword ? 'text' : 'password'"
                  v-model="myPasswordConfirm"
                  placeholder="Re-type new password"
                  class="w-full pl-10 pr-10 py-3 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-mono focus:outline-none focus:border-amber-500 transition-colors"
                  required
                />
                <Lock class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3.5" />
              </div>
            </div>

            <!-- Password Requirements Hint -->
            <div class="flex items-center gap-4 text-[11px] font-mono text-zinc-500 pt-1">
              <div class="flex items-center gap-1.5" :class="myPassword.length >= 6 ? 'text-emerald-400 font-bold' : ''">
                <Check class="w-3.5 h-3.5" />
                <span>At least 6 characters</span>
              </div>
              <div class="flex items-center gap-1.5" :class="myPassword && myPassword === myPasswordConfirm ? 'text-emerald-400 font-bold' : ''">
                <Check class="w-3.5 h-3.5" />
                <span>Passwords match</span>
              </div>
            </div>

            <button
              type="submit"
              :disabled="myPasswordProcessing || !myPassword || myPassword !== myPasswordConfirm"
              class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 via-red-600 to-amber-600 hover:brightness-110 disabled:opacity-50 text-white font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-red-950/60 transition-all cursor-pointer active:scale-98"
            >
              <RefreshCw v-if="myPasswordProcessing" class="w-4 h-4 animate-spin" />
              <KeyRound v-else class="w-4 h-4" />
              <span>{{ myPasswordProcessing ? 'Updating Password...' : 'Save New Password' }}</span>
            </button>
          </form>
        </div>

        <!-- =================================== -->
        <!-- TAB 2: Staff Accounts & Resets      -->
        <!-- =================================== -->
        <div v-if="securityModalTab === 'staff_accounts'" class="space-y-4 pt-1">
          <!-- Search Bar -->
          <div class="relative">
            <input
              type="text"
              v-model="staffSearch"
              placeholder="Search staff by name, email, or role..."
              class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-mono focus:outline-none focus:border-blue-500 transition-colors"
            />
            <Search class="w-4 h-4 text-zinc-500 absolute left-3.5 top-3" />
          </div>

          <!-- Active Staff Reset Sub-Form (If user selected) -->
          <div v-if="selectedStaffUser" class="p-4 rounded-2xl bg-blue-950/30 border border-blue-500/40 space-y-3.5 animate-in fade-in duration-150">
            <div class="flex items-center justify-between border-b border-blue-500/20 pb-2.5">
              <div>
                <span class="text-[10px] font-mono text-blue-400 uppercase font-bold block">Resetting Password For</span>
                <span class="text-xs font-black text-white">{{ selectedStaffUser.name }}</span>
                <span class="text-[11px] text-zinc-400 font-mono ml-2">({{ selectedStaffUser.email }})</span>
              </div>
              <button
                type="button"
                @click="selectedStaffUser = null"
                class="text-xs text-zinc-400 hover:text-white font-mono cursor-pointer"
              >
                Cancel
              </button>
            </div>

            <div v-if="staffResetSuccess" class="p-3 rounded-xl bg-emerald-950/80 border border-emerald-600/60 text-emerald-300 text-xs flex items-center gap-2">
              <CheckCircle2 class="w-4 h-4 shrink-0 text-emerald-400" />
              <span>{{ staffResetSuccess }}</span>
            </div>

            <div v-if="staffResetError" class="p-3 rounded-xl bg-red-950/80 border border-red-600/60 text-red-300 text-xs flex items-center gap-2">
              <AlertCircle class="w-4 h-4 shrink-0 text-red-400" />
              <span>{{ staffResetError }}</span>
            </div>

            <form @submit.prevent="handleResetStaffPassword" class="space-y-3">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                  <label class="text-[11px] font-mono text-zinc-300">New Password</label>
                  <div class="relative">
                    <input
                      :type="showStaffPassword ? 'text' : 'password'"
                      v-model="staffNewPassword"
                      placeholder="Min. 6 chars"
                      class="w-full pl-9 pr-9 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-mono focus:outline-none focus:border-blue-500"
                      required
                    />
                    <Lock class="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-2.5" />
                    <button
                      type="button"
                      @click="showStaffPassword = !showStaffPassword"
                      class="absolute right-3 top-2.5 text-zinc-500 hover:text-zinc-300"
                    >
                      <EyeOff v-if="showStaffPassword" class="w-3.5 h-3.5" />
                      <Eye v-else class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>

                <div class="space-y-1">
                  <label class="text-[11px] font-mono text-zinc-300">Confirm Password</label>
                  <div class="relative">
                    <input
                      :type="showStaffPassword ? 'text' : 'password'"
                      v-model="staffNewPasswordConfirm"
                      placeholder="Re-type password"
                      class="w-full pl-9 pr-3 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-zinc-100 text-xs font-mono focus:outline-none focus:border-blue-500"
                      required
                    />
                    <Lock class="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-2.5" />
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2 pt-1">
                <button
                  type="submit"
                  :disabled="staffResetProcessing || !staffNewPassword || staffNewPassword !== staffNewPasswordConfirm"
                  class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white font-bold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer"
                >
                  <RefreshCw v-if="staffResetProcessing" class="w-3.5 h-3.5 animate-spin" />
                  <KeyRound v-else class="w-3.5 h-3.5" />
                  <span>{{ staffResetProcessing ? 'Resetting...' : 'Confirm Password Reset' }}</span>
                </button>
                <button
                  type="button"
                  @click="selectedStaffUser = null"
                  class="px-4 py-2.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white font-mono text-xs transition-colors"
                >
                  Close
                </button>
              </div>
            </form>
          </div>

          <!-- Staff Accounts List -->
          <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
            <div
              v-for="user in filteredStaffUsers"
              :key="user.id"
              class="p-3.5 rounded-2xl bg-zinc-900/70 hover:bg-zinc-900 border border-zinc-800/80 flex items-center justify-between gap-3 transition-colors"
            >
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-200 font-bold text-xs uppercase shrink-0">
                  {{ user.name ? user.name.charAt(0) : 'U' }}
                </div>
                <div class="min-w-0">
                  <div class="text-xs font-bold text-white truncate">{{ user.name }}</div>
                  <div class="text-[11px] text-zinc-400 font-mono truncate">{{ user.email }}</div>
                </div>
              </div>

              <div class="flex items-center gap-2 shrink-0">
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-mono uppercase font-bold"
                  :class="[
                    user.role === 'super_admin' ? 'bg-red-950/80 text-red-300 border border-red-800/50' :
                    user.role === 'manager' ? 'bg-amber-950/80 text-amber-300 border border-amber-800/50' :
                    user.role === 'technician' ? 'bg-blue-950/80 text-blue-300 border border-blue-800/50' :
                    'bg-zinc-800 text-zinc-300 border border-zinc-700'
                  ]"
                >
                  {{ user.role }}
                </span>

                <button
                  type="button"
                  @click="openStaffPasswordReset(user)"
                  class="px-3 py-1.5 rounded-xl bg-zinc-800 hover:bg-blue-600 text-zinc-300 hover:text-white border border-zinc-700 text-xs font-mono font-semibold flex items-center gap-1.5 transition-all cursor-pointer"
                  title="Reset password for this user"
                >
                  <KeyRound class="w-3 h-3" />
                  <span>Reset</span>
                </button>
              </div>
            </div>

            <div v-if="filteredStaffUsers.length === 0" class="p-6 text-center text-xs font-mono text-zinc-500">
              No staff or user accounts found matching your search.
            </div>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>
