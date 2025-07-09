<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div>
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <h1 class="text-xl font-bold">Invoice App</h1>
        <div class="flex space-x-4">
          <Link href="/login" class="text-blue-600 hover:underline">Login</Link>
          <Link href="/register" class="text-blue-600 hover:underline">Register</Link>
        </div>
      </div>
    </header>

    <!-- 🟢 Wrap main content in the same max width container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>


