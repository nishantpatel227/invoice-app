<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- Top Navbar -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <h1 class="text-xl font-bold">Invoice App</h1>
        <div class="flex space-x-4">
          <Link href="/login" class="text-blue-600 hover:underline">Login</Link>
          <Link href="/register" class="text-blue-600 hover:underline">Register</Link>
        </div>
      </div>
    </header>

    <!-- Main content -->
    <main class="flex-grow flex justify-center items-center px-10">
      <slot />
    </main>
  </div>
</template>
