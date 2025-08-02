<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- Top Navbar -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <div class="flex items-center space-x-2 px-4 py-3">
          <Link href="/" class="text-xl font-bold text-gray-800">
            <img src="/images/InvoFlowLogo.svg" alt="InvoFlow Logo" class="w-32 h-auto" />
          </Link>
        </div>
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
