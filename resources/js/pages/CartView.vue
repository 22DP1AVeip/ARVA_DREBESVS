<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";

const page = usePage();
const cart = computed(() => (page.props as any).cart ?? { count: 0, items: [] });

const subtotal = computed(() =>
  (cart.value.items ?? []).reduce(
    (s: number, i: any) => s + Number(i.price) * Number(i.qty),
    0
  )
);

function updateQty(item: any, diff: number) {
  const newQty = item.qty + diff;
  if (newQty < 1) return;

  router.post(
    `/cart/update/${item.id}`,
    { qty: newQty },
    { preserveScroll: true }
  );
}

function removeItem(item: any) {
  router.post(
    `/cart/remove/${item.id}`,
    {},
    { preserveScroll: true }
  );
}
</script>

<template>
  <main class="cart-page">
    <NavBar />

    <div class="cart-wrap">
      <header class="cart-header">
        <div>
          <h1>Grozs</h1>
          <p class="sub">Pārskati un pielāgo savus pirkumus.</p>
        </div>
        <Link href="/checkout" class="cta" v-if="cart.count > 0">Uz apmaksu</Link>
      </header>

      <p v-if="cart.count === 0" class="empty">Grozs ir tukšs.</p>

      <div v-else class="cart-grid">
        <section class="items">
          <article
            v-for="item in cart.items"
            :key="item.id"
            class="cart-item"
          >
            <img
              :src="item.image_men || item.image_women"
              class="cart-thumb"
              :alt="item.name"
            />

            <div class="item-info">
              <div class="item-name">{{ item.name }}</div>
              <div class="item-price">€{{ Number(item.price).toFixed(2) }}</div>
            </div>

            <div class="qty-box">
              <button
                class="qty-btn"
                @click="updateQty(item, -1)"
                :disabled="item.qty <= 1"
                aria-label="Samazināt daudzumu"
              >
                -
              </button>

              <span class="qty-val">{{ item.qty }}</span>

              <button
                class="qty-btn"
                @click="updateQty(item, +1)"
                aria-label="Palielināt daudzumu"
              >
                +
              </button>
            </div>

            <div class="line-total">
              €{{ (item.price * item.qty).toFixed(2) }}
            </div>

            <button
              class="remove-btn"
              @click="removeItem(item)"
              aria-label="Noņemt"
              title="Noņemt"
            >
              ✖
            </button>
          </article>
        </section>

        <aside class="summary">
          <h2>Kopsavilkums</h2>
          <div class="summary-row">
            <span>Starpsumma</span>
            <strong>€{{ subtotal.toFixed(2) }}</strong>
          </div>
          <div class="summary-row muted">
            <span>Piegāde</span>
            <span>Aprēķināsim vēlāk</span>
          </div>
          <div class="summary-row total">
            <span>Kopā</span>
            <strong>€{{ subtotal.toFixed(2) }}</strong>
          </div>
          <Link href="/checkout" class="cta full">Uz apmaksu</Link>
        </aside>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.cart-page {
  background: #f7fbfc;
}

.cart-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding: 32px 16px 48px;
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 16px;
  margin-bottom: 20px;
}

.cart-header h1 {
  margin: 0 0 6px;
  font-size: 32px;
  font-weight: 900;
  color: #072536;
}

.sub {
  margin: 0;
  color: rgba(7, 37, 54, 0.7);
  font-weight: 600;
}

.empty {
  padding: 18px;
  background: #ffffff;
  border-radius: 14px;
  border: 1px solid rgba(7, 37, 54, 0.12);
}

.cart-grid {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 16px;
}

.items {
  display: grid;
  gap: 12px;
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto auto auto;
  align-items: center;
  gap: 14px;
  border: 1px solid rgba(7, 37, 54, 0.12);
  padding: 12px;
  border-radius: 14px;
  background: #fff;
  box-shadow: 0 10px 22px rgba(7, 37, 54, 0.06);
}

.cart-thumb {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 12px;
  border: 1px solid rgba(7, 37, 54, 0.08);
}

.item-info {
  display: grid;
  gap: 6px;
}

.item-name {
  font-weight: 800;
  color: #072536;
}

.item-price {
  color: rgba(7, 37, 54, 0.7);
  font-size: 14px;
}

.qty-box {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 8px;
  border-radius: 999px;
  border: 1px solid rgba(7, 37, 54, 0.12);
  background: #f7fbfc;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border-radius: 999px;
  border: 1px solid rgba(7, 37, 54, 0.25);
  background: #ffffff;
  cursor: pointer;
  font-weight: 900;
  font-size: 16px;
  color: #072536;
  transition: transform 120ms ease, background 120ms ease;
}

.qty-btn:hover {
  background: #e9f3f6;
  transform: translateY(-1px);
}

.qty-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none;
}

.qty-val {
  min-width: 20px;
  text-align: center;
  font-weight: 800;
  color: #072536;
}

.line-total {
  font-weight: 900;
  min-width: 90px;
  text-align: right;
  color: #072536;
}

.remove-btn {
  all: unset;
  cursor: pointer;
  font-size: 18px;
  font-weight: 900;
  color: #000;
  padding: 6px;
  border-radius: 8px;
  line-height: 1;
}

.remove-btn:hover {
  background: rgba(7, 37, 54, 0.06);
}

.summary {
  background: #ffffff;
  border: 1px solid rgba(7, 37, 54, 0.12);
  border-radius: 16px;
  padding: 16px;
  box-shadow: 0 14px 30px rgba(7, 37, 54, 0.08);
  height: fit-content;
  position: sticky;
  top: 16px;
}

.summary h2 {
  margin: 0 0 12px;
  font-size: 18px;
  font-weight: 900;
  color: #072536;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-weight: 700;
  color: rgba(7, 37, 54, 0.8);
}

.summary-row.muted {
  color: rgba(7, 37, 54, 0.6);
  font-weight: 600;
}

.summary-row.total {
  border-top: 1px solid rgba(7, 37, 54, 0.12);
  margin-top: 6px;
  padding-top: 10px;
  font-size: 16px;
  color: #072536;
}

.cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 16px;
  border-radius: 12px;
  background: #072536;
  color: #fff;
  font-weight: 800;
  text-decoration: none;
  border: 1px solid #072536;
  transition: transform 120ms ease, opacity 180ms ease;
}

.cta:hover {
  transform: translateY(-1px);
}

.cta.full {
  width: 100%;
  margin-top: 10px;
}

@media (max-width: 980px) {
  .cart-grid {
    grid-template-columns: 1fr;
  }

  .summary {
    position: static;
  }
}

@media (max-width: 720px) {
  .cart-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .cart-item {
    grid-template-columns: 70px 1fr;
    grid-template-areas:
      "thumb info"
      "thumb qty"
      "thumb total"
      "thumb remove";
  }

  .cart-thumb {
    grid-area: thumb;
  }

  .item-info {
    grid-area: info;
  }

  .qty-box {
    grid-area: qty;
    justify-self: flex-start;
  }

  .line-total {
    grid-area: total;
    text-align: left;
  }

  .remove-btn {
    grid-area: remove;
  }
}
</style>
