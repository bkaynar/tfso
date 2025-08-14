<template>

    <Head title="Destek - djevoo" />

    <div class="min-h-screen relative overflow-hidden gradient-bg">
        <!-- Floating Particles Background -->
        <div class="floating-particles">
            <div class="particle"
                style="left: 10%; width: 4px; height: 4px; animation-delay: 0s; animation-duration: 25s;"></div>
            <div class="particle"
                style="left: 20%; width: 3px; height: 3px; animation-delay: 2s; animation-duration: 30s;"></div>
            <div class="particle"
                style="left: 30%; width: 5px; height: 5px; animation-delay: 4s; animation-duration: 20s;"></div>
            <div class="particle"
                style="left: 40%; width: 2px; height: 2px; animation-delay: 6s; animation-duration: 35s;"></div>
            <div class="particle"
                style="left: 50%; width: 4px; height: 4px; animation-delay: 8s; animation-duration: 25s;"></div>
            <div class="particle"
                style="left: 60%; width: 3px; height: 3px; animation-delay: 10s; animation-duration: 30s;"></div>
            <div class="particle"
                style="left: 70%; width: 5px; height: 5px; animation-delay: 12s; animation-duration: 20s;"></div>
            <div class="particle"
                style="left: 80%; width: 2px; height: 2px; animation-delay: 14s; animation-duration: 35s;"></div>
            <div class="particle"
                style="left: 90%; width: 4px; height: 4px; animation-delay: 16s; animation-duration: 25s;"></div>
        </div>

        <!-- Top Navigation -->
        <header class="absolute top-4 left-4 right-4 z-50 flex items-center justify-between">
            <Link :href="route('home')"
                class="btn-hover bg-white bg-opacity-20 backdrop-blur-sm text-black px-6 py-3 rounded-xl hover:bg-opacity-30 transition-all text-sm font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
            ← Ana Sayfa
            </Link>
        </header>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-col items-center justify-center min-h-screen py-20">
            <!-- Logo -->
            <div class="mb-8">
                <img src="/white.png" alt="djevoo" class="max-w-48 drop-shadow-2xl filter"
                    style="filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.3));" />
            </div>

            <!-- Support Form -->
            <div class="max-w-2xl w-full mx-auto px-4">
                <div
                    class="bg-white bg-opacity-10 backdrop-blur-lg shadow-2xl rounded-3xl p-8 border border-white border-opacity-20">
                    <h1 class="text-3xl font-bold text-purple mb-6 text-center">
                        Support
                    </h1>

                    <div v-if="$page.props.flash?.success"
                        class="mb-6 bg-green-500 bg-opacity-20 border border-green-400 border-opacity-30 text-green-100 px-4 py-3 rounded-xl backdrop-blur-sm">
                        {{ $page.props.flash.success }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name"
                                    class="block text-sm font-medium text-black text-opacity-90 mb-1">
                                    Name
                                </label>
                                <input v-model="form.first_name" id="first_name" type="text" required
                                    class="support-input" placeholder="Adınız" />
                                <div v-if="errors.first_name" class="text-red-300 text-sm mt-1">{{ errors.first_name }}
                                </div>
                            </div>

                            <div>
                                <label for="last_name"
                                    class="block text-sm font-medium text-black text-opacity-90 mb-1">
                                    Last Name
                                </label>
                                <input v-model="form.last_name" id="last_name" type="text" required
                                    class="support-input" placeholder="Soyadınız" />
                                <div v-if="errors.last_name" class="text-red-300 text-sm mt-1">{{ errors.last_name }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-black text-opacity-90 mb-1">
                                Email
                            </label>
                            <input v-model="form.email" id="email" type="email" required class="support-input"
                                placeholder="ornek@email.com" />
                            <div v-if="errors.email" class="text-red-300 text-sm mt-1">{{ errors.email }}</div>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-black text-opacity-90 mb-1">
                                Subject
                            </label>
                            <input v-model="form.subject" id="subject" type="text" required class="support-input"
                                placeholder="Mesajınızın konusu" />
                            <div v-if="errors.subject" class="text-red-300 text-sm mt-1">{{ errors.subject }}</div>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-black text-opacity-90 mb-1">
                                Message
                            </label>
                            <textarea v-model="form.message" id="message" rows="6" required
                                class="support-input support-textarea"
                                placeholder="Mesajınızı buraya yazın..."></textarea>
                            <div v-if="errors.message" class="text-red-300 text-sm mt-1">{{ errors.message }}</div>
                        </div>

                        <div class="text-center pt-4">
                            <button type="submit" :disabled="processing"
                                class="btn-hover bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-xl hover:from-purple-700 hover:to-blue-700 disabled:from-purple-400 disabled:to-blue-400 text-lg font-medium shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200 disabled:transform-none">
                                <span v-if="processing" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Sending...
                                </span>
                                <span v-else>Send Message</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'

const { data: form, post, processing, errors } = useForm({
    first_name: '',
    last_name: '',
    email: '',
    subject: '',
    message: ''
})

const submit = () => {
    post('/support', {
        onSuccess: () => {
            form.first_name = ''
            form.last_name = ''
            form.email = ''
            form.subject = ''
            form.message = ''
        }
    })
}
</script>

<style>
.gradient-bg {
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
}

@keyframes gradientBG {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.floating-particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.particle {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float ease-in-out infinite;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0;
    }

    10% {
        opacity: 1;
    }

    90% {
        opacity: 1;
    }

    100% {
        transform: translateY(-100vh) rotate(360deg);
        opacity: 0;
    }
}

.btn-hover {
    transition: all 0.3s ease;
}

.btn-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Support form input styling */
.support-input {
    display: block !important;
    width: 100% !important;
    padding: 16px !important;
    background: rgba(255, 255, 255, 0.15) !important;
    border: 2px solid rgba(255, 255, 255, 0.3) !important;
    border-radius: 12px !important;
    color: black !important;
    font-size: 16px !important;
    backdrop-filter: blur(10px) !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.3s ease !important;
}

.support-input:focus {
    outline: none !important;
    border-color: rgba(147, 51, 234, 0.8) !important;
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.3) !important;
    background: rgba(255, 255, 255, 0.2) !important;
}

.support-input::placeholder {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 14px !important;
}

.support-textarea {
    min-height: 120px !important;
    resize: vertical !important;
}

/* Autofill handling */
.support-input:-webkit-autofill,
.support-input:-webkit-autofill:hover,
.support-input:-webkit-autofill:focus,
.support-input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.15) inset !important;
    -webkit-text-fill-color: black !important;
    background-color: transparent !important;
}
</style>
