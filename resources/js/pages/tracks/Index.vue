<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { Radio } from 'lucide-vue-next'

const props = defineProps({
    tracks: Object
});

const page = usePage()
const tracks = computed(() => props.tracks?.data ?? []);

// Helper function to check if user has specific role
const hasRole = (role: string) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && userRoles.includes(role);
};

// Check if user can edit/delete a track
const canEditTrack = (track: any) => {
    if (hasRole('admin')) return true;
    if (hasRole('dj') && track.user_id === page.props.auth.user?.id) return true;
    return false;
};

const trackToDelete = ref<any>(null)
const showDeleteModal = computed(() => trackToDelete.value !== null)

const askDeleteTrack = (track: any) => {
    if (!canEditTrack(track)) return;
    trackToDelete.value = track
}

const confirmDeleteTrack = () => {
    if (trackToDelete.value) {
        router.delete(route('tracks.destroy', trackToDelete.value.id))
        trackToDelete.value = null
    }
}

const cancelDeleteTrack = () => {
    trackToDelete.value = null
}
</script>

<template>

    <Head title="Tracks" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Tracks', href: '/tracks' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <Radio class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tracks</h1>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <Link :href="route('tracks.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        New Track
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tracks Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Track Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Artist
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Category
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Premium
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="track in tracks" :key="track.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                <Radio class="w-5 h-5 text-white" />
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ track.name }}
                                            </div>
                                            <div v-if="track.description"
                                                class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                {{ track.description }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ track.user?.name || 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ track.category?.name || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="track.is_premium ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ track.is_premium ? 'Premium' : 'Free' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- View is always available -->
                                        <Link :href="route('tracks.edit', track.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                            title="View">
                                        <EyeIcon class="w-4 h-4" />
                                        </Link>

                                        <!-- Edit only if user can edit -->
                                        <Link v-if="canEditTrack(track)" :href="route('tracks.edit', track.id)"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                                            title="Edit">
                                        <PencilIcon class="w-4 h-4" />
                                        </Link>

                                        <!-- Delete only if user can edit -->
                                        <button v-if="canEditTrack(track)" @click="askDeleteTrack(track)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                                            title="Delete">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="props.tracks && props.tracks.total > props.tracks.per_page"
                    class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.tracks && props.tracks.prev_page_url" :href="props.tracks.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Previous
                            </Link>
                            <Link v-if="props.tracks && props.tracks.next_page_url" :href="props.tracks.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ props.tracks && props.tracks.from }}</span>
                                    to
                                    <span class="font-medium">{{ props.tracks && props.tracks.to }}</span>
                                    of
                                    <span class="font-medium">{{ props.tracks && props.tracks.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <template v-for="(link, index) in props.tracks && props.tracks.links" :key="index">
                                        <Link v-if="link.url" :href="link.url" :class="[
                                            link.active
                                                ? 'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 text-blue-600 dark:text-blue-400'
                                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.tracks && props.tracks.links ? props.tracks.links.length - 1 : -1) ? 'rounded-r-md' : '',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]">
                                        <span v-if="index === 0">« Previous</span>
                                        <span
                                            v-else-if="index === (props.tracks && props.tracks.links ? props.tracks.links.length - 1 : -1)">Next
                                            »</span>
                                        <span v-else>{{ link.label }}</span>
                                        </Link>
                                        <span v-else :class="[
                                            'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.tracks && props.tracks.links ? props.tracks.links.length - 1 : -1) ? 'rounded-r-md' : ''
                                        ]">
                                            <span v-if="index === 0">« Previous</span>
                                            <span
                                                v-else-if="index === (props.tracks && props.tracks.links ? props.tracks.links.length - 1 : -1)">Next
                                                »</span>
                                            <span v-else>{{ link.label }}</span>
                                        </span>
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-sm w-full">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Delete Confirmation</h2>
                <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this track?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="cancelDeleteTrack"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</button>
                    <button @click="confirmDeleteTrack"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Yes,
                        Delete</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
