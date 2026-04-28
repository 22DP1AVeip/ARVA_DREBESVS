<script setup lang="ts">
import NavBar from "../../components/NavBar.vue";
import Footer from "../../components/NavFooter.vue";
import { computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage<any>();
const points     = computed<number>(() => page.props.points ?? 0);
const allCoupons = computed<any[]>(() => page.props.allCoupons ?? []);
const myCoupons  = computed<any[]>(() => page.props.myCoupons ?? []);
const flash      = computed(() => (page.props.flash as any) ?? {});

const tierMeta: Record<string, { emoji: string; color: string; bg: string; border: string }> = {
  bronze:   { emoji: "🥉", color: "#92600a", bg: "#fdf4e7", border: "#f0c070" },
  silver:   { emoji: "🥈", color: "#5a6475", bg: "#f3f5f7", border: "#b0bec5" },
  gold:     { emoji: "🥇", color: "#7a5800", bg: "#fffbea", border: "#f5c518" },
  platinum: { emoji: "💎", color: "#4a1c7c", bg: "#f5f0ff", border: "#b39ddb" },
  diamond:  { emoji: "💠", color: "#006064", bg: "#e0f7fa", border: "#4dd0e1" },
};

function redeem(couponId: number) {
  router.post(`/profile/coupons/redeem/${couponId}`, {}, { preserveScroll: true });
}

function copyCode(code: string) {
  navigator.clipboard.writeText(code);
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="head">
        <h1 class="title">Kuponi &amp; punkti</h1>
        <Link href="/profile/settings" class="back">← Atpakaļ uz profilu</Link>
      </div>

      <div v-if="flash.success" class="flash-ok">{{ flash.success }}</div>

      <!-- PUNKTU KARTE -->
      <div class="points-card">
        <div class="points-label">Tavs punktu atlikums</div>
        <div class="points-value">{{ points.toLocaleString("lv-LV") }} <span>pts</span></div>
        <div class="points-hint">Par katru iztērēto € pasūtījumā tu saņem 1 punktu.</div>
      </div>

      <!-- PIEEJAMIE KUPONI -->
      <h2 class="section-title">Pieejamie kuponi</h2>

      <div class="coupons-grid">
        <div
          v-for="c in allCoupons"
          :key="c.id"
          class="coupon-card"
          :style="{ background: tierMeta[c.tier]?.bg ?? '#f7fbfc', borderColor: tierMeta[c.tier]?.border ?? '#ddd' }"
        >
          <div class="coupon-emoji">{{ tierMeta[c.tier]?.emoji ?? "🎟️" }}</div>
          <div class="coupon-name" :style="{ color: tierMeta[c.tier]?.color ?? '#072536' }">{{ c.name }}</div>
          <div class="coupon-discount">{{ c.discount_percent }}% atlaide</div>
          <div class="coupon-cost">{{ c.points_required.toLocaleString("lv-LV") }} pts</div>

          <div class="coupon-progress-wrap">
            <div
              class="coupon-progress-bar"
              :style="{
                width: Math.min(100, Math.round((points / c.points_required) * 100)) + '%',
                background: tierMeta[c.tier]?.border ?? '#13c4ab',
              }"
            ></div>
          </div>
          <div class="coupon-progress-text">
            {{ Math.min(points, c.points_required).toLocaleString("lv-LV") }} / {{ c.points_required.toLocaleString("lv-LV") }} pts
          </div>

          <button
            class="redeem-btn"
            :disabled="points < c.points_required"
            :style="points >= c.points_required ? { background: tierMeta[c.tier]?.color ?? '#072536', borderColor: tierMeta[c.tier]?.color ?? '#072536' } : {}"
            @click="redeem(c.id)"
          >
            {{ points >= c.points_required ? "Aktivizēt" : `Pietrūkst ${(c.points_required - points).toLocaleString("lv-LV")} pts` }}
          </button>
        </div>
      </div>

      <!-- MANI KUPONI -->
      <h2 class="section-title" style="margin-top: 40px;">Mani kuponi</h2>

      <p v-if="myCoupons.length === 0" class="muted">
        Tev vēl nav aktivizētu kuponu. Sakrāj punktus un izmanto tos!
      </p>

      <div v-else class="my-coupons">
        <div v-for="uc in myCoupons" :key="uc.id" class="my-coupon" :class="{ used: uc.is_used }">
          <div class="my-coupon-left">
            <div class="my-coupon-emoji">{{ tierMeta[uc.tier]?.emoji ?? "🎟️" }}</div>
            <div>
              <div class="my-coupon-name">{{ uc.name }}</div>
              <div class="my-coupon-discount">{{ uc.discount_percent }}% atlaide</div>
            </div>
          </div>
          <div class="my-coupon-right">
            <div class="my-coupon-code" @click="copyCode(uc.code)" title="Noklikšķini, lai kopētu">
              {{ uc.code }} <span class="copy-hint">📋</span>
            </div>
            <div v-if="uc.is_used" class="used-badge">Izmantots {{ uc.used_at }}</div>
            <div v-else class="active-badge">Aktīvs</div>
          </div>
        </div>
      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap { max-width: 900px; margin: 0 auto; padding: 24px 16px 48px; color: #072536; }

.head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.title { font-size: 28px; font-weight: 900; }
.back { font-size: 14px; font-weight: 700; color: rgba(7,37,54,.65); text-decoration: none; }
.back:hover { color: #072536; }

.flash-ok {
  background: rgba(19,196,171,.12);
  border: 1px solid rgba(19,196,171,.4);
  color: #06616d;
  font-weight: 700;
  padding: 12px 16px;
  border-radius: 12px;
  margin-bottom: 20px;
}

.points-card {
  background: linear-gradient(135deg, #072536 0%, #97276b 100%);
  border-radius: 20px;
  padding: 28px 32px;
  color: #fff;
  margin-bottom: 36px;
}
.points-label { font-size: 13px; font-weight: 700; opacity: .75; text-transform: uppercase; letter-spacing: .08em; }
.points-value { font-size: 52px; font-weight: 900; line-height: 1.1; margin: 8px 0 4px; }
.points-value span { font-size: 22px; font-weight: 700; opacity: .7; }
.points-hint { font-size: 13px; opacity: .65; }

.section-title { font-size: 18px; font-weight: 900; margin-bottom: 16px; }

.coupons-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(165px, 1fr));
  gap: 14px;
}

.coupon-card {
  border: 1.5px solid;
  border-radius: 16px;
  padding: 20px 16px 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  text-align: center;
}

.coupon-emoji { font-size: 36px; line-height: 1; }
.coupon-name { font-size: 14px; font-weight: 800; }
.coupon-discount { font-size: 22px; font-weight: 900; color: #072536; }
.coupon-cost { font-size: 12px; font-weight: 700; color: rgba(7,37,54,.55); }

.coupon-progress-wrap {
  width: 100%;
  height: 6px;
  background: rgba(7,37,54,.1);
  border-radius: 999px;
  overflow: hidden;
  margin-top: 4px;
}
.coupon-progress-bar { height: 100%; border-radius: 999px; transition: width .4s ease; }
.coupon-progress-text { font-size: 11px; font-weight: 700; color: rgba(7,37,54,.5); }

.redeem-btn {
  margin-top: 6px;
  width: 100%;
  padding: 9px 12px;
  border-radius: 10px;
  border: 1.5px solid rgba(7,37,54,.2);
  background: transparent;
  color: rgba(7,37,54,.45);
  font-size: 12px;
  font-weight: 700;
  cursor: not-allowed;
  transition: all .15s;
  font-family: inherit;
}
.redeem-btn:not(:disabled) { color: #fff; cursor: pointer; }
.redeem-btn:not(:disabled):hover { filter: brightness(1.12); transform: translateY(-1px); }

.my-coupons { display: grid; gap: 10px; }

.my-coupon {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  background: #fff;
  border: 1px solid rgba(7,37,54,.1);
  border-radius: 14px;
  padding: 14px 18px;
  box-shadow: 0 4px 16px rgba(7,37,54,.06);
}
.my-coupon.used { opacity: .55; }

.my-coupon-left { display: flex; align-items: center; gap: 12px; }
.my-coupon-emoji { font-size: 28px; }
.my-coupon-name { font-weight: 800; font-size: 15px; }
.my-coupon-discount { font-size: 13px; color: rgba(7,37,54,.6); font-weight: 700; }

.my-coupon-right { text-align: right; }

.my-coupon-code {
  font-family: monospace;
  font-size: 15px;
  font-weight: 700;
  letter-spacing: .1em;
  color: #072536;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  justify-content: flex-end;
}
.my-coupon-code:hover { opacity: .75; }

.active-badge {
  display: inline-block;
  margin-top: 4px;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  background: rgba(19,196,171,.12);
  color: #06616d;
}
.used-badge {
  display: inline-block;
  margin-top: 4px;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  background: rgba(7,37,54,.07);
  color: rgba(7,37,54,.55);
}

.muted { color: rgba(7,37,54,.65); font-weight: 700; font-size: 15px; }
</style>
