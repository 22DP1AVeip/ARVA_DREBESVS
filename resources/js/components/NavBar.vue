<template>
  <nav class="nav-bar">
    <div class="left-section">
      <a href="/" class="logo-container">
        <img src="/bildites/Logo_Arva.png" class="logoarva" alt="Logo" />
      </a>
      <button class="dropdown button" @click="toggleDropdown" aria-label="Toggle Menu">☰</button> 
    </div>

    <div class="right-section">
      <button class="basket-button" @click="toggleCart" aria-label="Cart">
        <img src="/bildites/basket_icon.png" alt="Basket" class="basket-icon" />
        <span v-if="cartItemCount > 0" class="cart-count">{{ cartItemCount }}</span>
      </button>

      <div class="favorites-container" @mouseleave="showFavoritesBox = false">
        <button class="favorites-button" @click="toggleFavoritesBox" aria-label="Favorites">
          ❤️ Favorites ({{ favoriteCount }})
        </button>

        <transition name="fade">
          <div v-if="showFavoritesBox" class="favorites-box">
            <p v-if="favoriteProducts.length === 0" class="empty-msg">No favorites yet.</p>
            <ul v-else>
              <li v-for="product in favoriteProducts" :key="product.id" class="fav-item">
                <img :src="product.images[selectedGender]" alt="" class="fav-thumb" />
                <span>{{ product.name }}</span>
                <button @click="toggleFavorite(product.id)" class="remove-fav" aria-label="Remove favorite">✖</button>
              </li>
            </ul>
          </div>
        </transition>
      </div>

      <div class="auth-buttons" v-if="!auth?.user">
        <Link href="/register" class="signup-button">Sign Up</Link>
        <Link href="/login" class="login-button">Log In</Link>
      </div>

      <div class="auth-logged-in" v-else>
        <button class="username-button" @click="toggleUserMenu">
          {{ auth.user.name }} ▼
        </button>

        <transition name="dropdown-fade">
          <div v-if="isUserMenuOpen" class="user-dropdown-menu">
            <Link href="/profile/edit" class="dropdown-item" @click="closeUserMenu">Edit Profile</Link>
            <Link href="/favorites" class="dropdown-item" @click="closeUserMenu">Favorites</Link>
            <button class="dropdown-item logout-item" @click="logout">Logout</button>
          </div>
        </transition>
      </div>
    </div>
  </nav>

  <transition name="dropdown-slide">
    <div v-if="isDropdownOpen" class="dropdown-menu">
      <button class="close-button" @click="toggleDropdown">✖</button>
      <div class="categories">
        <a href="#" class="category-link" @click.prevent="toggleCategory('woman')" :class="{ active: activeCategory === 'woman' }">WOMAN</a>
        <a href="#" class="category-link" @click.prevent="toggleCategory('men')" :class="{ active: activeCategory === 'men' }">MEN</a>
        <a href="#" class="category-link" @click.prevent="toggleCategory('accessories')" :class="{ active: activeCategory === 'accessories' }">ACCESSORIES</a>
      </div>
      <div class="menu-links">
        <h3>FEATURED PRODUCTS</h3>
        <a href="#" class="menu-link">Top Picks</a>
        <a href="#" class="menu-link">New Arrivals</a>
        <a href="#" class="menu-link">Best Sellers</a>
        <a href="#" class="menu-link">Limited Edition</a>
      </div>
      <div class="menu-links">
        <h3>CUSTOMER SUPPORT</h3>
        <a href="#" class="menu-link">FAQ</a>
        <a href="#" class="menu-link">Contact Us</a>
        <a href="#" class="menu-link">Returns</a>
        <a href="#" class="menu-link">Shipping Info</a>
      </div>
    </div>
  </transition>

  <div
    v-if="isDropdownOpen || isCartVisible || isUserMenuOpen"
    class="blur-background"
    @click="closeAllMenus"
  ></div>

  <transition name="modal-fade">
    <div v-if="isCartVisible" class="cart-modal">
      <h2>Your Cart</h2>
      <p>Items in Cart: {{ cartItemCount }}</p>
      <button @click="toggleCart">Close</button>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { Link, usePage, useForm, router } from "@inertiajs/vue3";
import type { PageProps as InertiaPageProps } from "@inertiajs/core";

interface User {
  id: number;
  name: string;
  email: string;
}

interface Product {
  id: number;
  name: string;
  images: {
    men: string;
    women?: string;
  };
}

interface PageProps extends InertiaPageProps {
  auth: {
    user: User | null;
  };
  [key: string]: any;
}

// ✅ Props tagad NAV obligāti (ar defaultiem)
const props = withDefaults(
  defineProps<{
    favorites?: number[];
    toggleFavorite?: (id: number) => void;
    products?: Product[];
  }>(),
  {
    favorites: () => [],
    toggleFavorite: () => () => {},
    products: () => [],
  }
);

const auth = usePage<PageProps>().props.auth ?? { user: null };

const selectedGender = ref<"men" | "women">("men");

const isDropdownOpen = ref(false);
const activeCategory = ref("");
const cartItemCount = ref(0);
const isCartVisible = ref(false);
const isUserMenuOpen = ref(false);
const showFavoritesBox = ref(false);

function logout() {
  const form = useForm({});
  form.post("/logout", {
    preserveScroll: true,
    onSuccess: () => {
      router.reload();
      isUserMenuOpen.value = false;
    },
  });
}

function toggleDropdown() {
  isDropdownOpen.value = !isDropdownOpen.value;
}

function toggleCategory(category: string) {
  activeCategory.value = category;
}

function toggleCart() {
  isCartVisible.value = !isCartVisible.value;
}

function toggleUserMenu() {
  isUserMenuOpen.value = !isUserMenuOpen.value;
}

function closeUserMenu() {
  isUserMenuOpen.value = false;
}

function closeAllMenus() {
  isDropdownOpen.value = false;
  isCartVisible.value = false;
  isUserMenuOpen.value = false;
  showFavoritesBox.value = false;
}

function toggleFavoritesBox() {
  showFavoritesBox.value = !showFavoritesBox.value;
}

const favoriteProducts = computed(() => {
  const prods = props.products ?? [];
  const favs = props.favorites ?? [];
  return prods.filter((product) => favs.includes(product.id));
});

const favoriteCount = computed(() => (props.favorites?.length ?? 0));
</script>


<style scoped>
.nav-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background-color: white;
  border-bottom: 1px solid #ddd;
  position: relative;
  z-index: 1000;
}

.left-section {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-container img.logoarva {
  height: 70px;
  width: auto;
}

.dropdown-button {
  font-size: 26px;
  background: none;
  border: none;
  cursor: pointer;
  color: black;
  display: none;
}

.right-section {
  display: flex;
  align-items: center;
  gap: 20px;
}

.basket-button {
  position: relative;
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
}

.basket-icon {
  width: 32px;
  height: 32px;
  filter: brightness(0);
}

.cart-count {
  position: absolute;
  top: -5px;
  right: -5px;
  background: red;
  color: white;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 12px;
}

.favorites-container {
  position: relative;
  display: inline-block;
}

.favorites-button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  padding: 5px 10px;
  color: #e74c3c;
  user-select: none;
}

.favorites-box {
  position: absolute;
  top: 110%;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  width: 250px;
  max-height: 300px;
  overflow-y: auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  padding: 10px;
  z-index: 100;
}

.fav-item {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  padding: 5px;
  border-bottom: 1px solid #eee;
}

.fav-thumb {
  width: 40px;
  height: 40px;
  object-fit: cover;
  margin-right: 10px;
  border-radius: 4px;
}

.remove-fav {
  margin-left: auto;
  background: none;
  border: none;
  cursor: pointer;
  color: #e74c3c;
  font-weight: bold;
  font-size: 18px;
  user-select: none;
}

.empty-msg {
  color: #888;
  font-style: italic;
  text-align: center;
  padding: 20px 0;
}

.auth-buttons {
  display: flex;
  gap: 10px;
}

.auth-buttons .signup-button,
.auth-buttons .login-button {
  padding: 6px 12px;
  border: none;
  background-color: #333;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  transition: background-color 0.3s;
}

.auth-buttons .signup-button:hover,
.auth-buttons .login-button:hover {
  background-color: #555;
}

.auth-logged-in {
  position: relative;
}

.username-button {
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 600;
  font-size: 16px;
  user-select: none;
  color: black; /* added to make username text visible */
}

.user-dropdown-menu {
  position: absolute;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  min-width: 140px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.15);
  z-index: 1000;
}

.dropdown-item {
  display: block;
  padding: 10px 15px;
  color: #333;
  cursor: pointer;
  text-decoration: none;
  white-space: nowrap;
}

.dropdown-item:hover {
  background-color: #f0f0f0;
}

.logout-item {
  border-top: 1px solid #ddd;
  color: #e74c3c;
  font-weight: 600;
}

.dropdown-menu {
  position: fixed;
  top: 60px;
  left: 0;
  right: 0;
  background: white;
  box-shadow: 0 2px 12px rgba(0,0,0,0.2);
  padding: 20px;
  z-index: 10000;
}

.categories {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin-bottom: 20px;
}

.category-link {
  font-weight: 600;
  cursor: pointer;
  color: black;
  text-decoration: none;
  padding-bottom: 4px;
  border-bottom: 2px solid transparent;
  user-select: none;
}

.category-link.active {
  border-color: #333;
}

.menu-links {
  margin-top: 15px;
}

.menu-link {
  display: block;
  margin: 6px 0;
  color: #333;
  text-decoration: none;
  cursor: pointer;
}

.menu-link:hover {
  text-decoration: underline;
}

.close-button {
  float: right;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.blur-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  backdrop-filter: blur(4px);
  background-color: rgba(0, 0, 0, 0.15);
  z-index: 999;
}

.cart-modal {
  position: fixed;
  top: 80px;
  right: 40px;
  background: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 3px 15px rgba(0,0,0,0.3);
  z-index: 1001;
  width: 300px;
  max-height: 70vh;
  overflow-y: auto;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.dropdown-fade-enter-active, .dropdown-fade-leave-active {
  transition: opacity 0.2s ease;
}
.dropdown-fade-enter-from, .dropdown-fade-leave-to {
  opacity: 0;
}

.dropdown-slide-enter-active {
  transition: transform 0.3s ease;
}
.dropdown-slide-enter-from {
  transform: translateY(-15px);
  opacity: 0;
}
.dropdown-slide-leave-to {
  transform: translateY(-15px);
  opacity: 0;
}

.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
</style>
