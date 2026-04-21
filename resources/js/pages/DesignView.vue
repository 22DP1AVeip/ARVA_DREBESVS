<script setup lang="ts">
import NavBar from '../components/NavBar.vue'
import Footer from '../components/NavFooter.vue'
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

interface Garment { id: string; name: string; file: string; gender: string }
interface Preset   { name: string; file: string }
interface Color    { hex: string; name: string }

const props = defineProps<{ garments: Garment[]; presets: Preset[]; colors: Color[] }>()

const genderFilter     = ref<'men' | 'women'>('men')
const selectedGarment  = ref<Garment>(props.garments[0])
const selectedColor    = ref('#ffffff')
const selectedPreset   = ref<string | null>(null)
const uploadedImage    = ref<string | null>(null)
const uploadedFile     = ref<File | null>(null)
const submitting       = ref(false)
const designSize       = ref(100)
const designX          = ref(50)
const designY          = ref(40)
const isDragging       = ref(false)
const previewRef       = ref<HTMLDivElement | null>(null)

const filteredGarments = computed(() =>
    props.garments.filter(g => g.gender === genderFilter.value)
)

const activeDesignSrc = computed(() => {
    if (uploadedImage.value) return uploadedImage.value
    if (selectedPreset.value) return `/designs/presets/${selectedPreset.value}`
    return null
})

const colorFilter = computed(() => {
    const hex = selectedColor.value
    if (hex === '#ffffff') return 'none'
    if (hex === '#000000') return 'brightness(0.05) contrast(1.2)'
    if (hex === '#ff0000') return 'sepia(1) saturate(8) hue-rotate(0deg) brightness(0.9)'
    if (hex === '#0000ff') return 'sepia(1) saturate(8) hue-rotate(195deg) brightness(0.8)'
    if (hex === '#008000') return 'sepia(1) saturate(6) hue-rotate(85deg) brightness(0.7)'
    if (hex === '#ffff00') return 'sepia(1) saturate(8) hue-rotate(35deg) brightness(1.1)'
    if (hex === '#ffc0cb') return 'sepia(1) saturate(4) hue-rotate(295deg) brightness(1.1)'
    if (hex === '#808080') return 'grayscale(1) brightness(0.6)'
    return 'none'
})

const designOverlayStyle = computed(() => {
    const s = Math.max(20, Math.min(designSize.value, 250))
    return {
        position: 'absolute' as const,
        width: `${s}px`,
        height: `${s}px`,
        left: `calc(${designX.value}% - ${s / 2}px)`,
        top: `calc(${designY.value}% - ${s / 2}px)`,
        objectFit: 'contain' as const,
        pointerEvents: 'none' as const,
        zIndex: '10',
    }
})

function onMouseDown(e: MouseEvent) {
    if (!activeDesignSrc.value) return
    isDragging.value = true
    e.preventDefault()
}

function onMouseMove(e: MouseEvent) {
    if (!isDragging.value || !previewRef.value) return
    const rect = previewRef.value.getBoundingClientRect()
    const s = designSize.value / 2
    let x = ((e.clientX - rect.left) / rect.width) * 100
    let y = ((e.clientY - rect.top) / rect.height) * 100
    const halfW = (s / rect.width) * 100
    const halfH = (s / rect.height) * 100
    x = Math.max(halfW + 5, Math.min(100 - halfW - 5, x))
    y = Math.max(halfH + 5, Math.min(100 - halfH - 5, y))
    designX.value = x
    designY.value = y
}

function onMouseUp() { isDragging.value = false }

function onTouchMove(e: TouchEvent) {
    if (!isDragging.value || !previewRef.value) return
    e.preventDefault()
    const touch = e.touches[0]
    const rect = previewRef.value.getBoundingClientRect()
    const s = designSize.value / 2
    let x = ((touch.clientX - rect.left) / rect.width) * 100
    let y = ((touch.clientY - rect.top) / rect.height) * 100
    const halfW = (s / rect.width) * 100
    const halfH = (s / rect.height) * 100
    x = Math.max(halfW + 5, Math.min(100 - halfW - 5, x))
    y = Math.max(halfH + 5, Math.min(100 - halfH - 5, y))
    designX.value = x
    designY.value = y
}

onMounted(() => {
    window.addEventListener('mouseup', onMouseUp)
    window.addEventListener('mousemove', onMouseMove)
})

function onFileChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    uploadedFile.value = file
    selectedPreset.value = null
    const reader = new FileReader()
    reader.onload = (ev) => { uploadedImage.value = ev.target?.result as string }
    reader.readAsDataURL(file)
}

function selectPreset(file: string) {
    selectedPreset.value = selectedPreset.value === file ? null : file
    uploadedImage.value = null
    uploadedFile.value = null
}

function selectGarment(g: Garment) {
    selectedGarment.value = g
}

function submit() {
    if (submitting.value) return
    submitting.value = true
    const form = new FormData()
    form.append('garment_id',      selectedGarment.value.id)
    form.append('base_color',      selectedColor.value)
    form.append('design_position', `${Math.round(designX.value)},${Math.round(designY.value)}`)
    form.append('design_size',     String(designSize.value))
    if (selectedPreset.value) form.append('preset_design', selectedPreset.value)
    if (uploadedFile.value)   form.append('design_image',  uploadedFile.value)
    router.post('/design/store', form, {
        forceFormData: true,
        onFinish: () => (submitting.value = false),
    })
}
</script>

<template>
    <main>
        <NavBar />
        <div class="wrap">
            <h2 class="page-title">Dizaina veidotajs</h2>
            <div class="main-grid">

                <!-- Priekšskatījums -->
                <div class="preview-card">
                    <div class="section-label">Prieksskatijums</div>
                    <div
                        class="preview-box"
                        ref="previewRef"
                        @mousedown="onMouseDown"
                        @touchstart="isDragging = true"
                        @touchmove="onTouchMove"
                        @touchend="isDragging = false"
                        :style="{ cursor: isDragging ? 'grabbing' : (activeDesignSrc ? 'grab' : 'default') }"
                    >
                        <img
                            :src="`/designs/garments/${selectedGarment.file}`"
                            :alt="selectedGarment.name"
                            class="garment-img"
                            :style="{ filter: colorFilter }"
                            draggable="false"
                        />
                        <img
                            v-if="activeDesignSrc"
                            :src="activeDesignSrc"
                            alt="dizains"
                            :style="designOverlayStyle"
                            draggable="false"
                        />
                        <div v-if="!activeDesignSrc" class="drag-hint">
                            Izvēlies motivu vai augšupieladē attēlu
                        </div>
                    </div>
                    <div class="garment-name">{{ selectedGarment.name }}</div>
                </div>

                <!-- Iestatījumi -->
                <div class="settings">

                    <!-- 1. Apģērbs -->
                    <div class="section">
                        <div class="section-title">1. Apgerbs</div>
                        <div class="gender-tabs">
                            <button class="tab" :class="{ active: genderFilter === 'men' }" @click="genderFilter = 'men'" type="button">Viriesu</button>
                            <button class="tab" :class="{ active: genderFilter === 'women' }" @click="genderFilter = 'women'" type="button">Sieviesu</button>
                        </div>
                        <div class="garment-grid">
                            <button
                                v-for="g in filteredGarments" :key="g.id"
                                class="garment-btn"
                                :class="{ active: selectedGarment.id === g.id }"
                                type="button"
                                @click="selectGarment(g)"
                            >
                                <img :src="`/designs/garments/${g.file}`" :alt="g.name" class="garment-thumb" draggable="false" />
                                <span>{{ g.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Krāsa -->
                    <div class="section">
                        <div class="section-title">2. Krasa</div>
                        <div class="color-row">
                            <button
                                v-for="c in colors" :key="c.hex"
                                class="color-dot"
                                :class="{ active: selectedColor === c.hex }"
                                :style="{ backgroundColor: c.hex }"
                                :title="c.name"
                                type="button"
                                @click="selectedColor = c.hex"
                            />
                        </div>
                        <div class="color-label">{{ colors.find(c => c.hex === selectedColor)?.name }}</div>
                    </div>

                    <!-- 3. Motīvs -->
                    <div class="section">
                        <div class="section-title">3. Motivs</div>
                        <div class="preset-row">
                            <button
                                v-for="p in presets" :key="p.file"
                                class="preset-btn"
                                :class="{ active: selectedPreset === p.file }"
                                type="button"
                                @click="selectPreset(p.file)"
                            >
                                <img :src="`/designs/presets/${p.file}`" :alt="p.name" class="preset-img" />
                                <span>{{ p.name }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- 4. Sava bilde -->
                    <div class="section">
                        <div class="section-title">4. Sava bilde</div>
                        <label class="upload-label">
                            <input type="file" accept="image/*" class="file-hidden" @change="onFileChange" />
                            <span>{{ uploadedFile ? uploadedFile.name : 'Izveleties failu...' }}</span>
                        </label>
                        <div class="hint">PNG ar caurspidigu fonu — labakais rezultats</div>
                    </div>

                    <!-- 5. Izmērs -->
                    <div class="section">
                        <div class="section-title">5. Dizaina izmers (px)</div>
                        <div class="size-row">
                            <button class="size-btn" type="button" @click="designSize = Math.max(20, designSize - 10)">-</button>
                            <input type="number" v-model.number="designSize" min="20" max="250" class="size-input" />
                            <button class="size-btn" type="button" @click="designSize = Math.min(250, designSize + 10)">+</button>
                        </div>
                        <div class="hint">Velc dizainu ar peli lai mainitu poziciju</div>
                    </div>

                    <!-- Saglabāt -->
                    <button class="submit-btn" type="button" :disabled="submitting" @click="submit">
                        {{ submitting ? 'Saglaba...' : 'Saglabt dizainu' }}
                    </button>

                </div>
            </div>
        </div>
        <Footer />
    </main>
</template>

<style scoped>
.wrap { max-width: 1200px; margin: 0 auto; padding: 28px 16px; }
.page-title { font-size: 24px; font-weight: 800; margin-bottom: 24px; color: #111; }
.main-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px; align-items: start; }
@media (max-width: 850px) { .main-grid { grid-template-columns: 1fr; } }

.preview-card { background: #fff; border: 1px solid #e5e5e5; border-radius: 14px; padding: 16px; position: sticky; top: 20px; }
.section-label { font-weight: 700; margin-bottom: 12px; font-size: 15px; color: #111; }
.preview-box { position: relative; width: 100%; height: 420px; border-radius: 10px; overflow: hidden; background: #f8f8f8; border: 1px solid #e5e5e5; user-select: none; }
.garment-img { width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0; transition: filter 0.25s ease; user-select: none; }
.drag-hint { position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #999; background: rgba(255,255,255,0.8); padding: 4px 10px; border-radius: 20px; pointer-events: none; white-space: nowrap; }
.garment-name { text-align: center; margin-top: 10px; font-weight: 600; color: #555; font-size: 14px; }

.settings { display: flex; flex-direction: column; gap: 12px; }
.section { background: #fff; border: 1px solid #e5e5e5; border-radius: 12px; padding: 16px; }
.section-title { font-weight: 700; margin-bottom: 12px; font-size: 13px; color: #111; text-transform: uppercase; letter-spacing: 0.5px; }

.gender-tabs { display: flex; gap: 8px; margin-bottom: 12px; }
.tab { padding: 7px 20px; border-radius: 6px; border: 1.5px solid #111; background: #fff; cursor: pointer; font-weight: 600; font-size: 13px; color: #111; transition: all .15s; }
.tab.active { background: #111; color: #fff; }
.tab:hover:not(.active) { background: #f0f0f0; }

.garment-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.garment-btn { display: flex; flex-direction: column; align-items: center; gap: 5px; padding: 8px; border-radius: 8px; border: 1.5px solid #e5e5e5; background: #fafafa; cursor: pointer; transition: all .15s; font-size: 11px; color: #555; }
.garment-btn.active { border-color: #111; background: #f0f0f0; }
.garment-btn:hover:not(.active) { border-color: #999; }
.garment-thumb { width: 70px; height: 70px; object-fit: contain; background: #fff; border-radius: 6px; }

.color-row { display: flex; flex-wrap: wrap; gap: 8px; }
.color-dot { width: 30px; height: 30px; border-radius: 50%; border: 2.5px solid #ddd; cursor: pointer; transition: transform .15s, border-color .15s; }
.color-dot.active { border-color: #111; transform: scale(1.2); }
.color-label { margin-top: 8px; font-size: 13px; font-weight: 600; color: #333; }

.preset-row { display: flex; flex-wrap: wrap; gap: 8px; }
.preset-btn { display: flex; flex-direction: column; align-items: center; gap: 4px; padding: 8px; border-radius: 8px; border: 1.5px solid #e5e5e5; background: #fafafa; cursor: pointer; font-size: 11px; color: #555; width: 68px; transition: all .15s; }
.preset-btn.active { border-color: #111; background: #f0f0f0; }
.preset-btn:hover:not(.active) { border-color: #999; }
.preset-img { width: 44px; height: 44px; object-fit: contain; }

.upload-label { display: block; padding: 10px 14px; border: 1.5px dashed #bbb; border-radius: 8px; cursor: pointer; font-size: 13px; color: #555; text-align: center; transition: border-color .15s; }
.upload-label:hover { border-color: #111; color: #111; }
.file-hidden { display: none; }

.size-row { display: flex; align-items: center; gap: 10px; }
.size-btn { width: 36px; height: 36px; border-radius: 6px; border: 1.5px solid #111; background: #fff; font-size: 18px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .15s; color: #111; }
.size-btn:hover { background: #111; color: #fff; }
.size-input { width: 80px; padding: 8px; border: 1.5px solid #e5e5e5; border-radius: 6px; font-size: 15px; font-weight: 700; text-align: center; color: #111; }
.hint { font-size: 11px; color: #999; margin-top: 6px; }

.submit-btn { width: 100%; padding: 14px; border-radius: 8px; border: none; background: #111; color: #fff; font-weight: 800; font-size: 15px; cursor: pointer; transition: opacity .2s; letter-spacing: 0.3px; }
.submit-btn:hover { opacity: 0.85; }
.submit-btn:disabled { opacity: .4; cursor: not-allowed; }
</style>