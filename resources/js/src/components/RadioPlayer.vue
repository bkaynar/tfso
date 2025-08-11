<template>
  <div class="fixed bottom-0 left-0 right-0 bg-black bg-opacity-80 p-4 m-4 rounded-lg flex items-center justify-between z-50">
    <!-- Left side: Album cover and track info -->
    <div class="flex items-center space-x-4">
      <img
        :src="currentTrack.cover"
        alt="Album Cover"
        class="w-16 h-16 rounded-md object-cover"
        @error="$event.target.src = '/src/assets/djevoo.png'"
      />
      <div class="text-white">
        <div class="font-semibold text-lg">{{ currentTrack.title || t('nowPlaying') }}</div>
        <div class="text-gray-300 text-sm">{{ currentTrack.artist || t('radioName') }}</div>
      </div>
    </div>

    <!-- Right side: Volume and Play/Pause buttons -->
    <div class="flex items-center" :class="isRTL ? 'space-x-reverse space-x-3' : 'space-x-3'">
      <!-- Volume button -->
      <button
        @click="toggleMute"
        class="bg-white bg-opacity-20 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-30 transition-colors"
      >
        <i :class="isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up'" class="text-sm"></i>
      </button>

      <!-- Play/Pause button -->
      <button
        @click="togglePlay"
        class="bg-white text-black rounded-full w-12 h-12 flex items-center justify-center hover:bg-gray-100 transition-colors"
      >
        <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'" class="text-lg"></i>
      </button>
    </div>

    <!-- Hidden audio element -->
    <audio
      ref="audioPlayer"
      :src="streamUrl"
      @loadstart="onLoadStart"
      @canplay="onCanPlay"
      @error="onError"
    ></audio>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useLanguage } from '../composables/useLanguage.js'

export default {
  name: 'RadioPlayer',
  setup() {
    const { t, currentLanguage } = useLanguage()
    const audioPlayer = ref(null)
    const isRTL = computed(() => currentLanguage.value === 'HE')
    const isPlaying = ref(false)
    const isMuted = ref(false)
    const streamUrl = 'https://listen.radioking.com/radio/627604/stream/689965'
    const currentTrack = ref({
      artist: '',
      title: '',
      cover: '/src/assets/djevoo.png'
    })

    const togglePlay = async () => {
      if (!audioPlayer.value) return

      try {
        if (isPlaying.value) {
          audioPlayer.value.pause()
          isPlaying.value = false
        } else {
          await audioPlayer.value.play()
          isPlaying.value = true
        }
      } catch (error) {
        console.error('Error toggling playback:', error)
      }
    }

    const onLoadStart = () => {
      console.log('Loading stream...')
    }

    const onCanPlay = () => {
      console.log('Stream ready to play')
    }

    const onError = (error) => {
      console.error('Audio error:', error)
      isPlaying.value = false
    }

    const toggleMute = () => {
      if (audioPlayer.value) {
        audioPlayer.value.muted = !audioPlayer.value.muted
        isMuted.value = audioPlayer.value.muted
      }
    }

    const fetchCurrentTrack = async () => {
      try {
        const response = await fetch('https://api.radioking.io/widget/radio/tfso-israel1/track/current')
        const data = await response.json()
        
        currentTrack.value = {
          artist: data.artist || '',
          title: data.title || '',
          cover: data.cover || '/src/assets/djevoo.png'
        }
      } catch (error) {
        console.error('Error fetching track info:', error)
      }
    }

    onMounted(() => {
      console.log('RadioPlayer mounted')
      fetchCurrentTrack()
      
      // Update track info every 10 seconds
      const trackInterval = setInterval(fetchCurrentTrack, 10000)
      
      if (audioPlayer.value) {
        audioPlayer.value.addEventListener('pause', () => {
          isPlaying.value = false
        })
        audioPlayer.value.addEventListener('play', () => {
          isPlaying.value = true
        })
      }
      
      // Clean up interval on unmount
      onUnmounted(() => {
        clearInterval(trackInterval)
        if (audioPlayer.value && isPlaying.value) {
          audioPlayer.value.pause()
        }
      })
    })

    return {
      audioPlayer,
      isPlaying,
      isMuted,
      isRTL,
      streamUrl,
      currentTrack,
      togglePlay,
      toggleMute,
      onLoadStart,
      onCanPlay,
      onError,
      t
    }
  }
}
</script>
