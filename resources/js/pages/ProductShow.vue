<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

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

const page = usePage();

const product = computed(() => page.props.product as DbProduct);
const view = computed(() => (page.props.view as Gender) ?? "men");

// ✅ tikai viena bilde atkarībā no view
const selectedImage = computed(() => {
  if (view.value === "women") {
    return product.value.image_women ?? product.value.image_men ?? "";
  }
  return product.value.image_men ?? product.value.image_women ?? "";
});

// ✅ atpakaļ link (pēc view)
const backHref = computed(() => (view.value === "women" ? "/WomanWear" : "/MenWear"));
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="breadcrumbs">
        <Link :href="backHref">Atpakaļ uz produktiem</Link>
      </div>

      <div class="grid">
        <!-- LEFT: image -->
        <div class="images">
          <div class="main-img">
            <img :src="selectedImage" :alt="product.name" />
          </div>
        </div>

        <!-- RIGHT: info -->
        <div class="info">
          <h1 class="title">{{ product.name }}</h1>
          <div class="price">${{ Number(product.price).toFixed(2) }}</div>

          <div class="meta">
            <div><strong>Kategorija:</strong> {{ product.category }}</div>
          </div>

          <hr class="sep" />

          <!-- Sagataves nākotnei -->
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
            <div class="hint">Izmēri un noliktava būs pēc variants/stock sistēmas.</div>
          </div>

          <button class="add" disabled>Pievienot grozam (drīzumā)</button>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
}
.breadcrumbs {
  margin-bottom: 16px;
}
.grid {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 28px;
}
@media (max-width: 900px) {
  .grid { grid-template-columns: 1fr; }
}
.images .main-img {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid #eee;
}
.images .main-img img {
  width: 100%;
  height: 520px;
  object-fit: cover;
  display: block;
}
.info {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #eee;
  padding: 18px;
}
.title {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 8px;
}
.price {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 12px;
  color: #e74c3c;
}
.meta {
  color: #333;
  font-size: 14px;
}
.sep {
  border: none;
  border-top: 1px solid #eee;
  margin: 16px 0;
}
.option { margin-bottom: 14px; }
.label { font-weight: 600; margin-bottom: 6px; }
.placeholder { color: #666; font-size: 14px; }
.sizes { display: flex; gap: 8px; flex-wrap: wrap; }
.size {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #ddd;
  background: #fafafa;
}
.hint { margin-top: 8px; font-size: 12px; color: #777; }
.add {
  width: 100%;
  margin-top: 12px;
  padding: 14px 16px;
  border-radius: 12px;
  border: none;
  background: #111;
  color: #fff;
  font-weight: 700;
  opacity: 0.6;
}
</style>
