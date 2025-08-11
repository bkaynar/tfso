<template>
  <div class="fixed top-4 left-4 z-40 flex items-center" :class="isRTL ? 'space-x-reverse space-x-3' : 'space-x-3'">
    <a
      href="https://www.facebook.com/djevooapp"
      @click.prevent="openLink('facebook')"
      class="w-10 h-10 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-200 hover:scale-110"
    >
      <img
        :src="facebookIcon"
        alt="Facebook"
        class="w-6 h-6 object-contain"
      />
    </a>

    <a
      href="https://twitter.com/djevooapp"
      @click.prevent="openLink('twitter')"
      class="w-10 h-10 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-200 hover:scale-110"
    >
      <img
        :src="xIcon"
        alt="X (Twitter)"
        class="w-5 h-5 object-contain"
      />
    </a>

    <a
      href="https://www.instagram.com/djevooapp"
      @click.prevent="openLink('instagram')"
      class="w-10 h-10 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-200 hover:scale-110"
    >
      <img
        :src="instagramIcon"
        alt="Instagram"
        class="w-6 h-6 object-contain"
      />
    </a>

    <a
      href="https://www.youtube.com/@djevooapp"
      @click.prevent="openLink('youtube')"
      class="w-10 h-10 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-200 hover:scale-110"
    >
      <img
        :src="youtubeIcon"
        alt="YouTube"
        class="w-6 h-6 object-contain"
      />
    </a>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useLanguage } from '../composables/useLanguage.js'
import facebookIcon from '../assets/facebook.png'
import xIcon from '../assets/x_dark_logo.png'
import instagramIcon from '../assets/instagram.png'
import youtubeIcon from '../assets/youtube.png'

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
