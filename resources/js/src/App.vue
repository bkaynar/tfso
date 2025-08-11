<template>
  <div class="min-h-screen relative overflow-hidden" style="background-color: #6706ab;" :dir="isRTL ? 'rtl' : 'ltr'">
    <!-- Social Media Icons -->
    <SocialMedia />
    
    <!-- Language Selector -->
    <LanguageSelector />

    <!-- Main content -->
    <div class="relative z-10 flex flex-col items-center justify-center min-h-screen pb-24">
      <!-- Logo -->
      <img
        src="/src/assets/white.png"
        alt="Logo"
        class="mb-8 max-w-xs md:max-w-md lg:max-w-lg"
      />

      <!-- Title -->
      <h1 class="text-white text-2xl md:text-4xl font-bold text-center px-4 mb-8">
        {{ t('title') }}
      </h1>

      <!-- Features Accordion - directly under title -->
      <FeatureAccordion />
    </div>
    
    <!-- Radio Player -->
    <RadioPlayer />
  </div>
</template>

<script>
import { onMounted, computed } from 'vue'
import RadioPlayer from './components/RadioPlayer.vue'
import LanguageSelector from './components/LanguageSelector.vue'
import FeatureAccordion from './components/FeatureAccordion.vue'
import SocialMedia from './components/SocialMedia.vue'
import { useLanguage } from './composables/useLanguage.js'

export default {
  name: 'App',
  components: {
    RadioPlayer,
    LanguageSelector,
    FeatureAccordion,
    SocialMedia
  },
  setup() {
    const { t, initLanguage, currentLanguage } = useLanguage()

    const isRTL = computed(() => currentLanguage.value === 'HE')

    onMounted(() => {
      initLanguage()
    })

    return {
      t,
      isRTL
    }
  }
}
</script>
