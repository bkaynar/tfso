<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

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
    return 'user';
});

const welcomeMessage = computed(() => {
    const userName = page.props.auth.user?.name || 'Kullanıcı';
    const role = userRole.value;

    switch (role) {
        case 'admin':
            return `Hoş geldin ${userName}! Admin paneline tam erişiminiz var.`;
        case 'dj':
            return `Hoş geldin ${userName}! DJ olarak içeriklerinizi yönetebilirsiniz.`;
        default:
            return `Hoş geldin ${userName}! Sisteme hoş geldiniz.`;
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

                    <!-- All Users -->
                    <div class="space-y-2">
                        <h4 class="font-medium text-sidebar-foreground">Genel</h4>
                        <div class="space-y-1">
                            <a href="/profile"
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
