<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'


const props = defineProps({
  clients: Array,
})

const clients = ref([])
const showDeleteModal = ref(false)
const deletingClientId = ref(null)

onMounted(() => {
  clients.value = props.clients.map(client => ({ ...client, showActions: false }))
})

function toggleActions(client) {
  clients.value = clients.value.map(c =>
    c.id === client.id
      ? { ...c, showActions: !c.showActions }
      : { ...c, showActions: false }
  )
}

function confirmDelete(id) {
  deletingClientId.value = id
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  deletingClientId.value = null
}

function deleteClient() {
  if (!deletingClientId.value) return

  router.delete(`/clients/${deletingClientId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      clients.value = clients.value.filter(c => c.id !== deletingClientId.value)
      closeDeleteModal()
    },
    onError: e => {
      console.error('Delete failed:', e)
    },
  })
}
</script>

<template>
  <Head title="Clients" />

  <AuthenticatedLayout>
     <template #header>
      <div class="flex items-center justify-between w-full">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Clients</h2>
        <Link
          href="/clients/create"
          class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          + New Client
        </Link>
      </div>
    </template>


    <div class="min-h-screen bg-gray-50 py-10">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md border rounded-xl p-6 flex flex-col">
          <div class="overflow-x-auto overflow-y-visible pb-20">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-left text-gray-700">
              <thead class="bg-gray-100 text-xs uppercase">
                <tr>
                  <th class="px-4 py-3 whitespace-nowrap">Name</th>
                  <th class="px-4 py-3">Company</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Phone</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="client in clients" :key="client.id" class="border-b hover:bg-gray-50">
                  <td class="px-4 py-2 whitespace-nowrap">{{ client.name }}</td>
                  <td class="px-4 py-2 whitespace-nowrap">{{ client.company_name || '-' }}</td>
                  <td class="px-4 py-2 whitespace-nowrap">{{ client.email }}</td>
                  <td class="px-4 py-2 whitespace-nowrap">
                    {{ client.phone_personal || client.phone_business || '-' }}
                  </td>
                  <td class="px-4 py-2 text-right relative">
                    <div
                        class="inline-flex items-center gap-0.5 border border-gray-200 rounded-md shadow-sm bg-white overflow-hidden"
                    >
                        <!-- View Button (always visible) -->
                        <Link
                        :href="`/clients/${client.id}`"
                        class="px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        View
                        </Link>

                        <!-- Dropdown Arrow -->
                        <button
                        @click="toggleActions(client)"
                        class="px-2 py-1.5 text-gray-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="More actions"
                        >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                        </button>
                    </div>

                    <!-- Dropdown Actions -->
                    <div
                        v-if="client.showActions"
                        class="absolute top-full right-0 mt-2 w-32 bg-white border border-gray-200 rounded-xl shadow-2xl z-[9999]"
                    >
                        <Link
                        :href="`/clients/${client.id}/edit`"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                        Edit
                        </Link>
                        <button
                        @click="confirmDelete(client.id)"
                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                        >
                        Delete
                        </button>
                    </div>
                    </td>

                </tr>

                <tr v-if="!clients.length">
                  <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                    No clients found.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      role="dialog"
      aria-modal="true"
      aria-labelledby="delete-client-title"
    >
      <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <h2 id="delete-client-title" class="text-lg font-semibold text-gray-800 mb-4">Delete Client</h2>
        <p class="text-gray-600 mb-6">
          Are you sure you want to delete this client? This action cannot be undone.
        </p>
        <div class="flex justify-end space-x-3">
          <button
            @click="closeDeleteModal"
            class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-800"
          >
            Cancel
          </button>
          <button
            @click="deleteClient"
            class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
