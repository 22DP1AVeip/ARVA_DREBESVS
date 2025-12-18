<template>
  <div class="filters">
    <select v-model="selectedCategory" class="filter-select">
      <option value="all">All Categories</option>
      <option value="tshirt">T-Shirts</option>
      <option value="jeans">Jeans</option>
      <option value="jacket">Jackets</option>
      <option value="hat">Hats</option>
      <option value="shoes">Shoes</option>
    </select>

    <select v-model="selectedGender" class="filter-select">
      <option value="men">Men</option>
      <option value="women">Women</option>
    </select>

    <input type="text" placeholder="Search..." v-model="searchQuery" class="search-input" />

    <div class="price-range">
      <label>
        Min Price:
        <input type="number" v-model.number="minPrice" class="price-input" min="0" />
      </label>
      <label>
        Max Price:
        <input type="number" v-model.number="maxPrice" class="price-input" min="0" />
      </label>
    </div>

    <label class="favorites-toggle">
      <input type="checkbox" v-model="showOnlyFavorites" :disabled="!isLoggedIn" />
      Show only favorites
    </label>
  </div>

  <div class="product-container">
    <div class="product-card" v-for="product in filteredProducts" :key="product.id">
      <img :src="product.images[selectedGender]" :alt="product.name" class="product-img" />
      <div class="product-info">
        <h3 class="product-name">{{ product.name }}</h3>
        <p class="product-price">${{ product.price.toFixed(2) }}</p>
        <div
          class="favorite-icon"
          @click="handleFavoriteClick(product.id)"
          :style="{
            opacity: isLoggedIn ? 1 : 0.4,
            cursor: isLoggedIn ? 'pointer' : 'not-allowed'
          }"
        >
          <svg
            class="heart-icon"
            :class="{ favorited: isFavorited(product.id) }"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            width="24"
            height="24"
          >
            <path
              d="M20.8 4.6c-1.6-1.6-4.3-1.6-6 0L12 7.4l-2.8-2.8c-1.6-1.6-4.3-1.6-6 0-1.6 1.6-1.6 4.3 0 6L12 21.1l8.8-8.5c1.7-1.7 1.7-4.4 0-6z"
              :fill="isFavorited(product.id) ? 'red' : 'none'"
            />
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";

type Gender = "men" | "women";

interface UiProduct {
  id: number;
  name: string;
  price: number;
  category: string;
  images: {
    men: string;
    women: string;
  };
}

const props = defineProps<{
  isLoggedIn: boolean;
  favorites: number[];
  toggleFavorite: (id: number) => void;
  category: Gender;
  products: UiProduct[];
}>();

const searchQuery = ref("");
const minPrice = ref(0);
const maxPrice = ref(1000);
const selectedCategory = ref("all");
const selectedGender = ref<Gender>(props.category);
const showOnlyFavorites = ref(false);

const handleFavoriteClick = (id: number) => {
  if (!props.isLoggedIn) {
    alert("Please log in to add favorites.");
    return;
  }
  props.toggleFavorite(id);
};

const isFavorited = (id: number) => props.favorites.includes(id);

const filteredProducts = computed(() => {
  return props.products.filter((product) => {
    const matchesSearch = product.name
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase());
    const matchesPrice =
      product.price >= minPrice.value && product.price <= maxPrice.value;
    const matchesCategory =
      selectedCategory.value === "all" ||
      product.category === selectedCategory.value;
    const matchesFavorites =
      !showOnlyFavorites.value || props.favorites.includes(product.id);

    return matchesSearch && matchesPrice && matchesCategory && matchesFavorites;
  });
});
</script>


<style scoped>
.filters {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 15px;
  padding: 20px;
  background-color: white;
  margin: 20px auto;
  max-width: 1200px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Make text inside filters black */
.filters select,
.filters input,
.filters label {
  color: black;
}

/* Darker placeholder text for better visibility */
.filters input::placeholder {
  color: #555;
}

.filter-select {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ddd;
  background-color: white;
  font-size: 14px;
  min-width: 150px;
}

.search-input {
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #ddd;
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
  border: 1px solid #ddd;
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
  gap: 30px;
  justify-content: center;
  padding: 20px;
  margin: 0 auto;
  max-width: 1400px;
}

.product-card {
  display: flex;
  flex-direction: column;
  width: calc(20% - 30px);
  min-width: 250px;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.product-img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-bottom: 1px solid #eee;
}

.product-info {
  padding: 15px;
  position: relative;
}

.product-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-price {
  font-size: 18px;
  color: #e74c3c;
  font-weight: bold;
  margin-bottom: 10px;
}

.favorite-icon {
  position: absolute;
  top: 15px;
  right: 15px;
  cursor: pointer;
  font-size: 24px;
  user-select: none;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  transition: transform 0.3s ease;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  width: 36px;
  height: 36px;
}

.heart-icon {
  stroke: red;
  transition: fill 0.3s ease, transform 0.3s ease;
  fill: none;
  stroke-width: 2;
}

.heart-icon.favorited {
  fill: red;
  animation: pulse 1s infinite;
  transform-origin: center;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    filter: drop-shadow(0 0 0 red);
  }
  50% {
    transform: scale(1.2);
    filter: drop-shadow(0 0 6px red);
  }
}

@media (max-width: 1200px) {
  .product-card {
    width: calc(25% - 30px);
  }
}

@media (max-width: 992px) {
  .product-card {
    width: calc(33.33% - 30px);
  }
}

@media (max-width: 768px) {
  .filters {
    flex-direction: column;
    align-items: center;
  }
  
  .product-card {
    width: calc(50% - 30px);
  }
}

@media (max-width: 480px) {
  .product-card {
    width: calc(100% - 30px);
  }
}
</style>
