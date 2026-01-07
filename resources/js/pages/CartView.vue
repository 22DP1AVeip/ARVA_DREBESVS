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
  <main>
    <NavBar />

    <div style="max-width: 900px; margin: 0 auto; padding: 24px;">
      <h1>Grozs</h1>

      <p v-if="cart.count === 0">Grozs ir tukšs.</p>

      <div v-else>
        <div
          v-for="item in cart.items"
          :key="item.id"
          class="cart-item"
        >
          <img
            :src="item.image_men || item.image_women"
            class="cart-thumb"
          />

          <div style="flex:1;">
            <div class="item-name">{{ item.name }}</div>
            <div class="item-price">
              €{{ Number(item.price).toFixed(2) }}
            </div>
          </div>

          <!-- QTY -->
          <div class="qty-box">
            <button
              class="qty-btn"
              @click="updateQty(item, -1)"
              :disabled="item.qty <= 1"
            >
              −
            </button>

            <span class="qty-val">{{ item.qty }}</span>

            <button
              class="qty-btn"
              @click="updateQty(item, +1)"
            >
              +
            </button>
          </div>

          <!-- LINE TOTAL -->
          <div class="line-total">
            €{{ (item.price * item.qty).toFixed(2) }}
          </div>

          <!-- REMOVE -->
          <button
            class="remove-btn"
            @click="removeItem(item)"
            aria-label="Remove"
          >
            ✖
          </button>
        </div>

        <div class="summary">
          <span class="summary-label">Kopā</span>
          <span class="summary-total">€{{ subtotal.toFixed(2) }}</span>
        </div>

        <div style="margin-top:14px; display:flex; gap:10px;">
          <Link
            href="/checkout"
            class="checkout-btn"
          >
            Uz apmaksu
          </Link>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.cart-item {
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid #eee;
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 10px;
  background: #fff;
}

.cart-thumb {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #eee;
}

.item-name {
  font-weight: 800;
}

.item-price {
  color: #555;
  font-size: 14px;
}

/* QTY */
.qty-box {
  display: flex;
  align-items: center;
  gap: 6px;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: #f8f8f8;
  cursor: pointer;
  font-weight: 900;
  font-size: 16px;
}

.qty-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.qty-val {
  min-width: 20px;
  text-align: center;
  font-weight: 800;
}

/* LINE TOTAL */
.line-total {
  font-weight: 900;
  min-width: 80px;
  text-align: right;
}

/* REMOVE X — simple black */
.remove-btn {
  all: unset;
  cursor: pointer;
  font-size: 18px;
  font-weight: 900;
  color: #000;
  padding: 6px;
  border-radius: 6px;
}

.remove-btn:hover {
  background: rgba(0, 0, 0, 0.06);
}

.summary {
  display: flex;
  justify-content: space-between;
  border-top: 1px solid #eee;
  padding-top: 12px;
  margin-top: 12px;
}

.summary-label {
  font-weight: 700;
}

.summary-total {
  font-weight: 900;
}

.checkout-btn {
  background: #111;
  color: #fff;
  padding: 12px 14px;
  border-radius: 10px;
  font-weight: 800;
  text-decoration: none;
}
</style>
