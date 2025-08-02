<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  invoice: Object,
  user: Object,
})
</script>

<template>
  <Head :title="`Invoice #${invoice.invoice_number}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between w-full">
        <h2 class="text-xl font-semibold text-gray-800">
          Invoice #{{ invoice.invoice_number }}
        </h2>

        <div class="flex flex-wrap gap-2">
          <Link
            :href="`/invoices/${invoice.id}/edit`"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Edit Invoice
          </Link>

          <a
            :href="route('invoices.download', invoice.id)"
            target="_blank"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
          >
            Download Invoice
          </a>
        </div>
      </div>
    </template>

    <div class="py-10 max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white p-6 rounded shadow space-y-6">

        <!-- From & To Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="font-semibold text-gray-700 mb-1">From</h3>
            <p>{{ invoice.from_name }}</p>
          </div>
          <div>
            <h3 class="font-semibold text-gray-700 mb-1">To</h3>
            <p>{{ invoice.client?.name || 'N/A' }}</p>
            <p class="text-sm text-gray-600">{{ invoice.client?.email }}</p>
            <p class="text-sm text-gray-600">{{ invoice.client?.phone_personal }}</p>
          </div>
        </div>

        <!-- Date and Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
          <div><strong>Date:</strong> {{ invoice.date }}</div>
          <div><strong>Due Date:</strong> {{ invoice.due_date }}</div>
          <div><strong>Status:</strong> {{ invoice.status }}</div>
        </div>

        <!-- Items Table -->
        <div>
          <h3 class="text-lg font-semibold mb-3">Line Items</h3>
          <div class="overflow-x-auto">
            <table class="w-full border text-sm">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border px-3 py-2 text-left">Description</th>
                  <th class="border px-3 py-2 text-center">Qty</th>
                  <th class="border px-3 py-2 text-right">Rate</th>
                  <th class="border px-3 py-2 text-right">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in invoice.items" :key="index">
                  <td class="border px-3 py-2">{{ item.description }}</td>
                  <td class="border px-3 py-2 text-center">{{ item.quantity }}</td>
                  <td class="border px-3 py-2 text-right">{{ item.rate }}</td>
                  <td class="border px-3 py-2 text-right">{{ item.amount }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Notes & Terms -->
        <div v-if="invoice.notes || invoice.terms" class="space-y-4">
          <div v-if="invoice.notes">
            <h4 class="font-medium text-gray-700">Notes</h4>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ invoice.notes }}</p>
          </div>
          <div v-if="invoice.terms">
            <h4 class="font-medium text-gray-700">Terms</h4>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ invoice.terms }}</p>
          </div>
        </div>
        <!-- Summary Section -->
        <div class="text-right space-y-1 text-sm text-gray-800">
          <div><strong>Subtotal:</strong> {{ invoice.subtotal }}</div>
          <div><strong>Tax (%):</strong> {{ invoice.tax_percent }}</div>
          <div><strong>Discount (%):</strong> {{ invoice.discount }}</div>
          <div><strong>Shipping:</strong> {{ invoice.shipping }}</div>
          <div class="text-lg font-bold">Total: {{ invoice.total }}</div>
        </div>
      </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
