<!-- Login.vue -->
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
    <div class="min-h-screen flex flex-col justify-center items-center bg-white px-4 py-8">
        <Head title="Log in" />

        <img src="/bildites/Logo_Arva.png" alt="Logo" class="w-32 mb-6" />

        <div class="w-full max-w-md bg-white p-8 rounded shadow-md">
            <h1 class="text-2xl font-bold mb-2 text-center text-black">Log in to your account</h1>
            <p class="text-sm text-center text-gray-700 mb-6">Enter your email and password below to log in</p>

            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-black">Email address</Label>
                    <Input id="email" type="email" required autofocus :tabindex="1" autocomplete="email" v-model="form.email" placeholder="email@example.com" class="text-white" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-black">Password</Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-black font-semibold text-sm" :tabindex="5">
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input id="password" type="password" required :tabindex="2" autocomplete="current-password" v-model="form.password" placeholder="Password" class="text-white" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center gap-2" :tabindex="3">
                    <Checkbox id="remember" v-model:checked="form.remember" :tabindex="4" />
                    <Label for="remember" class="text-black">Remember me</Label>
                </div>

                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </form>

            <div class="text-center text-sm text-gray-700 mt-4">
                Don't have an account?
                <TextLink :href="route('register')" class="text-black font-semibold underline underline-offset-4" :tabindex="5">Sign up</TextLink>
            </div>
        </div>
    </div>
</template>
