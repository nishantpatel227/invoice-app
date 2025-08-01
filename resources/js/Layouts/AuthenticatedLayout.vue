<template>
  <div class="flex h-screen bg-gray-50 font-sans text-gray-800">
    <aside
      class="w-64 bg-white shadow-lg p-6 flex flex-col border-r border-gray-200 overflow-y-auto"
    >
      <div class="mb-10 flex-shrink-0">
        <Link :href="route('dashboard')" class="flex items-center">
          <InvoFlowLogo class="h-auto w-36" />
        </Link>
      </div>

      <nav class="flex flex-col gap-2 flex-grow">
        <Link
          :href="route('dashboard')"
          class="nav-link"
          :class="{ active: route().current('dashboard') }"
        >
          Dashboard
        </Link>
        <Link
          :href="route('invoices.index')"
          class="nav-link"
          :class="{ active: route().current('invoices.index') }"
        >
          Invoices
        </Link>
        <Link
          :href="route('clients.index')"
          class="nav-link"
          :class="{ active: route().current('clients.index') }"
        >
          Clients
        </Link>
      </nav>

      <div class="mt-auto pt-6 border-t border-gray-200">
        <div class="flex items-center gap-3">
          <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
            <span class="text-sm font-semibold text-gray-700">
              {{ $page.props.auth.user.name.charAt(0) }}
            </span>
          </div>
          <div class="flex-grow">
            <div class="text-sm font-semibold text-gray-800 leading-none">
              {{ $page.props.auth.user.name }}
            </div>
            <div class="text-xs text-gray-500 truncate">
              {{ $page.props.auth.user.email }}
            </div>
          </div>
        </div>

        <div class="mt-4 flex flex-col gap-2">
          <Link
            :href="route('profile.edit')"
            class="nav-link !py-1.5"
            :class="{ active: route().current('profile.edit') }"
          >
            Profile
          </Link>
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="nav-link text-left !py-1.5 text-red-500 hover:text-red-600 font-semibold"
          >
            Logout
          </Link>
        </div>
      </div>
    </aside>

  
  <div class="flex-1 flex flex-col overflow-y-auto">
    <div class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
      <div class="flex items-center justify-between px-6 py-4">
        <slot name="header">
          <h1 class="text-xl font-semibold text-gray-700">
            {{ title }}
          </h1>
        </slot>
      </div>
    </div>

    <main class="flex-1 p-6">
      <slot />
    </main>
  </div>


     
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import InvoFlowLogo from '@/Components/InvoFlowLogo.vue';

defineProps({
  title: {
    type: String,
    default: 'Dashboard',
  },
});
</script>

<style scoped>
.nav-link {
  @apply px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-600 hover:text-blue-600 hover:bg-gray-100;
}
.nav-link.active {
  @apply bg-blue-100 text-blue-700 font-semibold;
}
</style>