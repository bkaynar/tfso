<template>
    <Head title="Edit Event" />
    <AppLayout :breadcrumbs="[
        { title: 'Admin', href: '/admin/dashboard' },
        { title: 'Events', href: '/events' },
        { title: 'Edit Event', href: '' }
    ]">
        <div class="max-w-4xl mx-auto py-10">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center justify-center text-center">
                    <span class="inline-block bg-blue-500 p-2 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                    Edit Event
                </h1>
                <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Title</label>
                            <input v-model="form.title" id="title" type="text" required class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>
                    <div v-if="!isDJOnly">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">DJ *</label>
                        <select v-model="form.user_id" id="user_id" required class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select DJ</option>
                            <option v-for="user in djUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>
                    <input v-if="isDJOnly" type="hidden" v-model="form.user_id" />
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea v-model="form.description" id="description" rows="3" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cover_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-center">Cover Image</label>
                            <label for="cover_image" class="flex flex-col items-center justify-center border-2 border-dashed border-blue-400 dark:border-blue-600 rounded-lg p-6 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                <template v-if="coverImagePreview">
                                    <img :src="coverImagePreview" alt="Preview" class="w-32 h-32 object-cover rounded mb-2" />
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Click to change cover image</span>
                                </template>
                                <template v-else-if="event.cover_image">
                                    <img :src="event.cover_image" alt="Current cover" class="w-32 h-32 object-cover rounded mb-2" />
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Click to change cover image</span>
                                </template>
                                <template v-else>
                                    <svg class="w-12 h-12 text-blue-400 dark:text-blue-600 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Click to select cover image</span>
                                </template>
                                <input id="cover_image" type="file" accept="image/*" @change="handleCoverImageChange" class="hidden" />
                            </label>
                        </div>
                        <div>
                            <label for="event_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Date</label>
                            <input v-model="form.event_date" id="event_date" type="date" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="event_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Time</label>
                            <input v-model="form.event_time" id="event_time" type="time" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                            <input v-model="form.location" id="location" type="text" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>
                    </div>
                    <div>
                        <label for="ticket_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ticket URL</label>
                        <input v-model="form.ticket_url" id="ticket_url" type="url" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="flex items-center">
                        <input v-model="form.is_premium" id="is_premium" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_premium" class="ml-2 block text-sm text-gray-900 dark:text-white">Premium Event</label>
                    </div>
                    <div v-if="form.is_premium">
                        <label for="iap_product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">IAP Product ID</label>
                        <input v-model="form.iap_product_id" id="iap_product_id" type="text" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="flex items-center justify-center space-x-3">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Update Event
                        </button>
                        <Link :href="route('events.index')" class="inline-flex items-center px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Back
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

interface EventData {
    id: number;
    title: string;
    description?: string;
    user_id: number;
    cover_image?: string;
    event_date?: string;
    event_time?: string;
    location?: string;
    ticket_url?: string;
    is_premium: boolean;
    iap_product_id?: string;
}

const props = defineProps<{
    event: EventData;
    categories: any[];
    users: any[]
}>()

const page = usePage()

const hasRole = (role: string) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && userRoles.includes(role);
};

const isDJOnly = computed(() => {
    return hasRole('dj') && !hasRole('admin');
});

const djUsers = computed(() => {
    return props.users.filter(user => {
        // Spatie/Permission paketinde roles bir array olarak gelebilir
        if (user.roles && Array.isArray(user.roles)) {
            return user.roles.some((role: any) => role.name === 'dj' || role === 'dj');
        }
        // Eğer roles bir string array ise
        if (user.roles && user.roles.includes) {
            return user.roles.includes('dj');
        }
        return false;
    });
});

const form = ref({
    title: props.event.title || '',
    description: props.event.description || '',
    user_id: props.event.user_id?.toString() || '',
    cover_image: null as File | null,
    event_date: props.event.event_date || '',
    event_time: props.event.event_time || '',
    location: props.event.location || '',
    ticket_url: props.event.ticket_url || '',
    is_premium: props.event.is_premium || false,
    iap_product_id: props.event.iap_product_id || ''
})

onMounted(() => {
    // Debug bilgileri
    console.log('Event data:', props.event);
    console.log('DJ Users computed:', djUsers.value);
    console.log('isDJOnly:', isDJOnly.value);
});

const coverImagePreview = ref<string | null>(null)

const handleCoverImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        form.value.cover_image = target.files[0]
        coverImagePreview.value = URL.createObjectURL(target.files[0])
    } else {
        form.value.cover_image = null
        coverImagePreview.value = null
    }
}

const submit = async () => {
    const data = new FormData()
    data.append('_method', 'PUT') // Laravel için method spoofing
    data.append('title', form.value.title)
    data.append('description', form.value.description)
    data.append('user_id', form.value.user_id)
    data.append('event_date', form.value.event_date)
    data.append('event_time', form.value.event_time)
    data.append('location', form.value.location)
    data.append('ticket_url', form.value.ticket_url)
    data.append('is_premium', form.value.is_premium ? '1' : '0')
    if (form.value.iap_product_id) {
        data.append('iap_product_id', form.value.iap_product_id)
    }
    if (form.value.cover_image) {
        data.append('cover_image', form.value.cover_image)
    }

    await router.post(route('events.update', props.event.id), data)
}
</script>
