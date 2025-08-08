<template>
  <Head title="DJ Offers" />
  <AppLayout :breadcrumbs="[
    { title: 'DJ Offers', href: '' }
  ]">
    <div class="max-w-6xl mx-auto py-10">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="bg-purple-500 p-2 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isPlaceManager ? 'Sent Offers' : 'DJ Offers' }}
              </h1>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ isPlaceManager ? 'Manage your sent offers to DJs' : 'View offers received from venues' }}
              </p>
            </div>
          </div>
          
          <Link v-if="isPlaceManager" :href="route('dj-offers.dj-list')" 
            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Send New Offer
          </Link>
        </div>
      </div>

      <!-- Status Filter -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
        <div class="flex space-x-4">
          <button @click="activeFilter = 'all'" 
            :class="['px-4 py-2 text-sm font-medium rounded-lg transition-colors', 
              activeFilter === 'all' 
                ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' 
                : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600']">
            All ({{ offers.length }})
          </button>
          <button @click="activeFilter = 'pending'" 
            :class="['px-4 py-2 text-sm font-medium rounded-lg transition-colors', 
              activeFilter === 'pending' 
                ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200' 
                : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600']">
            Pending ({{ pendingCount }})
          </button>
          <button @click="activeFilter = 'accepted'" 
            :class="['px-4 py-2 text-sm font-medium rounded-lg transition-colors', 
              activeFilter === 'accepted' 
                ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' 
                : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600']">
            Accepted ({{ acceptedCount }})
          </button>
          <button @click="activeFilter = 'rejected'" 
            :class="['px-4 py-2 text-sm font-medium rounded-lg transition-colors', 
              activeFilter === 'rejected' 
                ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' 
                : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600']">
            Rejected ({{ rejectedCount }})
          </button>
        </div>
      </div>

      <!-- Offers List -->
      <div v-if="filteredOffers.length > 0" class="space-y-4">
        <div v-for="offer in filteredOffers" :key="offer.id" 
          class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
          
          <!-- Offer Header -->
          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-lg">
                  {{ isPlaceManager ? offer.dj.name.charAt(0).toUpperCase() : offer.place.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ isPlaceManager ? offer.dj.name : offer.place.name }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ isPlaceManager ? offer.dj.email : offer.place.location }}
                  </p>
                </div>
              </div>
              
              <div class="flex items-center space-x-3">
                <!-- Status Badge -->
                <span :class="getStatusBadgeClass(offer.status)" 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ getStatusText(offer.status) }}
                </span>
                
                <!-- Expiry Badge -->
                <span v-if="isExpiringSoon(offer)" 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Expires Soon
                </span>
              </div>
            </div>
          </div>

          <!-- Offer Details -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
              <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Event Date</h4>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatDate(offer.event_date) }}
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Time</h4>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatTime(offer.event_time) }}
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Duration</h4>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ offer.duration }}h
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Budget</h4>
                <p class="text-lg font-semibold text-purple-600 dark:text-purple-400">
                  ₺{{ formatBudget(offer.budget) }}
                </p>
              </div>
            </div>

            <div v-if="offer.description" class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</h4>
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                {{ offer.description }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
              <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a4 4 0 118 0v4M5 7h14l1 10a2 2 0 01-2 2H6a2 2 0 01-2-2L5 7z" />
                </svg>
                <span>{{ getTimeAgo(offer.created_at) }}</span>
                
                <span v-if="offer.expires_at" class="ml-4 flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Expires {{ formatDate(offer.expires_at) }}
                </span>
              </div>
              
              <div class="flex items-center space-x-3">
                <!-- DJ Actions (Accept/Reject) -->
                <div v-if="!isPlaceManager && offer.status === 'pending'" class="flex space-x-2">
                  <button @click="acceptOffer(offer)" 
                    class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Accept
                  </button>
                  <button @click="rejectOffer(offer)" 
                    class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reject
                  </button>
                </div>

                <!-- PlaceManager Actions (Cancel) -->
                <div v-if="isPlaceManager && offer.status === 'pending'" class="flex space-x-2">
                  <button @click="cancelOffer(offer)" 
                    class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                  </button>
                </div>

                <!-- View/Message Button -->
                <Link :href="route('dj-offers.show', offer.id)" 
                  class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  {{ offer.unread_messages_count > 0 ? `Message (${offer.unread_messages_count})` : 'View Details' }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Offers Found</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
          {{ isPlaceManager 
            ? "You haven't sent any offers yet. Browse DJs and send your first offer!" 
            : "You haven't received any offers yet. Check back later!" }}
        </p>
        <Link v-if="isPlaceManager" :href="route('dj-offers.dj-list')" 
          class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
          Find DJs
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, usePage, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const props = defineProps({
  offers: Array
})

const activeFilter = ref('all')

const isPlaceManager = computed(() => {
  const roles = page.props.auth.roles as string[]
  return roles && roles.includes('placeManager')
})

const filteredOffers = computed(() => {
  if (activeFilter.value === 'all') return props.offers
  return props.offers.filter((offer: any) => offer.status === activeFilter.value)
})

const pendingCount = computed(() => props.offers.filter((offer: any) => offer.status === 'pending').length)
const acceptedCount = computed(() => props.offers.filter((offer: any) => offer.status === 'accepted').length)
const rejectedCount = computed(() => props.offers.filter((offer: any) => offer.status === 'rejected').length)

const getStatusBadgeClass = (status: string) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    expired: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
  }
  return classes[status] || classes.pending
}

const getStatusText = (status: string) => {
  const texts = {
    pending: 'Pending',
    accepted: 'Accepted',
    rejected: 'Rejected',
    expired: 'Expired'
  }
  return texts[status] || 'Unknown'
}

const isExpiringSoon = (offer: any) => {
  if (!offer.expires_at) return false
  const expiryDate = new Date(offer.expires_at)
  const now = new Date()
  const diffHours = (expiryDate.getTime() - now.getTime()) / (1000 * 3600)
  return diffHours > 0 && diffHours < 48 // Expires within 48 hours
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR')
}

const formatTime = (time: string) => {
  return time.slice(0, 5) // Remove seconds
}

const formatBudget = (budget: number) => {
  return new Intl.NumberFormat('tr-TR').format(budget)
}

const getTimeAgo = (date: string) => {
  const now = new Date()
  const created = new Date(date)
  const diffMinutes = Math.floor((now.getTime() - created.getTime()) / (1000 * 60))
  
  if (diffMinutes < 60) return `${diffMinutes} minutes ago`
  if (diffMinutes < 1440) return `${Math.floor(diffMinutes / 60)} hours ago`
  return `${Math.floor(diffMinutes / 1440)} days ago`
}

const acceptOffer = (offer: any) => {
  const form = useForm({})
  form.post(route('dj-offers.accept', offer.id))
}

const rejectOffer = (offer: any) => {
  const form = useForm({})
  form.post(route('dj-offers.reject', offer.id))
}

const cancelOffer = (offer: any) => {
  if (confirm('Are you sure you want to cancel this offer?')) {
    const form = useForm({})
    form.post(route('dj-offers.cancel', offer.id))
  }
}
</script>