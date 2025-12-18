<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import ProductCards from "../components/ProductCards.vue";
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

interface DbProduct {
  id: number;
  name: string;
  price: string | number;
  category: string;
  image_men: string | null;
  image_women: string | null;
}

interface User {
  id: number;
  name: string;
}

interface Auth {
  user: User | null;
}

const page = usePage();

const auth = page.props.auth as Auth;
const isLoggedIn = computed(() => !!auth.user);

const favorites = ref<number[]>([]);

function toggleFavorite(id: number) {
  if (favorites.value.includes(id)) {
    favorites.value = favorites.value.filter((favId) => favId !== id);
  } else {
    favorites.value.push(id);
  }
}

const products = computed(() => {
  const dbProducts = (page.props.products as DbProduct[]) ?? [];
  return dbProducts.map((p) => ({
    id: p.id,
    name: p.name,
    price: Number(p.price),
    category: p.category,
    images: {
      men: p.image_men ?? "",
      women: p.image_women ?? "",
    },
  }));
});
</script>

<template>
  <main>
    <NavBar
      :favorites="favorites"
      :toggleFavorite="toggleFavorite"
      :products="products"
    />

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
