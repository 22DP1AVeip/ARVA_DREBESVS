<script setup lang="ts">
import { ref, onMounted } from 'vue'

interface Product {
  id: number
  name: string
  images: {
    men: string
  }
}

const favorites = ref<number[]>([])
const products = ref<Product[]>([
  { id: 1, name: 'Black Hoodie', images: { men: '/products/hoodie1.jpg' } },
  { id: 2, name: 'White T-Shirt', images: { men: '/products/tshirt1.jpg' } },
  { id: 3, name: 'Grey Pants', images: { men: '/products/pants1.jpg' } }
])

const favoriteProducts = ref<Product[]>([])

onMounted(() => {
  const saved = localStorage.getItem('favorites')
  if (saved) {
    favorites.value = JSON.parse(saved)
    favoriteProducts.value = products.value.filter(p => favorites.value.includes(p.id))
  }
})
</script>

<template>
  <div class="favorites-box">
    <h2>Your Favorites</h2>
    <div v-if="favoriteProducts.length === 0">No favorites yet.</div>
    <div class="grid">
      <div v-for="item in favoriteProducts" :key="item.id" class="card">
        <img :src="item.images.men" :alt="item.name" class="img" />
        <div class="name">{{ item.name }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.favorites-box {
  padding: 2rem;
}
.grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
}
.card {
  width: 180px;
  border: 1px solid #ddd;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.1);
}
.img {
  width: 100%;
  height: auto;
}
.name {
  padding: 10px;
  text-align: center;
}
</style>
