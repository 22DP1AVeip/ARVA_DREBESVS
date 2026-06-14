<script setup lang="ts">
import NavBar from "../components/NavBar.vue";
import Footer from "../components/NavFooter.vue";
import { computed, ref, watch } from "vue";
import { usePage, Link, router } from "@inertiajs/vue3";

type Gender = "men" | "women";

interface DbProduct {
  id: number;
  name: string;
  price: string | number;
  category: string;
  gender: string;
  image_men: string | null;
  image_women: string | null;
}

interface Variant {
  id: number;
  size: string;
  color: string;
  price: string | number | null;
}

const page = usePage<any>();

const product    = computed(() => page.props.product as DbProduct);
const view       = computed(() => (page.props.view as Gender) ?? "men");
const reviews    = computed<any[]>(() => page.props.reviews ?? []);
const userReview = computed<any>(() => page.props.userReview ?? null);
const avgRating  = computed<number|null>(() => page.props.avgRating ?? null);
const auth       = computed(() => (page.props.auth as any)?.user ?? null);

// Atsauksmes forma
const reviewRating     = ref<number>(0);
const reviewComment    = ref<string>("");
const hoverStar        = ref<number>(0);
const reviewSubmitting = ref(false);

watch(userReview, (r) => {
  reviewRating.value  = r?.rating  ?? 0;
  reviewComment.value = r?.comment ?? "";
}, { immediate: true });

function submitReview() {
  if (!reviewRating.value) return;
  reviewSubmitting.value = true;
  router.post(`/product/${product.value.id}/review`, {
    rating:  reviewRating.value,
    comment: reviewComment.value,
  }, {
    preserveScroll: true,
    onFinish: () => (reviewSubmitting.value = false),
  });
}

function deleteReview() {
  router.delete(`/product/${product.value.id}/review`, { preserveScroll: true });
}

const variants = computed<Variant[]>(() => page.props.variants ?? []);

const favoritesIds = computed<number[]>(() => page.props.favoritesIds ?? []);
const isFavorite = computed(() => favoritesIds.value.includes(product.value.id));

const heartBump = ref(false);

function toggleFavorite() {
  heartBump.value = true;
  setTimeout(() => (heartBump.value = false), 200);

  router.post(`/favorites/toggle/${product.value.id}`, {}, {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ["favoritesIds"] }),
  });
}

const selectedImage = computed(() => {
  if (view.value === "women") {
    return product.value.image_women ?? product.value.image_men ?? "";
  }
  return product.value.image_men ?? product.value.image_women ?? "";
});

const backHref = computed(() => (view.value === "women" ? "/WomanWear" : "/MenWear"));

const selectedColor = ref("");
const selectedSize = ref("");

const colors = computed(() => Array.from(new Set(variants.value.map(v => v.color))));

const sizes = computed(() => {
  const filtered = selectedColor.value
    ? variants.value.filter(v => v.color === selectedColor.value)
    : variants.value;

  return Array.from(new Set(filtered.map(v => v.size)));
});

const selectedVariant = computed(() =>
  variants.value.find(v => v.color === selectedColor.value && v.size === selectedSize.value)
);

const displayPrice = computed(() => {
  const p = selectedVariant.value?.price ?? product.value.price;
  return Number(p).toFixed(2);
});

const canAdd = computed(() => !!selectedVariant.value);

const adding = ref(false);
const flashGrey = ref(false);

function addToCart() {
  if (!selectedVariant.value || adding.value) return;

  adding.value = true;
  flashGrey.value = true;

  setTimeout(() => (flashGrey.value = false), 450);

  router.post(`/cart/add/${selectedVariant.value.id}`, { qty: 1, view: view.value }, {
    preserveScroll: true,
    onFinish: () => (adding.value = false),
  });
}
</script>

<template>
  <main>
    <NavBar />

    <div class="wrap">
      <div class="breadcrumbs">
        <Link :href="backHref">Atpakaļ uz produktiem</Link>
      </div>

      <div class="grid">
        <div class="images">
          <div class="main-img">
            <img :src="selectedImage" :alt="product.name" />
          </div>
        </div>

        <div class="info">
          <div class="title-row">
            <h1 class="title">{{ product.name }}</h1>

            <button
              class="heart"
              :class="{ active: isFavorite, bump: heartBump }"
              @click="toggleFavorite"
              aria-label="Favorīts"
              type="button"
            >
              <svg viewBox="0 0 24 24" class="heart-ico">
                <path
                  d="M12 21s-7-4.6-10-9.4C.5 8.7 2.3 5.5 5.8 5.1c1.8-.2 3.5.7 4.4 2 1-1.3 2.6-2.2 4.4-2 3.5.4 5.3 3.6 3.8 6.5C19 16.4 12 21 12 21z"
                />
              </svg>
            </button>
          </div>

          <div class="price">€{{ displayPrice }}</div>

          <div class="meta">
            <div><strong>Kategorija:</strong> {{ product.category }}</div>
          </div>

          <hr class="sep" />

          <div class="option">
            <div class="label">Krāsa</div>

            <div v-if="colors.length" class="chips">
              <button
                v-for="c in colors"
                :key="c"
                class="chip"
                :class="{ active: selectedColor === c }"
                type="button"
                @click="() => { selectedColor = c; selectedSize = '' }"
              >
                {{ c }}
              </button>
            </div>

            <div v-else class="hint">Šim produktam nav pievienoti varianti.</div>
          </div>

          <div class="option">
            <div class="label">Izmērs</div>

            <div class="sizes">
              <button
                v-for="s in sizes"
                :key="s"
                class="size"
                :class="{ active: selectedSize === s }"
                type="button"
                :disabled="!selectedColor"
                @click="() => { selectedSize = s }"
              >
                {{ s }}
              </button>
            </div>

            <div class="hint" v-if="!selectedVariant">Izvēlies krāsu un izmēru.</div>
          </div>

          <button
            class="add"
            :class="{ flash: flashGrey }"
            type="button"
            @click.prevent.stop="addToCart"
            :disabled="!canAdd || adding"
          >
            {{ adding ? "Pievieno..." : "Pievienot grozam" }}
          </button>

        </div>
      </div>

      <!-- ATSAUKSMES -->
      <div class="reviews-section">

        <!-- VIRSRAKSTS AR VIDĒJO VĒRTĒJUMU -->
        <div class="reviews-head">
          <h2 class="reviews-title">Atsauksmes</h2>
          <div v-if="avgRating" class="avg-rating">
            <span class="avg-stars">
              <span v-for="i in 5" :key="i" class="star-icon" :class="i <= Math.round(avgRating) ? 'filled' : ''">★</span>
            </span>
            <span class="avg-num">{{ avgRating }} / 5</span>
            <span class="avg-count">({{ reviews.length }} atsauksme{{ reviews.length === 1 ? '' : 's' }})</span>
          </div>
          <div v-else class="avg-empty">Vēl nav atsauksmju.</div>
        </div>

        <!-- FORMA — tikai pieslēgtiem lietotājiem -->
        <div v-if="auth" class="review-form-card">
          <div class="review-form-title">
            {{ userReview ? 'Tava atsauksme' : 'Raksti atsauksmi' }}
          </div>

          <!-- Zvaigznes -->
          <div class="stars-input">
            <button
              v-for="i in 5"
              :key="i"
              type="button"
              class="star-btn"
              :class="{ active: i <= (hoverStar || reviewRating) }"
              @mouseenter="hoverStar = i"
              @mouseleave="hoverStar = 0"
              @click="reviewRating = i"
            >★</button>
          </div>

          <textarea
            v-model="reviewComment"
            class="review-textarea"
            placeholder="Uzraksti savu viedokli par šo produktu... (neobligāti)"
            rows="3"
          />

          <div class="review-form-actions">
            <button
              class="review-submit-btn"
              :disabled="!reviewRating || reviewSubmitting"
              @click="submitReview"
            >
              {{ reviewSubmitting ? 'Saglabā...' : (userReview ? 'Atjaunināt' : 'Publicēt') }}
            </button>
            <button
              v-if="userReview"
              class="review-delete-btn"
              @click="deleteReview"
            >
              Dzēst
            </button>
          </div>
        </div>

        <div v-else class="review-login-hint">
          <Link href="/login" class="review-login-link">Pieslēdzies</Link>, lai atstātu atsauksmi.
        </div>

        <!-- ATSAUKSMJU SARAKSTS -->
        <div v-if="reviews.length" class="reviews-list">
          <div v-for="r in reviews" :key="r.id" class="review-card" :class="{ 'own': auth && r.user_id === auth.id }">
            <div class="review-top">
              <div class="review-author-wrap">
                <div class="review-avatar">{{ r.user_name.charAt(0).toUpperCase() }}</div>
                <div>
                  <div class="review-author">{{ r.user_name }}</div>
                  <div class="review-date">{{ r.created_at }}</div>
                </div>
              </div>
              <div class="review-stars">
                <span v-for="i in 5" :key="i" class="star-icon" :class="i <= r.rating ? 'filled' : ''">★</span>
              </div>
            </div>
            <p v-if="r.comment" class="review-comment">{{ r.comment }}</p>
          </div>
        </div>

      </div>
    </div>

    <Footer />
  </main>
</template>

<style scoped>
.wrap { max-width:1200px;margin:0 auto;padding:24px; }
.breadcrumbs { margin-bottom:16px; }
.grid { display:grid;grid-template-columns:1.2fr 1fr;gap:28px; }
@media (max-width:900px){ .grid{grid-template-columns:1fr;} }
.images .main-img{background:#fff;border-radius:12px;overflow:hidden;border:1px solid #eee;}
.images .main-img img{width:100%;height:520px;object-fit:cover;}
.info{background:#fff;border-radius:12px;border:1px solid #eee;padding:18px;}
.title{font-size:22px;font-weight:700;margin-bottom:8px;}
.price{font-size:20px;font-weight:700;margin-bottom:12px;color:#e74c3c;}
.meta{color:#333;font-size:14px;}
.sep{border:none;border-top:1px solid #eee;margin:16px 0;}
.option{margin-bottom:14px;}
.label{font-weight:600;margin-bottom:6px;}

.sizes{display:flex;gap:8px;flex-wrap:wrap;}
.size{padding:10px 12px;border-radius:10px;border:1px solid #ddd;background:#fafafa;cursor:pointer;}
.size.active{border-color:#111;background:#111;color:#fff;}

.hint{margin-top:8px;font-size:12px;color:#777;}

.add{width:100%;margin-top:12px;padding:14px;border-radius:10px;border:2px solid #111;background:#111;color:#fff;font-weight:800;font-size:16px;cursor:pointer;}
.add.flash{background:#8a8a8a;border-color:#8a8a8a;}
.add:disabled{opacity:.6;cursor:not-allowed;}

.chips{display:flex;gap:8px;flex-wrap:wrap;}
.chip{
  padding:10px 12px;
  border-radius:999px;
  border:1px solid #ddd;
  background:#fafafa;
  cursor:pointer;
  font-weight:600;
}
.chip.active{
  border-color:#111;
  background:#111;
  color:#fff;
}

.heart{width:42px;height:42px;border-radius:999px;border:1px solid rgba(0,0,0,.15);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:transform .15s ease;}
.heart-ico{width:22px;height:22px;}
.heart-ico path{fill:transparent;stroke:#000;stroke-width:1.8;transition:fill .2s ease;}
.heart.active .heart-ico path{fill:#de7388;stroke:#000;}
.heart.bump{transform:scale(1.15);}
.title-row{display:flex;justify-content:space-between;align-items:center;gap:10px;}

.reviews-section {
  margin-top: 40px;
  border-top: 1px solid rgba(7,37,54,.1);
  padding-top: 32px;
}

.reviews-head {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 20px;
}
.reviews-title { font-size: 20px; font-weight: 900; color: #072536; margin: 0; }
.avg-rating    { display: flex; align-items: center; gap: 8px; }
.avg-stars     { display: flex; gap: 2px; }
.avg-num       { font-size: 15px; font-weight: 800; color: #072536; }
.avg-count     { font-size: 13px; color: rgba(7,37,54,.55); font-weight: 600; }
.avg-empty     { font-size: 14px; color: rgba(7,37,54,.5); font-weight: 600; }

/* Zvaigznes */
.star-icon { font-size: 18px; color: #ddd; transition: color .1s; }
.star-icon.filled { color: #f5a623; }

/* Ievades forma */
.review-form-card {
  background: #fff;
  border: 1px solid rgba(7,37,54,.1);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 24px;
  box-shadow: 0 4px 16px rgba(7,37,54,.06);
}
.review-form-title { font-size: 15px; font-weight: 800; color: #072536; margin-bottom: 12px; }

.stars-input { display: flex; gap: 4px; margin-bottom: 12px; }
.star-btn {
  background: none; border: none; cursor: pointer;
  font-size: 32px; color: #ddd; transition: color .1s, transform .1s;
  padding: 0; line-height: 1;
}
.star-btn.active { color: #f5a623; }
.star-btn:hover  { transform: scale(1.15); }

.review-textarea {
  width: 100%;
  border: 1px solid rgba(7,37,54,.15);
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 14px;
  font-family: inherit;
  resize: vertical;
  color: #072536;
  background: #f7fbfc;
  transition: border-color .15s;
}
.review-textarea:focus { outline: none; border-color: #13c4ab; box-shadow: 0 0 0 3px rgba(19,196,171,.15); }

.review-form-actions { display: flex; gap: 10px; margin-top: 12px; }

.review-submit-btn {
  padding: 10px 22px; border-radius: 10px;
  background: linear-gradient(120deg, #072536, #97276b);
  color: #fff; border: none; font-weight: 700; font-size: 14px;
  cursor: pointer; transition: filter .15s;
}
.review-submit-btn:hover    { filter: brightness(1.1); }
.review-submit-btn:disabled { opacity: .6; cursor: not-allowed; }

.review-delete-btn {
  padding: 10px 16px; border-radius: 10px;
  background: transparent; border: 1px solid rgba(192,57,43,.35);
  color: #c0392b; font-weight: 700; font-size: 14px; cursor: pointer;
  transition: background .15s;
}
.review-delete-btn:hover { background: #c0392b; color: #fff; }

.review-login-hint {
  font-size: 14px; font-weight: 600; color: rgba(7,37,54,.6);
  margin-bottom: 20px;
}
.review-login-link { color: #06616d; font-weight: 700; text-decoration: underline; }

/* Atsauksmju saraksts */
.reviews-list { display: grid; gap: 12px; }

.review-card {
  background: #fff;
  border: 1px solid rgba(7,37,54,.08);
  border-radius: 14px;
  padding: 16px 18px;
  box-shadow: 0 3px 12px rgba(7,37,54,.05);
}
.review-card.own { border-color: rgba(19,196,171,.35); background: rgba(19,196,171,.03); }

.review-top {
  display: flex; justify-content: space-between;
  align-items: flex-start; gap: 12px; margin-bottom: 10px;
}
.review-author-wrap { display: flex; align-items: center; gap: 10px; }
.review-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, #072536, #97276b);
  color: #fff; font-weight: 800; font-size: 15px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.review-author { font-weight: 700; font-size: 14px; color: #072536; }
.review-date   { font-size: 12px; color: rgba(7,37,54,.5); margin-top: 2px; }
.review-stars  { display: flex; gap: 2px; flex-shrink: 0; }
.review-comment { font-size: 14px; color: rgba(7,37,54,.8); line-height: 1.6; margin: 0; }
</style>
