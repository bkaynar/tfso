<template>

    <Head :title="'Edit Set'" />
    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Sets', href: '/sets' },
        { title: 'Edit Set', href: '' }
    ]">
        <div class="max-w-4xl mx-auto py-10">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                <h1
                    class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
                    <span class="inline-block bg-purple-500 p-2 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </span>
                    Edit Set
                </h1>
                <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                    <!-- Name and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Set Name</label>
                            <input v-model="form.name" id="name" type="text" required
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>
                        <div>
                            <label for="category_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <select v-model="form.category_id" id="category_id" required
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Select Category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{
                                    category.name }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- DJ Selection -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DJ
                            *</label>
                        <select v-model="form.user_id" id="user_id" required
                            class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select DJ</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea v-model="form.description" id="description" rows="3"
                            class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                    </div>

                    <!-- Cover Image and Audio File -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cover Image -->
                        <div>
                            <label for="cover_image"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-center">Cover
                                Image</label>
                            <label for="cover_image"
                                class="flex flex-col items-center justify-center border-2 border-dashed border-purple-400 dark:border-purple-600 rounded-lg p-6 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                                <template v-if="coverImagePreview">
                                    <img :src="coverImagePreview" alt="Preview"
                                        class="w-32 h-32 object-cover rounded mb-2" />
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Click to change cover
                                        image</span>
                                </template>
                                <template v-else>
                                    <svg class="w-12 h-12 text-purple-400 dark:text-purple-600 mb-2" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Click to select cover
                                        image</span>
                                </template>
                                <input id="cover_image" type="file" accept="image/*" @change="handleCoverImageChange"
                                    class="hidden" />
                            </label>
                        </div>

                        <!-- Audio File -->
                        <div>
                            <label for="audio_file"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-center">Audio
                                File</label>

                            <!-- Current Audio Player -->
                            <div v-if="set.audio_file && !form.audio_file"
                                class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Current
                                        Audio</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ set.audio_file_name ||
                                        'Audio file' }}</span>
                                </div>
                                <audio controls preload="metadata" class="w-full h-10" style="max-height: 40px;">
                                    <source :src="set.audio_url" type="audio/mpeg">
                                    <source :src="set.audio_url" type="audio/wav">
                                    <source :src="set.audio_url" type="audio/ogg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>

                            <label for="audio_file"
                                class="flex flex-col items-center justify-center border-2 border-dashed border-purple-400 dark:border-purple-600 rounded-lg p-6 cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                                <template v-if="form.audio_file">
                                    <svg class="w-12 h-12 text-green-500 mb-2" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ form.audio_file.name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">New audio file
                                        selected</span>
                                </template>
                                <template v-else>
                                    <svg class="w-12 h-12 text-purple-400 dark:text-purple-600 mb-2" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ set.audio_file ? 'Click to change audio file' : 'Click to select audio file'
                                        }}
                                    </span>
                                </template>
                                <input id="audio_file" type="file" accept="audio/*" @change="handleAudioFileChange"
                                    class="hidden" />
                            </label>
                        </div>
                    </div>

                    <!-- Premium and IAP Product ID -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input v-model="form.is_premium" id="is_premium" type="checkbox"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="is_premium" class="ml-2 block text-sm text-gray-900 dark:text-white">Premium
                                Set</label>
                        </div>
                        <div v-if="form.is_premium">
                            <label for="iap_product_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">IAP Product
                                ID</label>
                            <input v-model="form.iap_product_id" id="iap_product_id" type="text"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-center space-x-3">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Update Set
                        </button>
                        <Link :href="route('sets.index')"
                            class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Back
                        </Link>
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

const props = defineProps({
    set: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    },
    users: {
        type: Array,
        default: () => []
    }
})

const form = ref({
    name: props.set.name || '',
    description: props.set.description || '',
    category_id: props.set.category_id || '',
    user_id: props.set.user_id || '',
    cover_image: null as File | null,
    audio_file: null as File | null,
    is_premium: props.set.is_premium || false,
    iap_product_id: props.set.iap_product_id || ''
})

const coverImagePreview = ref<string | null>(props.set.cover_image || null)

const handleCoverImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        form.value.cover_image = target.files[0]
        coverImagePreview.value = URL.createObjectURL(target.files[0])
    } else {
        form.value.cover_image = null
        coverImagePreview.value = props.set.cover_image || null
    }
}

const handleAudioFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        form.value.audio_file = target.files[0]
    } else {
        form.value.audio_file = null
    }
}

const submit = async () => {
    const data = new FormData()
    data.append('name', form.value.name)
    data.append('description', form.value.description)
    data.append('category_id', form.value.category_id)
    data.append('user_id', form.value.user_id)
    data.append('is_premium', form.value.is_premium ? '1' : '0')
    if (form.value.iap_product_id) {
        data.append('iap_product_id', form.value.iap_product_id)
    }
    if (form.value.cover_image) {
        data.append('cover_image', form.value.cover_image)
    }
    if (form.value.audio_file) {
        data.append('audio_file', form.value.audio_file)
    }
    await router.post(`/sets/${props.set.id}?_method=PUT`, data)
}
</script>
