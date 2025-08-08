<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
    userItems?: NavItem[];
}>();

const page = usePage();
</script>

<template>
    <!-- Main Navigation Group -->
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>TFSO Mobile</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                    <Link :href="item.href">
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>

    <!-- User Management Group -->
    <SidebarGroup v-if="userItems && userItems.length > 0" class="px-2 py-0">
        <SidebarGroupLabel>User Management</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in userItems" :key="item.title">
                <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                    <Link :href="item.href" class="flex items-center justify-between w-full">
                        <div class="flex items-center">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </div>
                        <span v-if="item.title === 'DJ Applications' && page.props.pendingDjApplicationsCount > 0" 
                            class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full ml-2">
                            {{ page.props.pendingDjApplicationsCount }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
