<template>
  <Head :title="`DJ Application - ${application.name}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'DJ Applications', href: '/admin/dj-applications' },
    { title: application.name, href: '' }
  ]">
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 h-16 w-16">
              <div class="h-16 w-16 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                <span class="text-white text-xl font-bold">{{ application.name.charAt(0).toUpperCase() }}</span>
              </div>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ application.name }}</h1>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ application.email }}</p>
              <p v-if="application.phone" class="text-sm text-gray-500 dark:text-gray-500">{{ application.phone }}</p>
              
              <!-- Status Badge -->
              <div class="mt-2">
                <span :class="[
                  'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
                  application.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '',
                  application.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '',
                  application.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ''
                ]">
                  {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-4 sm:mt-0 flex space-x-3">
            <Link :href="route('admin.dj-applications.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
              ← Back to Applications
            </Link>
            
            <template v-if="application.status === 'pending'">
              <button @click="showApproveModal = true"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Approve
              </button>
              
              <button @click="showRejectModal = true"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject
              </button>
            </template>
          </div>
        </div>
      </div>

      <!-- Application Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Application Letter -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Letter</h2>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <p class="text-gray-900 dark:text-white whitespace-pre-wrap leading-relaxed">{{ application.intention_letter }}</p>
            </div>
            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
              {{ application.intention_letter.length }} characters
            </div>
          </div>

          <!-- Admin Comments -->
          <div v-if="application.admin_comment" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Admin Comment</h2>
            <div :class="[
              'rounded-lg p-4',
              application.status === 'approved' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'
            ]">
              <p :class="[
                'whitespace-pre-wrap leading-relaxed',
                application.status === 'approved' ? 'text-green-900 dark:text-green-200' : 'text-red-900 dark:text-red-200'
              ]">{{ application.admin_comment }}</p>
            </div>
            <div v-if="application.approved_by" class="mt-4 text-sm text-gray-500 dark:text-gray-400">
              By {{ application.approved_by.name }} on {{ new Date(application.approved_at).toLocaleDateString('tr-TR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
              }) }}
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Application Info -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Info</h3>
            
            <dl class="space-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Application ID</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">#{{ application.id }}</dd>
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

              <div v-if="application.approved_by">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Processed By</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ application.approved_by.name }}</dd>
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
            </dl>
          </div>

          <!-- Contact Info -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
            
            <dl class="space-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ application.name }}</dd>
              </div>
              
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  <a :href="`mailto:${application.email}`" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                    {{ application.email }}
                  </a>
                </dd>
              </div>

              <div v-if="application.phone">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  <a :href="`tel:${application.phone}`" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                    {{ application.phone }}
                  </a>
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <!-- Approve Modal -->
    <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Approve DJ Application</h2>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
          Are you sure you want to approve <strong>{{ application.name }}</strong>'s DJ application?
        </p>
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
          This will create a DJ account and grant access to the platform.
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Admin Comment (Optional)
          </label>
          <textarea v-model="approveForm.admin_comment" rows="3"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="Welcome message or additional notes..."></textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="closeApproveModal"
            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Cancel
          </button>
          <button @click="approveApplication" :disabled="approving"
            class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white">
            {{ approving ? 'Approving...' : 'Approve Application' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Reject DJ Application</h2>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
          Are you sure you want to reject <strong>{{ application.name }}</strong>'s DJ application?
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Reason for Rejection *
          </label>
          <textarea v-model="rejectForm.admin_comment" rows="4" required
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
            placeholder="Please provide a detailed reason for rejection..."></textarea>
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            This message will be visible to the applicant.
          </p>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="closeRejectModal"
            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Cancel
          </button>
          <button @click="rejectApplication" :disabled="rejecting || !rejectForm.admin_comment.trim()"
            class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white">
            {{ rejecting ? 'Rejecting...' : 'Reject Application' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  application: Object
})

const showApproveModal = ref(false)
const showRejectModal = ref(false)
const approving = ref(false)
const rejecting = ref(false)

const approveForm = ref({
  admin_comment: ''
})

const rejectForm = ref({
  admin_comment: ''
})

const closeApproveModal = () => {
  showApproveModal.value = false
  approveForm.value.admin_comment = ''
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectForm.value.admin_comment = ''
}

const approveApplication = async () => {
  approving.value = true
  
  try {
    await router.post(route('admin.dj-applications.approve', props.application.id), {
      admin_comment: approveForm.value.admin_comment
    })
    closeApproveModal()
  } catch (error) {
    console.error('Error approving application:', error)
  } finally {
    approving.value = false
  }
}

const rejectApplication = async () => {
  if (!rejectForm.value.admin_comment.trim()) return
  
  rejecting.value = true
  
  try {
    await router.post(route('admin.dj-applications.reject', props.application.id), {
      admin_comment: rejectForm.value.admin_comment
    })
    closeRejectModal()
  } catch (error) {
    console.error('Error rejecting application:', error)
  } finally {
    rejecting.value = false
  }
}
</script>