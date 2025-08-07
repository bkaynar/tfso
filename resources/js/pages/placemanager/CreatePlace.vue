<template>
    <Head title="Create Your Place" />
    <AppLayout :breadcrumbs="[
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Create Your Place', href: '' }
    ]">
        <div class="max-w-4xl mx-auto py-10">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                <!-- Welcome Message -->
                <div class="text-center mb-8">
                    <div class="inline-block bg-purple-500 p-3 rounded-full mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Welcome to the Platform!
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Let's set up your place to complete your profile and start managing events.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-8" enctype="multipart/form-data">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                            Basic Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Place Name *
                                </label>
                                <input 
                                    v-model="form.name" 
                                    id="name" 
                                    type="text" 
                                    required
                                    placeholder="Enter your place name"
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Contact Phone
                                </label>
                                <input 
                                    v-model="form.phone" 
                                    id="phone" 
                                    type="tel"
                                    placeholder="Contact number for your place"
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="relative">
                                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Location *
                                </label>
                                <input 
                                    v-model="form.location" 
                                    id="location" 
                                    type="text" 
                                    required
                                    @input="handleLocationSearch" 
                                    @focus="showSuggestions = true"
                                    @blur="hideSuggestions" 
                                    placeholder="Enter your place's address or location"
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />

                                <!-- Suggestions dropdown -->
                                <div v-if="showSuggestions && locationSuggestions.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div v-for="(suggestion, index) in locationSuggestions" :key="index"
                                        @mousedown="selectLocation(suggestion)"
                                        class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ suggestion.place_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ suggestion.context?.join(', ') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading indicator -->
                                <div v-if="isSearching" class="absolute right-3 top-11">
                                    <svg class="animate-spin h-5 w-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea 
                                v-model="form.description" 
                                id="description" 
                                rows="4"
                                placeholder="Tell people about your place - atmosphere, music style, special features..."
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                            Place Images
                        </h2>
                        <div class="border-2 border-dashed border-purple-400 dark:border-purple-600 rounded-lg p-8">
                            <div v-if="imagePreviews.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                <div v-for="(preview, index) in imagePreviews" :key="index" class="relative">
                                    <img :src="preview" alt="Preview" class="w-full h-32 object-cover rounded-lg shadow" />
                                    <button 
                                        type="button" 
                                        @click="removeImage(index)"
                                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold shadow">
                                        ×
                                    </button>
                                </div>
                            </div>
                            <label for="images" class="cursor-pointer block text-center">
                                <svg class="w-12 h-12 text-purple-400 dark:text-purple-600 mb-4 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-lg text-gray-600 dark:text-gray-400 block mb-2">
                                    Add photos of your place
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-500">
                                    Click to upload images (optional)
                                </span>
                            </label>
                            <input id="images" type="file" accept="image/*" multiple @change="handleImageChange" class="hidden" />
                        </div>
                    </div>

                    <!-- Social Media & Maps (Optional) -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                            Online Presence <span class="text-sm font-normal text-gray-500">(Optional)</span>
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="google_maps_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Google Maps URL
                                </label>
                                <input 
                                    v-model="form.google_maps_url" 
                                    id="google_maps_url" 
                                    type="url"
                                    placeholder="https://maps.google.com/..."
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>

                            <div>
                                <label for="facebook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Facebook Page
                                </label>
                                <input 
                                    v-model="form.facebook_url" 
                                    id="facebook_url" 
                                    type="url"
                                    placeholder="https://facebook.com/..."
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>

                            <div>
                                <label for="instagram_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Instagram Page
                                </label>
                                <input 
                                    v-model="form.instagram_url" 
                                    id="instagram_url" 
                                    type="url"
                                    placeholder="https://instagram.com/..."
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>

                            <div>
                                <label for="youtube_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    YouTube Channel
                                </label>
                                <input 
                                    v-model="form.youtube_url" 
                                    id="youtube_url" 
                                    type="url"
                                    placeholder="https://youtube.com/..."
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-center space-x-4 pt-6">
                        <button 
                            type="submit"
                            :disabled="isSubmitting || !form.name || !form.location"
                            class="inline-flex items-center px-8 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-lg font-medium rounded-lg transition-colors duration-200 shadow-lg">
                            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isSubmitting ? 'Creating Your Place...' : 'Create My Place' }}
                        </button>
                        
                        <Link 
                            :href="route('dashboard')"
                            class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 text-lg font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                            Skip for Now
                        </Link>
                    </div>

                    <!-- Note -->
                    <div class="text-center pt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Don't worry, you can always update these details later from your place management page.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = ref({
    name: '',
    description: '',
    location: '',
    phone: '',
    google_maps_url: '',
    facebook_url: '',
    instagram_url: '',
    youtube_url: '',
    images: [] as File[]
})

const imagePreviews = ref<string[]>([])
const isSubmitting = ref(false)

// Location search variables
const locationSuggestions = ref<any[]>([])
const showSuggestions = ref(false)
const isSearching = ref(false)
let searchTimeout: number

// Location search functions
const handleLocationSearch = async (e: Event) => {
    const target = e.target as HTMLInputElement
    const query = target.value.trim()

    if (query.length < 3) {
        locationSuggestions.value = []
        return
    }

    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }

    // Debounce search
    searchTimeout = setTimeout(async () => {
        await searchLocations(query)
    }, 300)
}

const searchLocations = async (query: string) => {
    try {
        isSearching.value = true

        // Using Mapbox Geocoding API
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
        isSearching.value = false
    }
}

const selectLocation = (suggestion: any) => {
    form.value.location = suggestion.place_name
    locationSuggestions.value = []
    showSuggestions.value = false
}

const hideSuggestions = () => {
    // Delay hiding to allow click events
    setTimeout(() => {
        showSuggestions.value = false
    }, 200)
}

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        const newFiles = Array.from(target.files)
        form.value.images = [...form.value.images, ...newFiles]

        newFiles.forEach(file => {
            const reader = new FileReader()
            reader.onload = (e) => {
                if (e.target?.result) {
                    imagePreviews.value.push(e.target.result as string)
                }
            }
            reader.readAsDataURL(file)
        })
    }
}

const removeImage = (index: number) => {
    form.value.images.splice(index, 1)
    imagePreviews.value.splice(index, 1)
}

const submit = async () => {
    if (isSubmitting.value) return
    
    isSubmitting.value = true
    
    try {
        const data = new FormData()
        data.append('name', form.value.name)
        data.append('location', form.value.location)
        if (form.value.description) data.append('description', form.value.description)
        if (form.value.phone) data.append('phone', form.value.phone)
        if (form.value.google_maps_url) data.append('google_maps_url', form.value.google_maps_url)
        if (form.value.facebook_url) data.append('facebook_url', form.value.facebook_url)
        if (form.value.instagram_url) data.append('instagram_url', form.value.instagram_url)
        if (form.value.youtube_url) data.append('youtube_url', form.value.youtube_url)

        form.value.images.forEach((image, index) => {
            data.append(`images[${index}]`, image)
        })

        await router.post('/placemanager/place', data)
    } catch (error) {
        console.error('Error creating place:', error)
    } finally {
        isSubmitting.value = false
    }
}
</script>