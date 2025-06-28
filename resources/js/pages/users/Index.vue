<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { Users, Search, X } from 'lucide-vue-next'

const props = defineProps({
    users: Object,
    filters: Object
});

const users = computed(() => props.users?.data ?? []);

// Filter states
const searchQuery = ref(props.filters?.search || '')
const selectedRole = ref(props.filters?.role || '')

// Watch for changes and update URL
let debounceTimeout: ReturnType<typeof setTimeout>
watch([searchQuery, selectedRole], () => {
    clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
        const params = new URLSearchParams()

        if (searchQuery.value) {
            params.append('search', searchQuery.value)
        }

        if (selectedRole.value) {
            params.append('role', selectedRole.value)
        }

        const queryString = params.toString()
        const url = queryString ? `/users?${queryString}` : '/users'

        router.get(url, {}, {
            preserveState: true,
            preserveScroll: true
        })
    }, 300)
})

const clearFilters = () => {
    searchQuery.value = ''
    selectedRole.value = ''
}

const userToDelete = ref<any>(null)
const showDeleteModal = computed(() => userToDelete.value !== null)

const askDeleteUser = (user: any) => {
    userToDelete.value = user
}

const confirmDeleteUser = () => {
    if (userToDelete.value) {
        router.delete(route('users.destroy', userToDelete.value.id))
        userToDelete.value = null
    }
}

const cancelDeleteUser = () => {
    userToDelete.value = null
}

const getRoleBadgeColor = (role: string) => {
    switch (role) {
        case 'admin':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        case 'dj':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
    }
}
</script>

<template>

    <Head title="Users" />

    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Users', href: '/users' }
    ]">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-lg">
                            <Users class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
                            <p class="text-gray-600 dark:text-gray-400">Manage system users</p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <Link :href="route('users.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Add User
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input v-model="searchQuery" type="text" placeholder="Search users..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <select v-model="selectedRole"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="dj">DJ</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <div class="flex items-center">
                        <button @click="clearFilters"
                            class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
                            <X class="w-4 h-4 mr-2" />
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">All Users</h3>
                </div>

                <div v-if="users.length === 0" class="p-6 text-center">
                    <Users class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No users</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new user.</p>
                    <div class="mt-6">
                        <Link :href="route('users.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <PlusIcon class="w-5 h-5 mr-2" />
                        Add User
                        </Link>
                    </div>
                </div>

                <div v-else class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Created
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                                    {{ user.name?.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ user.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ user.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="role in user.roles" :key="role.id"
                                            :class="getRoleBadgeColor(role.name)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                                            {{ role.name }}
                                        </span>
                                        <span v-if="!user.roles || user.roles.length === 0"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            No Role
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('users.show', user.id)"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                            title="View">
                                        <EyeIcon class="w-4 h-4" />
                                        </Link>
                                        <Link :href="route('users.edit', user.id)"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                            title="Edit">
                                        <PencilIcon class="w-4 h-4" />
                                        </Link>
                                        <button @click="askDeleteUser(user)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            title="Delete">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="props.users?.links"
                        class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex flex-1 justify-between sm:hidden">
                            <Link v-if="props.users.prev_page_url" :href="props.users.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Previous
                            </Link>
                            <Link v-if="props.users.next_page_url" :href="props.users.next_page_url"
                                class="relative ml-3 inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Next
                            </Link>
                        </div>
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ props.users.from }}</span>
                                    to
                                    <span class="font-medium">{{ props.users.to }}</span>
                                    of
                                    <span class="font-medium">{{ props.users.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                    aria-label="Pagination">
                                    <template v-for="(link, index) in props.users.links" :key="index">
                                        <Link v-if="link.url" :href="link.url"
                                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium"
                                            :class="{
                                                'bg-blue-50 border-blue-500 text-blue-600 dark:bg-blue-900 dark:border-blue-600 dark:text-blue-300': link.active,
                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-600': !link.active
                                            }" v-html="link.label" />
                                        <span v-else
                                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default dark:bg-gray-700 dark:border-gray-600 dark:text-gray-600"
                                            v-html="link.label" />
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-[9999] overflow-y-auto" @click.self="cancelDeleteUser">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

                <!-- Modal Container -->
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <!-- Modal Content -->
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 dark:bg-gray-800"
                        @click.stop>
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <TrashIcon class="h-6 w-6 text-red-600" />
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                                    Delete User
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to delete user "{{ userToDelete?.name }}"? This action
                                        cannot be
                                        undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button @click="confirmDeleteUser" type="button"
                                class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                            <button @click="cancelDeleteUser" type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
