<template>
  <div class="fixed top-20 right-4 z-30">
    <div class="relative">
      <button @click="toggleDropdown"
        class="flex items-center bg-black bg-opacity-40 backdrop-blur-sm text-white px-3 py-2 rounded-lg hover:bg-opacity-60 transition-all"
        :class="isRTL ? 'space-x-reverse space-x-2' : 'space-x-2'">
        <img :src="getCurrentFlag()" :alt="currentLanguage" class="w-6 h-4 object-cover rounded-sm" />
        <span class="text-sm font-medium">{{ currentLanguage }}</span>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </button>

      <!-- Dropdown -->
      <div v-show="isOpen"
        class="absolute top-full right-0 mt-2 bg-black bg-opacity-80 rounded-lg shadow-lg overflow-hidden min-w-[120px]">
        <button v-for="lang in languages" :key="lang.code" @click="selectLanguage(lang.code)"
          class="w-full flex items-center px-4 py-3 hover:bg-white hover:bg-opacity-10 transition-colors"
          :class="[
            isRTL ? 'space-x-reverse space-x-3' : 'space-x-3',
            currentLanguage === lang.code 
              ? 'bg-white bg-opacity-20 text-black' 
              : 'text-white'
          ]">
          <img :src="lang.flag" :alt="lang.name" class="w-6 h-4 object-cover rounded-sm" />
          <span class="text-sm">{{ lang.name }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useLanguage } from '@/composables/src/useLanguage.js'
const englandFlag = '/england.png'
const israelFlag = '/israel.svg'
const turkiyeFlag = '/turkiye.png'
const russiaFlag = '/russia.png'

export default {
  name: 'LanguageSelector',
  setup() {
    const { currentLanguage, setLanguage } = useLanguage()
    const isOpen = ref(false)
    const isRTL = computed(() => currentLanguage.value === 'HE')

    const languages = [
      { code: 'EN', name: 'English', flag: englandFlag },
      { code: 'HE', name: 'עברית', flag: israelFlag },
      { code: 'TR', name: 'Türkçe', flag: turkiyeFlag },
      { code: 'RU', name: 'Русский', flag: russiaFlag },
    ]

    const toggleDropdown = () => {
      isOpen.value = !isOpen.value
    }

    const selectLanguage = (langCode) => {
      setLanguage(langCode)
      isOpen.value = false
    }

    const getCurrentFlag = () => {
      const currentLang = languages.find(lang => lang.code === currentLanguage.value)
      return currentLang?.flag || '/england.png'
    }

    const closeDropdown = (event) => {
      if (!event.target.closest('.relative')) {
        isOpen.value = false
      }
    }

    onMounted(() => {
      document.addEventListener('click', closeDropdown)
    })

    onUnmounted(() => {
      document.removeEventListener('click', closeDropdown)
    })

    return {
      currentLanguage,
      languages,
      isOpen,
      isRTL,
      toggleDropdown,
      selectLanguage,
      getCurrentFlag
    }
  }
}
</script>
