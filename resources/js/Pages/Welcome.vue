<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
defineOptions({
    layout: GuestLayout
})
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
  <div class="text-center py-10">
    <h1 class="text-4xl font-bold mb-4">Welcome to Invoice App</h1>
    <p class="text-gray-600">Create and manage your invoices easily.</p>
    <Link href="/login" class="mt-4 inline-block text-blue-600 hover:underline">Login</Link>
  </div>
</template>

