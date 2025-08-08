<template>

    <Head :title="'Edit Profile'" />
    <AppLayout :breadcrumbs="[
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Edit Profile', href: '' }
    ]">
        <div class="max-w-4xl mx-auto py-10">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
                    <span class="inline-block bg-green-500 p-2 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                    Edit My Profile
                </h1>

                <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                    <!-- Name and Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                            <input v-model="form.name" id="name" type="text" required
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                            <input v-model="form.email" id="email" type="email" required
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                            <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New
                                Password</label>
                            <input v-model="form.password" id="password" type="password"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                            <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password
                                }}</div>
                            <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                        </div>
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New
                                Password</label>
                            <input v-model="form.password_confirmation" id="password_confirmation" type="password"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                        <textarea v-model="form.bio" id="bio" rows="4" placeholder="Tell us about yourself..."
                            class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
                        <div v-if="form.errors.bio" class="mt-1 text-sm text-red-600">{{ form.errors.bio }}</div>
                        <p class="mt-1 text-sm text-gray-500">Maximum 5000 characters</p>
                    </div>

                    <!-- Location -->
                    <div class="relative">
                        <label for="location"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <div class="relative">
                            <input 
                                v-model="form.location" 
                                @input="onLocationInput"
                                @focus="showSuggestions = true"
                                @blur="hideSuggestions"
                                id="location" 
                                type="text" 
                                placeholder="e.g., Istanbul, Turkey"
                                class="block w-full px-4 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                autocomplete="off" />
                            
                            <!-- Loading icon -->
                            <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3">
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
                        <div v-if="showSuggestions && locationSuggestions.length > 0" 
                            class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto">
                            <ul class="py-1">
                                <li v-for="(suggestion, index) in locationSuggestions" 
                                    :key="index"
                                    @mousedown="selectLocation(suggestion)"
                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
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
                        
                        <div v-if="form.errors.location" class="mt-1 text-sm text-red-600">{{ form.errors.location }}</div>
                        <p class="mt-1 text-sm text-gray-500">Start typing to search for locations</p>
                    </div>

                    <!-- Social Media -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="instagram"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instagram</label>
                            <input v-model="form.instagram" id="instagram" type="text" placeholder="@username"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                        <div>
                            <label for="twitter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Twitter</label>
                            <input v-model="form.twitter" id="twitter" type="text" placeholder="@username"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="facebook"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Facebook</label>
                            <input v-model="form.facebook" id="facebook" type="text" placeholder="Facebook profile URL"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                        <div>
                            <label for="youtube"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Youtube</label>
                            <input v-model="form.youtube" id="youtube" type="text" placeholder="Youtube Link"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        </div>
                    </div>

                    <div>
                        <label for="soundcloud"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SoundCloud</label>
                        <input v-model="form.soundcloud" id="soundcloud" type="text"
                            placeholder="SoundCloud profile URL"
                            class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                    </div>

                    <!-- File Uploads -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Profile Photo -->
                        <div>
                            <label for="profile_photo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-center">Profile
                                Photo</label>

                            <!-- Current profile photo preview -->
                            <div v-if="props.user?.profile_photo && !form.profile_photo" class="mb-4 text-center">
                                <img :src="`/storage/${props.user.profile_photo}`" alt="Current profile photo"
                                    class="w-20 h-20 rounded-full mx-auto object-cover border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-500 mt-1">Current photo</p>
                            </div>

                            <label for="profile_photo"
                                class="flex flex-col items-center justify-center border-2 border-dashed border-green-400 dark:border-green-600 rounded-lg p-6 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                                <template v-if="form.profile_photo">
                                    <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ form.profile_photo.name
                                        }}</span>
                                </template>
                                <template v-else>
                                    <svg class="w-12 h-12 text-green-400 dark:text-green-600 mb-2" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Click to change profile
                                        photo</span>
                                </template>
                                <input id="profile_photo" type="file" accept="image/*"
                                    @change="handleProfilePhotoChange" class="hidden" />
                            </label>
                            <div v-if="form.errors.profile_photo" class="mt-1 text-sm text-red-600">{{
                                form.errors.profile_photo }}</div>
                        </div>

                        <!-- Cover Image -->
                        <div>
                            <label for="cover_image"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-center">Cover
                                Image</label>

                            <!-- Current cover image preview -->
                            <div v-if="props.user?.cover_image && !form.cover_image" class="mb-4 text-center">
                                <img :src="`/storage/${props.user.cover_image}`" alt="Current cover image"
                                    class="w-32 h-20 mx-auto object-cover rounded border-2 border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-500 mt-1">Current cover</p>
                            </div>

                            <label for="cover_image"
                                class="flex flex-col items-center justify-center border-2 border-dashed border-purple-400 dark:border-purple-600 rounded-lg p-6 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                                <template v-if="form.cover_image">
                                    <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ form.cover_image.name
                                        }}</span>
                                </template>
                                <template v-else>
                                    <svg class="w-12 h-12 text-purple-400 dark:text-purple-600 mb-2" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Click to change cover
                                        image</span>
                                </template>
                                <input id="cover_image" type="file" accept="image/*" @change="handleCoverImageChange"
                                    class="hidden" />
                            </label>
                            <div v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">{{
                                form.errors.cover_image }}</div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-center space-x-3">
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-300 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Profile</span>
                        </button>
                        <Link :href="route('dashboard')"
                            class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, nextTick } from 'vue'

const props = defineProps({
    user: Object,
    isOwnProfile: Boolean
})

// Location search states
const isSearching = ref(false)
const locationSuggestions = ref([])
const showSuggestions = ref(false)
const searchTimeout = ref<NodeJS.Timeout | null>(null)

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    bio: props.user?.bio || '',
    location: props.user?.location || '',
    profile_photo: null as File | null,
    cover_image: null as File | null,
    instagram: props.user?.instagram || '',
    twitter: props.user?.twitter || '',
    facebook: props.user?.facebook || '',
    youtube: props.user?.youtube || '',
    soundcloud: props.user?.soundcloud || '',
})

const handleProfilePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.profile_photo = target.files[0]
    }
}

const handleCoverImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.cover_image = target.files[0]
    }
}

// Location search functionality
const searchLocations = async (query: string) => {
    try {
        isSearching.value = true

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
        isSearching.value = false
    }
}

const selectLocation = (suggestion: any) => {
    form.location = suggestion.place_name
    locationSuggestions.value = []
    showSuggestions.value = false
}

const onLocationInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    const query = target.value.trim()
    
    // Clear existing timeout
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }
    
    if (query.length >= 2) {
        // Debounce search for 300ms
        searchTimeout.value = setTimeout(() => {
            searchLocations(query)
        }, 300)
        showSuggestions.value = true
    } else {
        locationSuggestions.value = []
        showSuggestions.value = false
    }
}

const hideSuggestions = () => {
    // Delay hiding to allow click events on suggestions
    setTimeout(() => {
        showSuggestions.value = false
    }, 150)
}

const submit = () => {
    console.log('Form data:', form);
    form.post(route('profile.update'), {
        forceFormData: true,
        _method: 'patch'
    });
}
</script>

<style scoped></style>
