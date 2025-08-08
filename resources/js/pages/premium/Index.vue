<template>
  <Head title="Premium Upgrade" />
  <AppLayout :breadcrumbs="[
    { title: 'Premium', href: '' }
  ]">
    <div class="max-w-4xl mx-auto py-10">
      <!-- Current Premium Status -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-4">
            <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ place.name }}</h1>
              <p class="text-sm text-gray-600 dark:text-gray-400">Premium Status Management</p>
            </div>
          </div>
          
          <!-- Status Badge -->
          <div class="flex items-center space-x-3">
            <span v-if="place.is_premium" 
              class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-purple-500 to-pink-500 text-white">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
              Premium Active
            </span>
            <span v-else
              class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
              Regular Plan
            </span>
          </div>
        </div>

        <!-- Premium Info -->
        <div v-if="place.is_premium" class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
              {{ place.days_until_expiry !== null ? Math.max(0, place.days_until_expiry) : '∞' }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
              {{ place.days_until_expiry !== null ? 'Days Remaining' : 'Unlimited' }}
            </div>
          </div>
          
          <div class="text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
              {{ place.premium_expires_at ? new Date(place.premium_expires_at).toLocaleDateString('tr-TR') : 'Never' }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Expires On</div>
          </div>
          
          <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
              {{ place.premium_trial_used ? 'Used' : 'Available' }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Free Trial</div>
          </div>
        </div>

        <!-- Expired Warning -->
        <div v-if="place.is_expired" class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.96-.833-2.73 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
              <h3 class="font-medium text-red-800 dark:text-red-200">Premium Expired</h3>
              <p class="text-sm text-red-600 dark:text-red-300">Your premium subscription has expired. Renew now to continue enjoying premium features.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Free Trial CTA -->
      <div v-if="!place.is_premium && place.is_eligible_for_trial" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg p-8 mb-8">
        <div class="text-center">
          <h2 class="text-2xl font-bold mb-2">🎉 Start Your Free Trial!</h2>
          <p class="text-purple-100 mb-6">Get 2 months of premium features absolutely free. No credit card required!</p>
          
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center">
              <div class="text-lg font-semibold">Priority Listing</div>
              <div class="text-sm text-purple-200">Top search results</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-semibold">Featured Placement</div>
              <div class="text-sm text-purple-200">Homepage spotlight</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-semibold">Analytics</div>
              <div class="text-sm text-purple-200">Advanced insights</div>
            </div>
            <div class="text-center">
              <div class="text-lg font-semibold">Support</div>
              <div class="text-sm text-purple-200">Priority assistance</div>
            </div>
          </div>
          
          <form @submit.prevent="startFreeTrial" class="inline-block">
            <button type="submit" :disabled="trialLoading"
              class="inline-flex items-center px-8 py-3 bg-white text-purple-600 font-bold rounded-lg hover:bg-gray-100 transition-colors duration-200 disabled:opacity-50">
              <svg v-if="trialLoading" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ trialLoading ? 'Starting Trial...' : 'Start Free Trial' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Pricing Plans -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="plan in pricing" :key="plan.id" 
          :class="[
            'bg-white dark:bg-gray-800 shadow rounded-lg p-8 border-2 transition-all duration-200',
            plan.id === '6months' ? 'border-purple-500 ring-2 ring-purple-200 dark:ring-purple-800 transform scale-105' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300'
          ]">
          
          <!-- Popular Badge -->
          <div v-if="plan.id === '6months'" class="text-center mb-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
              Most Popular
            </span>
          </div>
          
          <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ plan.name }}</h3>
            <div class="flex items-center justify-center space-x-2 mb-2">
              <span class="text-3xl font-bold text-gray-900 dark:text-white">₺{{ plan.price }}</span>
              <span class="text-sm text-gray-500 dark:text-gray-400">/ {{ plan.duration }} month{{ plan.duration > 1 ? 's' : '' }}</span>
            </div>
            <div v-if="plan.discount" class="text-sm font-medium text-green-600 dark:text-green-400">
              Save {{ plan.discount }}%
            </div>
          </div>
          
          <ul class="space-y-3 mb-8">
            <li v-for="feature in plan.features" :key="feature" class="flex items-start">
              <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <span class="text-sm text-gray-600 dark:text-gray-300">{{ feature }}</span>
            </li>
          </ul>
          
          <form @submit.prevent="purchasePlan(plan.id)" class="w-full">
            <button type="submit" :disabled="purchaseLoading"
              :class="[
                'w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 disabled:opacity-50',
                plan.id === '6months' 
                  ? 'bg-purple-600 hover:bg-purple-700 text-white' 
                  : 'bg-gray-100 hover:bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600'
              ]">
              {{ purchaseLoading ? 'Processing...' : (place.is_premium ? 'Extend Premium' : 'Upgrade Now') }}
            </button>
          </form>
        </div>
      </div>

      <!-- Features Comparison -->
      <div class="mt-12 bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-8">What's Included in Premium</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="bg-purple-100 dark:bg-purple-900/20 p-4 rounded-lg mb-4">
              <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Priority Ranking</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Appear at the top of search results and category listings</p>
          </div>
          
          <div class="text-center">
            <div class="bg-pink-100 dark:bg-pink-900/20 p-4 rounded-lg mb-4">
              <svg class="w-8 h-8 text-pink-600 dark:text-pink-400 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Featured Badge</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Special premium badge and featured placement on homepage</p>
          </div>
          
          <div class="text-center">
            <div class="bg-blue-100 dark:bg-blue-900/20 p-4 rounded-lg mb-4">
              <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Advanced Analytics</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Detailed insights into views, bookings, and customer behavior</p>
          </div>
          
          <div class="text-center">
            <div class="bg-green-100 dark:bg-green-900/20 p-4 rounded-lg mb-4">
              <svg class="w-8 h-8 text-green-600 dark:text-green-400 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25A9.75 9.75 0 002.25 12c0 5.385 4.365 9.75 9.75 9.75s9.75-4.365 9.75-9.75A9.75 9.75 0 0012 2.25z" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Priority Support</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">24/7 priority customer support and dedicated account management</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  place: Object,
  pricing: Array
})

const trialLoading = ref(false)
const purchaseLoading = ref(false)

const startFreeTrial = () => {
  trialLoading.value = true
  router.post(route('premium.trial'), {}, {
    onFinish: () => {
      trialLoading.value = false
    }
  })
}

const purchasePlan = (planId: string) => {
  purchaseLoading.value = true
  router.post(route('premium.purchase'), { plan: planId }, {
    onFinish: () => {
      purchaseLoading.value = false
    }
  })
}
</script>