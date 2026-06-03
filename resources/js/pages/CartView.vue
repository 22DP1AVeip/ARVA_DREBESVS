<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";

interface CartItem {
  variant_id: number;
  qty: number;
  name: string;
  price: number;
  size: string;
  color: string;
  image: string | null;
}

const page = usePage<any>();
const items = computed<CartItem[]>(() => page.props.items ?? []);

const total = computed(() =>
  items.value.reduce((sum, it) => sum + it.price * it.qty, 0)
);

function removeItem(variantId: number) {
  router.post(`/cart/remove/${variantId}`, {}, { preserveScroll: true });
}

function updateQty(variantId: number, qty: number) {
  router.post(`/cart/update/${variantId}`, { qty }, { preserveScroll: true });
}

function clearCart() {
  router.post(`/cart/clear`, {}, { preserveScroll: true });
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <h1 class="title">Grozs</h1>

      <div v-if="!items.length" class="empty">
        Grozs ir tukšs.
        <div class="mt">
          <Link href="/" class="link">Atgriezties uz sākumu</Link>
        </div>
      </div>

      <div v-else class="grid">
        <div class="list">
          <div v-for="it in items" :key="it.variant_id" class="row">

            <div class="img-wrap">
              <img v-if="it.image" :src="it.image" :alt="it.name" class="item-img" />
              <div v-else class="item-img-placeholder">👕</div>
            </div>

            <div class="left">
              <div class="name">{{ it.name }}</div>
              <div class="meta">Krāsa: <b>{{ it.color }}</b> • Izmērs: <b>{{ it.size }}</b></div>
              <div class="meta">Cena: €{{ it.price.toFixed(2) }}</div>

              <div class="qty">
                <button class="qbtn" @click="updateQty(it.variant_id, Math.max(1, it.qty - 1))">-</button>
                <div class="qval">{{ it.qty }}</div>
                <button class="qbtn" @click="updateQty(it.variant_id, it.qty + 1)">+</button>
              </div>
            </div>

            <div class="right">
              <div class="lineTotal">€{{ (it.price * it.qty).toFixed(2) }}</div>
              <button class="remove" @click="removeItem(it.variant_id)">✕</button>
            </div>
          </div>

          <button class="clear" @click="clearCart">Iztukšot grozu</button>
        </div>

        <div class="summary">
          <div class="sumTitle">Kopsumma</div>
          <div class="sumRow">
            <span>Summa</span>
            <b>€{{ total.toFixed(2) }}</b>
          </div>
          <Link href="/checkout" class="checkout">Uz Checkout</Link>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap{max-width:1100px;margin:0 auto;padding:24px;flex:1;}
.title{font-size:26px;font-weight:800;margin-bottom:18px;}
.empty{padding:24px;border:1px solid #eee;border-radius:12px;}
.mt{margin-top:10px;}
.link{text-decoration:underline;}

.grid{display:grid;grid-template-columns:1fr 320px;gap:18px;}
@media (max-width:900px){.grid{grid-template-columns:1fr;}}

.list{background:#fff;border:1px solid #eee;border-radius:12px;padding:14px;}
.row{display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #eee;}
.row:last-child{border-bottom:none;}

.img-wrap{flex-shrink:0;}
.item-img{width:80px;height:80px;object-fit:cover;border-radius:10px;border:1px solid #eee;}
.item-img-placeholder{width:80px;height:80px;border-radius:10px;border:1px solid #eee;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:28px;}

.left{flex:1;}
.name{font-weight:800;}
.meta{font-size:13px;color:#555;margin-top:4px;}

.qty{display:flex;align-items:center;gap:10px;margin-top:10px;}
.qbtn{width:34px;height:34px;border-radius:10px;border:1px solid #ddd;background:#fafafa;cursor:pointer;font-weight:900;}
.qval{min-width:26px;text-align:center;font-weight:800;}

.right{display:flex;flex-direction:column;align-items:flex-end;gap:10px;}
.lineTotal{font-weight:900;}
.remove{border:none;background:transparent;font-size:18px;cursor:pointer;}

.clear{margin-top:14px;width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;background:#fafafa;cursor:pointer;font-weight:700;}

.summary{background:#fff;border:1px solid #eee;border-radius:12px;padding:14px;height:fit-content;}
.sumTitle{font-weight:900;margin-bottom:10px;}
.sumRow{display:flex;justify-content:space-between;padding:10px 0;border-top:1px solid #eee;border-bottom:1px solid #eee;margin-bottom:12px;}
.checkout{display:block;text-align:center;padding:14px;border-radius:10px;background:#111;color:#fff;font-weight:900;text-decoration:none;}
</style>