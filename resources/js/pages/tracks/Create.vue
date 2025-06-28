<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Radio, Upload, Music, Clock, Star, DollarSign } from 'lucide-vue-next'

interface Category {
    id: number
    name: string
}

interface User {
    id: number
    name: string
}

const props = defineProps<{
    categories: Category[]
    users: User[]
}>();

const form = useForm({
    title: '',
    description: '',
    audio_file: null as File | null,
    image_file: null as File | null,
    duration: '',
    category_id: '',
    user_id: '',
    is_premium: false,
    iap_product_id: ''
})

const audioFileInput = ref<HTMLInputElement>()
const imageFileInput = ref<HTMLInputElement>()
const audioPreview = ref<string>('')
const imagePreview = ref<string>('')

const handleAudioUpload = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]

    if (file) {
        form.audio_file = file

        // Audio preview
        const reader = new FileReader()
        reader.onload = (e) => {
            audioPreview.value = e.target?.result as string
        }
        reader.readAsDataURL(file)
    }
}

const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]

    if (file) {
        form.image_file = file

        // Image preview
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string
        }
        reader.readAsDataURL(file)
    }
}

const removeAudioFile = () => {
    form.audio_file = null
    audioPreview.value = ''
    if (audioFileInput.value) {
        audioFileInput.value.value = ''
    }
}

const removeImageFile = () => {
    form.image_file = null
    imagePreview.value = ''
    if (imageFileInput.value) {
        imageFileInput.value.value = ''
    }
}

const submitForm = () => {
    form.post(route('tracks.store'), {
        forceFormData: true
    })
}

const categories = computed(() => props.categories ?? [])
const users = computed(() => props.users ?? [])
</script>

<template>

    <Head title="Create Track" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Tracks', href: '/tracks' },
        { title: 'Create Track', href: '/tracks/create' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500 p-2 rounded-lg">
                        <Radio class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Track</h1>
                        <p class="text-gray-600 dark:text-gray-400">Add a new track to your collection</p>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <form @submit.prevent="submitForm" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Title Field -->
                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Track Title *
                                </label>
                                <input id="title" v-model="form.title" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter track title" required />
                                <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description
                                </label>
                                <textarea id="description" v-model="form.description" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter track description"></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{
                                    form.errors.description }}</div>
                            </div>

                            <!-- Duration Field -->
                            <div>
                                <label for="duration"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <Clock class="w-4 h-4 inline mr-1" />
                                    Duration (mm:ss)
                                </label>
                                <input id="duration" v-model="form.duration" type="text"
                                    pattern="^([0-5]?\d):([0-5]\d)$"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="e.g., 3:45" />
                                <div v-if="form.errors.duration" class="mt-1 text-sm text-red-600">{{
                                    form.errors.duration }}</div>
                                <p class="mt-1 text-xs text-gray-500">Format: minutes:seconds (e.g., 3:45)</p>
                            </div>

                            <!-- Category Selection -->
                            <div>
                                <label for="category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Category
                                </label>
                                <select id="category" v-model="form.category_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select a category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{
                                    form.errors.category_id }}</div>
                            </div>

                            <!-- DJ/User Selection -->
                            <div>
                                <label for="user"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    DJ/Artist
                                </label>
                                <select id="user" v-model="form.user_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select DJ/Artist</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_id
                                    }}</div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Audio File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <Music class="w-4 h-4 inline mr-1" />
                                    Audio File *
                                </label>
                                <div
                                    class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                    <input ref="audioFileInput" type="file" @change="handleAudioUpload" accept="audio/*"
                                        class="hidden" />

                                    <div v-if="!audioPreview">
                                        <Upload class="mx-auto h-12 w-12 text-gray-400" />
                                        <div class="mt-4">
                                            <button type="button" @click="audioFileInput?.click()"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Choose Audio File
                                            </button>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">MP3, WAV, or other audio formats</p>
                                    </div>

                                    <div v-else class="space-y-4">
                                        <audio controls class="w-full">
                                            <source :src="audioPreview" />
                                            Your browser does not support the audio element.
                                        </audio>
                                        <div class="flex space-x-2">
                                            <button type="button" @click="audioFileInput?.click()"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Change File
                                            </button>
                                            <button type="button" @click="removeAudioFile"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.audio_file" class="mt-1 text-sm text-red-600">{{
                                    form.errors.audio_file }}</div>
                            </div>

                            <!-- Image File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Track Cover Image
                                </label>
                                <div
                                    class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                                    <input ref="imageFileInput" type="file" @change="handleImageUpload" accept="image/*"
                                        class="hidden" />

                                    <div v-if="!imagePreview">
                                        <Upload class="mx-auto h-12 w-12 text-gray-400" />
                                        <div class="mt-4">
                                            <button type="button" @click="imageFileInput?.click()"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Choose Image
                                            </button>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">PNG, JPG, or other image formats</p>
                                    </div>

                                    <div v-else class="space-y-4">
                                        <img :src="imagePreview"
                                            class="max-w-full h-32 object-cover rounded-lg mx-auto" />
                                        <div class="flex space-x-2">
                                            <button type="button" @click="imageFileInput?.click()"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Change Image
                                            </button>
                                            <button type="button" @click="removeImageFile"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="form.errors.image_file" class="mt-1 text-sm text-red-600">{{
                                    form.errors.image_file }}</div>
                            </div>

                            <!-- Premium Settings -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                                    <Star class="w-5 h-5 mr-2 text-yellow-500" />
                                    Premium Settings
                                </h3>

                                <!-- Premium Toggle -->
                                <div class="flex items-center">
                                    <input id="is_premium" v-model="form.is_premium" type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                                    <label for="is_premium" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                        This is a premium track
                                    </label>
                                </div>

                                <!-- IAP Product ID -->
                                <div v-if="form.is_premium">
                                    <label for="iap_product_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <DollarSign class="w-4 h-4 inline mr-1" />
                                        IAP Product ID
                                    </label>
                                    <input id="iap_product_id" v-model="form.iap_product_id" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="com.yourapp.track.premium" />
                                    <div v-if="form.errors.iap_product_id" class="mt-1 text-sm text-red-600">{{
                                        form.errors.iap_product_id }}</div>
                                    <p class="mt-1 text-xs text-gray-500">In-App Purchase product identifier</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="flex flex-col sm:flex-row sm:justify-end sm:space-x-4 space-y-3 sm:space-y-0 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <Link :href="route('tracks.index')"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Track</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
