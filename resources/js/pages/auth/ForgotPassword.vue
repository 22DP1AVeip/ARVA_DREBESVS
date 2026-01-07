<!-- ForgotPassword.vue -->
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
    <div class="min-h-screen flex flex-col justify-center items-center bg-white px-4 py-8">
        <Head title="Aizmirsu paroli" />

        <img src="/bildites/Logo_Arva.png" alt="Logo" class="w-32 mb-6" />

        <div class="w-full max-w-md bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-2 text-center text-black">Aizmirsu paroli</h1>
            <p class="text-sm text-center text-gray-700 mb-6">Ievadi e-pastu, lai saņemtu paroles atiestatīšanas saiti</p>

            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-black">E-pasta adrese</Label>
                    <Input id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" class="text-white" />
                    <InputError :message="form.errors.email" />
                </div>

                <Button class="mt-4 w-full" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Nosūtīt paroles atiestatīšanas saiti
                </Button>
            </form>

            <div class="text-center text-sm text-gray-700 mt-4">
                Vai atgriezties pie
                <TextLink :href="route('login')" class="text-black font-semibold underline underline-offset-4">ienākšanas</TextLink>
            </div>
        </div>
    </div>
</template>
