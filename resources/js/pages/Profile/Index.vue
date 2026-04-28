<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { computed } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";

const page = usePage<any>();

const user = computed(() => page.props.user);
const section = computed(() => page.props.section ?? "settings");
const orders = computed(() => page.props.orders ?? []);
const favorites = computed(() => page.props.favorites ?? []);

const profileForm = useForm({
  name: user.value?.name ?? "",
  email: user.value?.email ?? "",
});

const passwordForm = useForm({
  current_password: "",
  password: "",
  password_confirmation: "",
});

function saveProfile() {
  profileForm.patch("/profile", { preserveScroll: true });
}

function savePassword() {
  passwordForm.put("/profile/password", { preserveScroll: true });
}

function statusBadgeClass(s: string) {
  const v = (s || "").toLowerCase();
  if (v.includes("apstr")) return "badge badge-processing";
  if (v.includes("nosūt")) return "badge badge-shipped";
  if (v.includes("pieg")) return "badge badge-delivered";
  if (v.includes("atcel")) return "badge badge-cancelled";
  return "badge";
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <h1 class="title">Profils</h1>

      <div class="layout">
        <!-- LEFT MENU -->
        <aside class="side">
          <div class="hello">
            <div class="hello-title">Sveiki, {{ user?.name }}</div>
            <div class="hello-sub">{{ user?.email }}</div>
          </div>

          <nav class="menu">
            <Link href="/profile/orders" class="menu-item" :class="{ active: section === 'orders' }">
              Pasūtījumi
            </Link>

            <Link href="/profile/favorites" class="menu-item" :class="{ active: section === 'favorites' }">
              Favorīti
            </Link>

            <Link href="/profile/designs" class="menu-item" :class="{ active: section === 'designs' }">
              🎨 Mani dizaini
            </Link>

            <Link href="/profile/coupons" class="menu-item" :class="{ active: section === 'coupons' }">
              🎟️ Kuponi &amp; punkti
            </Link>

            <Link href="/profile/settings" class="menu-item" :class="{ active: section === 'settings' }">
              Iestatījumi
            </Link>
          </nav>
        </aside>

        <!-- RIGHT CONTENT -->
        <section class="content">
          <!-- ORDERS -->
          <div v-if="section === 'orders'" class="card">
            <h2 class="card-title">Pasūtījumi</h2>

            <p v-if="orders.length === 0" class="muted">Nav pasūtījumu.</p>

            <div v-else class="orders">
              <button v-for="o in orders" :key="o.id" class="order-row" type="button">
                <div class="order-left">
                  <div class="order-id">{{ o.id }}</div>
                  <div class="order-date">Datums: {{ o.date }}</div>
                </div>

                <div class="order-right">
                  <span :class="statusBadgeClass(o.status)">{{ o.status }}</span>
                  <div class="order-total">€{{ Number(o.total).toFixed(2) }}</div>
                </div>
              </button>

              <p class="hint">
                (Šobrīd placeholder. Nākamais solis: uzklikšķinot atvērt pasūtījuma detaļas lapu ar statusu.)
              </p>
            </div>
          </div>

          <!-- FAVORITES -->
          <div v-else-if="section === 'favorites'" class="card">
            <h2 class="card-title">Favorīti</h2>

            <p v-if="favorites.length === 0" class="muted">
              Te vēl nav favorītu. ❤️ (Pievienosim DB nākamajā solī)
            </p>

            <div v-else class="favorites">
              <!-- vēlāk te rādīsim produktu kartītes -->
            </div>
          </div>

          <!-- SETTINGS -->
          <div v-else class="stack">
            <div class="card">
              <h2 class="card-title">Profila iestatījumi</h2>

              <div class="field">
                <label>Vārds</label>
                <input v-model="profileForm.name" type="text" />
                <div v-if="profileForm.errors.name" class="err">{{ profileForm.errors.name }}</div>
              </div>

              <div class="field">
                <label>E-pasts</label>
                <input v-model="profileForm.email" type="email" />
                <div v-if="profileForm.errors.email" class="err">{{ profileForm.errors.email }}</div>
              </div>

              <button class="btn btn-primary" :disabled="profileForm.processing" @click="saveProfile">
                Saglabāt
              </button>

              <div v-if="profileForm.recentlySuccessful" class="ok">Saglabāts </div>
            </div>

            <div class="card">
              <h2 class="card-title">Mainīt paroli</h2>

              <div class="field">
                <label>Pašreizējā parole</label>
                <input v-model="passwordForm.current_password" type="password" />
                <div v-if="passwordForm.errors.current_password" class="err">
                  {{ passwordForm.errors.current_password }}
                </div>
              </div>

              <div class="field">
                <label>Jaunā parole</label>
                <input v-model="passwordForm.password" type="password" />
                <div v-if="passwordForm.errors.password" class="err">{{ passwordForm.errors.password }}</div>
              </div>

              <div class="field">
                <label>Apstiprināt paroli</label>
                <input v-model="passwordForm.password_confirmation" type="password" />
              </div>

              <button class="btn btn-primary" :disabled="passwordForm.processing" @click="savePassword">
                Nomainīt paroli
              </button>

              <div v-if="passwordForm.recentlySuccessful" class="ok">Parole nomainīta </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <Footer />
  </main>
</template>

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

.wrap { max-width: 1100px; margin: 0 auto; padding: 22px 16px 40px; color: var(--arva-ink); }
.title { margin: 0 0 14px; font-size: 32px; font-weight: 900; }

.layout { display: grid; grid-template-columns: 280px 1fr; gap: 16px; }
@media (max-width: 900px) { .layout { grid-template-columns: 1fr; } }

.side {
  background: var(--arva-bg);
  border: 1px solid var(--arva-border);
  border-radius: 14px;
  box-shadow: var(--arva-shadow);
  padding: 14px;
}

.hello-title { font-weight: 900; }
.hello-sub { color: rgba(7,37,54,.65); font-weight: 700; font-size: 13px; margin-top: 2px; }
.menu { margin-top: 12px; display: flex; flex-direction: column; gap: 6px; }

.menu-item {
  text-decoration: none;
  color: rgba(7,37,54,.85);
  font-weight: 900;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid rgba(7,37,54,.10);
  background: var(--arva-bg-soft);
}
.menu-item:hover { background: rgba(19,196,171,.10); }
.menu-item.active { border-color: rgba(19,196,171,.55); }

.content { min-width: 0; }
.stack { display: grid; gap: 16px; }

.card {
  background: var(--arva-bg);
  border: 1px solid var(--arva-border);
  border-radius: 14px;
  box-shadow: var(--arva-shadow);
  padding: 16px;
}

.card-title { margin: 0 0 12px; font-size: 18px; font-weight: 900; }

.field { display: grid; gap: 6px; margin-bottom: 12px; }
label { font-weight: 900; font-size: 13px; color: rgba(7,37,54,.8); }
input {
  border: 1px solid rgba(7,37,54,.16);
  border-radius: 12px;
  padding: 10px 12px;
  outline: none;
}
input:focus {
  border-color: rgba(19,196,171,.75);
  box-shadow: 0 0 0 3px rgba(19,196,171,.18);
}

.btn { border-radius: 12px; font-weight: 900; padding: 10px 12px; cursor: pointer; border: 1px solid transparent; }
.btn-primary { background: var(--arva-ink); border-color: var(--arva-ink); color: #fff; }
.btn-primary:disabled { opacity: .65; cursor: not-allowed; }

.err { color: #b21f2d; font-weight: 800; font-size: 12px; }
.ok { margin-top: 10px; color: rgba(6,97,109,.95); font-weight: 900; }

.muted { color: rgba(7,37,54,.65); font-weight: 800; }

.orders { display: grid; gap: 10px; }
.order-row {
  all: unset;
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid rgba(7,37,54,.10);
  background: var(--arva-bg-soft);
  cursor: pointer;
}
.order-row:hover { background: rgba(19,196,171,.08); }
.order-id { font-weight: 900; }
.order-date { color: rgba(7,37,54,.65); font-weight: 800; font-size: 13px; margin-top: 2px; }
.order-right { text-align: right; display: grid; gap: 6px; justify-items: end; }
.order-total { font-weight: 900; }

.badge {
  display: inline-flex; align-items: center;
  padding: 6px 10px; border-radius: 999px;
  font-weight: 900; font-size: 12px;
  border: 1px solid rgba(7,37,54,.12);
  background: #fff;
}
.badge-processing { border-color: rgba(222,115,136,.35); }
.badge-shipped { border-color: rgba(19,196,171,.55); }
.badge-delivered { border-color: rgba(151,39,107,.35); }
.badge-cancelled { border-color: rgba(0,0,0,.18); }

.hint { margin-top: 10px; color: rgba(7,37,54,.6); font-weight: 800; font-size: 13px; }
</style>