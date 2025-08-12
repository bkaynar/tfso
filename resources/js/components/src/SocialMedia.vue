<template>
  <div class="fixed z-40 flex items-center
    top-4 left-2 flex-col space-y-2
    sm:top-4 sm:left-4 sm:flex-row sm:space-y-0
    md:top-20 md:left-4
    lg:top-4 lg:left-4" :class="isRTL ? 'sm:space-x-reverse sm:space-x-4' : 'sm:space-x-4'">
    <a
      href="https://www.facebook.com/djevooapp"
      @click.prevent="openLink('facebook')"
      class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300 hover:scale-110 shadow-lg hover:shadow-xl border border-white border-opacity-30"
    >
      <img
        :src="facebookIcon"
        alt="Facebook"
        class="w-10 sm:w-8 sm:h-8 object-contain"
      />
    </a>

    <a
      href="https://twitter.com/djevooapp"
      @click.prevent="openLink('twitter')"
      class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300 hover:scale-110 shadow-lg hover:shadow-xl border border-white border-opacity-30"
    >
      <img
        :src="xIcon"
        alt="X (Twitter)"
        class="w-6 h-6 sm:w-5 sm:h-5 object-contain"
      />
    </a>

    <a
      href="https://www.instagram.com/djevooapp"
      @click.prevent="openLink('instagram')"
      class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300 hover:scale-110 shadow-lg hover:shadow-xl border border-white border-opacity-30"
    >
      <img
        :src="instagramIcon"
        alt="Instagram"
        class="w-7 h-7 sm:w-8 sm:h-8 object-contain"
      />
    </a>

    <a
      href="https://www.youtube.com/@djevooapp"
      @click.prevent="openLink('youtube')"
      class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300 hover:scale-110 shadow-lg hover:shadow-xl border border-white border-opacity-30"
    >
      <img
        :src="youtubeIcon"
        alt="YouTube"
        class="w-7 h-7 sm:w-8 sm:h-8 object-contain"
      />
    </a>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useLanguage } from '@/composables/src/useLanguage.js'
const facebookIcon = '/facebook.png'
const xIcon = '/x_dark_logo.png'
const instagramIcon = '/instagram.png'
const youtubeIcon = '/youtube.png'

export default {
  name: 'SocialMedia',
  setup() {
    const { currentLanguage } = useLanguage()
    const isRTL = computed(() => currentLanguage.value === 'HE')

    const openLink = (platform) => {
      const links = {
        facebook: 'https://www.facebook.com/djevooapp',
        twitter: 'https://twitter.com/djevooapp',
        instagram: 'https://www.instagram.com/djevooapp',
        youtube: 'https://www.youtube.com/@djevooapp'
      }

      if (links[platform]) {
        window.open(links[platform], '_blank')
      }
    }

    return {
      openLink,
      isRTL,
      facebookIcon,
      xIcon,
      instagramIcon,
      youtubeIcon
    }
  }
}
</script>

<style scoped>
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}
</style>
