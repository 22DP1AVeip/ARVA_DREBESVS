<template>
  <div class="checkout-page">
    <div class="checkout-header">
      <h1 class="page-title">Apmaksa</h1>
      <Link href="/cart" class="back-link">← Atpakaļ uz grozu</Link>
    </div>

    <div v-if="cartCount === 0" class="empty-state">
      Grozs ir tukšs. <Link href="/cart">Doties uz grozu</Link>
    </div>

    <div v-else class="grid">
      <!-- LEFT: FORM -->
      <div class="card">
        <h2 class="card-title">Piegādes dati</h2>

        <form @submit.prevent="submit">
          <div class="field-row">
            <label>Vārds, uzvārds *</label>
            <input v-model="form.full_name" type="text" autocomplete="name" />
            <div v-if="form.errors.full_name" class="err">{{ form.errors.full_name }}</div>
          </div>

          <div class="two-cols">
            <div class="field-row">
              <label>E-pasts *</label>
              <input v-model="form.email" type="email" autocomplete="email" />
              <div v-if="form.errors.email" class="err">{{ form.errors.email }}</div>
            </div>

            <div class="field-row">
              <label>Telefons</label>
              <input v-model="form.phone" type="text" autocomplete="tel" />
              <div v-if="form.errors.phone" class="err">{{ form.errors.phone }}</div>
            </div>
          </div>

          <div class="field-row">
            <label>Adrese *</label>
            <input v-model="form.address" type="text" autocomplete="street-address" />
            <div v-if="form.errors.address" class="err">{{ form.errors.address }}</div>
          </div>

          <div class="two-cols">
            <div class="field-row">
              <label>Pilsēta *</label>
              <input v-model="form.city" type="text" autocomplete="address-level2" />
              <div v-if="form.errors.city" class="err">{{ form.errors.city }}</div>
            </div>

            <div class="field-row">
              <label>Pasta indekss *</label>
              <input v-model="form.postcode" type="text" autocomplete="postal-code" />
              <div v-if="form.errors.postcode" class="err">{{ form.errors.postcode }}</div>
            </div>
          </div>

          <div class="field-row">
            <label>Valsts *</label>
            <input v-model="form.country" type="text" autocomplete="country-name" />
            <div v-if="form.errors.country" class="err">{{ form.errors.country }}</div>
          </div>

          <div class="divider"></div>

          <h2 class="card-title">Maksājums</h2>

          <div class="radio-row">
            <label class="radio">
              <input type="radio" value="card" v-model="form.payment_method" />
              Karte (demo)
            </label>

            <label class="radio">
              <input type="radio" value="cod" v-model="form.payment_method" />
              Apmaksa saņemot
            </label>
          </div>

          <div v-if="form.errors.payment_method" class="err">{{ form.errors.payment_method }}</div>

          <button class="btn btn-primary full" :disabled="form.processing">
            {{ form.processing ? "Nosūta..." : "Apstiprināt pasūtījumu" }}
          </button>

          <p class="hint">
            Šī ir demo apmaksas lapa. Maksājumi vēl nav pieslēgti.
          </p>
        </form>
      </div>

      <!-- RIGHT: SUMMARY -->
      <div class="card summary-card">
        <h2 class="card-title">Pasūtījuma kopsavilkums</h2>

        <div class="summary-items">
          <div v-for="item in cartItems" :key="item.id" class="summary-item">
            <img class="thumb" :src="item.image_men || item.image_women" alt="" />

            <div class="info">
              <div class="name">{{ item.name }}</div>
              <div class="meta">€{{ money(item.price) }} × {{ item.qty }}</div>
            </div>

            <div class="line-total">
              €{{ money(Number(item.price) * Number(item.qty)) }}
            </div>
          </div>
        </div>

        <div class="totals">
          <div class="row">
            <span>Starpsumma</span>
            <strong>€{{ money(subtotal) }}</strong>
          </div>
          <div class="row subtle">
            <span>Piegāde</span>
            <span>Aprēķināsim vēlāk</span>
          </div>

          <div class="row total">
            <span>Kopā</span>
            <strong>€{{ money(subtotal) }}</strong>
          </div>
        </div>

        <div class="summary-actions">
          <Link href="/cart" class="btn btn-secondary">Rediģēt grozu</Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";

type CartItem = {
  id: number | string;
  name: string;
  price: number | string;
  qty: number | string;
  image_men?: string;
  image_women?: string;
};

type Cart = {
  count?: number;
  items?: CartItem[];
};

const page = usePage<any>();

// Cart var nākt no props (no controller) vai shared props (kā tev navbarā)
const cart = computed<Cart>(() => (page.props.cart ?? { count: 0, items: [] }) as Cart);

const cartItems = computed(() => cart.value.items ?? []);
const cartCount = computed(() => Number(cart.value.count ?? 0));

const subtotal = computed(() =>
  cartItems.value.reduce((sum, it) => sum + Number(it.price) * Number(it.qty), 0)
);

const form = useForm({
  full_name: "",
  email: (page.props.auth?.user?.email ?? "") as string,
  phone: "",
  address: "",
  city: "",
  postcode: "",
  country: "Latvija",
  payment_method: "card",
});

function money(v: number | string) {
  const n = Number(v);
  return Number.isFinite(n) ? n.toFixed(2) : "0.00";
}

function submit() {
  form.post("/checkout", {
    preserveScroll: true,
  });
}
</script>

<style scoped>
:global(:root) {
  --arva-ink: #072536;
  --arva-teal: #13c4ab;
  --arva-teal-dark: #06616d;
  --arva-pink: #de7388;
  --arva-purple: #97276b;
  --arva-bg: #ffffff;
  --arva-bg-soft: #f7fbfc;
  --arva-border: rgba(7, 37, 54, 0.12);
  --arva-shadow: 0 14px 40px rgba(7, 37, 54, 0.18);
}

.checkout-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 22px 16px 40px;
  color: var(--arva-ink);
}

.checkout-header {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 14px;
}

.page-title {
  margin: 0;
  font-size: 32px;
  font-weight: 900;
  letter-spacing: 0.2px;
  color: var(--arva-ink);
}

.back-link {
  text-decoration: none;
  color: rgba(7, 37, 54, 0.7);
  font-weight: 700;
}
.back-link:hover {
  color: var(--arva-ink);
}

.empty-state {
  background: var(--arva-bg-soft);
  border: 1px solid var(--arva-border);
  border-radius: 14px;
  padding: 18px;
}

.grid {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 16px;
}

@media (max-width: 920px) {
  .grid {
    grid-template-columns: 1fr;
  }
}

.card {
  background: var(--arva-bg);
  border: 1px solid var(--arva-border);
  border-radius: 14px;
  box-shadow: var(--arva-shadow);
  padding: 16px;
}

.card-title {
  margin: 0 0 12px;
  font-size: 18px;
  font-weight: 900;
  color: var(--arva-ink);
}

.field-row {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 12px;
}

label {
  font-weight: 800;
  font-size: 13px;
  color: rgba(7, 37, 54, 0.8);
}

input[type="text"],
input[type="email"] {
  border: 1px solid rgba(7, 37, 54, 0.16);
  border-radius: 12px;
  padding: 10px 12px;
  outline: none;
  background: var(--arva-bg);
  color: var(--arva-ink);
}

input[type="text"]:focus,
input[type="email"]:focus {
  border-color: rgba(19, 196, 171, 0.75);
  box-shadow: 0 0 0 3px rgba(19, 196, 171, 0.18);
}

.two-cols {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

@media (max-width: 620px) {
  .two-cols {
    grid-template-columns: 1fr;
  }
}

.divider {
  height: 1px;
  background: rgba(7, 37, 54, 0.08);
  margin: 14px 0;
  position: relative;
}

.divider::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  top: -1px;
  height: 3px;
  background: linear-gradient(90deg, var(--arva-teal), var(--arva-pink), var(--arva-purple));
  opacity: 0.35;
}

.radio-row {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin: 6px 0 14px;
}

.radio {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid rgba(7, 37, 54, 0.12);
  background: var(--arva-bg-soft);
  font-weight: 800;
  color: rgba(7, 37, 54, 0.85);
}

.radio input {
  accent-color: var(--arva-teal);
}

.err {
  color: #b21f2d;
  font-weight: 700;
  font-size: 12px;
}

.btn {
  border-radius: 12px;
  font-weight: 900;
  letter-spacing: 0.2px;
  padding: 11px 12px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid transparent;
  cursor: pointer;
  user-select: none;
}

.btn-primary {
  background: var(--arva-ink);
  border-color: var(--arva-ink);
  color: #fff;
  transition: transform 120ms ease, opacity 180ms ease;
}
.btn-primary:hover {
  transform: translateY(-1px);
}
.btn-primary:active {
  transform: translateY(0);
  opacity: 0.95;
}
.btn-primary:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #fff;
  border-color: rgba(19, 196, 171, 0.55);
  color: var(--arva-teal-dark);
  transition: transform 120ms ease, background 180ms ease;
}
.btn-secondary:hover {
  transform: translateY(-1px);
  background: rgba(19, 196, 171, 0.08);
}
.btn-secondary:active {
  transform: translateY(0);
}

.full {
  width: 100%;
  margin-top: 8px;
}

.hint {
  margin: 10px 0 0;
  color: rgba(7, 37, 54, 0.65);
  font-size: 13px;
}

/* Summary */
.summary-card {
  position: sticky;
  top: 16px;
  align-self: start;
}

@media (max-width: 920px) {
  .summary-card {
    position: static;
  }
}

.summary-items {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 14px;
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--arva-bg-soft);
  border: 1px solid rgba(7, 37, 54, 0.08);
  border-radius: 12px;
  padding: 10px;
}

.thumb {
  width: 54px;
  height: 54px;
  border-radius: 10px;
  object-fit: cover;
}

.info {
  flex: 1;
  min-width: 0;
}

.name {
  font-weight: 900;
  color: var(--arva-ink);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.meta {
  color: rgba(7, 37, 54, 0.7);
  font-size: 13px;
}

.line-total {
  font-weight: 900;
  color: var(--arva-ink);
}

.totals {
  border-top: 1px solid rgba(7, 37, 54, 0.08);
  padding-top: 12px;
}

.row {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
}

.subtle {
  color: rgba(7, 37, 54, 0.65);
  font-weight: 700;
}

.total {
  border-top: 1px solid rgba(7, 37, 54, 0.08);
  margin-top: 8px;
  padding-top: 10px;
  font-size: 16px;
}

.summary-actions {
  margin-top: 12px;
}
</style>
