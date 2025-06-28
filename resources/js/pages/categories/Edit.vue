<template>
  <div>
    <h1>Kategori Düzenle</h1>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label for="name">Kategori Adı</label>
        <input v-model="form.name" id="name" type="text" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="image">Resim (URL)</label>
        <input v-model="form.image" id="image" type="text" class="form-control" />
      </div>
      <button type="submit" class="btn btn-success">Güncelle</button>
      <router-link :to="{ name: 'categories.index' }" class="btn btn-secondary ml-2">Geri</router-link>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const form = ref({
  name: '',
  image: ''
});

const page = usePage();
const categoryId = page.props.category?.id;

const fetchCategory = async () => {
  const response = await axios.get(`/categories/${categoryId}/edit`);
  form.value = {
    name: response.data.name,
    image: response.data.image
  };
};

const submit = async () => {
  await router.put(`/categories/${categoryId}`, form.value);
};

onMounted(fetchCategory);
</script>

