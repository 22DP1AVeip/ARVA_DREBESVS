<template>
  <nav class="nav-bar">
    <div class="left-section">
      <a href="/" class="logo-container">
        <img src="/bildites/Logo_Arva.png" class="logoarva" alt="Logo" />
      </a>

      <button class="dropdown-button" @click="toggleDropdown" aria-label="Atvērt izvēlni">☰</button>
    </div>

    <div class="right-section">
      <Link href="/design" class="design-nav-btn">Dizaina veidotājs</Link>

      <button class="basket-button" @click="toggleCart" aria-label="Grozs">
        <img src="/bildites/basket_icon.png" alt="Grozs" class="basket-icon" />
        <span v-if="cartItemCount > 0" class="cart-count">{{ cartItemCount }}</span>
      </button>

      <div class="auth-buttons" v-if="!auth?.user">
        <Link href="/register" class="signup-button">Reģistrēties</Link>
        <Link href="/login" class="login-button">Ienākt</Link>
      </div>

      <div class="auth-logged-in" v-else>
        <button class="username-button" @click="toggleUserMenu">
          {{ auth.user.name }} ▾
        </button>

        <transition name="dropdown-fade">
          <div v-if="isUserMenuOpen" class="user-dropdown-menu">
            <Link href="/profile/settings" class="dropdown-item" @click="closeUserMenu">Profils</Link>
            <Link href="/profile/favorites" class="dropdown-item" @click="closeUserMenu">Favorīti</Link>
            <Link href="/profile/designs" class="dropdown-item" @click="closeUserMenu">Mani dizaini</Link>
            <Link v-if="auth.user?.is_admin" href="/admin" class="dropdown-item admin-item" @click="closeUserMenu">Admin panelis</Link>
            <button class="dropdown-item logout-item" @click="logout">Izrakstīties</button>
          </div>
        </transition>
      </div>
    </div>
  </nav>

  <transition name="dropdown-slide">
    <div v-if="isDropdownOpen" class="dropdown-menu">
      <button class="close-button" @click="toggleDropdown">✖</button>
      <div class="categories">
  <a href="#" class="category-link" @click.prevent="toggleCategory('woman')" :class="{ active: activeCategory === 'woman' }">
    <span>SIEVIEŠU</span>
  </a>
  <a href="#" class="category-link" @click.prevent="toggleCategory('men')" :class="{ active: activeCategory === 'men' }">
    <span>VĪRIEŠU</span>
  </a>
  <a href="#" class="category-link" @click.prevent="toggleCategory('accessories')" :class="{ active: activeCategory === 'accessories' }">
    <span>AKSESUĀRI</span>
  </a>
</div>

      <div class="menu-links">
        <h3>IZCĒLTI PRODUKTI</h3>
        <a href="#" class="menu-link">Populārākie</a>
        <a href="#" class="menu-link">Jaunumi</a>
        <a href="#" class="menu-link">Vispirktākie</a>
        <a href="#" class="menu-link">Limitētā kolekcija</a>
      </div>

      <div class="menu-links">
        <h3>KLIENTU ATBALSTS</h3>
        <a href="#" class="menu-link">BUJ</a>
        <a href="#" class="menu-link">Sazinies ar mums</a>
        <a href="#" class="menu-link">Atgriešana</a>
        <a href="#" class="menu-link">Piegādes informācija</a>
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
      <div class="cart-header">
        <h2 class="cart-title">Grozs</h2>
        <button class="cart-close" @click="toggleCart" aria-label="Aizvērt">x</button>
      </div>

      <p v-if="cartItemCount === 0" class="cart-empty">Grozs ir tukšs.</p>

      <div v-else class="cart-body">
        <div class="cart-items">
          <div v-for="item in cart.items" :key="item.id" class="cart-item">
            <img class="cart-thumb" :src="item.image || item.image_men || item.image_women" alt="" />

            <div class="cart-item-info">
              <div class="cart-item-name">{{ item.name }}</div>
              <div class="cart-item-meta">
                €{{ Number(item.price).toFixed(2) }} x {{ item.qty }}
              </div>
            </div>

            <button
              class="cart-remove"
              @click="router.post(`/cart/remove/${item.id}`, {}, { preserveScroll: true })"
              aria-label="Noņemt"
              title="Noņemt"
            >
              x
            </button>
          </div>
        </div>

        <div class="cart-summary">
          <div class="summary-row">
            <span>Kopā</span>
            <strong>€{{ cartSubtotal.toFixed(2) }}</strong>
          </div>
        </div>

        <div class="cart-actions">
          <Link href="/cart" class="btn btn-secondary" @click="toggleCart">Apskatīt grozu</Link>
          <Link href="/checkout" class="btn btn-primary" @click="toggleCart">Uz apmaksu</Link>
        </div>

        <button class="btn btn-link" @click="router.post('/cart/clear', {}, { preserveScroll: true })">
          Iztukšot grozu
        </button>
      </div>
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
  is_admin?: boolean;
}

interface PageProps extends InertiaPageProps {
  auth: {
    user: User | null;
  };
  [key: string]: any;
}

const page = usePage<PageProps>();
const auth = page.props.auth ?? { user: null };

const cart = computed(() => (page.props as any).cart ?? { count: 0, items: [] });
const cartItemCount = computed(() => cart.value.count ?? 0);
const cartSubtotal = computed(() =>
  (cart.value.items ?? []).reduce(
    (sum: number, item: any) => sum + Number(item.price) * Number(item.qty),
    0
  )
);

const isDropdownOpen = ref(false);
const activeCategory = ref("");
const isCartVisible = ref(false);
const isUserMenuOpen = ref(false);

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
}
</script>

<style scoped>
:global(:root) {
  --arva-ink: #072536;
  --arva-teal: #13c4ab;
  --arva-teal-dark: #06616d;
  --arva-pink: #de7388;
  --arva-purple: #97276b;
  --arva-bg: #ffffff;
  --arva-bg-soft: #f7fbfc;
  --arva-border: rgba(7, 37, 54, 0.12);
  --arva-shadow: 0 14px 40px rgba(7, 37, 54, 0.18);
}

.nav-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background-color: white;
  border-bottom: 1px solid #ddd;
  position: relative;
  z-index: 2000;
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

.design-nav-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 999px;
  border: 1.5px solid rgba(7, 37, 54, 0.15);
  background: #ffffff;
  color: #072536;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  white-space: nowrap;
  transition: background 0.2s, border-color 0.2s, transform 0.15s;
}

.design-nav-btn:hover {
  background: #072536;
  color: #ffffff;
  border-color: #072536;
  transform: translateY(-1px);
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
  color: black;
}

.user-dropdown-menu {
  position: absolute;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  min-width: 180px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
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

.design-item {
  color: #6c63ff;
  font-weight: 600;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}

.design-item:hover {
  background-color: #f0eeff;
}

.admin-item {
  color: #97276b;
  font-weight: 600;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}

.admin-item:hover {
  background-color: #fdf0f7;
}

.logout-item {
  border-top: 1px solid #ddd;
  color: #e74c3c;
  font-weight: 600;
  width: 100%;
  text-align: left;
  background: none;
  border-left: none;
  border-right: none;
  border-bottom: none;
}

.dropdown-menu {
  position: fixed;
  top: 60px;
  left: 0;
  right: 0;
  background: white;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
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

.cart-modal {
  position: fixed;
  top: 78px;
  right: 20px;
  width: min(420px, 92vw);
  max-height: calc(100vh - 110px);
  background: var(--arva-bg);
  color: var(--arva-ink);
  border: 1px solid var(--arva-border);
  border-radius: 18px;
  box-shadow: 0 24px 60px rgba(7, 37, 54, 0.22);
  overflow: hidden;
  z-index: 10001;
}

.cart-header {
  padding: 14px 16px 12px;
  border-bottom: 1px solid rgba(7, 37, 54, 0.08);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.cart-header::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--arva-teal), var(--arva-pink), var(--arva-purple));
  opacity: 0.35;
}

.cart-title {
  color: var(--arva-ink);
  font-weight: 900;
  letter-spacing: 0.2px;
  margin: 0;
}

.cart-empty {
  color: rgba(7, 37, 54, 0.7);
  margin: 10px 16px 16px;
}

.cart-body {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: calc(100vh - 210px);
  overflow: hidden;
}

.cart-items {
  padding: 12px 16px;
  overflow-y: auto;
}

.cart-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  background: var(--arva-bg-soft);
  border: 1px solid rgba(7, 37, 54, 0.08);
  border-radius: 12px;
}

.cart-thumb {
  width: 56px;
  height: 56px;
  object-fit: cover;
  border-radius: 12px;
  border: 1px solid rgba(7, 37, 54, 0.08);
}

.cart-item-info {
  flex: 1;
  min-width: 0;
}

.cart-item-name {
  color: var(--arva-ink);
  font-weight: 700;
}

.cart-item-meta {
  color: rgba(7, 37, 54, 0.7);
  font-size: 14px;
}

.cart-close {
  all: unset;
  cursor: pointer;
  width: 30px;
  height: 30px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #000;
  font-size: 20px;
  font-weight: 800;
  line-height: 1;
  border-radius: 999px;
}
.cart-close:hover { background: rgba(7, 37, 54, 0.08); }
.cart-close:active { background: rgba(7, 37, 54, 0.12); }

.cart-remove {
  all: unset;
  cursor: pointer;
  width: 26px;
  height: 26px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #000;
  font-size: 16px;
  font-weight: 800;
  line-height: 1;
  border-radius: 999px;
}
.cart-remove:hover { background: rgba(7, 37, 54, 0.08); }
.cart-remove:active { background: rgba(7, 37, 54, 0.12); }

.cart-close:focus-visible,
.cart-remove:focus-visible {
  outline: 2px solid rgba(19, 196, 171, 0.65);
  outline-offset: 2px;
}

.cart-summary {
  padding: 0 16px;
}

.summary-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 0;
}

.summary-row span { color: rgba(7, 37, 54, 0.75); }
.summary-row strong { color: var(--arva-ink); }

.cart-actions {
  padding: 0 16px 12px;
  display: flex;
  gap: 10px;
}

.btn {
  border-radius: 12px;
  font-weight: 900;
  letter-spacing: 0.2px;
  padding: 10px 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary {
  background: var(--arva-ink);
  border: 1px solid var(--arva-ink);
  color: #fff;
  transition: transform 120ms ease, opacity 180ms ease;
}
.btn-primary:hover { transform: translateY(-1px); }
.btn-primary:active { transform: translateY(0); opacity: 0.95; }

.btn-secondary {
  background: #fff;
  border: 1px solid rgba(19, 196, 171, 0.55);
  color: var(--arva-teal-dark);
  transition: transform 120ms ease, background 180ms ease;
}
.btn-secondary:hover { transform: translateY(-1px); background: rgba(19, 196, 171, 0.08); }
.btn-secondary:active { transform: translateY(0); }

.btn-link {
  color: rgba(7, 37, 54, 0.65);
  background: transparent;
  border: none;
  padding: 0 16px 16px;
  cursor: pointer;
  text-align: left;
}
.btn-link:hover { color: var(--arva-ink); }

.dropdown-fade-enter-active,
.dropdown-fade-leave-active { transition: opacity 0.2s ease; }
.dropdown-fade-enter-from,
.dropdown-fade-leave-to { opacity: 0; }

.dropdown-slide-enter-active { transition: transform 0.3s ease; }
.dropdown-slide-enter-from { transform: translateY(-15px); opacity: 0; }
.dropdown-slide-leave-to { transform: translateY(-15px); opacity: 0; }

.modal-fade-enter-active,
.modal-fade-leave-active { transition: opacity 0.25s ease; }
.modal-fade-enter-from,
.modal-fade-leave-to { opacity: 0; }

.blur-background {
  position: fixed;
  inset: 0;
  z-index: 999;
  background: rgba(0, 0, 0, 0.2);
  backdrop-filter: blur(2px);
}
</style>