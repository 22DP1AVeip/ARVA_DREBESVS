<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Izskata iestatījumi',
        href: '/settings/appearance',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Izskata iestatījumi" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Izskata iestatījumi" description="Atjaunini sava konta izskata iestatījumus" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
