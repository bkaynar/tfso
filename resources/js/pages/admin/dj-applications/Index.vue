<template>
  <Head title="DJ Applications" />
  <AppLayout :breadcrumbs="[
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'DJ Applications', href: '/admin/dj-applications' }
  ]">
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center space-x-3">
            <div class="bg-purple-500 p-2 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">DJ Applications</h1>
              <p class="text-sm text-gray-600 dark:text-gray-400">Review and manage DJ applications</p>
            </div>
          </div>
          
          <!-- Status Filter -->
          <div class="mt-4 sm:mt-0 flex space-x-2">
            <select v-model="statusFilter" @change="filterApplications" 
              class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
              <option value="">All Applications</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Applications Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Applicant
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Applied
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Processed
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="application in applications.data" :key="application.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                
                <!-- Applicant Info -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                        <span class="text-white font-medium">{{ application.name.charAt(0).toUpperCase() }}</span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ application.name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ application.email }}
                      </div>
                      <div v-if="application.phone" class="text-xs text-gray-400 dark:text-gray-500">
                        {{ application.phone }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    application.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '',
                    application.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '',
                    application.status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ''
                  ]">
                    {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                  </span>
                </td>

                <!-- Applied Date -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ new Date(application.created_at).toLocaleDateString('tr-TR', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                  }) }}
                </td>

                <!-- Processed Date -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  <div v-if="application.approved_at">
                    {{ new Date(application.approved_at).toLocaleDateString('tr-TR', {
                      year: 'numeric',
                      month: 'short',
                      day: 'numeric'
                    }) }}
                    <div v-if="application.approved_by" class="text-xs text-gray-400">
                      by {{ application.approved_by.name }}
                    </div>
                  </div>
                  <span v-else class="text-gray-400">-</span>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <!-- View Details -->
                    <Link :href="route('admin.dj-applications.show', application.id)"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                      title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </Link>

                    <!-- Quick Actions for Pending Applications -->
                    <template v-if="application.status === 'pending'">
                      <!-- Quick Approve -->
                      <button @click="showQuickApproveModal(application)"
                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1 rounded-md hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200"
                        title="Quick Approve">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>

                      <!-- Quick Reject -->
                      <button @click="showQuickRejectModal(application)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                        title="Quick Reject">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </template>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="applications.data.length === 0">
                <td colspan="5" class="px-6 py-12 text-center">
                  <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg font-medium">No applications found</p>
                    <p class="text-sm">{{ statusFilter ? `No ${statusFilter} applications` : 'No DJ applications have been submitted yet' }}</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="applications.total > applications.per_page"
          class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link v-if="applications.prev_page_url" :href="applications.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Previous
              </Link>
              <Link v-if="applications.next_page_url" :href="applications.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  Showing <span class="font-medium">{{ applications.from }}</span> to
                  <span class="font-medium">{{ applications.to }}</span> of
                  <span class="font-medium">{{ applications.total }}</span> results
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Approve Modal -->
    <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Approve DJ Application</h2>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
          Are you sure you want to approve <strong>{{ selectedApplication?.name }}</strong>'s DJ application?
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Admin Comment (Optional)
          </label>
          <textarea v-model="approveForm.admin_comment" rows="3"
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
            placeholder="Add a comment for the applicant..."></textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="closeApproveModal"
            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Cancel
          </button>
          <button @click="approveApplication" :disabled="approving"
            class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white">
            {{ approving ? 'Approving...' : 'Approve' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Quick Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
        <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Reject DJ Application</h2>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
          Are you sure you want to reject <strong>{{ selectedApplication?.name }}</strong>'s DJ application?
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Reason for Rejection *
          </label>
          <textarea v-model="rejectForm.admin_comment" rows="3" required
            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
            placeholder="Please provide a reason for rejection..."></textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="closeRejectModal"
            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Cancel
          </button>
          <button @click="rejectApplication" :disabled="rejecting || !rejectForm.admin_comment.trim()"
            class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white">
            {{ rejecting ? 'Rejecting...' : 'Reject' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  applications: Object
})

const statusFilter = ref('')
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const selectedApplication = ref(null)
const approving = ref(false)
const rejecting = ref(false)

const approveForm = ref({
  admin_comment: ''
})

const rejectForm = ref({
  admin_comment: ''
})

const filterApplications = () => {
  const params = new URLSearchParams(window.location.search)
  if (statusFilter.value) {
    params.set('status', statusFilter.value)
  } else {
    params.delete('status')
  }
  
  const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '')
  router.get(url)
}

const showQuickApproveModal = (application: any) => {
  selectedApplication.value = application
  approveForm.value.admin_comment = ''
  showApproveModal.value = true
}

const showQuickRejectModal = (application: any) => {
  selectedApplication.value = application
  rejectForm.value.admin_comment = ''
  showRejectModal.value = true
}

const closeApproveModal = () => {
  showApproveModal.value = false
  selectedApplication.value = null
  approveForm.value.admin_comment = ''
}

const closeRejectModal = () => {
  showRejectModal.value = false
  selectedApplication.value = null
  rejectForm.value.admin_comment = ''
}

const approveApplication = async () => {
  if (!selectedApplication.value) return
  
  approving.value = true
  
  try {
    await router.post(route('admin.dj-applications.approve', selectedApplication.value.id), {
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
  if (!selectedApplication.value || !rejectForm.value.admin_comment.trim()) return
  
  rejecting.value = true
  
  try {
    await router.post(route('admin.dj-applications.reject', selectedApplication.value.id), {
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