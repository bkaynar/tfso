<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, HomeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    categories: Object
});

const categories = computed(() => props.categories?.data ?? []);

const categoryToDelete = ref<any>(null)
const showDeleteModal = computed(() => categoryToDelete.value !== null)

const deleteCategory = (category: any) => {
    if (confirm('Silmek istediğinize emin misiniz?')) {
        router.delete(route('categories.destroy', category.id));
    }
}

const askDeleteCategory = (category: any) => {
    categoryToDelete.value = category
}

const confirmDeleteCategory = () => {
    if (categoryToDelete.value) {
        router.delete(route('categories.destroy', categoryToDelete.value.id))
        categoryToDelete.value = null
    }
}

const cancelDeleteCategory = () => {
    categoryToDelete.value = null
}
</script>

<template>

    <Head title="Categories" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Odalar', href: '/admin/rooms' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 rounded-lg">
                            <HomeIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Categories</h1>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">

                        <Link :href="route('categories.create')"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        New Category
                        </Link>
                    </div>
                </div>
            </div>
            <!-- Rooms Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Image
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Category Name
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="category in categories" :key="category.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div v-if="category.image_url"
                                                class="h-10 w-10 rounded-full overflow-hidden">
                                                <img :src="category.image_url" :alt="category.name"
                                                    class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else
                                                class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                                                <HomeIcon class="w-5 h-5 text-white" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ category.name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link :href="route('categories.edit', category.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                                            title="Görüntüle">
                                        <EyeIcon class="w-4 h-4" />
                                        </Link>
                                        <Link :href="route('categories.edit', category.id)"
                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 p-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                                            title="Düzenle">
                                        <PencilIcon class="w-4 h-4" />
                                        </Link>
                                        <button @click="askDeleteCategory(category)"
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
                <div v-if="props.categories && props.categories.total > props.categories.per_page"
                    class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="props.categories && props.categories.prev_page_url"
                                :href="props.categories.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Önceki
                            </Link>
                            <Link v-if="props.categories && props.categories.next_page_url"
                                :href="props.categories.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Sonraki
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">{{ props.categories && props.categories.from }}</span>
                                    -
                                    <span class="font-medium">{{ props.categories && props.categories.to }}</span>
                                    arası gösteriliyor,
                                    <span class="font-medium">{{ props.categories && props.categories.total }}</span>
                                    toplam sonuç
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <template v-for="(link, index) in props.categories && props.categories.links"
                                        :key="index">
                                        <Link v-if="link.url" :href="link.url" :class="[
                                            link.active
                                                ? 'z-10 bg-purple-50 dark:bg-purple-900 border-purple-500 text-purple-600 dark:text-purple-400'
                                                : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.categories && props.categories.links ? props.categories.links.length - 1 : -1) ? 'rounded-r-md' : '',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]">
                                        <span v-if="index === 0">« Önceki</span>
                                        <span
                                            v-else-if="index === (props.categories && props.categories.links ? props.categories.links.length - 1 : -1)">Sonraki
                                            »</span>
                                        <span v-else>{{ link.label }}</span>
                                        </Link>
                                        <span v-else :class="[
                                            'relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-not-allowed',
                                            index === 0 ? 'rounded-l-md' : '',
                                            index === (props.categories && props.categories.links ? props.categories.links.length - 1 : -1) ? 'rounded-r-md' : ''
                                        ]">
                                            <span v-if="index === 0">« Önceki</span>
                                            <span
                                                v-else-if="index === (props.categories && props.categories.links ? props.categories.links.length - 1 : -1)">Sonraki
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
                <p class="mb-6 text-gray-700 dark:text-gray-300">Bu kategoriyi silmek istediğinize emin misiniz?</p>
                <div class="flex justify-end space-x-3">
                    <button @click="cancelDeleteCategory"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Vazgeç</button>
                    <button @click="confirmDeleteCategory"
                        class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">Evet, Sil</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
