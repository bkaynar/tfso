<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const djApplication = computed(() => page.props.djApplication);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Helper function to check if user has any of the specified roles
const hasAnyRole = (roles: string[]) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && roles.some(role => userRoles.includes(role));
};

// Helper function to check if user has specific role
const hasRole = (role: string) => {
    const userRoles = page.props.auth.roles as string[];
    return userRoles && userRoles.includes(role);
};

const userRole = computed(() => {
    const userRoles = page.props.auth.roles as string[];
    if (!userRoles || userRoles.length === 0) return 'user';
    if (userRoles.includes('admin')) return 'admin';
    if (userRoles.includes('dj')) return 'dj';
    if (userRoles.includes('placeManager')) return 'placeManager';
    return 'user';
});

const welcomeMessage = computed(() => {
    const userName = page.props.auth.user?.name || 'Kullanıcı';
    const role = userRole.value;

    switch (role) {
        case 'admin':
            return `Welcome ${userName}! You have full access to the admin panel.`;
        case 'dj':
            return `Welcome ${userName}! As a DJ, you can manage your content.`;
        case 'placeManager':
            return `Welcome ${userName}! As a venue manager, you can manage your venue and events.`;
        default:
            return `Welcome ${userName}! You can manage your profile and view content.`;
    }
});
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Welcome Message -->
            <div
                class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <h2 class="text-2xl font-bold text-sidebar-foreground">{{ welcomeMessage }}</h2>
                <p class="mt-2 text-sidebar-foreground/70">
                    Rol: <span
                        class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">{{
                        userRole.toUpperCase() }}</span>
                </p>
            </div>

            <!-- DJ Application Status Widget -->
            <div v-if="djApplication"
                class="rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-sidebar-foreground flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        DJ Application Status
                    </h3>
                    <Link :href="route('dj.application.status')"
                        class="text-sm text-primary hover:text-primary/80 font-medium">
                        View Details →
                    </Link>
                </div>

                <!-- Status Badge and Message -->
                <div v-if="djApplication.status === 'pending'" class="flex items-start space-x-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-yellow-800 dark:text-yellow-200">Under Review</h4>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Your DJ application is being reviewed by our admin team. You'll be notified once approved.
                        </p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">
                            Submitted {{ new Date(djApplication.created_at).toLocaleDateString('tr-TR') }}
                        </p>
                    </div>
                </div>

                <div v-else-if="djApplication.status === 'rejected'" class="flex items-start space-x-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-medium text-red-800 dark:text-red-200">Application Rejected</h4>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            Unfortunately, your DJ application was not approved. Check the details page for admin comments.
                        </p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-2">
                            Processed {{ new Date(djApplication.approved_at).toLocaleDateString('tr-TR') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Role-specific Quick Stats -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Admin sees all stats -->
                <div v-if="hasRole('admin')"
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <h3 class="text-lg font-semibold text-sidebar-foreground">Tüm Kullanıcılar</h3>
                    <p class="text-2xl font-bold text-primary">{{ page.props.auth.user ? '1+' : '0' }}</p>
                    <p class="text-sm text-sidebar-foreground/70">Sistemdeki toplam kullanıcı sayısı</p>
                </div>

                <!-- DJ and Admin see content stats -->
                <div v-if="hasAnyRole(['admin', 'dj'])"
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <h3 class="text-lg font-semibold text-sidebar-foreground">{{ hasRole('admin') ? 'Toplam' : 'Benim'
                        }} Sets</h3>
                    <p class="text-2xl font-bold text-primary">0</p>
                    <p class="text-sm text-sidebar-foreground/70">{{ hasRole('admin') ? 'Sistemdeki toplam set sayısı' :
                        'Oluşturduğunuz set sayısı' }}</p>
                </div>

                <div v-if="hasAnyRole(['admin', 'dj'])"
                    class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                    <h3 class="text-lg font-semibold text-sidebar-foreground">{{ hasRole('admin') ? 'Toplam' : 'Benim'
                        }} Tracks</h3>
                    <p class="text-2xl font-bold text-primary">0</p>
                    <p class="text-sm text-sidebar-foreground/70">{{ hasRole('admin') ? 'Sistemdeki toplam track sayısı'
                        : 'Oluşturduğunuz track sayısı' }}</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="relative min-h-[200px] flex-1 rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-sidebar">
                <h3 class="text-lg font-semibold text-sidebar-foreground mb-4">Hızlı İşlemler</h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Admin Actions -->
                    <div v-if="hasRole('admin')" class="space-y-2">
                        <h4 class="font-medium text-sidebar-foreground">Yönetim</h4>
                        <div class="space-y-1">
                            <a href="/users"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                👥 Kullanıcı Yönetimi
                            </a>
                        </div>
                    </div>

                    <!-- DJ and Admin Actions -->
                    <div v-if="hasAnyRole(['admin', 'dj'])" class="space-y-2">
                        <h4 class="font-medium text-sidebar-foreground">İçerik Yönetimi</h4>
                        <div class="space-y-1">
                            <a href="/categories"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                📁 Kategoriler
                            </a>
                            <a href="/sets"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                🎵 Sets
                            </a>
                            <a href="/tracks"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                🎧 Tracks
                            </a>
                        </div>
                    </div>

                    <!-- PlaceManager Actions -->
                    <div v-if="hasRole('placeManager')" class="space-y-2">
                        <h4 class="font-medium text-sidebar-foreground">Mekan Yönetimi</h4>
                        <div class="space-y-1">
                            <a href="/placemanager/place/edit"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                🏢 Mekanımı Düzenle
                            </a>
                            <a href="/events"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                🎉 Etkinliklerim
                            </a>
                        </div>
                    </div>

                    <!-- All Users -->
                    <div class="space-y-2">
                        <h4 class="font-medium text-sidebar-foreground">Genel</h4>
                        <div class="space-y-1">
                            <a href="/settings/profile"
                                class="block rounded-lg bg-primary/10 p-3 text-sm text-primary hover:bg-primary/20 transition-colors">
                                👤 Profil Ayarları
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
