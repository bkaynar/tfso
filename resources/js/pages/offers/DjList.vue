<template>
  <Head title="Find DJs" />
  <AppLayout :breadcrumbs="[
    { title: 'Find DJs', href: '' }
  ]">
    <div class="max-w-6xl mx-auto py-10">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="bg-purple-500 p-2 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Find DJs</h1>
              <p class="text-sm text-gray-600 dark:text-gray-400">Browse available DJs and send offers</p>
            </div>
          </div>
          
          <Link :href="route('dj-offers.index')" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            My Offers
          </Link>
        </div>

        <!-- Search and Filter Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Name Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search by name</label>
            <div class="relative">
              <input 
                v-model="searchName" 
                @input="applyFilters"
                type="text" 
                placeholder="Enter DJ name..."
                class="block w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Location Search -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search by location</label>
            <div class="relative">
              <input 
                v-model="searchLocation" 
                @input="onLocationInput"
                @focus="showLocationSuggestions = true"
                @blur="hideLocationSuggestions"
                type="text" 
                placeholder="Enter location..."
                class="block w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                autocomplete="off" />
              
              <!-- Loading icon -->
              <div v-if="isLocationSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
              
              <!-- Location icon -->
              <div v-else class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
            </div>
            
            <!-- Location suggestions dropdown -->
            <div v-if="showLocationSuggestions && locationSuggestions.length > 0" 
              class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-auto">
              <ul class="py-1">
                <li v-for="(suggestion, index) in locationSuggestions" 
                  :key="index"
                  @mousedown="selectLocationFilter(suggestion)"
                  class="px-4 py-2 cursor-pointer hover:bg-purple-100 dark:hover:bg-purple-900/50 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                  <div class="flex items-start">
                    <svg class="h-4 w-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ suggestion.text }}</div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">{{ suggestion.context?.join(', ') }}</div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Active Filters -->
        <div v-if="searchName || searchLocation" class="flex items-center space-x-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <span class="text-sm text-gray-500 dark:text-gray-400">Active filters:</span>
          <span v-if="searchName" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
            Name: {{ searchName }}
            <button @click="clearNameFilter" class="ml-1 text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-200">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </span>
          <span v-if="searchLocation" class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
            Location: {{ searchLocation }}
            <button @click="clearLocationFilter" class="ml-1 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </span>
          <button @click="clearAllFilters" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            Clear all
          </button>
        </div>
      </div>

      <!-- DJ Grid -->
      <div v-if="filteredDjs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="dj in filteredDjs" :key="dj.id" 
          class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
          
          <!-- DJ Header -->
          <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-lg font-bold">
                {{ dj.name.charAt(0).toUpperCase() }}
              </div>
              <div class="ml-4">
                <h3 class="text-xl font-semibold">{{ dj.name }}</h3>
                <p class="text-purple-100 text-sm">{{ dj.email }}</p>
                <p v-if="dj.location" class="text-purple-200 text-xs flex items-center mt-1">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ dj.location }}
                </p>
              </div>
            </div>
          </div>

          <!-- DJ Info -->
          <div class="p-6">
            <div v-if="dj.bio" class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">About</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">{{ dj.bio }}</p>
            </div>

            <!-- Social Links -->
            <div v-if="hasSocialLinks(dj)" class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Social Media</h4>
              <div class="flex space-x-3">
                <a v-if="dj.instagram" :href="formatInstagramUrl(dj.instagram)" target="_blank" 
                  class="text-pink-500 hover:text-pink-600" :title="`@${dj.instagram}`">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </a>
                <a v-if="dj.twitter" :href="formatTwitterUrl(dj.twitter)" target="_blank" 
                  class="text-blue-500 hover:text-blue-600" :title="`@${dj.twitter}`">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                </a>
                <a v-if="dj.facebook" :href="formatFacebookUrl(dj.facebook)" target="_blank" 
                  class="text-blue-600 hover:text-blue-700" :title="dj.facebook">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                </a>
                <a v-if="dj.soundcloud" :href="formatSoundcloudUrl(dj.soundcloud)" target="_blank" 
                  class="text-orange-500 hover:text-orange-600" :title="dj.soundcloud">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1.175 12.225c-.051 0-.094.046-.101.1l-.233 2.154.233 2.105c.007.058.05.098.101.098.05 0 .09-.04.099-.098l.255-2.105-.255-2.154c-.009-.057-.049-.1-.099-.1zm1.06.936c-.045 0-.084.039-.09.087l-.206 1.218.206 1.178c.006.048.045.083.09.083.041 0 .082-.035.088-.083l.244-1.178-.244-1.218c-.006-.048-.047-.087-.088-.087zm1.073.581c-.044 0-.082.039-.088.087l-.175.637.175.619c.006.048.044.082.088.082.043 0 .081-.034.087-.082l.207-.619-.207-.637c-.006-.048-.044-.087-.087-.087zm.928.568c-.044 0-.081.039-.088.087l-.175.619.175.637c.007.048.044.087.088.087.043 0 .081-.039.087-.087l.175-.637-.175-.619c-.006-.048-.044-.087-.087-.087z"/>
                  </svg>
                </a>
                <a v-if="dj.youtube" :href="formatYoutubeUrl(dj.youtube)" target="_blank" 
                  class="text-red-500 hover:text-red-600" :title="dj.youtube">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                  </svg>
                </a>
              </div>
            </div>

            <!-- Application Status -->
            <div class="mb-4">
              <span v-if="hasApprovedApplication(dj)" 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Verified DJ
              </span>
              <span v-else 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                Application Pending
              </span>
            </div>

            <!-- Send Offer Button -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
              <Link :href="route('dj-offers.create', dj.id)" 
                class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Send Offer
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
          {{ searchName || searchLocation ? 'No DJs match your filters' : 'No DJs Found' }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ searchName || searchLocation 
            ? 'Try adjusting your search criteria or clear the filters.' 
            : 'There are currently no DJs available. Check back later!' }}
        </p>
        <button v-if="searchName || searchLocation" 
          @click="clearAllFilters"
          class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
          Clear Filters
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  djs: Array
})

// Search and filter states
const searchName = ref('')
const searchLocation = ref('')
const isLocationSearching = ref(false)
const locationSuggestions = ref([])
const showLocationSuggestions = ref(false)
const locationSearchTimeout = ref<NodeJS.Timeout | null>(null)

// Filtered DJs based on search criteria
const filteredDjs = computed(() => {
  let filtered = props.djs || []
  
  // Filter by name
  if (searchName.value) {
    const nameQuery = searchName.value.toLowerCase()
    filtered = filtered.filter((dj: any) => 
      dj.name.toLowerCase().includes(nameQuery) ||
      dj.email.toLowerCase().includes(nameQuery)
    )
  }
  
  // Filter by location
  if (searchLocation.value) {
    const locationQuery = searchLocation.value.toLowerCase()
    filtered = filtered.filter((dj: any) => 
      dj.location && dj.location.toLowerCase().includes(locationQuery)
    )
  }
  
  return filtered
})

// Location search functionality
const searchLocations = async (query: string) => {
  try {
    isLocationSearching.value = true

    // Mapbox Geocoding API
    const mapboxToken = 'sk.eyJ1IjoiYnVyYWtrYXluYXIiLCJhIjoiY21keXVmNzE1MDUwZTJscXhvczVpdXp0bSJ9.Jxt1o-slW0hL6PaQYIXx6Q'
    const mapboxUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?access_token=${mapboxToken}&types=place,locality,neighborhood,address&limit=5&language=en`

    const response = await fetch(mapboxUrl)
    const data = await response.json()

    if (data.features) {
      locationSuggestions.value = data.features.map((feature: any) => ({
        place_name: feature.place_name,
        text: feature.text,
        context: feature.context?.map((c: any) => c.text) || [],
        coordinates: feature.center
      }))
    }
  } catch (error) {
    console.error('Location search error:', error)
    locationSuggestions.value = []
  } finally {
    isLocationSearching.value = false
  }
}

const selectLocationFilter = (suggestion: any) => {
  searchLocation.value = suggestion.text
  locationSuggestions.value = []
  showLocationSuggestions.value = false
}

const onLocationInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  const query = target.value.trim()
  
  // Clear existing timeout
  if (locationSearchTimeout.value) {
    clearTimeout(locationSearchTimeout.value)
  }
  
  if (query.length >= 2) {
    // Debounce search for 300ms
    locationSearchTimeout.value = setTimeout(() => {
      searchLocations(query)
    }, 300)
    showLocationSuggestions.value = true
  } else {
    locationSuggestions.value = []
    showLocationSuggestions.value = false
  }
}

const hideLocationSuggestions = () => {
  // Delay hiding to allow click events on suggestions
  setTimeout(() => {
    showLocationSuggestions.value = false
  }, 150)
}

const applyFilters = () => {
  // Filters are applied automatically through computed property
}

const clearNameFilter = () => {
  searchName.value = ''
}

const clearLocationFilter = () => {
  searchLocation.value = ''
  locationSuggestions.value = []
  showLocationSuggestions.value = false
}

const clearAllFilters = () => {
  searchName.value = ''
  searchLocation.value = ''
  locationSuggestions.value = []
  showLocationSuggestions.value = false
}

const hasSocialLinks = (dj: any) => {
  return dj.instagram || dj.twitter || dj.facebook || dj.soundcloud || dj.youtube
}

const hasApprovedApplication = (dj: any) => {
  return dj.dj_applications && dj.dj_applications.some((app: any) => app.status === 'approved')
}

// Social media URL formatters
const formatInstagramUrl = (username: string) => {
  if (!username) return '#'
  
  // If it's already a full URL, return it
  if (username.startsWith('http')) {
    return username
  }
  
  // Remove @ if it exists and add Instagram URL
  const cleanUsername = username.replace('@', '')
  return `https://instagram.com/${cleanUsername}`
}

const formatTwitterUrl = (username: string) => {
  if (!username) return '#'
  
  // If it's already a full URL, return it
  if (username.startsWith('http')) {
    return username
  }
  
  // Remove @ if it exists and add Twitter URL
  const cleanUsername = username.replace('@', '')
  return `https://twitter.com/${cleanUsername}`
}

const formatFacebookUrl = (url: string) => {
  if (!url) return '#'
  
  // If it's already a full URL, return it
  if (url.startsWith('http')) {
    return url
  }
  
  // If it's just a username/page name, add Facebook URL
  return `https://facebook.com/${url}`
}

const formatSoundcloudUrl = (url: string) => {
  if (!url) return '#'
  
  // If it's already a full URL, return it
  if (url.startsWith('http')) {
    return url
  }
  
  // If it's just a username, add SoundCloud URL
  return `https://soundcloud.com/${url}`
}

const formatYoutubeUrl = (url: string) => {
  if (!url) return '#'
  
  // If it's already a full URL, return it
  if (url.startsWith('http')) {
    return url
  }
  
  // If it's just a channel name or ID, add YouTube URL
  return `https://youtube.com/${url}`
}
</script>