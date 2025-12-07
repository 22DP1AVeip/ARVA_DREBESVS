import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useProductStore = defineStore('productStore', () => {
  // Favorites stored as array of product IDs
  const favorites = ref<number[]>([]);

  // Load favorites from localStorage on store init
  if (typeof window !== 'undefined') {
    const saved = localStorage.getItem('favorites');
    if (saved) {
      favorites.value = JSON.parse(saved);
    }
  }

  // Toggle favorite: add if not exists, remove if exists
  function toggleFavorite(id: number) {
    if (favorites.value.includes(id)) {
      favorites.value = favorites.value.filter(favId => favId !== id);
    } else {
      favorites.value.push(id);
    }
    localStorage.setItem('favorites', JSON.stringify(favorites.value));
  }

  // Check if product is favorited
  function isFavorited(id: number) {
    return favorites.value.includes(id);
  }

  return { favorites, toggleFavorite, isFavorited };
});
