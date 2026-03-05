<template>
  <div class="filters">
    <select v-model="selectedCategory" class="filter-select">
      <option value="all">Visas kategorijas</option>
      <option value="tshirt">T-krekli</option>
      <option value="jeans">Džinsi</option>
      <option value="jacket">Jakas</option>
      <option value="hat">Cepures</option>
      <option value="shoes">Apavi</option>
    </select>

    <input
      type="text"
      placeholder="Meklēt..."
      v-model="searchQuery"
      class="search-input"
    />

    <div class="price-range">
      <label>
        Min. cena:
        <input type="number" v-model.number="minPrice" class="price-input" min="0" />
      </label>
      <label>
        Maks. cena:
        <input type="number" v-model.number="maxPrice" class="price-input" min="0" />
      </label>
    </div>

    <!-- FAVORITES filtrs pagaidām IZSLĒGTS, kamēr favorīti nav DB
    <label class="favorites-toggle">
      <input type="checkbox" v-model="showOnlyFavorites" :disabled="!isLoggedIn" />
      Show only favorites
    </label>
    -->
  </div>

  <div class="product-container">
    <Link
      class="product-card"
      v-for="product in products"
      :key="product.id"
      :href="`/product/${product.id}?view=${category}`"
    >
      <img :src="product.images[selectedGender]" :alt="product.name" class="product-img" />
      <div class="product-info">
        <h3 class="product-name">{{ product.name }}</h3>
        <p class="product-price">€{{ product.price.toFixed(2) }}</p>
      </div>
    </Link>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";

type Gender = "men" | "women";

interface UiProduct {
  id: number;
  name: string;
  price: number;
  category: string;
  images: { men: string; women: string };
}

const props = defineProps<{
  isLoggedIn: boolean;
  favorites: number[];
  toggleFavorite: (id: number) => void;
  category: Gender;
  products: UiProduct[];
  filters?: {
    search?: string;
    category?: string;
    minPrice?: number;
    maxPrice?: number;
  };
}>();

const emit = defineEmits<{
  (e: "filtersChanged", v: { search: string; category: string; minPrice: number; maxPrice: number }): void;
}>();

const searchQuery = ref(props.filters?.search ?? "");
const minPrice = ref(Number(props.filters?.minPrice ?? 0));
const maxPrice = ref(Number(props.filters?.maxPrice ?? 1000));
const selectedCategory = ref(props.filters?.category ?? "all");

watch(
  () => props.filters,
  (f) => {
    searchQuery.value = f?.search ?? "";
    selectedCategory.value = f?.category ?? "all";
    minPrice.value = Number(f?.minPrice ?? 0);
    maxPrice.value = Number(f?.maxPrice ?? 1000);
  },
  { immediate: true, deep: true }
);

// Dzimumu nosaka lapa (MenView/WomanView). Te nav select, lai nesajauc ar backend filtriem.
const selectedGender = ref<Gender>(props.category);

// Atstājām, bet šobrīd nelietojam (favorites filtrs būs, kad būs DB)
const showOnlyFavorites = ref(false);

let searchTimer: any = null;

// Search – lēnāk
watch(searchQuery, () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    emit("filtersChanged", {
      search: searchQuery.value,
      category: selectedCategory.value,
      minPrice: minPrice.value,
      maxPrice: maxPrice.value,
    });
  }, 600);
});

// Category/Price – ātri
watch([selectedCategory, minPrice, maxPrice], () => {
  emit("filtersChanged", {
    search: searchQuery.value,
    category: selectedCategory.value,
    minPrice: minPrice.value,
    maxPrice: maxPrice.value,
  });
});
</script>

<style scoped>
.product-card {
  text-decoration: none;
  color: inherit;
}

.filters {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 15px;
  padding: 20px;
  background-color: #ffffff;
  margin: 20px auto;
  max-width: 1200px;
  border-radius: 10px;
  border: 1px solid rgba(7, 37, 54, 0.08);
  box-shadow: 0 6px 18px rgba(7, 37, 54, 0.06);
}

.filters select,
.filters input,
.filters label {
  color: #072536;
}

.filters input::placeholder {
  color: rgba(7, 37, 54, 0.45);
}

.filter-select {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid rgba(7, 37, 54, 0.12);
  background-color: white;
  font-size: 14px;
  min-width: 150px;
}

.search-input {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid rgba(7, 37, 54, 0.12);
  font-size: 14px;
  min-width: 200px;
}

.price-range {
  display: flex;
  gap: 10px;
  align-items: center;
}

.price-input {
  padding: 8px;
  border-radius: 6px;
  border: 1px solid rgba(7, 37, 54, 0.12);
  width: 70px;
  margin-left: 5px;
}

.favorites-toggle {
  display: flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
}

.product-container {
  display: flex;
  flex-wrap: wrap;
  gap: 26px;
  justify-content: center;
  padding: 20px;
  margin: 0 auto;
  max-width: 1500px;
}

.product-card {
  display: flex;
  flex-direction: column;
  width: calc(20% - 26px);
  min-width: 240px;
  background-color: #ffffff;
  border-radius: 12px;
  border: 1px solid rgba(7, 37, 54, 0.1);
  box-shadow: 0 8px 20px rgba(7, 37, 54, 0.08);
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(7, 37, 54, 0.12);
}

.product-img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-bottom: 1px solid rgba(7, 37, 54, 0.08);
  background: #f7fbfc;
}

.product-info {
  padding: 14px 16px 16px;
}

.product-name {
  font-size: 16px;
  font-weight: 600;
  color: #072536;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-price {
  font-size: 16px;
  color: #072536;
  font-weight: 700;
}

@media (max-width: 1200px) {
  .product-card {
    width: calc(25% - 26px);
  }
}

@media (max-width: 992px) {
  .product-card {
    width: calc(33.33% - 26px);
  }
}

@media (max-width: 768px) {
  .filters {
    flex-direction: column;
    align-items: center;
  }

  .product-card {
    width: calc(50% - 26px);
  }
}

@media (max-width: 480px) {
  .product-card {
    width: calc(100% - 26px);
  }
}
</style>
