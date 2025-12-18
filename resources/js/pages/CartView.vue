<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

const page = usePage();
const cart = computed(() => (page.props as any).cart ?? { count: 0, items: [] });

const subtotal = computed(() =>
  (cart.value.items ?? []).reduce((s: number, i: any) => s + Number(i.price) * Number(i.qty), 0)
);
</script>

<template>
  <main>
    <NavBar />
    <div style="max-width: 900px; margin: 0 auto; padding: 24px;">
      <h1>Grozs</h1>

      <p v-if="cart.count === 0">Grozs ir tukšs.</p>

      <div v-else>
        <div
          v-for="item in cart.items"
          :key="item.id"
          style="display:flex;gap:12px;align-items:center;border:1px solid #eee;padding:12px;border-radius:10px;margin-bottom:10px;background:#fff;"
        >
          <img
            :src="item.image_men || item.image_women"
            style="width:70px;height:70px;object-fit:cover;border-radius:10px;border:1px solid #eee;"
          />
          <div style="flex:1;">
            <div style="font-weight:800;">{{ item.name }}</div>
            <div style="color:#555;">€{{ Number(item.price).toFixed(2) }} × {{ item.qty }}</div>
          </div>
          <Link :href="`/product/${item.id}`" style="text-decoration:underline;">Atvērt</Link>
        </div>

        <div style="display:flex;justify-content:space-between;border-top:1px solid #eee;padding-top:12px;margin-top:12px;">
          <span style="font-weight:700;">Kopā</span>
          <span style="font-weight:900;">€{{ subtotal.toFixed(2) }}</span>
        </div>

        <div style="margin-top:14px; display:flex; gap:10px;">
          <Link href="/checkout" style="background:#111;color:#fff;padding:12px 14px;border-radius:10px;font-weight:800;text-decoration:none;">
            Uz apmaksu
          </Link>
        </div>
      </div>
    </div>
    <Footer />
  </main>
</template>
