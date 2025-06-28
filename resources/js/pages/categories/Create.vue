<template>
  <div>
    <h1>Yeni Kategori</h1>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label for="name">Kategori Adı</label>
        <input v-model="form.name" id="name" type="text" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="image">Resim (URL)</label>
        <input v-model="form.image" id="image" type="text" class="form-control" />
      </div>
      <button type="submit" class="btn btn-success">Kaydet</button>
      <router-link :to="{ name: 'categories.index' }" class="btn btn-secondary ml-2">Geri</router-link>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const form = ref({
  name: '',
  image: ''
});

const submit = async () => {
  await router.post('/categories', form.value);
};
</script>

