<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const scrollToSection = (sectionId: string) => {
  const element = document.getElementById(sectionId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}
</script>

<template>
    <Head title="Privacy Policy - djevoo">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-purple-800 to-indigo-900 relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="privacy-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="10" cy="10" r="1" fill="white" opacity="0.3"/>
                        <circle cx="5" cy="5" r="0.5" fill="white" opacity="0.2"/>
                        <circle cx="15" cy="15" r="0.5" fill="white" opacity="0.2"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#privacy-pattern)"/>
            </svg>
        </div>

        <!-- Header -->
        <header class="relative z-10 bg-dark bg-opacity-95 backdrop-blur-sm border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <Link :href="route('home')" class="flex items-center space-x-3 group">
                        <img src="/white.png" alt="djevoo" class="h-8 w-auto transition-transform group-hover:scale-105" />
                    </Link>

                    <nav class="hidden md:flex space-x-8">
                        <button @click="scrollToSection('collection')" class="text-white hover:text-purple-700 transition-colors">Data Collection</button>
                        <button @click="scrollToSection('usage')" class="text-white hover:text-purple-700 transition-colors">Data Usage</button>
                        <button @click="scrollToSection('security')" class="text-white hover:text-purple-700 transition-colors">Security</button>
                        <button @click="scrollToSection('contact')" class="text-white hover:text-purple-700 transition-colors">Contact</button>
                    </nav>

                    <Link
                        :href="route('home')"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-all hover:scale-105 transform"
                    >
                        Back to Home
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Hero Section -->
            <div class="text-center mb-16">

                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">
                    Privacy Policy
                </h1>
                <p class="text-xl text-purple-200 mb-2">Your privacy is our priority</p>
                <p class="text-purple-300">Last updated: August 14, 2025</p>
            </div>

            <!-- Content -->
            <div class="bg-white bg-opacity-95 backdrop-blur-sm rounded-3xl border border-white border-opacity-50 overflow-hidden shadow-2xl">
                <!-- Introduction -->
                <div class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Introduction</h2>
                    <p class="text-gray-700 leading-relaxed">
                        This Privacy Policy describes our policies and procedures on the collection, use and disclosure of your information when you use the djevoo service and tells you about your privacy rights and how the law protects you.
                    </p>
                </div>

                <!-- Definitions Section -->
                <div class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Key Definitions</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Service</h3>
                            <p class="text-gray-600 text-sm">Refers to the djevoo application and website</p>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Personal Data</h3>
                            <p class="text-gray-600 text-sm">Information that relates to an identified or identifiable individual</p>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Usage Data</h3>
                            <p class="text-gray-600 text-sm">Data collected automatically when using the service</p>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-6 border border-purple-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Cookies</h3>
                            <p class="text-gray-600 text-sm">Small files placed on your device for tracking and preferences</p>
                        </div>
                    </div>
                </div>

                <!-- Data Collection Section -->
                <div id="collection" class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-database text-purple-300 mr-3"></i>
                        Data Collection
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information We Collect</h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-purple-600 bg-opacity-30 rounded-lg p-4 text-center">
                                    <i class="fas fa-envelope text-2xl text-purple-200 mb-2"></i>
                                    <p class="text-gray-800 font-medium">Email Address</p>
                                </div>
                                <div class="bg-purple-600 bg-opacity-30 rounded-lg p-4 text-center">
                                    <i class="fas fa-user text-2xl text-purple-200 mb-2"></i>
                                    <p class="text-gray-800 font-medium">Name</p>
                                </div>
                                <div class="bg-purple-600 bg-opacity-30 rounded-lg p-4 text-center">
                                    <i class="fas fa-chart-line text-2xl text-purple-200 mb-2"></i>
                                    <p class="text-gray-800 font-medium">Usage Data</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Automatically Collected Data</h3>
                            <p class="text-gray-700 leading-relaxed">
                                We automatically collect certain information when you use our service, including your IP address, browser type, device information, and usage patterns to improve our service and ensure security.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Data Usage Section -->
                <div id="usage" class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-cogs text-purple-300 mr-3"></i>
                        How We Use Your Data
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-500 bg-opacity-30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-check text-green-300 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Service Provision</p>
                                    <p class="text-gray-600 text-sm">To provide and maintain our music streaming service</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-500 bg-opacity-30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-check text-green-300 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Account Management</p>
                                    <p class="text-gray-600 text-sm">To manage your registration and account features</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-500 bg-opacity-30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-check text-green-300 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Communication</p>
                                    <p class="text-gray-600 text-sm">To contact you about updates and important information</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-500 bg-opacity-30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-chart-bar text-blue-300 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Analytics</p>
                                    <p class="text-gray-600 text-sm">To analyze usage trends and improve our service</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-500 bg-opacity-30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-shield-alt text-blue-300 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Security</p>
                                    <p class="text-gray-600 text-sm">To protect against fraud and unauthorized access</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-500 bg-opacity-30 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-balance-scale text-blue-300 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-gray-800 font-medium">Legal Compliance</p>
                                    <p class="text-gray-600 text-sm">To comply with legal obligations and requests</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div id="security" class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-lock text-purple-300 mr-3"></i>
                        Data Security
                    </h2>

                    <div class="bg-gradient-to-r from-green-600 to-blue-600 bg-opacity-20 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Our Security Commitment</h3>
                        <p class="text-gray-700 leading-relaxed">
                            We implement industry-standard security measures to protect your personal data. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-500 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-encrypt text-2xl text-green-300"></i>
                            </div>
                            <p class="text-gray-800 font-medium">Encryption</p>
                            <p class="text-gray-600 text-sm">Data encrypted in transit and at rest</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-500 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-server text-2xl text-blue-300"></i>
                            </div>
                            <p class="text-gray-800 font-medium">Secure Servers</p>
                            <p class="text-gray-600 text-sm">Protected infrastructure and monitoring</p>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-purple-500 bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user-shield text-2xl text-purple-300"></i>
                            </div>
                            <p class="text-gray-800 font-medium">Access Control</p>
                            <p class="text-gray-600 text-sm">Limited access on need-to-know basis</p>
                        </div>
                    </div>
                </div>

                <!-- Your Rights Section -->
                <div class="p-8 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-user-check text-purple-300 mr-3"></i>
                        Your Privacy Rights
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                            <i class="fas fa-eye text-purple-600 text-xl"></i>
                            <div>
                                <p class="text-gray-800 font-medium">Access</p>
                                <p class="text-gray-600 text-sm">Request access to your personal data</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                            <i class="fas fa-edit text-purple-600 text-xl"></i>
                            <div>
                                <p class="text-gray-800 font-medium">Correction</p>
                                <p class="text-gray-600 text-sm">Update or correct your information</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                            <i class="fas fa-trash text-purple-600 text-xl"></i>
                            <div>
                                <p class="text-gray-800 font-medium">Deletion</p>
                                <p class="text-gray-600 text-sm">Request deletion of your data</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                            <i class="fas fa-download text-purple-600 text-xl"></i>
                            <div>
                                <p class="text-gray-800 font-medium">Portability</p>
                                <p class="text-gray-600 text-sm">Export your data in a portable format</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div id="contact" class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-envelope text-purple-300 mr-3"></i>
                        Contact Us
                    </h2>

                    <div class="bg-gradient-to-r from-purple-100 to-indigo-100 rounded-xl p-6 border border-purple-200">
                        <p class="text-gray-700 mb-4">
                            If you have any questions about this Privacy Policy or want to exercise your privacy rights, please contact us:
                        </p>

                        <div class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-purple-300"></i>
                            <a href="mailto:info@djevoo.com" class="text-purple-700 hover:text-purple-900 transition-colors font-medium">
                                info@djevoo.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-white border-opacity-20 bg-white bg-opacity-5 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center">
                    <p class="text-purple text-sm">
                        © 2025 djevoo. All rights reserved. |
                        <Link :href="route('home')" class="hover:text-red transition-colors">Back to Home</Link>
                    </p>
                    <p class="text-purple-500 text-xs mt-2">
                        This Privacy Policy is subject to change.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}

/* Animation for background pattern */
@keyframes float {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.7; }
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}
</style>
