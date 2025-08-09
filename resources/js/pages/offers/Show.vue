<template>

  <Head title="Offer Details" />
  <AppLayout :breadcrumbs="[
    { title: isPlaceManager ? 'Sent Offers' : 'DJ Offers', href: route('dj-offers.index') },
    { title: 'Offer Details', href: '' }
  ]">
    <div class="max-w-6xl mx-auto py-10">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Offer Details Panel -->
        <div class="lg:col-span-2">
          <!-- Offer Header -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-4">
                <div
                  class="w-16 h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-xl">
                  {{ isPlaceManager ? offer.dj.name.charAt(0).toUpperCase() : offer.place.name.charAt(0).toUpperCase()
                  }}
                </div>
                <div>
                  <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ isPlaceManager ? `Offer to ${offer.dj.name}` : `Offer from ${offer.place.name}` }}
                  </h1>
                  <p class="text-gray-600 dark:text-gray-400">
                    {{ isPlaceManager ? offer.dj.email : offer.place.location }}
                  </p>
                </div>
              </div>

              <span :class="getStatusBadgeClass(offer.status)"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                {{ getStatusText(offer.status) }}
              </span>
            </div>

            <!-- Quick Actions -->
            <div v-if="!isPlaceManager && offer.status === 'pending'" class="flex space-x-3">
              <button @click="acceptOffer" :disabled="isProcessing"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Accept Offer
              </button>
              <button @click="rejectOffer" :disabled="isProcessing"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject Offer
              </button>
            </div>

            <div v-if="isPlaceManager && offer.status === 'pending'" class="flex space-x-3">
              <button @click="cancelOffer" :disabled="isProcessing"
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-400 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel Offer
              </button>
            </div>
          </div>

          <!-- Event Details -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M8 7V3a4 4 0 118 0v4m-4 8l2-2 2 2M8 13h8m-5-5.5A2.5 2.5 0 0113.5 5c0-1 1-2 2.5-2s2.5 1 2.5 2A2.5 2.5 0 0116 7.5V8a1 1 0 01-1 1H9a1 1 0 01-1-1v-.5z" />
              </svg>
              Event Details
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Date</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatDate(offer.event_date) }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Time</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatTime(offer.event_time) }}
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ offer.duration }} hours
                </p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget</h3>
                <div v-if="isPlaceManager && offer.status === 'pending' && !isOfferExpired"
                  class="flex items-center space-x-3">
                  <div v-if="!isEditingBudget" class="flex items-center space-x-3">
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                      ₺{{ formatBudget(localBudget) }}
                    </p>
                    <button type="button" @click="startEditBudget"
                      class="p-2 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/30 text-purple-600 dark:text-purple-300"
                      title="Edit budget">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                      </svg>
                    </button>
                  </div>
                  <form v-else @submit.prevent="saveBudget" class="flex items-center space-x-2">
                    <div class="relative">
                      <span class="absolute left-2 top-1/2 -translate-y-1/2 text-sm text-gray-500">₺</span>
                      <input v-model="editBudget" type="number" step="0.01" min="0"
                        class="pl-5 pr-2 py-1.5 border border-purple-300 dark:border-purple-600 rounded-md bg-white dark:bg-gray-700 text-sm w-32 focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <button type="submit" :disabled="savingBudget"
                      class="px-2 py-1.5 text-xs font-medium rounded-md bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white">Kaydet</button>
                    <button type="button" @click="cancelEditBudget" :disabled="savingBudget"
                      class="px-2 py-1.5 text-xs font-medium rounded-md bg-gray-500 hover:bg-gray-600 disabled:opacity-50 text-white">Vazgeç</button>
                  </form>
                </div>
                <p v-else class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  ₺{{ formatBudget(offer.budget) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Event Description -->
          <div v-if="offer.description" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Event Description
            </h2>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ offer.description }}</p>
          </div>

          <!-- Venue Information -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ isPlaceManager ? 'Your Venue' : 'Venue Information' }}
            </h2>

            <div class="flex items-start space-x-4">
              <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0v-4a2 2 0 011-1h4a2 2 0 011 1v4M9 7h6m0 0v10m0-10h2m-8 0V5a2 2 0 011-1h2a2 2 0 011 1v2M9 7H7m2 0h4" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ offer.place.name }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ offer.place.location }}</p>
                <span v-if="offer.place.is_premium"
                  class="inline-flex items-center px-2 py-1 mt-2 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                  ⭐ Premium Venue
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Messaging Panel -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg h-[600px] flex flex-col">
            <!-- Messages Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Messages
              </h2>
            </div>

            <!-- Messages List -->
            <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
              <div v-if="messages.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">
                <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-sm">No messages yet.</p>
                <p class="text-xs mt-1">Start the conversation!</p>
              </div>

              <div v-for="message in messages" :key="message.id"
                :class="['flex mb-4', message.user_id === currentUserId ? 'justify-end' : 'justify-start']">

                <!-- Message bubble with avatar -->
                <div :class="['flex max-w-[70%]', message.user_id === currentUserId ? 'flex-row-reverse' : 'flex-row']">

                  <!-- Avatar -->
                  <div :class="['flex-shrink-0', message.user_id === currentUserId ? 'ml-2' : 'mr-2']">
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold',
                      message.user_id === currentUserId ? 'bg-purple-500' : 'bg-gray-500']">
                      {{ message.user.name.charAt(0).toUpperCase() }}
                    </div>
                  </div>

                  <!-- Message content -->
                  <div class="flex flex-col">
                    <!-- Message header -->
                    <div
                      :class="['flex items-center mb-1', message.user_id === currentUserId ? 'flex-row-reverse' : 'flex-row']">
                      <span class="text-xs font-medium text-gray-600 dark:text-gray-400">
                        {{ message.user.name }}
                      </span>
                      <span
                        :class="['text-xs text-gray-500 dark:text-gray-500', message.user_id === currentUserId ? 'mr-2' : 'ml-2']">
                        {{ formatMessageTime(message.created_at) }}
                      </span>
                    </div>

                    <!-- Message bubble -->
                    <div :class="['px-4 py-3 rounded-2xl shadow-sm relative',
                      message.user_id === currentUserId
                        ? 'bg-purple-600 text-white rounded-br-md'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-bl-md']">

                      <!-- Message text -->
                      <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ message.message }}</p>

                      <!-- Message status for sent messages -->
                      <div v-if="message.user_id === currentUserId" class="flex items-center justify-end mt-1">
                        <svg v-if="message.sending" class="w-3 h-3 text-purple-300 animate-pulse" fill="currentColor"
                          viewBox="0 0 20 20">
                          <circle cx="10" cy="10" r="2" />
                        </svg>
                        <svg v-else-if="message.is_read" class="w-4 h-4 text-purple-200" fill="currentColor"
                          viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                          <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L4 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                        </svg>
                        <svg v-else class="w-4 h-4 text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
              <form @submit.prevent="sendMessage" class="flex space-x-2">
                <textarea v-model="newMessage" @keydown.enter.exact.prevent="sendMessage"
                  @keydown.enter.shift.exact="newMessage += '\n'"
                  placeholder="Type your message... (Shift+Enter for new line)" maxlength="2000" rows="1"
                  :disabled="isSendingMessage || props.canMessage === false"
                  class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none disabled:opacity-60"
                  style="min-height: 44px; max-height: 120px;"></textarea>
                <button type="submit" :disabled="props.canMessage === false || !newMessage.trim() || isSendingMessage"
                  class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-sm font-medium rounded-lg transition-colors">
                  <svg v-if="isSendingMessage" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                  </svg>
                </button>
              </form>
              <p v-if="props.canMessage !== false" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ newMessage.length }}/2000 characters
              </p>
              <p v-else class="text-xs text-red-500 dark:text-red-400 mt-1">Messaging disabled (offer cancelled)</p>
            </div>
          </div>

          <!-- Offer Timeline -->
          <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Offer Timeline</h3>
            <div class="space-y-2">
              <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                <span>Offer created {{ formatDate(offer.created_at) }}</span>
              </div>

              <div v-if="offer.status !== 'pending'" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div :class="['w-2 h-2 rounded-full mr-2',
                  offer.status === 'accepted' ? 'bg-green-500' : 'bg-red-500']"></div>
                <span>Offer {{ offer.status }} {{ formatDate(offer.updated_at) }}</span>
              </div>

              <div v-if="offer.expires_at" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                <span>{{ isOfferExpired ? 'Expired' : 'Expires' }} {{ formatDate(offer.expires_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, usePage, useForm } from '@inertiajs/vue3'
import { computed, ref, onMounted, nextTick } from 'vue'
import axios from 'axios'

const page = usePage()
interface OfferMessage {
  id: number | string
  message: string
  user_id: number
  user: { name: string }
  created_at: string
  is_read: boolean
  sending?: boolean
}

interface Offer {
  id: number
  status: string
  created_at: string
  updated_at: string
  expires_at?: string
  dj: { name: string; email: string }
  place: { name: string; location: string; is_premium: boolean }
  event_date: string
  event_time: string
  duration: number
  budget: number
  description?: string
  place_manager_id: number
  dj_id: number
}

const props = defineProps<{
  offer: Offer
  messages: OfferMessage[]
  canMessage?: boolean
}>()

const messagesContainer = ref<HTMLElement | null>(null)
const newMessage = ref('')
const messages = ref<OfferMessage[]>(props.messages || [])
const isSendingMessage = ref(false)
const isProcessing = ref(false)

const currentUserId = computed(() => page.props.auth.user?.id)
const isPlaceManager = computed(() => {
  const roles = page.props.auth.roles as string[]
  return roles && roles.includes('placeManager')
})

const isOfferExpired = computed(() => {
  if (!props.offer.expires_at) return false
  return new Date(props.offer.expires_at) < new Date()
})

// Budget edit state & logic
const isEditingBudget = ref(false)
const savingBudget = ref(false)
const localBudget = ref(Number(props.offer.budget))
const editBudget = ref(localBudget.value)

const startEditBudget = () => {
  editBudget.value = localBudget.value
  isEditingBudget.value = true
}
const cancelEditBudget = () => {
  isEditingBudget.value = false
}
const saveBudget = async () => {
  if (savingBudget.value) return
  savingBudget.value = true
  try {
    const { data } = await axios.patch(route('dj-offers.update-budget', props.offer.id), { budget: editBudget.value })
    if (data.success) {
      localBudget.value = Number(data.budget)
      isEditingBudget.value = false
    }
  } catch (e) {
    console.error('Budget update failed', e)
  } finally {
    savingBudget.value = false
  }
}

const scrollToBottom = async () => {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

onMounted(() => {
  scrollToBottom()
  if (props.canMessage !== false) {
    // Poll for new messages every 5 seconds
    setInterval(fetchMessages, 5000)
  }
})

const fetchMessages = async () => {
  if (props.canMessage === false) return
  try {
    const response = await fetch(route('offer-messages.index', props.offer.id))
    const data = await response.json()
    messages.value = data.messages as OfferMessage[]
    // Okunmamışlar backend'de işaretlendi; global unread sayısını tazele
    window.dispatchEvent(new CustomEvent('offer-unread-updated'))
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

const sendMessage = async () => {
  if (props.canMessage === false) return
  if (!newMessage.value.trim() || isSendingMessage.value) return

  const messageText = newMessage.value.trim()
  newMessage.value = ''
  isSendingMessage.value = true

  // Optimistic update - add message immediately
  const tempMessage: OfferMessage = {
    id: 'temp-' + Date.now(),
    message: messageText,
    user_id: currentUserId.value as number,
    user: { name: page.props.auth.user?.name || 'User' },
    created_at: new Date().toISOString(),
    is_read: false,
    sending: true
  }

  messages.value.push(tempMessage)
  scrollToBottom()

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const response = await fetch(route('offer-messages.store', props.offer.id), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || ''
      },
      body: JSON.stringify({
        message: messageText
      })
    })

    const data = await response.json()

    if (data.success) {
      // Replace temp message with real one
      const tempIndex = messages.value.findIndex(m => m.id === tempMessage.id)
      if (tempIndex !== -1) {
        messages.value[tempIndex] = data.message
      }
    } else {
      // Remove temp message on error
      const tempIndex = messages.value.findIndex(m => m.id === tempMessage.id)
      if (tempIndex !== -1) {
        messages.value.splice(tempIndex, 1)
      }
      newMessage.value = messageText // Restore message
    }
  } catch (error) {
    console.error('Error sending message:', error)
    // Remove temp message on error
    const tempIndex = messages.value.findIndex(m => m.id === tempMessage.id)
    if (tempIndex !== -1) {
      messages.value.splice(tempIndex, 1)
    }
    newMessage.value = messageText // Restore message
  } finally {
    isSendingMessage.value = false
  }
}

const getStatusBadgeClass = (status: string) => {
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    accepted: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    expired: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
  }
  return classes[status] || classes.pending
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    pending: 'Pending Response',
    accepted: 'Accepted',
    rejected: 'Rejected',
    expired: 'Expired'
  }
  return texts[status] || 'Unknown'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('tr-TR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (time: string) => {
  return time.slice(0, 5)
}

const formatBudget = (budget: number) => {
  return new Intl.NumberFormat('tr-TR').format(budget)
}

const formatMessageTime = (date: string) => {
  const messageDate = new Date(date)
  const now = new Date()
  const diffMinutes = Math.floor((now.getTime() - messageDate.getTime()) / (1000 * 60))

  if (diffMinutes < 1) return 'now'
  if (diffMinutes < 60) return `${diffMinutes}m`
  if (diffMinutes < 1440) return `${Math.floor(diffMinutes / 60)}h`
  if (diffMinutes < 10080) return `${Math.floor(diffMinutes / 1440)}d`
  return messageDate.toLocaleDateString('tr-TR')
}

const acceptOffer = () => {
  if (confirm('Are you sure you want to accept this offer?')) {
    isProcessing.value = true
    const form = useForm({})
    form.post(route('dj-offers.accept', props.offer.id))
  }
}

const rejectOffer = () => {
  if (confirm('Are you sure you want to reject this offer?')) {
    isProcessing.value = true
    const form = useForm({})
    form.post(route('dj-offers.reject', props.offer.id))
  }
}

const cancelOffer = () => {
  if (confirm('Are you sure you want to cancel this offer?')) {
    isProcessing.value = true
    const form = useForm({})
    form.post(route('dj-offers.cancel', props.offer.id))
  }
}
</script>
