<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineOptions({
  layout: GuestLayout,
});

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
});
</script>


<template>
  <div class="relative bg-gray-50 min-h-screen flex items-center justify-center px-4 py-16">
    <div class="max-w-3xl text-center bg-white shadow-lg rounded-2xl p-10">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-6">
        Welcome to <span class="text-indigo-600">InvoFlow</span>
      </h1>
      <p class="text-lg text-gray-600 mb-8">
        Easily create, track, and download your invoices. <br />
        Designed for freelancers, small businesses, and teams.
      </p>
      <div class="flex justify-center gap-4">
        <Link
          href="/login"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md text-md font-medium transition"
        >
          Get Started
        </Link>
        
      </div>
    </div>
  </div>
</template>


