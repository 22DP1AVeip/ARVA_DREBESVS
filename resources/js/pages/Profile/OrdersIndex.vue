<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage<any>();
const orders = page.props.orders ?? [];

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
      <h1>Pasūtījumi</h1>

      <p v-if="orders.length === 0">Nav pasūtījumu.</p>

      <div v-else style="display:grid; gap:10px;">
        <Link
          v-for="o in orders"
          :key="o.id"
          :href="`/profile/orders/${o.id}`"
          style="text-decoration:none;color:inherit;"
        >
          <div style="border:1px solid #eee;padding:12px;border-radius:12px;background:#fff;display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div style="font-weight:900;">Pasūtījums #{{ o.id }}</div>
              <div style="color:#666;font-weight:700;font-size:13px;">
                Datums: {{ String(o.created_at).slice(0,10) }}
              </div>
            </div>
            <div style="text-align:right;">
              <div style="font-weight:900;">€{{ Number(o.total).toFixed(2) }}</div>
              <div style="color:#072536;font-weight:900;font-size:13px;">
                {{ labelStatus(o.status) }}
              </div>
            </div>
          </div>
        </Link>
      </div>

      <div style="margin-top:14px;">
        <Link href="/profile/settings" style="text-decoration:underline;">← Atpakaļ uz profilu</Link>
      </div>
    </div>
    <Footer />
  </main>
</template>
