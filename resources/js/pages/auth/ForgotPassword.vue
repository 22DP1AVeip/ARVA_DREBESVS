<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div class="auth-page">
        <Head title="Aizmirsu paroli" />

        <div class="auth-shell">
            <section class="auth-hero">
                <img src="/bildites/Logo_Arva.png" alt="Arva logo" class="auth-logo" />
                <p class="auth-kicker">Atgūsti piekļuvi</p>
                <h1>Aizmirsu paroli</h1>
                <p class="auth-subtitle">
                    Ievadi e-pasta adresi, lai nosūtītu paroles atiestatīšanas saiti.
                </p>
                <div class="auth-pills">
                    <span>Droša atiestatīšana</span>
                    <span>Ātra palīdzība</span>
                    <span>Atgriezies pie iepirkšanās</span>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-card-head">
                    <h2>Paroles atjaunošana</h2>
                    <p>Nosūtīsim saiti uz tavu e-pastu.</p>
                </div>

                <div v-if="status" class="auth-status">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="auth-form">
                    <div class="field">
                        <Label for="email">E-pasta adrese</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="off"
                            v-model="form.email"
                            autofocus
                            placeholder="email@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <Button class="auth-button" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Nosūtīt paroles atiestatīšanas saiti
                    </Button>
                </form>

                <div class="auth-footer">
                    Vai atgriezties pie
                    <TextLink :href="route('login')" class="auth-link">ienākšanas</TextLink>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
.auth-page {
    --arva-ink: #072536;
    --arva-teal: #13c4ab;
    --arva-pink: #de7388;
    --arva-purple: #97276b;
    --arva-bg: #ffffff;
    --arva-bg-soft: #f7fbfc;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
    background: linear-gradient(135deg, #f7fbfc 0%, #ffffff 50%, #fdf3f6 100%);
    color: var(--arva-ink);
}

.auth-shell {
    width: min(1100px, 100%);
    display: grid;
    gap: 24px;
}

.auth-hero {
    background: linear-gradient(140deg, rgba(19, 196, 171, 0.18), rgba(151, 39, 107, 0.08));
    border: 1px solid rgba(7, 37, 54, 0.08);
    border-radius: 24px;
    padding: 32px;
    display: grid;
    gap: 14px;
    box-shadow: 0 22px 50px rgba(7, 37, 54, 0.16);
}

.auth-logo {
    width: 120px;
}

.auth-kicker {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 11px;
    color: rgba(7, 37, 54, 0.72);
}

.auth-hero h1 {
    font-size: clamp(26px, 3.2vw, 34px);
    font-weight: 800;
}

.auth-subtitle {
    font-size: 15px;
    line-height: 1.6;
    color: rgba(7, 37, 54, 0.72);
}

.auth-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.auth-pills span {
    background: rgba(7, 37, 54, 0.08);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.auth-card {
    background: var(--arva-bg);
    border: 1px solid rgba(7, 37, 54, 0.12);
    border-radius: 24px;
    padding: 28px;
    display: grid;
    gap: 18px;
    box-shadow: 0 22px 50px rgba(7, 37, 54, 0.16);
}

.auth-card-head h2 {
    font-size: 20px;
    font-weight: 800;
    margin: 0;
}

.auth-card-head p {
    margin: 4px 0 0;
    color: rgba(7, 37, 54, 0.65);
}

.auth-status {
    background: rgba(19, 196, 171, 0.16);
    border: 1px solid rgba(19, 196, 171, 0.35);
    color: rgba(7, 37, 54, 0.9);
    padding: 10px 12px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
}

.auth-form {
    display: grid;
    gap: 16px;
}

.field {
    display: grid;
    gap: 6px;
}

.auth-button {
    background: linear-gradient(120deg, var(--arva-ink), var(--arva-purple));
    color: #fff;
    font-weight: 700;
}

.auth-footer {
    text-align: center;
    font-size: 14px;
    color: rgba(7, 37, 54, 0.72);
}

.auth-link {
    font-weight: 700;
    color: var(--arva-ink);
    text-decoration: underline;
    text-underline-offset: 4px;
}

.auth-page :deep(input),
.auth-page :deep(textarea),
.auth-page :deep(select) {
    color: #ffffff;
}

.auth-page :deep(input::placeholder),
.auth-page :deep(textarea::placeholder) {
    color: rgba(255, 255, 255, 0.7);
}

@media (min-width: 960px) {
    .auth-shell {
        grid-template-columns: 1.05fr 0.95fr;
        align-items: center;
    }
}
</style>
