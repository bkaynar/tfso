<template>

    <Head title="Yeni Mekan" />
    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Mekanlar', href: '/places' },
        { title: 'Yeni Mekan', href: '' }
    ]">
        <div class="max-w-4xl mx-auto py-10">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
                    <span class="inline-block bg-purple-500 p-2 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    Yeni Mekan Ekle
                </h1>

                <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Mekan Adı *
                                </label>
                                <input v-model="form.name" id="name" type="text" required
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>

                            <div class="relative">
                                <label for="location"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Konum *
                                </label>
                                <input v-model="form.location" id="location" type="text" required
                                    @input="handleLocationSearch" @focus="showSuggestions = true"
                                    @blur="hideSuggestions" placeholder="Şehir, ülke veya adres yazın..."
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />

                                <!-- Suggestions dropdown -->
                                <div v-if="showSuggestions && locationSuggestions.length > 0"
                                    class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div v-for="(suggestion, index) in locationSuggestions" :key="index"
                                        @mousedown="selectLocation(suggestion)"
                                        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ suggestion.place_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ suggestion.context?.join(', ') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading indicator -->
                                <div v-if="isSearching" class="absolute right-3 top-9">
                                    <svg class="animate-spin h-4 w-4 text-purple-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Açıklama
                                </label>
                                <textarea v-model="form.description" id="description" rows="4"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                            </div>

                            <div>
                                <label for="phone"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Telefon
                                </label>
                                <input v-model="form.phone" id="phone" type="tel"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                            </div>
                        </div>

                        <!-- Images and Premium -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Mekan Resimleri
                                </label>
                                <div
                                    class="border-2 border-dashed border-purple-400 dark:border-purple-600 rounded-lg p-6">
                                    <div v-if="imagePreviews.length > 0" class="grid grid-cols-2 gap-2 mb-4">
                                        <div v-for="(preview, index) in imagePreviews" :key="index" class="relative">
                                            <img :src="preview" alt="Preview"
                                                class="w-full h-24 object-cover rounded" />
                                            <button type="button" @click="removeImage(index)"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                    <label for="images" class="cursor-pointer block text-center">
                                        <svg class="w-8 h-8 text-purple-400 dark:text-purple-600 mb-2 mx-auto"
                                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Resim eklemek için tıklayın
                                        </span>
                                    </label>
                                    <input id="images" type="file" accept="image/*" multiple @change="handleImageChange"
                                        class="hidden" />
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input v-model="form.is_premium" id="is_premium" type="checkbox"
                                        class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                                    <label for="is_premium"
                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                        Premium Mekan
                                    </label>
                                </div>

                                <!-- Place Manager Selection - Only for Admin -->
                                <div v-if="isAdmin">
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Mekan Yöneticisi
                                    </label>
                                    <select v-model="form.user_id" id="user_id"
                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                        <option :value="null">Yönetici atanmamış</option>
                                        <option v-for="manager in placeManagers" :key="manager.id" :value="manager.id">
                                            {{ manager.name }}
                                        </option>
                                        <option v-if="placeManagers.length === 0" disabled value="">
                                            Henüz PlaceManager kullanıcısı yok
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Bu mekanın yönetiminden sorumlu PlaceManager kullanıcısını seçin
                                        <span v-if="placeManagers.length === 0">
                                            (Önce
                                            <Link :href="route('users.create')"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline font-medium">
                                                PlaceManager rolü olan kullanıcı oluşturun
                                            </Link>)
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maps URLs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="google_maps_url"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Google Maps URL
                            </label>
                            <input v-model="form.google_maps_url" id="google_maps_url" type="url"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>

                        <div>
                            <label for="apple_maps_url"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Apple Maps URL
                            </label>
                            <input v-model="form.apple_maps_url" id="apple_maps_url" type="url"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="facebook_url"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Facebook URL
                            </label>
                            <input v-model="form.facebook_url" id="facebook_url" type="url"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>

                        <div>
                            <label for="instagram_url"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Instagram URL
                            </label>
                            <input v-model="form.instagram_url" id="instagram_url" type="url"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>

                        <div>
                            <label for="twitter_url"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Twitter URL
                            </label>
                            <input v-model="form.twitter_url" id="twitter_url" type="url"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>

                        <div>
                            <label for="youtube_url"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                YouTube URL
                            </label>
                            <input v-model="form.youtube_url" id="youtube_url" type="url"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Kaydet
                        </button>
                        <Link :href="route('places.index')"
                            class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Geri
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  placeManagers: {
    type: Array,
    default: () => []
  }
})

const page = usePage()

// Helper function to check if user has specific role
const hasRole = (role: string) => {
  const userRoles = page.props.auth.roles as string[];
  return userRoles && userRoles.includes(role);
};

const isAdmin = computed(() => hasRole('admin'))

const form = ref({
    name: '',
    description: '',
    location: '',
    phone: '',
    google_maps_url: '',
    apple_maps_url: '',
    facebook_url: '',
    instagram_url: '',
    twitter_url: '',
    youtube_url: '',
    is_premium: false,
    user_id: null as number | null,
    images: [] as File[]
})

const imagePreviews = ref<string[]>([])

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
    const data = new FormData()
    data.append('name', form.value.name)
    data.append('location', form.value.location)
    if (form.value.description) data.append('description', form.value.description)
    if (form.value.phone) data.append('phone', form.value.phone)
    if (form.value.google_maps_url) data.append('google_maps_url', form.value.google_maps_url)
    if (form.value.apple_maps_url) data.append('apple_maps_url', form.value.apple_maps_url)
    if (form.value.facebook_url) data.append('facebook_url', form.value.facebook_url)
    if (form.value.instagram_url) data.append('instagram_url', form.value.instagram_url)
    if (form.value.twitter_url) data.append('twitter_url', form.value.twitter_url)
    if (form.value.youtube_url) data.append('youtube_url', form.value.youtube_url)
    data.append('is_premium', form.value.is_premium ? '1' : '0')
    if (form.value.user_id) data.append('user_id', form.value.user_id.toString())

    form.value.images.forEach((image, index) => {
        data.append(`images[${index}]`, image)
    })

    await router.post('/places', data)
}
</script>
