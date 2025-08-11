<template>
  <div class="w-full max-w-7xl mx-auto px-4 py-8">
    <!-- Horizontal Feature Cards -->
    <div class="flex overflow-x-scroll pb-8 pt-4 px-4 -mx-2 justify-start md:justify-center" :class="isRTL ? 'space-x-reverse space-x-2' : 'space-x-2'" style="scroll-snap-type: x mandatory;">
      <div
        v-for="(feature, index) in features"
        :key="index"
        @click="openModal(index + 1)"
        class="flex-shrink-0 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-3 cursor-pointer transition-all duration-300 ease-out hover:bg-opacity-20 w-40 card-hover mx-1 my-2"
        :class="{ 'mr-16': index === features.length - 1 && !isRTL, 'ml-16': index === features.length - 1 && isRTL }"
        style="scroll-snap-align: start;"
      >
        <div class="text-center">
          <h3 class="text-white font-semibold text-sm leading-tight">
            {{ index + 1 }}. {{ getFeatureTitle(index + 1) }}
          </h3>
        </div>
      </div>
    </div>

    <!-- Modern Modal with Animations -->
    <transition name="modal-backdrop">
      <div
        v-if="selectedFeature"
        @click="closeModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      >
        <transition name="modal-content" appear>
          <div
            v-if="selectedFeature"
            @click.stop
            class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[85vh] overflow-hidden transform"
          >
        <!-- Modern Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-3xl font-bold text-white">
                {{ getFeatureTitle(selectedFeature) }}
              </h2>
            </div>
            <button
              @click="closeModal"
              class="text-white hover:text-gray-200 transition-colors duration-200 p-2 hover:bg-white hover:bg-opacity-10 rounded-full"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>

        <!-- Modern Content -->
        <div class="p-8 overflow-y-auto max-h-[60vh]">
          <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 leading-relaxed text-lg font-light">
              {{ getFeatureContent(selectedFeature) }}
            </p>
          </div>
        </div>

        <!-- Modern Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-100">
          <div class="flex justify-end">
            <button
              @click="closeModal"
              class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-full font-medium hover:from-purple-700 hover:to-blue-700 transition-all duration-200 transform hover:scale-105"
            >
              Close
            </button>
          </div>
        </div>
          </div>
        </transition>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useLanguage } from '../composables/useLanguage.js'

export default {
  name: 'FeatureAccordion',
  setup() {
    const { t, currentLanguage } = useLanguage()
    const selectedFeature = ref(null)
    const isRTL = computed(() => currentLanguage.value === 'HE')

    const features = Array.from({ length: 7 }, (_, i) => i + 1)

    const openModal = (featureNumber) => {
      selectedFeature.value = featureNumber
      document.body.style.overflow = 'hidden' // Prevent background scroll
    }

    const closeModal = () => {
      selectedFeature.value = null
      document.body.style.overflow = 'auto' // Restore scroll
    }

    const getEmojiForFeature = (number) => {
      const emojis = {
        1: '🎧',
        2: '🔊',
        3: '🏢',
        4: '🌍',
        5: '📲',
        6: '🔁',
        7: '📣'
      }
      return emojis[number] || '🎵'
    }

    const getFeatureTitle = (featureNumber) => {
      return t(`features.${featureNumber}.title`)
    }

    const getFeatureContent = (featureNumber) => {
      return t(`features.${featureNumber}.content`)
    }

    return {
      selectedFeature,
      features,
      openModal,
      closeModal,
      getEmojiForFeature,
      getFeatureTitle,
      getFeatureContent,
      isRTL,
      t
    }
  }
}
</script>

<style scoped>
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

/* Modal Backdrop Animations */
.modal-backdrop-enter-active {
  transition: opacity 0.3s ease-out;
}

.modal-backdrop-leave-active {
  transition: opacity 0.2s ease-in;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
  opacity: 0;
}

/* Modal Content Animations */
.modal-content-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-content-leave-active {
  transition: all 0.2s ease-in;
}

.modal-content-enter-from {
  opacity: 0;
  transform: scale(0.8) translateY(-50px);
}

.modal-content-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(20px);
}

/* Card Hover Enhancement */
.card-hover {
  z-index: 1;
}

.card-hover:hover {
  transform: scale(1.05) translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  z-index: 10;
}

.card-hover:active {
  transform: scale(0.98) translateY(-2px);
}

/* Scroll behavior fixes */
.flex.overflow-x-scroll {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}

.flex.overflow-x-scroll::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

/* Ensure last card is fully visible */
.flex.overflow-x-scroll::after {
  content: '';
  flex-shrink: 0;
  width: 2rem;
}
</style>
