<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, CalendarIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    events: Object
});

const page = usePage()
const events = computed(() => props.events?.data ?? []);

// Helper function to check if user has specific role
const hasRole = (role: string) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && userRoles.includes(role);
};

// Check if user is admin or placeManager
const isAdmin = computed(() => hasRole('admin'));
const isPlaceManager = computed(() => hasRole('placeManager'));
const canManageEvents = computed(() => isAdmin.value || isPlaceManager.value);

const eventToDelete = ref<any>(null)
const showDeleteModal = computed(() => eventToDelete.value !== null)


const askDeleteEvent = (event: any) => {
    if (!canManageEvents.value) return; // Admin or placeManager can delete
    eventToDelete.value = event
}

const confirmDeleteEvent = () => {
    if (eventToDelete.value && canManageEvents.value) {
        router.delete(route('events.destroy', eventToDelete.value.id))
        eventToDelete.value = null
    }
}

const cancelDeleteEvent = () => {
    eventToDelete.value = null
}

const formatDate = (dateString: string) => {
    if (!dateString) return 'Tarih belirtilmemiş';
    return new Date(dateString).toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}
</script>

<template>

    <Head title="Events" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Etkinlikler', href: '/admin/events' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <CalendarIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Etkinlikler</h1>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <!-- Admin and placeManager can create new events -->
                        <Link v-if="canManageEvents" :href="route('events.create')"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Yeni Etkinlik
                        </Link>

                        <!-- Others see read-only notice -->
                        <div v-else
                            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg">
                            <EyeIcon class="w-4 h-4 mr-2" />
                            Sadece Görüntüleme
                        </div>
                    </div>
                </div>
            </div>
            <!-- Events Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Resim
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Başlık
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Tarih
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Lokasyon
                                </th>
                                <th v-if="canManageEvents"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="event in events" :key="event.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div v-if="event.image"
                                                class="h-10 w-10 rounded-full overflow-hidden">
                                                <img :src="`/storage/${event.image}`" :alt="event.title"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                                                <CalendarIcon class="w-5 h-5 text-white" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ event.title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" v-if="event.description">
                                                {{ event.description.length > 50 ? event.description.substring(0, 50) + '...' : event.description }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ formatDate(event.date) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ event.location || 'Belirtilmemiş' }}
                                    </div>
                                </td>

                                <td v-if="canManageEvents" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- View is available for all -->
                                        <Link :href="route('events.edit', event.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                            title="Görüntüle">
                                        <EyeIcon class="w-4 h-4" />
                                        </Link>

                                        <!-- Edit for admin and placeManager (for their own place events) -->
                                        <Link :href="route('events.edit', event.id)"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                                            title="Düzenle">
                                        <PencilIcon class="w-4 h-4" />
                                        </Link>

                                        <!-- Delete for admin and placeManager (for their own place events) -->
                                        <button @click="askDeleteEvent(event)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                                            title="Sil">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="props.events && props.events.total > props.events.per_page"
                    class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.events && props.events.prev_page_url"
                                :href="props.events.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Önceki
                            </Link>
                            <Link v-if="props.events && props.events.next_page_url"
                                :href="props.events.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Sonraki
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">{{ props.events && props.events.from }}</span>
                                    -
                                    <span class="font-medium">{{ props.events && props.events.to }}</span>
                                    arası gösteriliyor,
                                    <span class="font-medium">{{ props.events && props.events.total }}</span>
                                    toplam sonuç
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <template v-for="(link, index) in props.events && props.events.links"
                                        :key="index">
                                        <Link v-if="link.url" :href="link.url" :class="[
                                            link.active
                                                ? 'z-10 bg-purple-50 dark:bg-purple-900 border-purple-500 text-purple-600 dark:text-purple-400'
                                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.events && props.events.links ? props.events.links.length - 1 : -1) ? 'rounded-r-md' : '',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]">
                                        <span v-if="index === 0">« Önceki</span>
                                        <span
                                            v-else-if="index === (props.events && props.events.links ? props.events.links.length - 1 : -1)">Sonraki
                                            »</span>
                                        <span v-else>{{ link.label }}</span>
                                        </Link>
                                        <span v-else :class="[
                                            'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.events && props.events.links ? props.events.links.length - 1 : -1) ? 'rounded-r-md' : ''
                                        ]">
                                            <span v-if="index === 0">« Önceki</span>
                                            <span
                                                v-else-if="index === (props.events && props.events.links ? props.events.links.length - 1 : -1)">Sonraki
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
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Silme Onayı</h2>
                <p class="mb-6 text-gray-700 dark:text-gray-300">Bu etkinliği silmek istediğinize emin misiniz?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="cancelDeleteEvent"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Vazgeç</button>
                    <button @click="confirmDeleteEvent"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Evet, Sil</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>