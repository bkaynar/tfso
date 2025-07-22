<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, CalendarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    events: Object
});

const page = usePage()
const events = computed(() => props.events?.data ?? [])

const hasAnyRole = (roles: string[]) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && roles.some(role => userRoles.includes(role));
};

const hasRole = (role: string) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && userRoles.includes(role);
};

const canEditEvent = (event: any) => {
    if (hasRole('admin')) return true;
    if (hasRole('dj') && event.user_id === page.props.auth.user?.id) return true;
    return false;
};

const eventToDelete = ref<any>(null)
const showDeleteModal = computed(() => eventToDelete.value !== null)

const askDeleteEvent = (event: any) => {
    if (!canEditEvent(event)) return;
    eventToDelete.value = event
}

const confirmDeleteEvent = () => {
    if (eventToDelete.value) {
        router.delete(route('events.destroy', eventToDelete.value.id))
        eventToDelete.value = null
    }
}

const cancelDeleteEvent = () => {
    eventToDelete.value = null
}
</script>

<template>
    <Head title="Events" />
    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Events', href: '/events' }
    ]">
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <CalendarIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Events</h1>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <Link :href="route('events.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        New Event
                        </Link>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cover</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">DJ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Premium</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="event in events" :key="event.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <img v-if="event.cover_image" :src="event.cover_image" :alt="event.name" class="h-12 w-12 rounded-lg object-cover" />
                                            <div v-else class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                <CalendarIcon class="w-6 h-6 text-white" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ event.name }}</span>
                                        <span v-if="event.description" class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ event.description }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ event.user?.name || 'Unknown' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="event.is_premium ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">{{ event.is_premium ? 'Premium' : 'Free' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link :href="route('events.show', event.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200" title="View">
                                            <EyeIcon class="w-4 h-4" />
                                        </Link>
                                        <Link v-if="canEditEvent(event)" :href="route('events.edit', event.id)" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200" title="Edit">
                                            <PencilIcon class="w-4 h-4" />
                                        </Link>
                                        <button v-if="canEditEvent(event)" @click="askDeleteEvent(event)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200" title="Delete">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="props.events && props.events.total > props.events.per_page" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.events && props.events.prev_page_url" :href="props.events.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Previous</Link>
                            <Link v-if="props.events && props.events.next_page_url" :href="props.events.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Next</Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ props.events && props.events.from }}</span>
                                    to
                                    <span class="font-medium">{{ props.events && props.events.to }}</span>
                                    of
                                    <span class="font-medium">{{ props.events && props.events.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <template v-for="(link, index) in props.events && props.events.links" :key="index">
                                        <Link v-if="link.url" :href="link.url" :class="[
                                            link.active
                                                ? 'z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 text-blue-600 dark:text-blue-400'
                                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.events && props.events.links ? props.events.links.length - 1 : -1) ? 'rounded-r-md' : '',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]">
                                        <span v-if="index === 0">« Previous</span>
                                        <span v-else-if="index === (props.events && props.events.links ? props.events.links.length - 1 : -1)">Next »</span>
                                        <span v-else>{{ link.label }}</span>
                                        </Link>
                                        <span v-else :class="[
                                            'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.events && props.events.links ? props.events.links.length - 1 : -1) ? 'rounded-r-md' : ''
                                        ]">
                                            <span v-if="index === 0">« Previous</span>
                                            <span v-else-if="index === (props.events && props.events.links ? props.events.links.length - 1 : -1)">Next »</span>
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
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-sm w-full">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Delete Confirmation</h2>
                <p class="mb-6 text-gray-700 dark:text-gray-300">Are you sure you want to delete this event?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="cancelDeleteEvent" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</button>
                    <button @click="confirmDeleteEvent" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Yes, Delete</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* You can add custom styles here if needed */
</style>
