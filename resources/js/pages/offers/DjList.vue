<template>
  <Head title="Find DJs" />
  <AppLayout :breadcrumbs="[
    { title: 'Find DJs', href: '' }
  ]">
    <div class="max-w-6xl mx-auto py-10">
      <!-- Header -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="bg-purple-500 p-2 rounded-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Find DJs</h1>
              <p class="text-sm text-gray-600 dark:text-gray-400">Browse available DJs and send offers</p>
            </div>
          </div>
          
          <Link :href="route('dj-offers.index')" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            My Offers
          </Link>
        </div>
      </div>

      <!-- DJ Grid -->
      <div v-if="djs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="dj in djs" :key="dj.id" 
          class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
          
          <!-- DJ Header -->
          <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-lg font-bold">
                {{ dj.name.charAt(0).toUpperCase() }}
              </div>
              <div class="ml-4">
                <h3 class="text-xl font-semibold">{{ dj.name }}</h3>
                <p class="text-purple-100 text-sm">{{ dj.email }}</p>
              </div>
            </div>
          </div>

          <!-- DJ Info -->
          <div class="p-6">
            <div v-if="dj.bio" class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">About</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">{{ dj.bio }}</p>
            </div>

            <!-- Social Links -->
            <div v-if="hasSocialLinks(dj)" class="mb-4">
              <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Social Media</h4>
              <div class="flex space-x-3">
                <a v-if="dj.instagram" :href="dj.instagram" target="_blank" 
                  class="text-pink-500 hover:text-pink-600">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.004 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.515-3.305-1.372-.858-.857-1.372-2.009-1.372-3.305s.515-2.448 1.372-3.305c.857-.858 2.008-1.372 3.305-1.372s2.448.515 3.305 1.372c.857.857 1.372 2.008 1.372 3.305s-.515 2.448-1.372 3.305c-.858.857-2.009 1.372-3.305 1.372zm7.718-6.377h-1.887v-1.106c0-.606-.493-1.099-1.099-1.099s-1.099.493-1.099 1.099v1.106H9.595c-.606 0-1.099.493-1.099 1.099s.493 1.099 1.099 1.099h2.487v1.106c0 .606.493 1.099 1.099 1.099s1.099-.493 1.099-1.099v-1.106h1.887c.606 0 1.099-.493 1.099-1.099s-.493-1.099-1.099-1.099z"/>
                  </svg>
                </a>
                <a v-if="dj.twitter" :href="dj.twitter" target="_blank" 
                  class="text-blue-500 hover:text-blue-600">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                </a>
                <a v-if="dj.soundcloud" :href="dj.soundcloud" target="_blank" 
                  class="text-orange-500 hover:text-orange-600">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1.175 12.225c-.051 0-.094.046-.101.1l-.233 2.154.233 2.105c.007.058.05.098.101.098.05 0 .09-.04.099-.098l.255-2.105-.255-2.154c-.009-.057-.049-.1-.099-.1zm1.06.936c-.045 0-.084.039-.09.087l-.206 1.218.206 1.178c.006.048.045.083.09.083.041 0 .082-.035.088-.083l.244-1.178-.244-1.218c-.006-.048-.047-.087-.088-.087zm1.073.581c-.044 0-.082.039-.088.087l-.175.637.175.619c.006.048.044.082.088.082.043 0 .081-.034.087-.082l.207-.619-.207-.637c-.006-.048-.044-.087-.087-.087z"/>
                  </svg>
                </a>
                <a v-if="dj.youtube" :href="dj.youtube" target="_blank" 
                  class="text-red-500 hover:text-red-600">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                  </svg>
                </a>
              </div>
            </div>

            <!-- Application Status -->
            <div class="mb-4">
              <span v-if="hasApprovedApplication(dj)" 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Verified DJ
              </span>
              <span v-else 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                Application Pending
              </span>
            </div>

            <!-- Send Offer Button -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
              <Link :href="route('dj-offers.create', dj.id)" 
                class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Send Offer
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No DJs Found</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          There are currently no DJs available. Check back later!
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  djs: Array
})

const hasSocialLinks = (dj: any) => {
  return dj.instagram || dj.twitter || dj.soundcloud || dj.youtube
}

const hasApprovedApplication = (dj: any) => {
  return dj.dj_applications && dj.dj_applications.some((app: any) => app.status === 'approved')
}
</script>