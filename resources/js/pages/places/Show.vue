<template>
  <Head :title="props.place.name" />
  <AppLayout :breadcrumbs="[
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Mekanlar', href: '/places' },
    { title: props.place.name, href: '' }
  ]">
    <div class="max-w-4xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <!-- Header with images -->
        <div v-if="props.place.images && props.place.images.length > 0" class="relative h-64">
          <img :src="props.place.images[0]" :alt="props.place.name" class="w-full h-full object-cover" />
          <div class="absolute inset-0 bg-black bg-opacity-40 flex items-end">
            <div class="p-6 text-white">
              <div class="flex items-center space-x-3 mb-2">
                <h1 class="text-3xl font-bold">{{ props.place.name }}</h1>
                <span v-if="props.place.is_premium" 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-400 text-yellow-900">
                  Premium
                </span>
              </div>
              <p class="text-gray-200 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ props.place.location }}
              </p>
            </div>
          </div>
        </div>

        <!-- Header without images -->
        <div v-else class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white">
          <div class="flex items-center space-x-3 mb-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <h1 class="text-3xl font-bold">{{ props.place.name }}</h1>
            <span v-if="props.place.is_premium" 
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-400 text-yellow-900">
              Premium
            </span>
          </div>
          <p class="text-purple-100 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ props.place.location }}
          </p>
        </div>

        <div class="p-6">
          <!-- Description -->
          <div v-if="props.place.description" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Açıklama</h3>
            <p class="text-gray-600 dark:text-gray-300">{{ props.place.description }}</p>
          </div>

          <!-- Contact Info -->
          <div v-if="props.place.phone" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">İletişim</h3>
            <div class="flex items-center text-gray-600 dark:text-gray-300">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <a :href="`tel:${props.place.phone}`" class="hover:text-purple-600 dark:hover:text-purple-400">
                {{ props.place.phone }}
              </a>
            </div>
          </div>

          <!-- Maps -->
          <div v-if="props.place.google_maps_url || props.place.apple_maps_url" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Haritalar</h3>
            <div class="flex flex-wrap gap-2">
              <a v-if="props.place.google_maps_url" :href="props.place.google_maps_url" target="_blank"
                class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C8.1 2 5 5.1 5 9c0 5.3 7 13 7 13s7-7.7 7-13c0-3.9-3.1-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5z"/>
                </svg>
                Google Maps
              </a>
              <a v-if="props.place.apple_maps_url" :href="props.place.apple_maps_url" target="_blank"
                class="inline-flex items-center px-3 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C8.1 2 5 5.1 5 9c0 5.3 7 13 7 13s7-7.7 7-13c0-3.9-3.1-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5z"/>
                </svg>
                Apple Maps
              </a>
            </div>
          </div>

          <!-- Social Media -->
          <div v-if="hasSocialMedia" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sosyal Medya</h3>
            <div class="flex flex-wrap gap-2">
              <a v-if="props.place.facebook_url" :href="props.place.facebook_url" target="_blank"
                class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
              </a>
              <a v-if="props.place.instagram_url" :href="props.place.instagram_url" target="_blank"
                class="inline-flex items-center px-3 py-2 bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987c6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.39-3.398-1.16-.95-.77-1.548-1.84-1.548-3.065 0-1.225.598-2.295 1.548-3.065.95-.77 2.101-1.16 3.398-1.16 1.297 0 2.448.39 3.398 1.16.95.77 1.548 1.84 1.548 3.065 0 1.225-.598 2.295-1.548 3.065-.95.77-2.101 1.16-3.398 1.16z"/>
                </svg>
                Instagram
              </a>
              <a v-if="props.place.twitter_url" :href="props.place.twitter_url" target="_blank"
                class="inline-flex items-center px-3 py-2 bg-blue-400 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
                Twitter
              </a>
              <a v-if="props.place.youtube_url" :href="props.place.youtube_url" target="_blank"
                class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                YouTube
              </a>
            </div>
          </div>

          <!-- Image Gallery -->
          <div v-if="props.place.images && props.place.images.length > 1" class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Galeri</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
              <img v-for="(image, index) in props.place.images.slice(1)" :key="index" 
                :src="image" :alt="`${props.place.name} - ${index + 2}`" 
                class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                @click="openImageModal(image)" />
            </div>
          </div>

          <!-- Actions -->
          <div v-if="canManage" class="flex items-center space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <Link :href="route('places.edit', props.place.id)"
              class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Düzenle
            </Link>
            <Link :href="route('places.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
              Geri
            </Link>
          </div>
          <div v-else class="flex items-center justify-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <Link :href="route('places.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
              Geri
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <div v-if="selectedImage" @click="closeImageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
      <div class="max-w-4xl max-h-full p-4">
        <img :src="selectedImage" alt="Full size" class="max-w-full max-h-full object-contain" />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  place: {
    type: Object,
    required: true
  }
})

const page = usePage()
const selectedImage = ref<string | null>(null)

// Helper function to check if user has specific role
const hasRole = (role: string) => {
  const userRoles = page.props.auth.roles as string[];
  return userRoles && userRoles.includes(role);
};

// Check if user can manage places
const isAdmin = computed(() => hasRole('admin'));
const isPlaceManager = computed(() => hasRole('placeManager'));
const canManage = computed(() => isAdmin.value || isPlaceManager.value);

// Check if place has social media links
const hasSocialMedia = computed(() => {
  return props.place.facebook_url || props.place.instagram_url || 
         props.place.twitter_url || props.place.youtube_url;
});

const openImageModal = (imageUrl: string) => {
  selectedImage.value = imageUrl;
};

const closeImageModal = () => {
  selectedImage.value = null;
};
</script>