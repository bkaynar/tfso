<template>

  <Head :title="isAdmin ? 'Etkinlik Düzenle' : 'Etkinlik Görüntüle'" />
  <AppLayout :breadcrumbs="[
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Etkinlikler', href: '/events' },
    { title: isAdmin ? 'Etkinlik Düzenle' : 'Etkinlik Görüntüle', href: '' }
  ]">
    <div class="max-w-2xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
          <span class="inline-block bg-blue-500 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0V7a1 1 0 00-1 1v10a1 1 0 001 1h6a1 1 0 001-1V8a1 1 0 00-1-1V7" />
            </svg>
          </span>
          {{ isAdmin ? 'Etkinlik Düzenle' : 'Etkinlik Görüntüle' }}
        </h1>

        <!-- Read-only notice for DJ -->
        <div v-if="!isAdmin"
          class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
          <p class="text-sm text-yellow-800 dark:text-yellow-200">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Sadece görüntüleme yetkisine sahipsiniz. Etkinlikleri düzenlemek için admin yetkisi gerekir.
          </p>
        </div>

        <form v-if="isAdmin" @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
          <!-- Title and Image Row -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0 items-center">
            <div class="flex-1">
              <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Etkinlik Başlığı *
              </label>
              <input v-model="form.title" id="title" type="text" required
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <div class="flex-1 flex flex-col items-center justify-center">
              <label for="image"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 w-full text-center">Resim</label>
              <label for="image"
                class="flex flex-col items-center justify-center border-2 border-dashed border-blue-400 dark:border-blue-600 rounded-lg p-6 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 transition mb-2 w-full">
                <template v-if="imagePreview">
                  <img :src="imagePreview" alt="Önizleme" class="w-32 h-32 object-cover rounded mb-2 mx-auto" />
                  <span class="text-xs text-gray-500 dark:text-gray-400">Farklı bir resim seçmek için tıklayın</span>
                </template>
                <template v-else>
                  <svg class="w-12 h-12 text-blue-400 dark:text-blue-600 mb-2 mx-auto" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="text-sm text-gray-500 dark:text-gray-400">Resim seçmek için tıklayın</span>
                </template>
                <input id="image" type="file" accept="image/*" @change="handleFileChange" class="hidden" />
              </label>
            </div>
          </div>

          <!-- User and Place Row -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
            <div class="flex-1">
              <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                DJ
              </label>
              <select v-model="form.user_id" id="user_id"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">DJ seçin</option>
                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
              </select>
            </div>
            <div class="flex-1">
              <label for="place_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Mekan
              </label>
              <select v-model="form.place_id" id="place_id"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Mekan seçin</option>
                <option v-for="place in places" :key="place.id" :value="place.id">{{ place.name }}</option>
              </select>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Açıklama
            </label>
            <textarea v-model="form.description" id="description" rows="4"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Etkinlik hakkında detaylar..."></textarea>
          </div>

          <!-- Date and Location Row -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
            <div class="flex-1">
              <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tarih
              </label>
              <input v-model="form.date" id="date" type="datetime-local"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <div class="flex-1">
              <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Lokasyon
              </label>
              <input v-model="form.location" id="location" type="text"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Etkinlik konumu" />
            </div>
          </div>

          <!-- Ticket Link -->
          <div>
            <label for="ticket_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Bilet Linki
            </label>
            <input v-model="form.ticket_link" id="ticket_link" type="url"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="https://..." />
          </div>

          <div class="flex items-center space-x-3">
            <button type="submit"
              class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
              Güncelle
            </button>
            <Link :href="route('events.index')"
              class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Geri
            </Link>
          </div>
        </form>

        <!-- Read-only view for DJ -->
        <div v-else class="space-y-6">
          <!-- Title and Image Row -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0 items-center">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Etkinlik Başlığı</label>
              <div
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                {{ props.event.title }}
              </div>
            </div>
            <div class="flex-1 flex flex-col items-center justify-center">
              <label
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 w-full text-center">Resim</label>
              <div
                class="flex flex-col items-center justify-center border-2 border-gray-300 dark:border-gray-600 rounded-lg p-6 w-full bg-gray-50 dark:bg-gray-700">
                <template v-if="props.event.image">
                  <img :src="`/storage/${props.event.image}`" alt="Etkinlik Resmi"
                    class="w-32 h-32 object-cover rounded mb-2 mx-auto" />
                </template>
                <template v-else>
                  <svg class="w-12 h-12 text-gray-400 mb-2 mx-auto" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="text-sm text-gray-500 dark:text-gray-400">Resim yok</span>
                </template>
              </div>
            </div>
          </div>

          <!-- User and Place Row (Read-only) -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DJ</label>
              <div
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                {{ props.event.user?.name || 'Belirtilmemiş' }}
              </div>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mekan</label>
              <div
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                {{ props.event.place?.name || 'Belirtilmemiş' }}
              </div>
            </div>
          </div>

          <!-- Description (Read-only) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Açıklama</label>
            <div
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white min-h-[100px]">
              {{ props.event.description || 'Açıklama yok' }}
            </div>
          </div>

          <!-- Date and Location Row (Read-only) -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarih</label>
              <div
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                {{ formatDate(props.event.date) }}
              </div>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasyon</label>
              <div
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                {{ props.event.location || 'Belirtilmemiş' }}
              </div>
            </div>
          </div>

          <!-- Ticket Link (Read-only) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bilet Linki</label>
            <div
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
              <a v-if="props.event.ticket_link" :href="props.event.ticket_link" target="_blank"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                {{ props.event.ticket_link }}
              </a>
              <span v-else>Bilet linki yok</span>
            </div>
          </div>

          <div class="flex items-center justify-center">
            <Link :href="route('events.index')"
              class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Geri
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  event: {
    type: Object,
    required: true
  },
  users: Array,
  places: Array
})

const page = usePage()

// Helper function to check if user has specific role
const hasRole = (role: string) => {
  const userRoles = page.props.auth.roles as string[];
  return userRoles && userRoles.includes(role);
};

// Check if user is admin
const isAdmin = computed(() => hasRole('admin'));

// Format date to local string
const formatDate = (dateString: string) => {
  if (!dateString) return 'Tarih belirtilmemiş';
  return new Date(dateString).toLocaleString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

// Format date for datetime-local input
const formatDateForInput = (dateString: string) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toISOString().slice(0, 16);
}

const form = ref({
  user_id: props.event.user_id || '',
  place_id: props.event.place_id || '',
  title: props.event.title || '',
  description: props.event.description || '',
  date: formatDateForInput(props.event.date) || '',
  location: props.event.location || '',
  image: null as File | null,
  ticket_link: props.event.ticket_link || ''
})

const imagePreview = ref<string | null>(
  props.event.image ? `/storage/${props.event.image}` : null
)

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.value.image = target.files[0]
    imagePreview.value = URL.createObjectURL(target.files[0])
  } else {
    form.value.image = null
    imagePreview.value = props.event.image ? `/storage/${props.event.image}` : null
  }
}

const submit = async () => {
  if (!isAdmin.value) return; // Only admin can submit

  const data = new FormData()
  data.append('user_id', form.value.user_id)
  data.append('place_id', form.value.place_id)
  data.append('title', form.value.title)
  data.append('description', form.value.description)
  data.append('date', form.value.date)
  data.append('location', form.value.location)
  data.append('ticket_link', form.value.ticket_link)
  
  if (form.value.image) {
    data.append('image', form.value.image)
  }
  
  await router.post(`/events/${props.event.id}?_method=PUT`, data)
}
</script>