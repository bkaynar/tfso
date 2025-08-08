<template>
  <Head title="Send DJ Offer" />
  <AppLayout :breadcrumbs="[
    { title: 'Find DJs', href: route('dj-offers.dj-list') },
    { title: 'Send Offer', href: '' }
  ]">
    <div class="max-w-3xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
          <span class="inline-block bg-purple-500 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          </span>
          Send Offer to {{ dj.name }}
        </h1>

        <!-- DJ Info Card -->
        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-lg">
              {{ dj.name.charAt(0).toUpperCase() }}
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ dj.name }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ dj.email }}</p>
              <span class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Verified DJ
              </span>
            </div>
          </div>
        </div>

        <!-- Offer Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Event Details -->
          <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Event Date -->
              <div>
                <label for="event_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Event Date *
                </label>
                <input v-model="form.event_date" id="event_date" type="date" :min="minDate" required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                <div v-if="form.errors.event_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.event_date }}
                </div>
              </div>

              <!-- Event Time -->
              <div>
                <label for="event_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Event Time *
                </label>
                <input v-model="form.event_time" id="event_time" type="time" required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                <div v-if="form.errors.event_time" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.event_time }}
                </div>
              </div>

              <!-- Duration -->
              <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Duration (Hours) *
                </label>
                <input v-model="form.duration" id="duration" type="number" step="0.5" min="0.5" max="24" required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                  placeholder="e.g., 3.5" />
                <div v-if="form.errors.duration" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.duration }}
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Enter the performance duration in hours (e.g., 3.5 for 3 hours 30 minutes)
                </p>
              </div>

              <!-- Budget -->
              <div>
                <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Budget (₺) *
                </label>
                <input v-model="form.budget" id="budget" type="number" min="0" step="0.01" required
                  class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                  placeholder="e.g., 2500.00" />
                <div v-if="form.errors.budget" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.budget }}
                </div>
              </div>
            </div>
          </div>

          <!-- Offer Details -->
          <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Offer Details</h3>

            <!-- Description -->
            <div class="mb-6">
              <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Event Description
              </label>
              <textarea v-model="form.description" id="description" rows="6"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Provide details about the event, music genre preferences, audience type, special requirements, etc."></textarea>
              <div v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ form.errors.description }}
              </div>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ form.description.length }}/2000 characters
              </p>
            </div>

            <!-- Expiry Date -->
            <div>
              <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Offer Expires At
              </label>
              <input v-model="form.expires_at" id="expires_at" type="datetime-local" :min="minDateTime"
                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
              <div v-if="form.errors.expires_at" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ form.errors.expires_at }}
              </div>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Leave empty to expire in 7 days (default)
              </p>
            </div>
          </div>

          <!-- Place Info -->
          <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Venue Information</h3>
            <div class="flex items-center">
              <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div>
                <h4 class="font-semibold text-gray-900 dark:text-white">{{ place.name }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ place.location }}</p>
                <span v-if="place.is_premium" class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                  ⭐ Premium Venue
                </span>
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
            <Link :href="route('dj-offers.dj-list')"
              class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
              </svg>
              Cancel
            </Link>
            
            <button type="submit" :disabled="form.processing"
              class="inline-flex items-center px-8 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-sm font-medium rounded-lg transition-colors duration-200">
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              {{ form.processing ? 'Sending Offer...' : 'Send Offer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  dj: Object,
  place: Object
})

const form = useForm({
  dj_id: props.dj.id,
  event_date: '',
  event_time: '',
  duration: '',
  budget: '',
  description: '',
  expires_at: ''
})

// Minimum date is tomorrow
const minDate = computed(() => {
  const tomorrow = new Date()
  tomorrow.setDate(tomorrow.getDate() + 1)
  return tomorrow.toISOString().split('T')[0]
})

// Minimum datetime is now
const minDateTime = computed(() => {
  const now = new Date()
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
  return now.toISOString().slice(0, 16)
})

const submit = () => {
  form.post(route('dj-offers.store'))
}
</script>