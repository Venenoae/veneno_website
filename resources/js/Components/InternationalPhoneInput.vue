<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { ChevronDown, Search, X, Check } from 'lucide-vue-next';
import { useI18n } from '@/i18n';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  inputClass: {
    type: String,
    default: '',
  },
  id: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue', 'change']);

const { currentLocale } = useI18n();

// Comprehensive country list with flags, codes, names in EN and AR
const countries = [
  // GCC & Middle East (Priority)
  { code: 'AE', dial: '+971', flag: '🇦🇪', nameEn: 'United Arab Emirates', nameAr: 'الإمارات العربية المتحدة', sample: '501231231' },
  { code: 'SA', dial: '+966', flag: '🇸🇦', nameEn: 'Saudi Arabia', nameAr: 'المملكة العربية السعودية', sample: '501231231' },
  { code: 'QA', dial: '+974', flag: '🇶🇦', nameEn: 'Qatar', nameAr: 'قطر', sample: '33 123 456' },
  { code: 'KW', dial: '+965', flag: '🇰🇼', nameEn: 'Kuwait', nameAr: 'الكويت', sample: '91 234 567' },
  { code: 'OM', dial: '+968', flag: '🇴🇲', nameEn: 'Oman', nameAr: 'عُمان', sample: '91 234 567' },
  { code: 'BH', dial: '+973', flag: '🇧🇭', nameEn: 'Bahrain', nameAr: 'البحرين', sample: '39 123 456' },
  { code: 'EG', dial: '+20', flag: '🇪🇬', nameEn: 'Egypt', nameAr: 'مصر', sample: '10 1234 5678' },
  { code: 'JO', dial: '+962', flag: '🇯🇴', nameEn: 'Jordan', nameAr: 'الأردن', sample: '7 9123 4567' },
  { code: 'LB', dial: '+961', flag: '🇱🇧', nameEn: 'Lebanon', nameAr: 'لبنان', sample: '70 123 456' },
  { code: 'IQ', dial: '+964', flag: '🇮🇶', nameEn: 'Iraq', nameAr: 'العراق', sample: '770 123 4567' },
  { code: 'SY', dial: '+963', flag: '🇸🇾', nameEn: 'Syria', nameAr: 'سوريا', sample: '944 123 456' },
  { code: 'MA', dial: '+212', flag: '🇲🇦', nameEn: 'Morocco', nameAr: 'المغرب', sample: '612 345 678' },
  { code: 'DZ', dial: '+213', flag: '🇩🇿', nameEn: 'Algeria', nameAr: 'الجزائر', sample: '551 234 567' },
  { code: 'TN', dial: '+216', flag: '🇹🇳', nameEn: 'Tunisia', nameAr: 'تونس', sample: '20 123 456' },

  // Key International Markets
  { code: 'US', dial: '+1', flag: '🇺🇸', nameEn: 'United States', nameAr: 'الولايات المتحدة', sample: '201 555 0123' },
  { code: 'GB', dial: '+44', flag: '🇬🇧', nameEn: 'United Kingdom', nameAr: 'المملكة المتحدة', sample: '7911 123456' },
  { code: 'CA', dial: '+1', flag: '🇨🇦', nameEn: 'Canada', nameAr: 'كندا', sample: '416 555 0199' },
  { code: 'DE', dial: '+49', flag: '🇩🇪', nameEn: 'Germany', nameAr: 'ألمانيا', sample: '151 12345678' },
  { code: 'FR', dial: '+33', flag: '🇫🇷', nameEn: 'France', nameAr: 'فرنسا', sample: '6 12 34 56 78' },
  { code: 'IT', dial: '+39', flag: '🇮🇹', nameEn: 'Italy', nameAr: 'إيطاليا', sample: '312 345 6789' },
  { code: 'ES', dial: '+34', flag: '🇪🇸', nameEn: 'Spain', nameAr: 'إسبانيا', sample: '612 34 56 78' },
  { code: 'CH', dial: '+41', flag: '🇨🇭', nameEn: 'Switzerland', nameAr: 'سويسرا', sample: '78 123 45 67' },
  { code: 'NL', dial: '+31', flag: '🇳🇱', nameEn: 'Netherlands', nameAr: 'هولندا', sample: '6 12345678' },
  { code: 'BE', dial: '+32', flag: '🇧🇪', nameEn: 'Belgium', nameAr: 'بلجيكا', sample: '470 12 34 56' },
  { code: 'AT', dial: '+43', flag: '🇦🇹', nameEn: 'Austria', nameAr: 'النمسا', sample: '660 1234567' },
  { code: 'SE', dial: '+46', flag: '🇸🇪', nameEn: 'Sweden', nameAr: 'السويد', sample: '70 123 45 67' },
  { code: 'NO', dial: '+47', flag: '🇳🇴', nameEn: 'Norway', nameAr: 'النرويج', sample: '412 34 567' },
  { code: 'DK', dial: '+45', flag: '🇩🇰', nameEn: 'Denmark', nameAr: 'الدانمارك', sample: '20 12 34 56' },
  { code: 'RU', dial: '+7', flag: '🇷🇺', nameEn: 'Russia', nameAr: 'روسيا', sample: '912 345-67-89' },
  { code: 'TR', dial: '+90', flag: '🇹🇷', nameEn: 'Turkey', nameAr: 'تركيا', sample: '532 123 45 67' },

  // Asia & Subcontinent
  { code: 'IN', dial: '+91', flag: '🇮🇳', nameEn: 'India', nameAr: 'الهند', sample: '98765 43210' },
  { code: 'PK', dial: '+92', flag: '🇵🇰', nameEn: 'Pakistan', nameAr: 'باكستان', sample: '300 1234567' },
  { code: 'CN', dial: '+86', flag: '🇨🇳', nameEn: 'China', nameAr: 'الصين', sample: '138 0013 8000' },
  { code: 'JP', dial: '+81', flag: '🇯🇵', nameEn: 'Japan', nameAr: 'اليابان', sample: '90 1234 5678' },
  { code: 'KR', dial: '+82', flag: '🇰🇷', nameEn: 'South Korea', nameAr: 'كوريا الجنوبية', sample: '10 1234 5678' },
  { code: 'SG', dial: '+65', flag: '🇸🇬', nameEn: 'Singapore', nameAr: 'سنغافورة', sample: '8123 4567' },
  { code: 'MY', dial: '+60', flag: '🇲🇾', nameEn: 'Malaysia', nameAr: 'ماليزيا', sample: '12 345 6789' },
  { code: 'TH', dial: '+66', flag: '🇹🇭', nameEn: 'Thailand', nameAr: 'تايلاند', sample: '81 234 5678' },
  { code: 'ID', dial: '+62', flag: '🇮🇩', nameEn: 'Indonesia', nameAr: 'إندونيسيا', sample: '812 3456 7890' },
  { code: 'PH', dial: '+63', flag: '🇵🇭', nameEn: 'Philippines', nameAr: 'الفلبين', sample: '917 123 4567' },
  { code: 'BD', dial: '+880', flag: '🇧🇩', nameEn: 'Bangladesh', nameAr: 'بنغلاديش', sample: '1712 345678' },

  // Americas & Oceania & Africa
  { code: 'AU', dial: '+61', flag: '🇦🇺', nameEn: 'Australia', nameAr: 'أستراليا', sample: '412 345 678' },
  { code: 'NZ', dial: '+64', flag: '🇳🇿', nameEn: 'New Zealand', nameAr: 'نيوزيلندا', sample: '21 123 4567' },
  { code: 'BR', dial: '+55', flag: '🇧🇷', nameEn: 'Brazil', nameAr: 'البرازيل', sample: '11 91234-5678' },
  { code: 'MX', dial: '+52', flag: '🇲🇽', nameEn: 'Mexico', nameAr: 'المكسيك', sample: '55 1234 5678' },
  { code: 'ZA', dial: '+27', flag: '🇿🇦', nameEn: 'South Africa', nameAr: 'جنوب أفريقيا', sample: '71 123 4567' },
];

const isOpen = ref(false);
const searchQuery = ref('');
const containerRef = ref(null);
const searchInputRef = ref(null);

// Selected country (Default UAE +971)
const selectedCountry = ref(countries[0]);
const localNumber = ref('');

// Parse incoming initial value
const parseIncomingValue = (val) => {
  if (!val) {
    localNumber.value = '';
    return;
  }

  const cleanVal = String(val).trim();
  
  // Look for matching dial code
  const matched = countries.find(c => cleanVal.startsWith(c.dial));
  if (matched) {
    selectedCountry.value = matched;
    localNumber.value = cleanVal.slice(matched.dial.length).trim();
  } else {
    localNumber.value = cleanVal;
  }
};

const getFullNumber = () => {
  const digits = localNumber.value.trim();
  if (!digits) return '';
  return `${selectedCountry.value.dial} ${digits}`;
};

watch(() => props.modelValue, (newVal) => {
  if (newVal !== getFullNumber()) {
    parseIncomingValue(newVal);
  }
}, { immediate: true });

const emitUpdate = () => {
  const full = getFullNumber();
  emit('update:modelValue', full);
  emit('change', full);
};

const onLocalNumberInput = (e) => {
  localNumber.value = e.target.value;
  emitUpdate();
};

const selectCountry = (country) => {
  selectedCountry.value = country;
  isOpen.value = false;
  searchQuery.value = '';
  emitUpdate();
};

const toggleDropdown = () => {
  if (props.disabled) return;
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    setTimeout(() => {
      searchInputRef.value?.focus();
    }, 50);
  }
};

const closeDropdown = () => {
  isOpen.value = false;
  searchQuery.value = '';
};

// Filtered countries based on search query
const filteredCountries = computed(() => {
  const q = searchQuery.value.toLowerCase().trim();
  if (!q) return countries;

  return countries.filter(c => {
    return (
      c.nameEn.toLowerCase().includes(q) ||
      c.nameAr.includes(q) ||
      c.dial.includes(q) ||
      c.code.toLowerCase().includes(q)
    );
  });
});

// Click outside handling
const handleClickOutside = (e) => {
  if (containerRef.value && !containerRef.value.contains(e.target)) {
    closeDropdown();
  }
};

// Escape key handling
const handleKeyDown = (e) => {
  if (e.key === 'Escape' && isOpen.value) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleKeyDown);
});

const currentCountryName = (country) => {
  return currentLocale.value === 'ar' ? country.nameAr : country.nameEn;
};

const displayPlaceholder = computed(() => {
  if (props.placeholder) return props.placeholder;
  return selectedCountry.value.sample || '50 123 4567';
});
</script>

<template>
  <div ref="containerRef" class="relative w-full text-left" dir="ltr">
    <!-- Main Input Wrapper -->
    <div
      class="group relative flex items-center w-full rounded-xl bg-zinc-900/95 border border-zinc-800 focus-within:border-red-500/80 focus-within:ring-1 focus-within:ring-red-500/30 transition-all duration-200 shadow-inner"
      :class="[
        isOpen ? 'border-red-500/80 ring-1 ring-red-500/30' : '',
        disabled ? 'opacity-50 cursor-not-allowed' : ''
      ]"
    >
      <!-- Country Selector Button -->
      <button
        type="button"
        @click="toggleDropdown"
        :disabled="disabled"
        class="flex items-center gap-1.5 px-3 py-2.5 sm:py-3 text-zinc-200 hover:text-white bg-zinc-950/40 hover:bg-zinc-800/60 rounded-l-xl transition-all cursor-pointer select-none shrink-0"
        :title="currentCountryName(selectedCountry)"
      >
        <span class="text-base sm:text-lg leading-none" role="img" :aria-label="selectedCountry.nameEn">
          {{ selectedCountry.flag }}
        </span>
        <span class="font-mono text-xs sm:text-sm font-bold text-white tracking-tight">
          {{ selectedCountry.dial }}
        </span>
        <ChevronDown
          class="w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-200 transition-transform duration-200"
          :class="{ 'rotate-180 text-red-400': isOpen }"
        />
      </button>

      <!-- Vertical Divider -->
      <div class="h-6 w-[1px] bg-zinc-700/60 mx-1 shrink-0"></div>

      <!-- Local Number Input -->
      <input
        :id="id"
        type="tel"
        :value="localNumber"
        @input="onLocalNumberInput"
        :required="required"
        :disabled="disabled"
        :placeholder="displayPlaceholder"
        class="w-full px-3 py-2.5 sm:py-3 bg-transparent text-white placeholder-zinc-500 focus:outline-none text-xs sm:text-sm font-mono tracking-wide"
        :class="inputClass"
        autocomplete="tel"
      />
    </div>

    <!-- Dropdown Modal / Popover -->
    <transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-2"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-2"
    >
      <div
        v-if="isOpen"
        class="absolute left-0 top-full mt-2 w-72 sm:w-80 max-w-[90vw] z-50 rounded-2xl bg-zinc-950/98 backdrop-blur-xl border border-zinc-700/80 shadow-2xl shadow-black/80 overflow-hidden"
      >
        <!-- Search Header -->
        <div class="p-2.5 border-b border-zinc-800 bg-zinc-900/60">
          <div class="relative flex items-center">
            <Search class="w-4 h-4 text-zinc-400 absolute left-3 pointer-events-none" />
            <input
              ref="searchInputRef"
              v-model="searchQuery"
              type="text"
              :placeholder="currentLocale === 'ar' ? 'ابحث بالدولة أو الرمز...' : 'Search country or code...'"
              class="w-full pl-9 pr-8 py-2 rounded-xl bg-zinc-900 border border-zinc-700 text-white placeholder-zinc-500 text-xs focus:outline-none focus:border-red-500"
            />
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              type="button"
              class="absolute right-2.5 text-zinc-400 hover:text-white p-0.5"
            >
              <X class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>

        <!-- Country Options List -->
        <div class="max-h-60 overflow-y-auto divide-y divide-zinc-900/60 scrollbar-thin scrollbar-thumb-zinc-700">
          <div
            v-for="country in filteredCountries"
            :key="country.code"
            @click="selectCountry(country)"
            class="flex items-center justify-between px-3.5 py-2.5 hover:bg-zinc-800/80 cursor-pointer transition-colors group"
            :class="{ 'bg-red-950/30 text-white': selectedCountry.code === country.code }"
          >
            <div class="flex items-center gap-2.5 min-w-0 pr-2">
              <span class="text-lg shrink-0 leading-none">{{ country.flag }}</span>
              <div class="truncate">
                <div class="text-xs font-semibold text-zinc-200 group-hover:text-white truncate">
                  {{ currentCountryName(country) }}
                </div>
                <div class="text-[10px] text-zinc-500 font-mono">
                  {{ country.nameEn }} ({{ country.code }})
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <span class="font-mono text-xs font-bold text-red-400 group-hover:text-red-300">
                {{ country.dial }}
              </span>
              <Check
                v-if="selectedCountry.code === country.code"
                class="w-3.5 h-3.5 text-red-500 shrink-0"
              />
            </div>
          </div>

          <div
            v-if="filteredCountries.length === 0"
            class="p-6 text-center text-zinc-500 text-xs"
          >
            {{ currentLocale === 'ar' ? 'لم يتم العثور على أي دولة' : 'No country found matching query' }}
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>
