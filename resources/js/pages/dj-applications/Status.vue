<template>
  <Head title="DJ Application Status" />
  <AppLayout :breadcrumbs="[
    { title: 'DJ Application Status', href: '' }
  ]">
    <div class="max-w-2xl mx-auto py-10">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
          <span class="inline-block bg-purple-500 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
          DJ Application Status
        </h1>

        <!-- Pending Status -->
        <div v-if="application.status === 'pending'" class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                Application Under Review
              </h3>
              <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                <p>Your DJ application is currently being reviewed by our admin team. You will be notified once a decision has been made.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Approved Status -->
        <div v-else-if="application.status === 'approved'" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                Application Approved!
              </h3>
              <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                <p>Congratulations! Your DJ application has been approved. You now have access to the DJ features.</p>
                <div class="mt-3">
                  <Link :href="route('dashboard')" 
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Go to Dashboard
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Rejected Status -->
        <div v-else-if="application.status === 'rejected'" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                Application Rejected
              </h3>
              <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                <p>Unfortunately, your DJ application was not approved at this time.</p>
                <div v-if="application.admin_comment" class="mt-2">
                  <p class="font-medium">Admin Comment:</p>
                  <p class="italic">{{ application.admin_comment }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Application Details -->
        <div class="space-y-6">
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Application Details</h2>
            
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ application.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ application.email }}</dd>
              </div>
              <div v-if="application.phone">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ application.phone }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="mt-1">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    application.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '',
                    application.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '',
                    application.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ''
                  ]">
                    {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                  </span>
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ new Date(application.created_at).toLocaleDateString('tr-TR', {
                    year: 'numeric',
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </dd>
              </div>
              <div v-if="application.approved_at">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Processed</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ new Date(application.approved_at).toLocaleDateString('tr-TR', {
                    year: 'numeric',
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </dd>
              </div>
            </dl>
          </div>

          <!-- Intention Letter -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Your Application Letter</h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ application.intention_letter }}</p>
            </div>
          </div>

          <!-- Admin Comment -->
          <div v-if="application.admin_comment" class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Admin Comment</h3>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
              <p class="text-sm text-blue-900 dark:text-blue-200">{{ application.admin_comment }}</p>
              <div v-if="application.approved_by" class="mt-2 text-xs text-blue-700 dark:text-blue-300">
                - {{ application.approved_by.name }}
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
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  application: Object
})
</script>