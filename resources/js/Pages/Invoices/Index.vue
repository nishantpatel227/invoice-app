<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue' // Import 'watch'
import useTable from '@/Composables/useTable'

const props = defineProps({
    invoices: Array,
})

// State
const showDeleteModal = ref(false)
const deletingInvoiceId = ref(null)
const updatingStatus = ref(null)
const search = ref('')

// Reactive invoice list (initial data from props)
const invoices = ref(props.invoices.map(inv => ({ ...inv, showActions: false })))

// Computed: Filtered invoices (before pagination and sorting by useTable)
const preFilteredInvoices = computed(() => {
  let list = invoices.value

  if (search.value && search.value.trim().length >= 3) {
    const term = search.value.toLowerCase()
    list = list.filter(inv => {
      const clientName = inv.client?.name?.toLowerCase() || ''
      const invoiceNum = inv.invoice_number?.toLowerCase() || ''
      return clientName.includes(term) || invoiceNum.includes(term)
    })
  }
  return list
})

// Table logic - now pass the preFilteredInvoices to useTable
const {
  currentPage,
  paginatedData,
  sortBy,
  sortColumn,
  sortDirection,
  totalPages,
} = useTable(preFilteredInvoices, { sortColumn: 'id', perPage: 10 }) // Pass preFilteredInvoices here

// Watch for search input changes to reset paginatio
function onSearchOrFilterChange() {
  currentPage.value = 1
}

// Actions
const updateStatus = (invoice) => {
  updatingStatus.value = invoice.id

  router.put(`/invoices/${invoice.id}/status`, { status: invoice.status }, {
    preserveScroll: true,
    onSuccess: () => console.log('Status updated'),
    onError: (errors) => console.error('Failed to update status:', errors),
    onFinish: () => updatingStatus.value = null,
  })
}

const toggleActions = (invoice) => {
  invoices.value = invoices.value.map(inv =>
    inv.id === invoice.id
      ? { ...inv, showActions: !inv.showActions }
      : { ...inv, showActions: false }
  )
}

const viewInvoice = (invoice) => {
  router.visit(`/invoices/${invoice.id}`, {
    preserveState: true, // Preserve state to avoid reloading the page
    preserveScroll: true
   } // Preserve scroll position   
  )

}

const confirmDelete = (id) => {
  deletingInvoiceId.value = id
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deletingInvoiceId.value = null
}

const deleteInvoice = () => {
  if (!deletingInvoiceId.value) return

  router.delete(`/invoices/${deletingInvoiceId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      // Update the local invoices ref after deletion
      invoices.value = invoices.value.filter(inv => inv.id !== deletingInvoiceId.value)
      closeDeleteModal()
      console.log('Invoice deleted')
    },
    onError: (e) => console.error('Delete failed:', e),
  })
}
</script>

<template>
  <Head title="Invoices" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Invoices</h2>
        <Link
          href="/invoices/create"
          class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          + New Invoice
        </Link>
      </div>
    </template>

    <div class="min-h-screen bg-gray-50 py-10">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
          <input
            v-model="search"
            type="text"
            placeholder="Search by client or invoice number..."
            class="w-full max-w-sm border rounded-md px-3 py-2 text-sm"
          />
        </div>

        <div class="bg-white shadow-md border rounded-xl p-6 flex flex-col">
          <div class="overflow-x-auto overflow-y-visible pb-20">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-left text-gray-700">
              <thead class="bg-gray-100 text-xs uppercase">
                <tr>
                  <th class="px-4 py-3 cursor-pointer" @click="sortBy('client.name')">Client</th>
                  <th class="px-4 py-3 cursor-pointer" @click="sortBy('invoice_number')">Invoice #</th>
                  <th class="px-4 py-3 cursor-pointer" @click="sortBy('date')">Date</th>
                  <th class="px-4 py-3 cursor-pointer" @click="sortBy('due_date')">Due Date</th>
                  <th class="px-4 py-3 text-right cursor-pointer" @click="sortBy('total')">Total</th>
                  <th class="px-4 py-3 text-center">Status</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="invoice in paginatedData"
                  :key="invoice.id"
                  class="border-b hover:bg-gray-50"
                >
                  <td class="px-4 py-2">{{ invoice.client?.name || '' }}</td>
                  <td class="px-4 py-2">{{ invoice.invoice_number }}</td>
                  <td class="px-4 py-2">{{ invoice.date }}</td>
                  <td class="px-4 py-2">{{ invoice.due_date }}</td>
                  <td class="px-4 py-2 text-right">${{ parseFloat(invoice.total).toFixed(2) }}</td>
                  <td class="px-4 py-2 text-center">
                    <select
                      v-model="invoice.status"
                      @change="() => updateStatus(invoice)"
                      :disabled="updatingStatus === invoice.id"
                      class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"
                    >
                      <option value="draft">Draft</option>
                      <option value="sent">Sent</option>
                      <option value="paid">Paid</option>
                    </select>
                  </td>
                  <td class="px-4 py-2 text-right relative">
                    <div class="inline-flex items-center gap-0.5 border border-gray-200 rounded-md shadow-sm bg-white overflow-hidden">
                      <button
                        @click="viewInvoice(invoice)"
                        class="px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50"
                      >
                        View
                      </button>
                      <button
                        @click="toggleActions(invoice)"
                        class="px-2 py-1.5 text-gray-500 hover:bg-gray-50"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                      </button>
                    </div>
                    <div
                        v-if="invoice.showActions"
                        class="absolute top-full right-0 mt-2 w-30 bg-white border border-gray-200 rounded-xl shadow-2xl z-[9999]"
                    >
                        <Link
                            :href="`/invoices/${invoice.id}/edit`"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        >
                            Edit
                        </Link>
                        <button
                            @click="confirmDelete(invoice.id)"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                        >
                            Delete
                        </button>
                       
                    </div>
                  </td>
                </tr>
                <tr v-if="!paginatedData.length">
                  <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                    No invoices found.
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="flex items-center justify-between mt-4">
              <div class="text-sm text-gray-600">
                Page {{ currentPage }} of {{ totalPages }}
              </div>
              <div class="space-x-2">
                <button
                  @click="currentPage = Math.max(currentPage - 1, 1)"
                  :disabled="currentPage === 1"
                  class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                >
                  Prev
                </button>
                <button
                  @click="currentPage = Math.min(currentPage + 1, totalPages)"
                  :disabled="currentPage === totalPages"
                  class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                >
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Delete Invoice</h2>
        <p class="text-gray-600 mb-6">
          Are you sure you want to delete this invoice? This action cannot be undone.
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="closeDeleteModal" class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-800">
            Cancel
          </button>
          <button @click="deleteInvoice" class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">
            Delete
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>