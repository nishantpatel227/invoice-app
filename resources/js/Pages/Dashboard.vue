<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

// Correct way to accept props in <script setup>
const props = defineProps({
  metrics: Object,
  monthlyChart: Object,
  recentInvoices: Array,
});


// Build chart data from monthlyChart
const chartData = {
  labels: Array.from({ length: 12 }, (_, i) =>
    new Date(0, i).toLocaleString('default', { month: 'short' })
  ),
  datasets: [
    {
      label: 'Monthly Invoice Total',
      backgroundColor: '#3B82F6',
      // This line is correct with props.monthlyChart being an object with keys 1-12
      data: Array.from({ length: 12 }, (_, i) => props.monthlyChart[i + 1] || 0),
    },
  ],
};
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Invoice App Dashboard
      </h2>
    </template>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
      <!-- Metrics -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-4 rounded shadow">
          <div class="text-gray-500 text-sm">Total Invoices</div>
          <div class="text-xl font-bold">{{ props.metrics.totalInvoices }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-gray-500 text-sm">Total Collected</div>
          <div class="text-xl font-bold">${{ props.metrics.totalCollected.toFixed(2) }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-gray-500 text-sm">Total Pending</div>
          <div class="text-xl font-bold">${{ props.metrics.totalPending.toFixed(2) }}</div>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <div class="text-gray-500 text-sm">Overdue Invoices</div>
          <div class="text-xl font-bold">{{ props.metrics.overdue }}</div>
        </div>
      </div>

      <!-- Chart -->
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Monthly Revenue</h3>
        <Bar :data="chartData" />
      </div>

      <!-- Recent Invoices -->
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Recent Invoices</h3>
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="text-gray-600 border-b">
              <th class="py-2">#</th>
              <th class="py-2">Client</th>
              <th class="py-2">Amount</th>
              <th class="py-2">Status</th>
              <th class="py-2">Date</th>
            </tr>
          </thead>
          <tbody>
    <tr
      v-for="invoice in props.recentInvoices"
      :key="invoice.id"
      class="border-b"
    >
        <td class="py-2">{{ invoice.invoice_number }}</td>
        <td class="py-2">{{ invoice.client?.name || '—' }}</td>
        <td class="py-2">${{ invoice.total.toFixed(2) }}</td> <td class="py-2 capitalize">{{ invoice.status }}</td>
        <td class="py-2">{{ new Date(invoice.date).toLocaleDateString() }}</td>
    </tr>
</tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
