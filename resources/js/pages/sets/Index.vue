<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, MusicalNoteIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    sets: Object
});

const sets = computed(() => props.sets?.data ?? []);

const setToDelete = ref<any>(null)
const showDeleteModal = computed(() => setToDelete.value !== null)

const askDeleteSet = (set: any) => {
    setToDelete.value = set
}

const confirmDeleteSet = () => {
    if (setToDelete.value) {
        router.delete(route('sets.destroy', setToDelete.value.id))
        setToDelete.value = null
    }
}

const cancelDeleteSet = () => {
    setToDelete.value = null
}
</script>

<template>

    <Head title="Sets" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Sets', href: '/sets' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <MusicalNoteIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sets</h1>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <Link :href="route('sets.create')"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        New Set
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Sets Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Cover
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Set Name
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    DJ
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
                            <tr v-for="set in sets" :key="set.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img v-if="set.cover_image" :src="set.cover_image" :alt="set.name"
                                                class="h-12 w-12 rounded-lg object-cover" />
                                            <div v-else
                                                class="h-12 w-12 rounded-lg bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                                                <MusicalNoteIcon class="w-6 h-6 text-white" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ set.name }}
                                        </span>
                                        <span v-if="set.description"
                                            class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                            {{ set.description }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ set.user?.name || 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="set.is_premium ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ set.is_premium ? 'Premium' : 'Free' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link :href="route('sets.edit', set.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                            title="View">
                                        <EyeIcon class="w-4 h-4" />
                                        </Link>
                                        <Link :href="route('sets.edit', set.id)"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                                            title="Edit">
                                        <PencilIcon class="w-4 h-4" />
                                        </Link>
                                        <button @click="askDeleteSet(set)"
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
                <div v-if="props.sets && props.sets.total > props.sets.per_page"
                    class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.sets && props.sets.prev_page_url" :href="props.sets.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Previous
                            </Link>
                            <Link v-if="props.sets && props.sets.next_page_url" :href="props.sets.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ props.sets && props.sets.from }}</span>
                                    to
                                    <span class="font-medium">{{ props.sets && props.sets.to }}</span>
                                    of
                                    <span class="font-medium">{{ props.sets && props.sets.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <template v-for="(link, index) in props.sets && props.sets.links" :key="index">
                                        <Link v-if="link.url" :href="link.url" :class="[
                                            link.active
                                                ? 'z-10 bg-purple-50 dark:bg-purple-900 border-purple-500 text-purple-600 dark:text-purple-400'
                                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.sets && props.sets.links ? props.sets.links.length - 1 : -1) ? 'rounded-r-md' : '',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]">
                                        <span v-if="index === 0">« Previous</span>
                                        <span
                                            v-else-if="index === (props.sets && props.sets.links ? props.sets.links.length - 1 : -1)">Next
                                            »</span>
                                        <span v-else>{{ link.label }}</span>
                                        </Link>
                                        <span v-else :class="[
                                            'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.sets && props.sets.links ? props.sets.links.length - 1 : -1) ? 'rounded-r-md' : ''
                                        ]">
                                            <span v-if="index === 0">« Previous</span>
                                            <span
                                                v-else-if="index === (props.sets && props.sets.links ? props.sets.links.length - 1 : -1)">Next
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
                <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this set?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="cancelDeleteSet"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</button>
                    <button @click="confirmDeleteSet"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Yes,
                        Delete</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
