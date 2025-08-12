<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue'
import RadioPlayer from '@/components/src/RadioPlayer.vue'
import LanguageSelector from '@/components/src/LanguageSelector.vue'
import FeatureAccordion from '@/components/src/FeatureAccordion.vue'
import SocialMedia from '@/components/src/SocialMedia.vue'
import { useLanguage } from '@/composables/src/useLanguage.js'

const { t, initLanguage, currentLanguage } = useLanguage()
const isRTL = computed(() => currentLanguage.value === 'HE')

onMounted(() => {
  initLanguage()
})
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    </Head>

    <div class="min-h-screen relative overflow-hidden gradient-bg" :dir="isRTL ? 'rtl' : 'ltr'">
        <!-- Floating Particles Background -->
        <div class="floating-particles">
            <div class="particle" style="left: 10%; width: 4px; height: 4px; animation-delay: 0s; animation-duration: 25s;"></div>
            <div class="particle" style="left: 20%; width: 3px; height: 3px; animation-delay: 2s; animation-duration: 30s;"></div>
            <div class="particle" style="left: 30%; width: 5px; height: 5px; animation-delay: 4s; animation-duration: 20s;"></div>
            <div class="particle" style="left: 40%; width: 2px; height: 2px; animation-delay: 6s; animation-duration: 35s;"></div>
            <div class="particle" style="left: 50%; width: 4px; height: 4px; animation-delay: 8s; animation-duration: 25s;"></div>
            <div class="particle" style="left: 60%; width: 3px; height: 3px; animation-delay: 10s; animation-duration: 30s;"></div>
            <div class="particle" style="left: 70%; width: 5px; height: 5px; animation-delay: 12s; animation-duration: 20s;"></div>
            <div class="particle" style="left: 80%; width: 2px; height: 2px; animation-delay: 14s; animation-duration: 35s;"></div>
            <div class="particle" style="left: 90%; width: 4px; height: 4px; animation-delay: 16s; animation-duration: 25s;"></div>
        </div>

        <!-- Top Navigation -->
        <header class="absolute top-4 right-4 z-50 flex items-center gap-4">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="btn-hover bg-white bg-opacity-20 backdrop-blur-sm text-white px-6 py-3 rounded-xl hover:bg-opacity-30 transition-all text-sm font-medium shadow-lg hover:shadow-xl transform hover:scale-105"
            >
                Dashboard
            </Link>
            <template v-else>
                <Link
                    :href="route('login')"
                    class="btn-hover border-2 border-white text-white px-6 py-3 rounded-xl hover:bg-white hover:text-purple-700 transition-all text-sm font-medium shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                    Log in
                </Link>
                <Link
                    :href="route('register')"
                    class="btn-hover border-2 border-white text-white px-6 py-3 rounded-xl hover:bg-white hover:text-purple-700 transition-all text-sm font-medium shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                    Register
                </Link>
            </template>
        </header>

        <!-- Social Media Icons -->
        <SocialMedia />

        <!-- Language Selector -->
        <LanguageSelector />

        <!-- Main content -->
        <div class="relative z-10 flex flex-col items-center justify-center min-h-screen pb-24">
            <!-- Logo with glow effect -->
            <div class="mb-12 transform hover:scale-105 transition-all duration-500">
                <img
                    src="/white.png"
                    alt="Logo"
                    class="max-w-xs md:max-w-md lg:max-w-lg drop-shadow-2xl filter"
                    style="filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.3));"
                />
            </div>

            <!-- Title with enhanced styling -->
            <div class="text-center mb-16 px-4">
                <h1 class="text-white text-3xl md:text-5xl lg:text-6xl font-bold mb-4 leading-tight">
                    <span class="bg-gradient-to-r from-white via-purple-200 to-white bg-clip-text text-transparent animate-pulse">
                        {{ t('title') }}
                    </span>
                </h1>
                <div class="w-32 h-1 bg-gradient-to-r from-transparent via-white to-transparent mx-auto opacity-60"></div>
            </div>

            <!-- Features Accordion - directly under title -->
            <FeatureAccordion />
        </div>

        <!-- Radio Player -->
        <RadioPlayer />
    </div>
                                   
</template>
