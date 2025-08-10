<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, FolderOpen, Music, Radio, Users, UserCheck, MessageSquare } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

const page = usePage();

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

// Main navigation items - filtered by role
const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ];



    // Only admin and dj can see content management
    if (hasAnyRole(['admin', 'dj'])) {
        items.push(
            {
                title: 'Categories',
                href: '/categories',
                icon: FolderOpen,
            },
            {
                title: 'Sets',
                href: '/sets',
                icon: Music,
            },
            {
                title: 'Tracks',
                href: '/tracks',
                icon: Radio,
            }
        );
    }
    if (hasAnyRole(['dj'])) {
        items.push(
            {
                title: 'Profile',
                href: '/profile/edit',
                icon: Users,
            },
        );
    }
    if (hasAnyRole(['admin', 'dj', 'placeManager'])) {
        items.push(
            {
                title: 'Events',
                href: '/events',
                icon: BookOpen,
            },
        );
    }

    // DJ Offers - for PlaceManagers and DJs
    if (hasAnyRole(['placeManager'])) {
        items.push(
            {
                title: 'Find DJs',
                href: '/dj-offers/dj-list',
                icon: MessageSquare,
            },
            {
                title: 'My Offers',
                href: '/dj-offers',
                icon: MessageSquare,
                // unreadCount bir ref; .value kullanarak reactivity'i computed içinde tetikliyoruz
                badge: unreadCount.value,
            },
        );
    }

    if (hasAnyRole(['dj'])) {
        items.push(
            {
                title: 'DJ Offers',
                href: '/dj-offers',
                icon: MessageSquare,
                badge: unreadCount.value,
            },
        );
    }

    return items;
});

// User management items - only admin can see
const userNavItems = computed((): NavItem[] => {
    if (!hasRole('admin')) {
        return [];
    }

    return [
        {
            title: 'Users',
            href: '/users',
            icon: Users,
        },
        {
            title: 'DJ Applications',
            href: '/admin/dj-applications',
            icon: UserCheck,
        },
        {
            title: 'Venues',
            href: '/places',
            icon: Folder,
        },
    ];
});

// Footer navigation items
const footerNavItems = computed((): NavItem[] => {
    return [];
});

const unreadCount = ref(0);

const fetchUnreadCount = async () => {
    try {
        const response = await axios.get('/api/offers/unread-count');
        unreadCount.value = response.data.unread_count;
    } catch (error) {
        // Sessiz geç, console'a sadece dev ortamında yaz.
        if (import.meta.env.DEV) console.error('Failed to fetch unread count:', error);
    }
};

let unreadInterval: number | undefined;

onMounted(async () => {
    await fetchUnreadCount();
    // Mesaj okunma / gönderme olaylarında tekrar çek
    const handler = () => fetchUnreadCount();
    window.addEventListener('offer-unread-updated', handler);
    // Periyodik (opsiyonel) 30 sn'de bir güncelle
    unreadInterval = window.setInterval(fetchUnreadCount, 30000);
    // Temizlik için ref'e koy
    (window as any).__offerUnreadHandler = handler;
});

onBeforeUnmount(() => {
    if ((window as any).__offerUnreadHandler) {
        window.removeEventListener('offer-unread-updated', (window as any).__offerUnreadHandler);
        delete (window as any).__offerUnreadHandler;
    }
    if (unreadInterval) clearInterval(unreadInterval);
});

// Artık ayrı bir computed'a gerek yok; doğrudan unreadCount.value kullanıyoruz
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" :user-items="userNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
