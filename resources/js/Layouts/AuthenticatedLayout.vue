<template>
  <div class="flex h-screen bg-gray-50 font-sans text-gray-800 overflow-hidden">
    <aside
      class="bg-white shadow-lg p-6 flex-shrink-0 flex flex-col border-r border-gray-200 overflow-y-auto transform transition-all duration-300 ease-in-out"
      :class="isSidebarOpen ? 'w-64' : 'w-0'"
    >
      <div class="mb-10 flex-shrink-0" :class="{ 'opacity-0': !isSidebarOpen, 'hidden': !isSidebarOpen }">
        <Link :href="route('dashboard')" class="flex items-center">
          <InvoFlowLogo class="h-auto w-36" />
        </Link>
      </div>

      <nav class="flex flex-col gap-2 flex-grow" :class="{ 'opacity-0': !isSidebarOpen, 'hidden': !isSidebarOpen }">
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

      <div class="mt-auto pt-6 border-t border-gray-200" :class="{ 'opacity-0': !isSidebarOpen, 'hidden': !isSidebarOpen }">
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

    <div class="flex flex-col flex-1 overflow-y-auto">
      <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center px-6 py-4">
          <button
            @click="toggleSidebar"
            class="text-gray-600 hover:text-blue-600 focus:outline-none mr-4"
          >
            <svg v-if="isSidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
          
          <div class="flex items-center justify-between flex-1">
            <slot name="header">
                <h1 class="text-xl font-semibold text-gray-700">{{ pageTitle }}</h1>
            </slot>
          </div>
        </div>
      </header>

      <main class="flex-1 px-6 py-6 bg-gray-50">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import InvoFlowLogo from '@/Components/InvoFlowLogo.vue';
import { computed, ref } from 'vue';

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
});

const isSidebarOpen = ref(true);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const page = usePage();
const pageTitle = computed(() => {
  if (props.title) return props.title;
  const name = page.component.split('/').pop();
  return name.replace(/([A-Z])/g, ' $1').trim();
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