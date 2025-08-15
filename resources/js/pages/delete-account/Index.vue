<template>
  <AuthLayout>
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50 flex items-center justify-center px-4 py-8">
      <div class="w-full max-w-md">
        <!-- Modern card with glass effect -->
        <div class="bg-white/80 backdrop-blur-sm border border-red-100 rounded-2xl shadow-2xl shadow-red-500/10 p-8">
          <!-- Header with icon -->
          <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mb-4 shadow-lg shadow-red-500/25">
              <Icon name="Trash2" class="h-8 w-8 text-white" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Hesabı Kalıcı Olarak Sil</h1>
            <p class="text-gray-600 text-sm leading-relaxed">
              Bu işlem geri alınamaz. Devam etmek için bilgilerinizi doğrulayın.
            </p>
          </div>

          <!-- Warning card -->
          <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200/50 rounded-xl p-4 mb-6">
            <div class="flex">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                  <Icon name="AlertTriangle" class="h-4 w-4 text-red-500" />
                </div>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-semibold text-red-800 mb-2">
                  ⚠️ Bu İşlem Geri Alınamaz
                </h3>
                <div class="text-xs text-red-700 space-y-1">
                  <p>• Tüm hesap verileriniz kalıcı olarak silinecek</p>
                  <p>• Profil fotoğrafları ve medya dosyaları kaldırılacak</p>
                  <p>• Yeniden aynı e-posta ile kayıt olmanız gerekecek</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="deleteAccount" class="space-y-5">
            <div class="space-y-2">
              <Label for="email" class="text-sm font-medium text-gray-700">E-posta Adresi</Label>
              <div class="relative">
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="ornek@email.com"
                  class="pl-10 h-11 border-gray-200 focus:border-red-300 focus:ring-red-300/20 rounded-lg transition-all duration-200"
                  :class="{ 'border-red-500 focus:border-red-500': errors.email }"
                />
                <Icon name="Mail" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              </div>
              <InputError :message="errors.email" />
            </div>

            <div class="space-y-2">
              <Label for="password" class="text-sm font-medium text-gray-700">Şifre</Label>
              <div class="relative">
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="••••••••"
                  class="pl-10 h-11 border-gray-200 focus:border-red-300 focus:ring-red-300/20 rounded-lg transition-all duration-200"
                  :class="{ 'border-red-500 focus:border-red-500': errors.password }"
                />
                <Icon name="Lock" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              </div>
              <InputError :message="errors.password" />
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
              <div class="flex items-start space-x-3">
                <Checkbox
                  id="confirm_deletion"
                  v-model:checked="form.confirm_deletion"
                  class="mt-1 border-gray-300 text-red-600 focus:ring-red-500"
                  :class="{ 'border-red-500': errors.confirm_deletion }"
                />
                <div class="flex-1">
                  <Label for="confirm_deletion" class="text-sm font-medium text-gray-700 cursor-pointer">
                    Bu hesabı kalıcı olarak silmek istediğimi anlıyor ve onaylıyorum
                  </Label>
                  <p class="text-xs text-gray-500 mt-1">Bu işlem geri alınamaz olduğunu kabul ediyorum</p>
                  <InputError :message="errors.confirm_deletion" />
                </div>
              </div>
            </div>

            <div class="flex flex-col space-y-3 pt-2">
              <Button
                type="submit"
                :disabled="processing || !form.email || !form.password || !form.confirm_deletion"
                class="w-full h-11 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-lg shadow-lg shadow-red-500/25 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:scale-100"
              >
                <Icon v-if="processing" name="Loader2" class="mr-2 h-4 w-4 animate-spin" />
                <Icon v-else name="Trash2" class="mr-2 h-4 w-4" />
                <span v-if="processing">Hesap Siliniyor...</span>
                <span v-else>Hesabı Kalıcı Olarak Sil</span>
              </Button>
              
              <TextLink 
                :href="route('home')" 
                class="text-center text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200"
              >
                İptal Et ve Ana Sayfaya Dön
              </TextLink>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import InputError from '@/components/InputError.vue'
import TextLink from '@/components/TextLink.vue'
import Icon from '@/components/Icon.vue'

const processing = ref(false)
const errors = ref<Record<string, string>>({})

const form = reactive({
  email: '',
  password: '',
  confirm_deletion: false,
})

const deleteAccount = () => {
  processing.value = true
  errors.value = {}

  router.delete(route('delete-account.destroy'), {
    data: form,
    onSuccess: () => {
      // User will be redirected to home page by the controller
    },
    onError: (error) => {
      errors.value = error
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>