<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage<any>();
const order        = computed(() => page.props.order);
const pointsEarned = computed<number>(() => page.props.pointsEarned ?? 0);
const userPoints   = computed<number>(() => page.props.userPoints ?? 0);

function labelStatus(s: string) {
  const v = (s || "").toLowerCase();
  if (v === "processing") return "Apstrādē";
  if (v === "shipped")    return "Nosūtīts";
  if (v === "delivered")  return "Piegādāts";
  if (v === "cancelled")  return "Atcelts";
  return s;
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">

      <!-- PUNKTU BANNERIS -->
      <div v-if="pointsEarned > 0" class="points-banner">
        <div class="points-banner-icon">⭐</div>
        <div class="points-banner-text">
          <div class="points-banner-title">Nopelnīji {{ pointsEarned }} punktus!</div>
          <div class="points-banner-sub">
            Tavs kopējais atlikums: <strong>{{ userPoints }} pts</strong> —
            <Link href="/profile/coupons" class="points-banner-link">apskatīt kuponus →</Link>
          </div>
        </div>
      </div>

      <!-- VIRSRAKSTS -->
      <div class="head">
        <div>
          <div class="order-id">Pasūtījums #{{ order.id }}</div>
          <div class="order-date" v-if="order.created_at">
            {{ new Date(order.created_at).toLocaleDateString("lv-LV", { year: "numeric", month: "long", day: "numeric" }) }}
          </div>
        </div>
        <span class="status-badge" :class="`status-${order.status}`">
          {{ labelStatus(order.status) }}
        </span>
      </div>

      <div class="grid">
        <!-- PRECES -->
        <div class="card">
          <h2 class="card-title">Preces</h2>

          <div v-for="it in order.items" :key="it.id" class="item">
            <div class="item-info">
              <div class="item-name">{{ it.name }}</div>
              <div class="item-meta">
                <span v-if="it.color">{{ it.color }}</span>
                <span v-if="it.color && it.size"> · </span>
                <span v-if="it.size">{{ it.size }}</span>
                <span> · €{{ Number(it.price).toFixed(2) }} × {{ it.qty }}</span>
              </div>
            </div>
            <div class="item-total">€{{ (Number(it.price) * Number(it.qty)).toFixed(2) }}</div>
          </div>

          <div class="total-row">
            <span>Kopā samaksāts</span>
            <strong>€{{ Number(order.total).toFixed(2) }}</strong>
          </div>
        </div>

        <!-- PIEGĀDES DATI -->
        <div class="card">
          <h2 class="card-title">Piegādes dati</h2>
          <div class="info-row"><span>Vārds</span><span>{{ order.full_name }}</span></div>
          <div class="info-row"><span>E-pasts</span><span>{{ order.email }}</span></div>
          <div class="info-row" v-if="order.phone"><span>Telefons</span><span>{{ order.phone }}</span></div>
          <div class="info-row"><span>Adrese</span><span>{{ order.address }}</span></div>
          <div class="info-row"><span>Pilsēta</span><span>{{ order.city }}, {{ order.postcode }}</span></div>
          <div class="info-row"><span>Valsts</span><span>{{ order.country }}</span></div>
        </div>
      </div>

      <div class="footer-links">
        <Link href="/profile/orders" class="back-link">← Visi pasūtījumi</Link>
        <Link href="/" class="shop-link">Turpināt iepirkties →</Link>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap {
  max-width: 900px;
  margin: 0 auto;
  padding: 28px 16px 56px;
  color: #072536;
}

/* PUNKTU BANNERIS */
.points-banner {
  display: flex;
  align-items: center;
  gap: 16px;
  background: linear-gradient(120deg, #072536, #97276b);
  color: #fff;
  border-radius: 16px;
  padding: 20px 24px;
  margin-bottom: 24px;
}
.points-banner-icon { font-size: 36px; flex-shrink: 0; }
.points-banner-title { font-size: 20px; font-weight: 900; margin-bottom: 4px; }
.points-banner-sub { font-size: 14px; opacity: .85; }
.points-banner-link { color: #13c4ab; font-weight: 700; text-decoration: none; }
.points-banner-link:hover { text-decoration: underline; }

/* VIRSRAKSTS */
.head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 20px;
}
.order-id   { font-size: 24px; font-weight: 900; }
.order-date { font-size: 13px; color: rgba(7,37,54,.6); font-weight: 600; margin-top: 3px; }

.status-badge {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 700;
  border: 1.5px solid rgba(7,37,54,.15);
  background: #fff;
  white-space: nowrap;
}
.status-processing { border-color: rgba(222,115,136,.5); color: #97276b; background: rgba(222,115,136,.08); }
.status-shipped    { border-color: rgba(19,196,171,.5);  color: #06616d; background: rgba(19,196,171,.08); }
.status-delivered  { border-color: rgba(39,174,96,.4);   color: #1e8449; background: rgba(39,174,96,.08); }
.status-cancelled  { border-color: rgba(192,57,43,.3);   color: #c0392b; background: rgba(192,57,43,.06); }

/* GRID */
.grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 14px;
}
@media (max-width: 720px) { .grid { grid-template-columns: 1fr; } }

/* KARTE */
.card {
  background: #fff;
  border: 1px solid rgba(7,37,54,.1);
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 6px 24px rgba(7,37,54,.07);
}
.card-title { font-size: 16px; font-weight: 900; margin-bottom: 14px; }

/* PRECES */
.item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid rgba(7,37,54,.07);
}
.item:last-of-type { border-bottom: none; }
.item-name { font-weight: 700; font-size: 15px; }
.item-meta { font-size: 13px; color: rgba(7,37,54,.6); margin-top: 3px; }
.item-total { font-weight: 900; white-space: nowrap; }

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 14px;
  padding-top: 12px;
  border-top: 2px solid rgba(7,37,54,.1);
  font-size: 16px;
}

/* PIEGĀDES DATI */
.info-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px solid rgba(7,37,54,.06);
  font-size: 14px;
}
.info-row:last-child { border-bottom: none; }
.info-row span:first-child { color: rgba(7,37,54,.55); font-weight: 600; }
.info-row span:last-child  { font-weight: 700; text-align: right; }

/* LINKS */
.footer-links {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
}
.back-link { font-weight: 700; color: rgba(7,37,54,.65); text-decoration: none; font-size: 14px; }
.back-link:hover { color: #072536; }
.shop-link { font-weight: 700; color: #06616d; text-decoration: none; font-size: 14px; }
.shop-link:hover { text-decoration: underline; }
</style>
