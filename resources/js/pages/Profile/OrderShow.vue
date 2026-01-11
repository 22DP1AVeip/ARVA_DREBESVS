<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage<any>();
const order = computed(() => page.props.order);

function labelStatus(s: string) {
  const v = (s || "").toLowerCase();
  if (v === "processing") return "Apstrādē";
  if (v === "shipped") return "Nosūtīts";
  if (v === "delivered") return "Piegādāts";
  if (v === "cancelled") return "Atcelts";
  return s;
}
</script>

<template>
  <main>
    <NavBar />
    <div style="max-width: 900px; margin: 0 auto; padding: 24px;">
      <div style="display:flex;justify-content:space-between;align-items:baseline;gap:10px;">
        <h1>Pasūtījums #{{ order.id }}</h1>
        <div style="font-weight:900;">Statuss: {{ labelStatus(order.status) }}</div>
      </div>

      <div style="border:1px solid #eee;border-radius:14px;background:#fff;padding:16px;margin-top:12px;">
        <h2 style="margin:0 0 10px;">Piegādes dati</h2>
        <div><strong>Vārds:</strong> {{ order.full_name }}</div>
        <div><strong>E-pasts:</strong> {{ order.email }}</div>
        <div v-if="order.phone"><strong>Telefons:</strong> {{ order.phone }}</div>
        <div style="margin-top:8px;">
          <strong>Adrese:</strong> {{ order.address }}, {{ order.city }}, {{ order.postcode }}, {{ order.country }}
        </div>
      </div>

      <div style="border:1px solid #eee;border-radius:14px;background:#fff;padding:16px;margin-top:12px;">
        <h2 style="margin:0 0 10px;">Preces</h2>

        <div v-for="it in order.items" :key="it.id"
             style="display:flex;justify-content:space-between;gap:10px;padding:10px 0;border-bottom:1px solid #f1f1f1;">
          <div>
            <div style="font-weight:900;">{{ it.name }}</div>
            <div style="color:#666;font-weight:700;font-size:13px;">
              €{{ Number(it.price).toFixed(2) }} × {{ it.qty }}
            </div>
          </div>
          <div style="font-weight:900;">
            €{{ (Number(it.price) * Number(it.qty)).toFixed(2) }}
          </div>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:12px;">
          <span style="font-weight:700;">Kopā</span>
          <span style="font-weight:900;">€{{ Number(order.total).toFixed(2) }}</span>
        </div>
      </div>

      <div style="margin-top:14px; display:flex; gap:12px;">
        <Link href="/profile/orders" style="text-decoration:underline;">← Atpakaļ uz pasūtījumiem</Link>
      </div>
    </div>
    <Footer />
  </main>
</template>
