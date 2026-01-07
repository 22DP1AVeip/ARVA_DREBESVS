<!-- Register.vue -->
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-white px-4 py-8">
        <Head title="Reģistrēties" />

        <img src="/bildites/Logo_Arva.png" alt="Logo" class="w-32 mb-6" />

        <div class="w-full max-w-md bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-2 text-center text-black">Izveido kontu</h1>
            <p class="text-sm text-center text-gray-700 mb-6">Ievadi savus datus, lai izveidotu kontu</p>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <Label for="name" class="text-black">Vārds</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Pilns vārds" class="text-white" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email" class="text-black">E-pasta adrese</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" class="text-white" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-black">Parole</Label>
                    <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" v-model="form.password" placeholder="Parole" class="text-white" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-black">Apstiprināt paroli</Label>
                    <Input id="password_confirmation" type="password" required :tabindex="4" autocomplete="new-password" v-model="form.password_confirmation" placeholder="Apstiprināt paroli" class="text-white" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Izveidot kontu
                </Button>

                <div class="text-center text-sm text-gray-700 mt-4">
                    Jau ir konts?
                    <TextLink :href="route('login')" class="text-black font-semibold underline underline-offset-4" :tabindex="6">Ienākt</TextLink>
                </div>
            </form>
        </div>
    </div>
</template>
