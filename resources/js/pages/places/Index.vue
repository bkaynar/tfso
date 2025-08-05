<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, MapPinIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    places: Object
});

const page = usePage()
const places = computed(() => props.places?.data ?? []);

// Helper function to check if user has specific role
const hasRole = (role: string) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && userRoles.includes(role);
};

// Check if user is admin or placeManager
const isAdmin = computed(() => hasRole('admin'));
const isPlaceManager = computed(() => hasRole('placeManager'));
const canManage = computed(() => isAdmin.value || isPlaceManager.value);

const placeToDelete = ref<any>(null)
const showDeleteModal = computed(() => placeToDelete.value !== null)

const deletePlace = (place: any) => {
    if (confirm('Silmek istediğinize emin misiniz?')) {
        router.delete(route('places.destroy', place.id));
    }
}

const askDeletePlace = (place: any) => {
    if (!canManage.value) return;
    placeToDelete.value = place
}

const confirmDeletePlace = () => {
    if (placeToDelete.value && canManage.value) {
        router.delete(route('places.destroy', placeToDelete.value.id))
        placeToDelete.value = null
    }
}

const cancelDeletePlace = () => {
    placeToDelete.value = null
}
</script>

<template>
    <Head title="Places" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Mekanlar', href: '/places' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <MapPinIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mekanlar</h1>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <!-- Admin and PlaceManager can create new places -->
                        <Link v-if="canManage" :href="route('places.create')"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <PlusIcon class="w-4 h-4 mr-2" />
                            Yeni Mekan
                        </Link>

                        <!-- View only notice for others -->
                        <div v-else
                            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg">
                            <EyeIcon class="w-4 h-4 mr-2" />
                            Sadece Görüntüleme
                        </div>
                    </div>
                </div>
            </div>

            <!-- Places Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Resim
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Mekan Adı
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Konum
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Premium
                                </th>
                                <th v-if="canManage" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="place in places" :key="place.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div v-if="place.images && place.images.length > 0"
                                                class="h-10 w-10 rounded-full overflow-hidden">
                                                <img :src="place.images[0]" :alt="place.name"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                                                <MapPinIcon class="w-5 h-5 text-white" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ place.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ place.location }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="place.is_premium" 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Premium
                                    </span>
                                    <span v-else 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        Standard
                                    </span>
                                </td>
                                <td v-if="canManage" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- View -->
                                        <Link :href="route('places.show', place.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                            title="Görüntüle">
                                            <EyeIcon class="w-4 h-4" />
                                        </Link>

                                        <!-- Edit -->
                                        <Link :href="route('places.edit', place.id)"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                                            title="Düzenle">
                                            <PencilIcon class="w-4 h-4" />
                                        </Link>

                                        <!-- Delete -->
                                        <button @click="askDeletePlace(place)"
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
                <div v-if="props.places && props.places.total > props.places.per_page"
                    class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.places && props.places.prev_page_url"
                                :href="props.places.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Önceki
                            </Link>
                            <Link v-if="props.places && props.places.next_page_url"
                                :href="props.places.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Sonraki
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">{{ props.places && props.places.from }}</span>
                                    -
                                    <span class="font-medium">{{ props.places && props.places.to }}</span>
                                    arası gösteriliyor,
                                    <span class="font-medium">{{ props.places && props.places.total }}</span>
                                    toplam sonuç
                                </p>
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
                <p class="mb-6 text-gray-700 dark:text-gray-300">Bu mekanı silmek istediğinize emin misiniz?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="cancelDeletePlace"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Vazgeç</button>
                    <button @click="confirmDeletePlace"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Evet, Sil</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>