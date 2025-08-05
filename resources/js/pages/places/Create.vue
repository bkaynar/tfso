<template>
  <Head title="Yeni Mekan" />
  <AppLayout :breadcrumbs="[
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Mekanlar', href: '/places' },
    { title: 'Yeni Mekan', href: '' }
  ]">
    <div class="max-w-4xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
          <span class="inline-block bg-purple-500 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
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
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Mekan Adı *
                </label>
                <input v-model="form.name" id="name" type="text" required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
              </div>

              <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Konum *
                </label>
                <input v-model="form.location" id="location" type="text" required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Açıklama
                </label>
                <textarea v-model="form.description" id="description" rows="4"
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
              </div>

              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
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
                <div class="border-2 border-dashed border-purple-400 dark:border-purple-600 rounded-lg p-6">
                  <div v-if="imagePreviews.length > 0" class="grid grid-cols-2 gap-2 mb-4">
                    <div v-for="(preview, index) in imagePreviews" :key="index" class="relative">
                      <img :src="preview" alt="Preview" class="w-full h-24 object-cover rounded" />
                      <button type="button" @click="removeImage(index)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                      </button>
                    </div>
                  </div>
                  <label for="images" class="cursor-pointer block text-center">
                    <svg class="w-8 h-8 text-purple-400 dark:text-purple-600 mb-2 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                      Resim eklemek için tıklayın
                    </span>
                  </label>
                  <input id="images" type="file" accept="image/*" multiple @change="handleImageChange" class="hidden" />
                </div>
              </div>

              <div class="flex items-center">
                <input v-model="form.is_premium" id="is_premium" type="checkbox" 
                  class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
                <label for="is_premium" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                  Premium Mekan
                </label>
              </div>
            </div>
          </div>

          <!-- Maps URLs -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="google_maps_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Google Maps URL
              </label>
              <input v-model="form.google_maps_url" id="google_maps_url" type="url"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
            </div>

            <div>
              <label for="apple_maps_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Apple Maps URL
              </label>
              <input v-model="form.apple_maps_url" id="apple_maps_url" type="url"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
            </div>
          </div>

          <!-- Social Media -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="facebook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Facebook URL
              </label>
              <input v-model="form.facebook_url" id="facebook_url" type="url"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
            </div>

            <div>
              <label for="instagram_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Instagram URL
              </label>
              <input v-model="form.instagram_url" id="instagram_url" type="url"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
            </div>

            <div>
              <label for="twitter_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Twitter URL
              </label>
              <input v-model="form.twitter_url" id="twitter_url" type="url"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
            </div>

            <div>
              <label for="youtube_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
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
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

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
  images: [] as File[]
})

const imagePreviews = ref<string[]>([])

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
  
  form.value.images.forEach((image, index) => {
    data.append(`images[${index}]`, image)
  })
  
  await router.post('/places', data)
}
</script>