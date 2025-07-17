<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  invoice: Object,
  user: Object,

});
</script>

<template>
  <Head title="View Invoice" />
  <AuthenticatedLayout>
   <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">
          Invoice #{{ invoice.invoice_number }}
        </h2>

        <a
          :href="route('invoices.download', invoice.id)"
          target="_blank"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Download Invoice
        </a>
      </div>
    </template>

    <div class="py-10 max-w-6xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white p-6 rounded shadow space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div><strong>From:</strong> {{ invoice.from_name }}</div>
          <div><strong>To:</strong> {{ invoice.client?.name || 'N/A'}}</div>
          <div><strong>Date:</strong> {{ invoice.date }}</div>
          <div><strong>Due Date:</strong> {{ invoice.due_date }}</div>
          <div><strong>Status:</strong> {{ invoice.status }}</div>
        </div>

        <div>
          <h3 class="text-lg font-medium mb-2">Items</h3>
          <table class="w-full border-collapse">
            <thead class="bg-gray-100">
              <tr>
                <th class="border p-2 text-left">Description</th>
                <th class="border p-2">Qty</th>
                <th class="border p-2">Rate</th>
                <th class="border p-2">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in invoice.items" :key="index">
                <td class="border p-2">{{ item.description }}</td>
                <td class="border p-2 text-center">{{ item.quantity }}</td>
                <td class="border p-2 text-right">{{ item.rate }}</td>
                <td class="border p-2 text-right">{{ item.amount }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="text-right space-y-1">
          <div>Subtotal: {{ invoice.subtotal }}</div>
          <div>Tax (%): {{ invoice.tax_percent }}</div>
          <div>Discount (%): {{ invoice.discount }}</div>
          <div>Shipping: {{ invoice.shipping }}</div>
          <div class="text-xl font-bold">Total: {{ invoice.total }}</div>
        </div>

        <div>
          <h4 class="font-medium">Notes</h4>
          <p class="text-sm text-gray-600">{{ invoice.notes }}</p>
        </div>
        <div>
          <h4 class="font-medium">Terms</h4>
          <p class="text-sm text-gray-600">{{ invoice.terms }}</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
