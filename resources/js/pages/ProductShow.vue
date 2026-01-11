<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed, ref } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";

type Gender = "men" | "women";

interface DbProduct {
  id: number;
  name: string;
  price: string | number;
  category: string;
  gender: string;
  image_men: string | null;
  image_women: string | null;
}

const page = usePage<any>();

const product = computed(() => page.props.product as DbProduct);
const view = computed(() => (page.props.view as Gender) ?? "men");

// favorites IDs no Inertia shared
const favoritesIds = computed<number[]>(() => page.props.favoritesIds ?? []);
const isFavorite = computed(() => favoritesIds.value.includes(product.value.id));

// sirsniņas animācija
const heartBump = ref(false);

function toggleFavorite() {
  heartBump.value = true;
  setTimeout(() => (heartBump.value = false), 200);

  router.post(`/favorites/toggle/${product.value.id}`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      // pārlādē tikai favoritesIds no servera
      router.reload({ only: ["favoritesIds"] });
    },
  });
}

// tikai viena bilde atkarībā no view
const selectedImage = computed(() => {
  if (view.value === "women") {
    return product.value.image_women ?? product.value.image_men ?? "";
  }
  return product.value.image_men ?? product.value.image_women ?? "";
});

// atpakaļ links
const backHref = computed(() =>
  view.value === "women" ? "/WomanWear" : "/MenWear"
);

const flashGrey = ref(false);

function addToCart() {
  flashGrey.value = true;
  setTimeout(() => (flashGrey.value = false), 450);

  router.post(`/cart/add/${product.value.id}`, { qty: 1 }, { preserveScroll: true });
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="breadcrumbs">
        <Link :href="backHref">Atpakaļ uz produktiem</Link>
      </div>

      <div class="grid">
        <!-- LEFT -->
        <div class="images">
          <div class="main-img">
            <img :src="selectedImage" :alt="product.name" />
          </div>
        </div>

        <!-- RIGHT -->
        <div class="info">
          
          <!-- TITLE + HEART -->
          <div class="title-row">
            <h1 class="title">{{ product.name }}</h1>

            <!-- ❤️ FAVORITE BUTTON -->
            <button
              class="heart"
              :class="{ active: isFavorite, bump: heartBump }"
              @click="toggleFavorite"
              aria-label="Favorīts"
            >
              <svg viewBox="0 0 24 24" class="heart-ico">
                <path
                  d="M12 21s-7-4.6-10-9.4C.5 8.7 2.3 5.5 5.8 5.1c1.8-.2 3.5.7 4.4 2 1-1.3 2.6-2.2 4.4-2 3.5.4 5.3 3.6 3.8 6.5C19 16.4 12 21 12 21z"
                />
              </svg>
            </button>
          </div>

          <div class="price">€{{ Number(product.price).toFixed(2) }}</div>

          <div class="meta">
            <div><strong>Kategorija:</strong> {{ product.category }}</div>
          </div>

          <hr class="sep" />

          <div class="option">
            <div class="label">Krāsa</div>
            <div class="placeholder">Drīzumā (variants)</div>
          </div>

          <div class="option">
            <div class="label">Izmērs</div>
            <div class="sizes">
              <button class="size" disabled>XS</button>
              <button class="size" disabled>S</button>
              <button class="size" disabled>M</button>
              <button class="size" disabled>L</button>
              <button class="size" disabled>XL</button>
            </div>
            <div class="hint">Izmēri un noliktava būs pēc variantu sistēmas.</div>
          </div>

          <button class="add" :class="{ flash: flashGrey }" @click="addToCart">
            Pievienot grozam
          </button>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
/* ---- tava esošā CSS ---- */
.wrap { max-width:1200px;margin:0 auto;padding:24px; }
.breadcrumbs { margin-bottom:16px; }
.grid { display:grid;grid-template-columns:1.2fr 1fr;gap:28px; }
@media (max-width:900px){ .grid{grid-template-columns:1fr;} }
.images .main-img{background:#fff;border-radius:12px;overflow:hidden;border:1px solid #eee;}
.images .main-img img{width:100%;height:520px;object-fit:cover;}
.info{background:#fff;border-radius:12px;border:1px solid #eee;padding:18px;}
.title{font-size:22px;font-weight:700;margin-bottom:8px;}
.price{font-size:20px;font-weight:700;margin-bottom:12px;color:#e74c3c;}
.meta{color:#333;font-size:14px;}
.sep{border:none;border-top:1px solid #eee;margin:16px 0;}
.option{margin-bottom:14px;}
.label{font-weight:600;margin-bottom:6px;}
.placeholder{color:#666;font-size:14px;}
.sizes{display:flex;gap:8px;flex-wrap:wrap;}
.size{padding:10px 12px;border-radius:10px;border:1px solid #ddd;background:#fafafa;}
.hint{margin-top:8px;font-size:12px;color:#777;}
.add{width:100%;margin-top:12px;padding:14px;border-radius:10px;border:2px solid #111;background:#111;color:#fff;font-weight:800;font-size:16px;cursor:pointer;}
.add.flash{background:#8a8a8a;border-color:#8a8a8a;}

/* ---- HEART STYLE ---- */

.title-row{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:10px;
}

.heart{
  width:42px;
  height:42px;
  border-radius:999px;
  border:1px solid rgba(0,0,0,.15);
  background:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition:transform .15s ease;
}

.heart-ico{
  width:22px;
  height:22px;
}

.heart-ico path{
  fill: transparent;
  stroke:#000;
  stroke-width:1.8;
  transition: fill .2s ease;
}

/* active = favorīts */
.heart.active .heart-ico path{
  fill:#de7388;   /* ARVA pink */
  stroke:#000;
}

/* pop animation */
.heart.bump{
  transform: scale(1.15);
}
</style>
