<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue'
import { useLanguage } from '@/composables/src/useLanguage.js'

const { t, initLanguage, currentLanguage } = useLanguage()
const isRTL = computed(() => currentLanguage.value === 'HE')

onMounted(() => {
  initLanguage()
})
</script>

<template>
    <Head title="Downloads - djevoo">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    </Head>

    <div class="min-h-screen relative overflow-hidden gradient-bg flex items-center justify-center" :dir="isRTL ? 'rtl' : 'ltr'">
        <!-- Floating Particles Background -->
        <div class="floating-particles">
            <div class="particle" style="left: 10%; width: 4px; height: 4px; animation-delay: 0s; animation-duration: 25s;"></div>
            <div class="particle" style="left: 20%; width: 3px; height: 3px; animation-delay: 2s; animation-duration: 30s;"></div>
            <div class="particle" style="left: 30%; width: 5px; height: 5px; animation-delay: 4s; animation-duration: 20s;"></div>
            <div class="particle" style="left: 40%; width: 2px; height: 2px; animation-delay: 6s; animation-duration: 35s;"></div>
            <div class="particle" style="left: 50%; width: 6px; height: 6px; animation-delay: 8s; animation-duration: 15s;"></div>
            <div class="particle" style="left: 60%; width: 3px; height: 3px; animation-delay: 10s; animation-duration: 28s;"></div>
            <div class="particle" style="left: 70%; width: 4px; height: 4px; animation-delay: 12s; animation-duration: 22s;"></div>
            <div class="particle" style="left: 80%; width: 5px; height: 5px; animation-delay: 14s; animation-duration: 32s;"></div>
            <div class="particle" style="left: 90%; width: 3px; height: 3px; animation-delay: 16s; animation-duration: 26s;"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 max-w-2xl mx-auto px-6 py-16 text-center">
            <!-- Logo/Icon -->
            <div class="mb-8">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-400 to-purple-600 rounded-3xl flex items-center justify-center shadow-2xl">
                    <i class="fab fa-apple text-white text-6xl"></i>
                </div>
            </div>

            <!-- Main Heading -->
            <h1 class="text-6xl font-black text-white mb-6 tracking-tight">
                djevoo
            </h1>
            
            <!-- Subtitle -->
            <h2 class="text-2xl font-semibold text-blue-100 mb-8">
                iOS App
            </h2>

            <!-- Coming Soon Badge -->
            <div class="inline-flex items-center gap-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-8 py-4 rounded-full text-xl font-bold shadow-lg mb-12 animate-pulse">
                <i class="fas fa-clock text-2xl"></i>
                <span>Çok Yakında</span>
                <i class="fas fa-rocket text-2xl"></i>
            </div>

            <!-- Description -->
            <div class="text-lg text-blue-100 mb-12 leading-relaxed">
                <p class="mb-4">
                    iOS kullanıcıları için özel olarak tasarlanan djevoo uygulaması çok yakında App Store'da!
                </p>
                <p>
                    En iyi DJ'leri keşfedin, müzik deneyiminizi yaşayın.
                </p>
            </div>

            <!-- Features Preview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-music text-white text-xl"></i>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Müzik Keşfi</h3>
                    <p class="text-blue-100 text-sm">En iyi DJ'leri ve setleri keşfedin</p>
                </div>

                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-headphones text-white text-xl"></i>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Canlı Yayın</h3>
                    <p class="text-blue-100 text-sm">Canlı DJ performanslarını dinleyin</p>
                </div>

                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="w-12 h-12 bg-pink-500 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-heart text-white text-xl"></i>
                    </div>
                    <h3 class="text-white font-semibold mb-2">Favoriler</h3>
                    <p class="text-blue-100 text-sm">Sevdiğiniz müzikleri kaydedin</p>
                </div>
            </div>

            <!-- Android Download Button -->
            <div class="mb-8">
                <a href="https://play.google.com/store/apps/details?id=com.tfsoisrael.mobile" 
                   class="inline-flex items-center gap-3 bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fab fa-google-play text-2xl"></i>
                    <span>Android'de İndir</span>
                </a>
            </div>

            <!-- Newsletter Signup -->
            <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
                <h3 class="text-xl font-semibold text-white mb-4">
                    iOS lansmanından haberdar olmak ister misiniz?
                </h3>
                <p class="text-blue-100 mb-6">
                    E-posta adresinizi bırakın, iOS uygulaması yayınlandığında size haber verelim.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="email" 
                           placeholder="E-posta adresiniz" 
                           class="flex-1 px-4 py-3 rounded-xl bg-white/10 backdrop-blur-lg border border-white/20 text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all duration-300 whitespace-nowrap">
                        Bilgilendir
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.floating-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    top: 0;
    left: 0;
}

.particle {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    pointer-events: none;
    animation: float linear infinite;
}

@keyframes float {
    0% {
        transform: translateY(100vh) scale(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-10vh) scale(1);
        opacity: 0;
    }
}
</style>