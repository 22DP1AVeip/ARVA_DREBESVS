<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page    = usePage<any>();
const designs = computed<any[]>(() => page.props.designs ?? []);
const flash   = computed(() => (page.props.flash as any) ?? {});

const positionLabel: Record<string, string> = {
  center: "Centrs", top: "Augša", bottom: "Apakša",
  left: "Kreisā", right: "Labā",
};
const sizeLabel: Record<string, string> = {
  small: "Mazs", medium: "Vidējs", large: "Liels",
};

function addToCart(id: number) {
  router.post(`/design/${id}/cart`, {}, { preserveScroll: true });
}

function remove(id: number) {
  if (!confirm("Dzēst šo dizainu?")) return;
  router.delete(`/profile/designs/${id}`, { preserveScroll: true });
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="head">
        <h1 class="title">Mani dizaini</h1>
        <Link href="/design" class="new-btn">+ Jauns dizains</Link>
      </div>

      <div v-if="flash.success" class="flash-ok">{{ flash.success }}</div>

      <p v-if="designs.length === 0" class="empty">
        Vēl nav saglabātu dizainu.
        <Link href="/design" class="empty-link">Izveidot pirmo →</Link>
      </p>

      <div v-else class="grid">
        <div v-for="d in designs" :key="d.id" class="card">

          <!-- Priekšskatījums -->
          <div class="preview" :style="{ background: d.base_color }">
            <img
              v-if="d.garment_file"
              :src="`/bildites/${d.garment_file}`"
              class="garment-img"
              alt=""
            />
            <div v-if="d.preset_design" class="preset-badge">
              {{ d.preset_design }}
            </div>
          </div>

          <!-- Info -->
          <div class="info">
            <div class="garment-name">{{ d.garment_name }}</div>
            <div class="meta-row">
              <span class="meta-chip" :style="{ background: d.base_color, border: '1.5px solid rgba(0,0,0,.12)' }"></span>
              <span class="meta-text">{{ positionLabel[d.design_position] ?? d.design_position }}</span>
              <span class="meta-sep">·</span>
              <span class="meta-text">{{ sizeLabel[d.design_size] ?? d.design_size }}</span>
            </div>
            <div class="date">{{ d.created_at }}</div>
          </div>

          <!-- Darbības -->
          <div class="actions">
            <button class="btn-cart" @click="addToCart(d.id)">
              🛒 Pievienot grozam
            </button>
            <button class="btn-del" @click="remove(d.id)" title="Dzēst">✕</button>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap { max-width: 1000px; margin: 0 auto; padding: 28px 16px 56px; color: #072536; }

.head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.title { font-size: 28px; font-weight: 900; }

.new-btn {
  padding: 10px 20px;
  background: linear-gradient(120deg, #072536, #97276b);
  color: #fff;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
  transition: filter .15s;
}
.new-btn:hover { filter: brightness(1.12); }

.flash-ok {
  background: rgba(19,196,171,.12);
  border: 1px solid rgba(19,196,171,.4);
  color: #06616d;
  font-weight: 700;
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 20px;
}

.empty { font-size: 15px; font-weight: 700; color: rgba(7,37,54,.6); }
.empty-link { color: #06616d; font-weight: 700; text-decoration: underline; margin-left: 6px; }

/* GRID */
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

/* KARTE */
.card {
  background: #fff;
  border: 1px solid rgba(7,37,54,.1);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(7,37,54,.07);
  display: flex;
  flex-direction: column;
}

/* PRIEKŠSKATĪJUMS */
.preview {
  position: relative;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.garment-img {
  height: 160px;
  width: auto;
  object-fit: contain;
  filter: drop-shadow(0 4px 8px rgba(0,0,0,.15));
}
.preset-badge {
  position: absolute;
  bottom: 8px;
  left: 8px;
  background: rgba(0,0,0,.5);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 999px;
}

/* INFO */
.info { padding: 12px 14px 8px; flex: 1; }
.garment-name { font-size: 15px; font-weight: 800; color: #072536; margin-bottom: 6px; }
.meta-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
.meta-chip { display: inline-block; width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; }
.meta-text { font-size: 12px; font-weight: 600; color: rgba(7,37,54,.65); }
.meta-sep  { color: rgba(7,37,54,.3); font-size: 12px; }
.date { font-size: 11px; color: rgba(7,37,54,.45); margin-top: 2px; }

/* DARBĪBAS */
.actions { padding: 10px 14px 14px; display: flex; gap: 8px; }
.btn-cart {
  flex: 1;
  padding: 9px 12px;
  background: #072536;
  color: #fff;
  border: none;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: background .15s;
}
.btn-cart:hover { background: #0f3a4c; }
.btn-del {
  width: 36px;
  height: 36px;
  border: 1px solid rgba(192,57,43,.3);
  background: transparent;
  color: #c0392b;
  border-radius: 10px;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background .15s;
}
.btn-del:hover { background: #c0392b; color: #fff; }
</style>
