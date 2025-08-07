<template>

  <Head title="Yeni Etkinlik" />
  <AppLayout :breadcrumbs="[
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Etkinlikler', href: '/events' },
    { title: 'Yeni Etkinlik', href: '' }
  ]">
    <div class="max-w-2xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
          <span class="inline-block bg-blue-500 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0V7a1 1 0 00-1 1v10a1 1 0 001 1h6a1 1 0 001-1V8a1 1 0 00-1-1V7" />
            </svg>
          </span>
          Yeni Etkinlik
        </h1>
        <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
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
                  <span class="text-sm text-gray-500 dark:text-gray-400">
                    Resim seçmek için tıklayın
                  </span>
                </template>
                <input id="image" type="file" accept="image/*" @change="handleFileChange" class="hidden" />
              </label>
            </div>
          </div>

          <!-- User and Place Row -->
          <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
            <div :class="isPlaceManager ? 'w-full' : 'flex-1'">
              <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                DJ
              </label>
              
              <!-- DJ Selection for Admin/DJ (dropdown) -->
              <div v-if="!isPlaceManager">
                <select v-model="form.user_id" id="user_id"
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="">DJ seçin</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
              </div>
              
              <!-- DJ Search for PlaceManager -->
              <div v-else class="relative">
                <div class="flex">
                  <input 
                    v-model="djSearchQuery" 
                    type="text"
                    placeholder="DJ adı yazın ve arayın..."
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    @input="searchDJs"
                  />
                  <button 
                    type="button" 
                    @click="searchDJs"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg border border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </button>
                </div>
                
                <!-- Search Results Dropdown -->
                <div v-if="djSearchResults.length > 0 && showDjResults" 
                  class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                  <div 
                    v-for="dj in djSearchResults" 
                    :key="dj.id"
                    @click="selectDJ(dj)"
                    class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                    <div class="font-medium text-gray-900 dark:text-white">{{ dj.name }}</div>
                  </div>
                </div>
                
                <!-- Selected DJ Display -->
                <div v-if="selectedDJ" class="mt-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                  <div class="flex items-center justify-between">
                    <span class="text-green-800 dark:text-green-200">
                      <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                      </svg>
                      Seçilen DJ: {{ selectedDJ.name }}
                    </span>
                    <button type="button" @click="clearDJSelection" 
                      class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  DJ seçimi isteğe bağlıdır. Etkinlikte DJ varsa arayıp seçebilirsiniz.
                </p>
              </div>
            </div>
            <div v-if="!isPlaceManager" class="flex-1">
              <label for="place_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Mekan
              </label>
              <select v-model="form.place_id" id="place_id"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Mekan seçin</option>
                <option v-for="place in places" :key="place.id" :value="place.id">{{ place.name }}</option>
              </select>
            </div>
            <!-- Hidden input for placeManager's place -->
            <input v-if="isPlaceManager" type="hidden" v-model="form.place_id" />
            <!-- Show selected place for placeManager -->
            <div v-if="isPlaceManager && places && places.length > 0" class="flex-1">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Mekan
              </label>
              <div class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                {{ places[0].name }} (Sizin mekanınız)
              </div>
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
              Kaydet
            </button>
            <Link :href="route('events.index')"
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
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  users: Array,
  places: Array
})

const page = usePage()

// Helper function to check if user has specific role
const hasRole = (role: string) => {
  const userRoles = page.props.auth.roles as string[];
  return userRoles && userRoles.includes(role);
};

const isPlaceManager = computed(() => hasRole('placeManager'));

const form = ref({
  user_id: '',
  place_id: '',
  title: '',
  description: '',
  date: '',
  location: '',
  image: null as File | null,
  ticket_link: ''
})

// Auto-select place for placeManager
onMounted(() => {
  if (isPlaceManager.value && props.places && props.places.length > 0) {
    form.value.place_id = props.places[0].id;
  }
});

// DJ Search for PlaceManager
const djSearchQuery = ref('')
const djSearchResults = ref([])
const showDjResults = ref(false)
const selectedDJ = ref(null)
let searchTimeout: number

const searchDJs = async () => {
  const query = djSearchQuery.value.trim()
  
  if (query.length < 2) {
    djSearchResults.value = []
    showDjResults.value = false
    return
  }

  // Clear previous timeout
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  // Debounce search
  searchTimeout = setTimeout(async () => {
    try {
      // Filter from existing users list
      djSearchResults.value = props.users.filter(user => 
        user.name.toLowerCase().includes(query.toLowerCase())
      )
      showDjResults.value = true
    } catch (error) {
      console.error('DJ search error:', error)
    }
  }, 300)
}

const selectDJ = (dj: any) => {
  selectedDJ.value = dj
  form.value.user_id = dj.id
  djSearchQuery.value = dj.name
  showDjResults.value = false
}

const clearDJSelection = () => {
  selectedDJ.value = null
  form.value.user_id = ''
  djSearchQuery.value = ''
  djSearchResults.value = []
  showDjResults.value = false
}

const imagePreview = ref<string | null>(null)

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.value.image = target.files[0]
    imagePreview.value = URL.createObjectURL(target.files[0])
  } else {
    form.value.image = null
    imagePreview.value = null
  }
}

const submit = async () => {
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

  await router.post('/events', data)
}
</script>
