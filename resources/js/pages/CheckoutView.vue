<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed, ref } from "vue";
import { useForm, usePage, Link } from "@inertiajs/vue3";

const page = usePage<any>();
const cart = computed(() => page.props.cart ?? { count: 0, items: [] });

const subtotal = computed(() =>
  (cart.value.items ?? []).reduce(
    (s: number, i: any) => s + Number(i.price) * Number(i.qty),
    0
  )
);

// --- Fake card fields (UI only)
const cardNumber = ref("");
const cardExpiry = ref("");
const cardCvv = ref("");

// --- Format card number: 4242 4242 4242 4242
function formatCardNumber(e: Event) {
  const input = e.target as HTMLInputElement;
  let value = input.value;

  value = value.replace(/\D/g, "");   // only digits
  value = value.slice(0, 16);        // max 16 digits
  value = value.replace(/(.{4})/g, "$1 ").trim(); // spaces

  cardNumber.value = value;
}

// --- Format expiry: MM/YY
function formatExpiry(e: Event) {
  const input = e.target as HTMLInputElement;
  let value = input.value;

  value = value.replace(/\D/g, ""); // only digits
  value = value.slice(0, 4);       // max 4 digits

  if (value.length >= 3) {
    value = value.slice(0, 2) + "/" + value.slice(2);
  }

  cardExpiry.value = value;
}

// --- Format CVV: 123
function formatCvv(e: Event) {
  const input = e.target as HTMLInputElement;
  let value = input.value;

  value = value.replace(/\D/g, ""); // only digits
  value = value.slice(0, 3);       // max 3 digits

  cardCvv.value = value;
}

// --- Checkout form
const form = useForm({
  full_name: "",
  email: "",
  phone: "",
  address: "",
  city: "",
  postcode: "",
  country: "Latvija",
  payment_method: "card",
});

function submit() {
  if ((cart.value.count ?? 0) <= 0) return;

  if (form.payment_method === "card") {
    if (!cardNumber.value || !cardExpiry.value || !cardCvv.value) {
      alert("Lūdzu aizpildi kartes datus (testa režīms).");
      return;
    }
  }

  form.post("/checkout");
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="head">
        <h1 class="title">Apmaksa</h1>
        <Link href="/cart" class="back">← Atpakaļ uz grozu</Link>
      </div>

      <p v-if="cart.count === 0" class="empty">
        Grozs ir tukšs.
        <Link href="/" class="link">Atgriezties uz veikalu</Link>
      </p>

      <div v-else class="grid">
        <!-- LEFT -->
        <div class="card">
          <h2 class="card-title">Piegādes dati</h2>

          <div class="field">
            <label>Vārds Uzvārds</label>
            <input v-model="form.full_name" placeholder="Jānis Bērziņš" />
          </div>

          <div class="row">
            <div class="field">
              <label>E-pasts</label>
              <input v-model="form.email" placeholder="janis@email.com" />
            </div>
            <div class="field">
              <label>Telefons</label>
              <input v-model="form.phone" placeholder="+371 20000000" />
            </div>
          </div>

          <div class="field">
            <label>Adrese</label>
            <input v-model="form.address" placeholder="Brīvības iela 1" />
          </div>

          <div class="row">
            <div class="field">
              <label>Pilsēta</label>
              <input v-model="form.city" placeholder="Rīga" />
            </div>
            <div class="field">
              <label>Pasta indekss</label>
              <input v-model="form.postcode" placeholder="LV-1001" />
            </div>
          </div>

          <div class="field">
            <label>Valsts</label>
            <input v-model="form.country" />
          </div>

          <hr class="sep" />

          <h2 class="card-title">Maksājums</h2>

          <div class="field">
            <label>Maksājuma metode</label>
            <select v-model="form.payment_method">
              <option value="card">Bankas karte</option>
              <option value="cod">Apmaksa pie saņemšanas</option>
            </select>
          </div>

          <!-- Card fields -->
          <div v-if="form.payment_method === 'card'" class="card-box">
            <div class="field">
              <label>Kartes numurs</label>
              <input
                type="text"
                inputmode="numeric"
                pattern="[0-9 ]*"
                :value="cardNumber"
                @input="formatCardNumber"
                placeholder="4242 4242 4242 4242"
              />
            </div>

            <div class="row">
              <div class="field">
                <label>Derīguma termiņš</label>
                <input
                  type="text"
                  inputmode="numeric"
                  pattern="[0-9/]*"
                  :value="cardExpiry"
                  @input="formatExpiry"
                  placeholder="MM/YY"
                />
              </div>

              <div class="field">
                <label>CVV</label>
                <input
                  type="text"
                  inputmode="numeric"
                  pattern="[0-9]*"
                  :value="cardCvv"
                  @input="formatCvv"
                  placeholder="123"
                />
              </div>
            </div>

            <p class="test-note">
              Testa režīms — maksājums netiek veikts. Kartes dati netiek saglabāti.
            </p>
          </div>

          <button class="btn" @click="submit">
            Apmaksāt (Testa režīms)
          </button>
        </div>

        <!-- RIGHT -->
        <div class="card summary">
          <h2 class="card-title">Kopsavilkums</h2>

          <div v-for="it in cart.items" :key="it.id" class="item">
            <div>
              <strong>{{ it.name }}</strong>
              <div class="muted">€{{ it.price }} × {{ it.qty }}</div>
            </div>
            <strong>€{{ (it.price * it.qty).toFixed(2) }}</strong>
          </div>

          <hr class="sep" />

          <div class="sum">
            <span>Kopā:</span>
            <strong>€{{ subtotal.toFixed(2) }}</strong>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap { max-width:1100px;margin:auto;padding:24px; }
.grid { display:grid;grid-template-columns:1.1fr 0.9fr;gap:16px; }
@media(max-width:900px){.grid{grid-template-columns:1fr;}}

.card { background:#fff;border:1px solid #eee;border-radius:12px;padding:16px; }
.card-title { font-weight:900;margin-bottom:10px; }

.field { margin-bottom:10px; display:grid; gap:4px; }
input, select { padding:10px;border:1px solid #ddd;border-radius:8px; }

.row { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
@media(max-width:600px){.row{grid-template-columns:1fr;}}

.btn {
  margin-top:10px;
  background:#111;
  color:#fff;
  padding:12px;
  width:100%;
  border-radius:10px;
  font-weight:900;
}

.card-box { background:#f7f7f7;padding:10px;border-radius:10px;margin-bottom:10px; }
.test-note { font-size:12px;color:#666;font-weight:700; }

.summary .item { display:flex;justify-content:space-between;margin-bottom:8px; }
.sep { border:none;border-top:1px solid #eee;margin:10px 0; }
.muted { font-size:13px;color:#666; }
.sum { display:flex;justify-content:space-between;font-size:16px; }

.head { display:flex;justify-content:space-between;align-items:center;margin-bottom:10px; }
.back { text-decoration:none;color:#555;font-weight:700; }
.empty { font-weight:700;color:#666; }
.link { text-decoration:underline;color:#111; }
</style>
