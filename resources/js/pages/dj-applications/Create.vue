<template>
  <Head title="DJ Application" />
  <AppLayout :breadcrumbs="[
    { title: 'DJ Application', href: '' }
  ]">
    <div class="max-w-2xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
          <span class="inline-block bg-purple-500 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
          </span>
          DJ Application Form
        </h1>

        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
          <p class="text-sm text-blue-800 dark:text-blue-200">
            Complete your DJ application by providing additional information. Your application will be reviewed by our admin team.
          </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Phone Number
            </label>
            <input v-model="form.phone" id="phone" type="tel"
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="+90 555 123 4567" />
            <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ form.errors.phone }}
            </div>
          </div>

          <!-- Intention Letter -->
          <div>
            <label for="intention_letter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Why do you want to be a DJ on our platform? *
            </label>
            <textarea v-model="form.intention_letter" id="intention_letter" rows="8" required
              class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
              placeholder="Please tell us about your DJ experience, musical background, why you want to join our platform, and what you can bring to our community. (Minimum 50 characters)"
            ></textarea>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ form.intention_letter.length }}/2000 characters (minimum 50 required)
            </div>
            <div v-if="form.errors.intention_letter" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ form.errors.intention_letter }}
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex items-center space-x-3">
            <button type="submit" :disabled="form.processing"
              class="inline-flex items-center px-6 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-sm font-medium rounded-lg transition-colors duration-200">
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Submitting...' : 'Submit Application' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
  phone: '',
  intention_letter: ''
})

const submit = () => {
  form.post(route('dj.application.store'))
}
</script>