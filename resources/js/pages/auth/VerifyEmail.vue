<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, MailCheck, ShieldCheck } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <div class="verify-page">
        <Head title="E-pasta apstiprināšana" />

        <div class="verify-shell">
            <section class="verify-hero">
                <img src="/bildites/Logo_Arva.png" alt="Arva logo" class="verify-logo" />
                <div class="verify-badge">
                    <ShieldCheck class="h-4 w-4" />
                    Droša piekļuve Arva videi
                </div>
                <h1>Apstiprini e-pastu</h1>
                <p>
                    Lūdzu, apstiprini savu e-pasta adresi, noklikšķinot uz saites, ko tikko nosūtījām. Tas palīdz mums
                    saglabāt kontu drošu.
                </p>
                <div class="verify-steps">
                    <div class="verify-step">
                        <span class="step-dot"></span>
                        Pārbaudi iesūtni vai mēstuļu mapi
                    </div>
                    <div class="verify-step">
                        <span class="step-dot"></span>
                        Atver apstiprinājuma saiti
                    </div>
                    <div class="verify-step">
                        <span class="step-dot"></span>
                        Atgriezies, lai turpinātu
                    </div>
                </div>
            </section>

            <section class="verify-card">
                <div class="verify-card-head">
                    <div class="icon-ring">
                        <MailCheck class="h-6 w-6" />
                    </div>
                    <div>
                        <h2>Vēl tikai viens solis</h2>
                        <p>Pārbaudi savu pastkasti un apstiprini e-pastu.</p>
                    </div>
                </div>

                <div v-if="status === 'verification-link-sent'" class="status-card">
                    Jauna apstiprinājuma saite ir nosūtīta uz tavu e-pasta adresi.
                </div>

                <form @submit.prevent="submit" class="verify-actions">
                    <Button type="submit" :disabled="form.processing" class="verify-button">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Nosūtīt apstiprinājuma e-pastu vēlreiz
                    </Button>

                    <p class="verify-help">
                        Nesaņēmi e-pastu? Pārbaudi mēstuļu mapi vai mēģini nosūtīt vēlreiz pēc dažām minūtēm.
                    </p>

                    <TextLink :href="route('logout')" method="post" as="button" class="verify-logout">
                        Izrakstīties
                    </TextLink>
                </form>
            </section>
        </div>
    </div>
</template>

<style scoped>
.verify-page {
    --arva-ink: #072536;
    --arva-teal: #13c4ab;
    --arva-pink: #de7388;
    --arva-purple: #97276b;
    --arva-bg: #ffffff;
    --arva-bg-soft: #f7fbfc;
    --arva-border: rgba(7, 37, 54, 0.12);
    --arva-shadow: 0 24px 60px rgba(7, 37, 54, 0.18);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: linear-gradient(135deg, #f7fbfc 0%, #ffffff 45%, #fdf3f6 100%);
    color: var(--arva-ink);
    position: relative;
    overflow: hidden;
}

.verify-page::before,
.verify-page::after {
    content: '';
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 999px;
    filter: blur(10px);
    opacity: 0.35;
    z-index: 0;
}

.verify-page::before {
    top: -120px;
    right: -140px;
    background: radial-gradient(circle, rgba(19, 196, 171, 0.4), rgba(19, 196, 171, 0));
}

.verify-page::after {
    bottom: -160px;
    left: -140px;
    background: radial-gradient(circle, rgba(222, 115, 136, 0.4), rgba(222, 115, 136, 0));
}

.verify-shell {
    position: relative;
    z-index: 1;
    width: min(980px, 100%);
    display: grid;
    gap: 24px;
}

.verify-hero {
    background: linear-gradient(140deg, rgba(19, 196, 171, 0.16), rgba(151, 39, 107, 0.08));
    border: 1px solid rgba(7, 37, 54, 0.08);
    border-radius: 24px;
    padding: 28px;
    display: grid;
    gap: 16px;
    box-shadow: var(--arva-shadow);
}

.verify-logo {
    width: 120px;
}

.verify-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(7, 37, 54, 0.08);
    color: var(--arva-ink);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
}

.verify-hero h1 {
    font-size: clamp(26px, 3vw, 34px);
    font-weight: 700;
}

.verify-hero p {
    font-size: 15px;
    line-height: 1.6;
    color: rgba(7, 37, 54, 0.78);
}

.verify-steps {
    display: grid;
    gap: 10px;
    margin-top: 6px;
}

.verify-step {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 600;
}

.step-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: linear-gradient(140deg, var(--arva-teal), var(--arva-pink));
    box-shadow: 0 0 0 4px rgba(19, 196, 171, 0.12);
}

.verify-card {
    background: var(--arva-bg);
    border: 1px solid var(--arva-border);
    border-radius: 24px;
    padding: 28px;
    display: grid;
    gap: 20px;
    box-shadow: var(--arva-shadow);
}

.verify-card-head {
    display: flex;
    gap: 16px;
    align-items: center;
}

.icon-ring {
    width: 44px;
    height: 44px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: linear-gradient(140deg, rgba(19, 196, 171, 0.2), rgba(222, 115, 136, 0.2));
    color: var(--arva-ink);
}

.verify-card h2 {
    font-size: 20px;
    font-weight: 700;
}

.verify-card p {
    font-size: 14px;
    color: rgba(7, 37, 54, 0.72);
}

.status-card {
    background: rgba(19, 196, 171, 0.16);
    border: 1px solid rgba(19, 196, 171, 0.32);
    color: rgba(7, 37, 54, 0.9);
    padding: 12px 14px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
}

.verify-actions {
    display: grid;
    gap: 12px;
}

.verify-button {
    background: linear-gradient(120deg, var(--arva-ink), var(--arva-purple));
    color: #fff;
    font-weight: 600;
    border: none;
    box-shadow: 0 14px 30px rgba(7, 37, 54, 0.25);
}

.verify-button:disabled {
    opacity: 0.7;
    box-shadow: none;
}

.verify-help {
    font-size: 13px;
    color: rgba(7, 37, 54, 0.68);
}

.verify-logout {
    font-size: 13px;
    font-weight: 600;
    color: var(--arva-ink);
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 4px;
}

@media (min-width: 900px) {
    .verify-shell {
        grid-template-columns: 1.05fr 0.95fr;
        align-items: center;
    }
}
</style>
