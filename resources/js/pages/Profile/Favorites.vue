<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage<any>();
const favorites = computed<number[]>(() => page.props.favorites ?? []);
const products = computed<any[]>(() => page.props.products ?? []);

function remove(productId: number) {
  router.post(`/favorites/toggle/${productId}`, {}, { preserveScroll: true });
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="head">
        <h1 class="title">Favorīti</h1>
        <Link href="/profile/settings" class="back">← Atpakaļ uz profilu</Link>
      </div>

      <p v-if="products.length === 0" class="muted">
        Te vēl nav favorītu.
      </p>

      <div v-else class="grid">
        <div v-for="p in products" :key="p.id" class="card">
          <Link :href="`/product/${p.id}`" class="card-link">
            <img
              class="img"
              :src="p.image_men || p.image_women || (p.images?.men ?? '')"
              alt=""
            />
            <div class="name">{{ p.name }}</div>
            <div class="price">€{{ Number(p.price).toFixed(2) }}</div>
          </Link>

          <button class="remove" @click="remove(p.id)" aria-label="Noņemt no favorītiem">
            Noņemt
          </button>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
:global(:root) {
  --arva-ink: #072536;
  --arva-teal: #13c4ab;
  --arva-pink: #de7388;
  --arva-purple: #97276b;
  --arva-bg: #ffffff;
  --arva-bg-soft: #f7fbfc;
  --arva-border: rgba(7, 37, 54, 0.12);
  --arva-shadow: 0 14px 40px rgba(7, 37, 54, 0.18);
}

.wrap { max-width: 1100px; margin: 0 auto; padding: 22px 16px 40px; color: var(--arva-ink); }
.head { display:flex; justify-content:space-between; align-items:baseline; gap:12px; margin-bottom: 14px; }
.title { margin:0; font-size:32px; font-weight:900; }
.back { color: rgba(7,37,54,.7); text-decoration:none; font-weight:800; }
.back:hover { color: var(--arva-ink); }

.muted { color: rgba(7,37,54,.65); font-weight:800; }

.grid {
  display:grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}
@media (max-width: 980px) { .grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 520px) { .grid { grid-template-columns: 1fr; } }

.card {
  background: var(--arva-bg);
  border: 1px solid var(--arva-border);
  border-radius: 14px;
  box-shadow: var(--arva-shadow);
  overflow: hidden;
}

.card-link { display:block; text-decoration:none; color:inherit; padding: 12px; }
.img { width:100%; height: 180px; object-fit: cover; border-radius: 12px; background: var(--arva-bg-soft); }
.name { margin-top: 10px; font-weight: 900; }
.price { margin-top: 4px; color: rgba(7,37,54,.7); font-weight: 800; }

.remove {
  width: 100%;
  border: 0;
  border-top: 1px solid rgba(7,37,54,.10);
  background: var(--arva-bg-soft);
  padding: 10px 12px;
  cursor: pointer;
  font-weight: 900;
  color: var(--arva-ink);
}
.remove:hover { background: rgba(222,115,136,.10); }
</style>
