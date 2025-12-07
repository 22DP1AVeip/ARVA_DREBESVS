<script setup lang="ts">
import { ref } from 'vue'
import NavBar from "../components/NavBar.vue";
import Footer from "../components/Footer.vue";
import MainHome from "../components/MainHome.vue";

interface Product {
  id: number
  name: string
  images: {
    men: string
    women?: string
  }
}

const products = ref<Product[]>([
  {
    id: 1,
    name: "Classic T-Shirt",
    images: { men: "/images/products/tshirt_men.jpg", women: "/images/products/tshirt_women.jpg" }
  },
  {
    id: 2,
    name: "Leather Jacket",
    images: { men: "/images/products/jacket_men.jpg", women: "/images/products/jacket_women.jpg" }
  }
])

const favorites = ref<number[]>([])

function toggleFavorite(id: number) {
  if (favorites.value.includes(id)) {
    favorites.value = favorites.value.filter(favId => favId !== id)
  } else {
    favorites.value.push(id)
  }
}
</script>

<template>
  <main>
    <NavBar 
      :favorites="favorites" 
      :toggleFavorite="toggleFavorite" 
      :products="products" 
    />
    <MainHome />
    <Footer />
  </main>
</template>
