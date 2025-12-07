<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/Footer.vue";
import ProductCards from "../components/ProductCards.vue";
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface User {
  id: number;
  name: string;
}

interface Auth {
  user: User | null;
}

const auth = usePage().props.auth as Auth;
const isLoggedIn = computed(() => !!auth.user);

const favorites = ref<number[]>([]);
const products = ref([
  { id: 1, name: 'Men Jeans', images: { men: '/bildites/Men_jeans_1.jpg', women: '/bildites/Woman_jeans_1.jpg' } },
  { id: 2, name: 'Men T-Shirt', images: { men: '/bildites/Men_tshirt_1.jpg', women: '/bildites/Woman_tshirt_1.jpg' } },
  { id: 3, name: 'Men Jacket', images: { men: '/bildites/Men_jacket_1.jpg', women: '/bildites/Woman_jacket_1.jpg' } },
  { id: 4, name: 'Men Shoes', images: { men: '/bildites/Men_shoes_1.jpg', women: '/bildites/Woman_shoes_1.jpg' } },
  { id: 5, name: 'Men Hat', images: { men: '/bildites/Men_hats_1.jpg', women: '/bildites/Woman_hats_1.jpg' } },
]);

function toggleFavorite(id: number) {
  if (favorites.value.includes(id)) {
    favorites.value = favorites.value.filter(favId => favId !== id);
  } else {
    favorites.value.push(id);
  }
}
</script>

<template>
  <main>
    <NavBar />
    
    <ProductCards
      :isLoggedIn="isLoggedIn"
      :favorites="favorites"
      :toggleFavorite="toggleFavorite"
      category="men"
      :products="products"
    />
    
    <Footer />
  </main>
</template>
