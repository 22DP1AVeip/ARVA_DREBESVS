<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="auth-page">
        <Head title="Ienākt" />

        <div class="auth-shell">
            <section class="auth-hero">
                <img src="/bildites/Logo_Arva.png" alt="Arva logo" class="auth-logo" />
                <p class="auth-kicker">Laipni lūgti atpakaļ</p>
                <h1>Ienāc savā kontā</h1>
                <p class="auth-subtitle">
                    Ievadi savu e-pastu un paroli, lai turpinātu iepirkšanos Arva vidē.
                </p>
                <div class="auth-pills">
                    <span>Drošs profils</span>
                    <span>Ātra piekļuve pasūtījumiem</span>
                    <span>Favorītu saraksts</span>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-card-head">
                    <h2>Autorizācija</h2>
                    <p>Priecājamies tevi redzēt!</p>
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
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="field">
                        <div class="field-row">
                            <Label for="password">Parole</Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="auth-link"
                                :tabindex="5"
                            >
                                Aizmirsāt paroli?
                            </TextLink>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            v-model="form.password"
                            placeholder="Parole"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="remember" :tabindex="3">
                        <Checkbox id="remember" v-model:checked="form.remember" :tabindex="4" />
                        <Label for="remember">Atcerēties mani</Label>
                    </div>

                    <Button type="submit" class="auth-button" :tabindex="4" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Ienākt
                    </Button>
                </form>

                <div class="auth-footer">
                    Nav konta?
                    <TextLink :href="route('register')" class="auth-link" :tabindex="6">Reģistrēties</TextLink>
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

.auth-page :deep(input),
.auth-page :deep(textarea),
.auth-page :deep(select) {
    color: var(--arva-ink);
    background-color: var(--arva-bg);
    border-color: rgba(7, 37, 54, 0.16);
}

.auth-page :deep(input::placeholder),
.auth-page :deep(textarea::placeholder) {
    color: rgba(7, 37, 54, 0.45);
}

.field-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.remember {
    display: flex;
    align-items: center;
    gap: 8px;
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

@media (min-width: 960px) {
    .auth-shell {
        grid-template-columns: 1.05fr 0.95fr;
        align-items: center;
    }
}
</style>
