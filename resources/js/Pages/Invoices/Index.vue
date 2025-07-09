<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
    invoices: Array,
})

const updatingStatus = ref(null)
const invoices = ref([])

const showDeleteModal = ref(false)
const deletingInvoiceId = ref(null)

onMounted(() => {
    invoices.value = props.invoices.map(inv => ({ ...inv, showActions: false }))
})

const updateStatus = (invoice) => {
    updatingStatus.value = invoice.id

    router.put(`/invoices/${invoice.id}/status`, {
        status: invoice.status,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Status updated')
        },
        onError: (errors) => {
            console.error('Failed to update status:', errors)
        },
        onFinish: () => {
            updatingStatus.value = null
        },
    })
}

const toggleActions = (invoice) => {
    invoices.value = invoices.value.map(inv =>
        inv.id === invoice.id
            ? { ...inv, showActions: !inv.showActions }
            : { ...inv, showActions: false }
    )
}

function confirmDelete(id) {
    deletingInvoiceId.value = id
    showDeleteModal.value = true
}

function closeDeleteModal() {
    showDeleteModal.value = false
    deletingInvoiceId.value = null
}

function deleteInvoice() {
    if (!deletingInvoiceId.value) return

    router.delete(`/invoices/${deletingInvoiceId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove the deleted invoice from the local list
            invoices.value = invoices.value.filter(inv => inv.id !== deletingInvoiceId.value)

            // Reset modal state
            showDeleteModal.value = false
            deletingInvoiceId.value = null

            console.log('Invoice deleted')
        },
        onError: (e) => {
            console.error('Delete failed:', e)
        },
    })
}


const viewInvoice = (invoice) => {
    router.visit(`/invoices/${invoice.id}`)
}
</script>


<template>
    <Head title="Invoices" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Invoices</h2>
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
        <div class="bg-white shadow-md border rounded-xl p-6 flex flex-col">
            <div class="overflow-x-auto overflow-y-visible pb-20">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left text-gray-700">
                    <!-- your thead and tbody go here -->

                            <thead class="bg-gray-100 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">Client</th>
                                    <th class="px-4 py-3 whitespace-nowrap">Invoice #</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Due Date</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in invoices" :key="invoice.id" class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ invoice.to_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ invoice.invoice_number }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ invoice.date }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ invoice.due_date }}</td>
                                    <td class="px-4 py-2 text-right whitespace-nowrap">${{ invoice.total }}</td>
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
                                                <!-- View Button -->
                                                <button
                                                    @click="viewInvoice(invoice)"
                                                    class="px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    View
                                                </button>

                                                <!-- Dropdown Arrow -->
                                                <button
                                                    @click="toggleActions(invoice)"
                                                    class="px-2 py-1.5 text-gray-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Dropdown Content -->
                                            <div
                                                v-if="invoice.showActions"
                                                class="absolute top-full right-0 mt-2 w-40 bg-white border border-gray-200 rounded-xl shadow-2xl z-[9999]"
                                            >
                                                <Link
                                                    :href="`/invoices/${invoice.id}/edit`"
                                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                    >
                                                    Edit
                                                    </Link>

                                                <button
                                                    @click="confirmDelete(invoice.id)"
                                                    class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                >
                                                    Delete
                                                </button>

                                            </div>
                                        </td>



                                </tr>

                                <tr v-if="!invoices.length">
                                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                                        No invoices found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
            <button
                @click="closeDeleteModal"
                class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 text-gray-800"
            >
                Cancel
            </button>
            <button
                @click="deleteInvoice"
                class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white"
            >
                Delete
            </button>
        </div>
    </div>
</div>
    </AuthenticatedLayout>
</template>
<!-- Delete Confirmation Modal -->

