<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import ProductCards from "../components/ProductCards.vue";
import { computed, ref, watch } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";

interface DbProduct {
  id: number;
  name: string;
  price: string | number;
  category: string;
  image_men: string | null;
  image_women: string | null;
}

interface User { id: number; name: string; }
interface Auth { user: User | null; }

const page = usePage();
const auth = page.props.auth as Auth;
const isLoggedIn = computed(() => !!auth.user);

const favorites = ref<number[]>([]);
function toggleFavorite(id: number) {
  favorites.value = favorites.value.includes(id)
    ? favorites.value.filter(x => x !== id)
    : [...favorites.value, id];
}

const paginator = computed(() => page.props.products as any);

// 🔑 LOKĀLS filters state
const filtersState = ref({
  search: "",
  category: "all",
  minPrice: 0,
  maxPrice: 1000,
});

// 🔁 SINHRONIZĀCIJA AR BACKEND PROPS
watch(
  () => page.props.filters,
  (f: any) => {
    filtersState.value = {
      search: f?.search ?? "",
      category: f?.category ?? "all",
      minPrice: Number(f?.minPrice ?? 0),
      maxPrice: Number(f?.maxPrice ?? 1000),
    };
  },
  { immediate: true, deep: true }
);

const products = computed(() => {
  const raw = paginator.value;
  const items: DbProduct[] = Array.isArray(raw) ? raw : (raw?.data ?? []);
  return items.map(p => ({
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

function onFiltersChanged(f: typeof filtersState.value) {
  router.get("/WomanWear", f, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
    only: ["products", "filters"],
  });
}
</script>

<template>
  <main class="women-view">
    <NavBar :favorites="favorites" :toggleFavorite="toggleFavorite" :products="products" />

    <ProductCards
      :isLoggedIn="isLoggedIn"
      :favorites="favorites"
      :toggleFavorite="toggleFavorite"
      category="women"
      :products="products"
      :filters="filtersState"
      @filtersChanged="onFiltersChanged"
    />

    <div class="pagination" v-if="paginator?.links">
      <Link
        v-for="l in paginator.links"
        :key="l.label"
        :href="l.url ?? ''"
        v-html="l.label"
        :class="{ disabled: !l.url, active: l.active }"
        preserve-scroll
        preserve-state
      />
    </div>

    <Footer />
  </main>
</template>
