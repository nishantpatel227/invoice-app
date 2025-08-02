<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { Head, router, Link, usePage } from '@inertiajs/vue3';
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

// Props
const props = defineProps({
  metrics: Object,
  monthlyChartData: Array,
  monthlyChartLabels: Array,
  recentInvoices: Array,
  selectedRange: String,
  initialFromDate: String,
  initialToDate: String,
});

// Date filter state
const selectedRange = ref(props.selectedRange || 'this_month');
const fromDate = ref(props.initialFromDate || '');
const toDate = ref(props.initialToDate || '');

// Chart data
const chartData = ref({
  labels: props.monthlyChartLabels,
  datasets: [
    {
      label: 'Monthly Invoice Total',
      backgroundColor: '#3B82F6',
      data: props.monthlyChartData,
    },
  ],
});

// Update chart reactively
watch([() => props.monthlyChartData, () => props.monthlyChartLabels], ([newData, newLabels]) => {
  chartData.value.datasets[0].data = newData;
  chartData.value.labels = newLabels;
}, { deep: true });

// Clear dates if not custom range
watch(selectedRange, (val) => {
  if (val !== 'custom_range') {
    fromDate.value = '';
    toDate.value = '';
  }
});

// Filter handler
function applyFilter() {
  let filterFrom = fromDate.value;
  let filterTo = toDate.value;

  if (selectedRange.value !== 'custom_range') {
    const now = new Date();
    switch (selectedRange.value) {
      case 'this_month':
        filterFrom = new Date(now.getFullYear(), now.getMonth(), 1);
        filterTo = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        break;
      case 'last_month':
        filterFrom = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        filterTo = new Date(now.getFullYear(), now.getMonth(), 0);
        break;
      case 'last_3_months':
        filterFrom = new Date(now.getFullYear(), now.getMonth() - 2, 1);
        filterTo = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        break;
      case 'this_year':
        filterFrom = new Date(now.getFullYear(), 0, 1);
        filterTo = new Date(now.getFullYear(), 11, 31);
        break;
    }

    filterFrom = filterFrom.toISOString().slice(0, 10);
    filterTo = filterTo.toISOString().slice(0, 10);
  }

  router.get(route('dashboard'), {
    range: selectedRange.value,
    from: filterFrom,
    to: filterTo,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between w-full min-h-10">
        <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
        </div>
    </template>
    <div class="py-10">
      <div class="mx-auto max-w-7xl px-4 space-y-6">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
          <div>
            <label for="date-range-select" class="text-sm font-medium mr-2">Date Range:</label>
            <select
              id="date-range-select"
              v-model="selectedRange"
              @change="applyFilter"
              class="border rounded px-2 py-1 text-sm"
            >
              <option value="this_month">This Month</option>
              <option value="last_month">Last Month</option>
              <option value="last_3_months">Last 3 Months</option>
              <option value="this_year">This Year</option>
              <option value="custom_range">Custom Range</option>
            </select>
          </div>

          <div v-if="selectedRange === 'custom_range'" class="flex gap-2 items-center">
            <label class="text-sm">From:</label>
            <input type="date" v-model="fromDate" @change="applyFilter" class="border rounded px-2 py-1 text-sm" />
            <label class="text-sm">To:</label>
            <input type="date" v-model="toDate" @change="applyFilter" class="border rounded px-2 py-1 text-sm" />
          </div>
        </div>

        <!-- Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-4 rounded shadow">
            <div class="text-gray-500 text-sm">Total Invoices</div>
            <div class="text-xl font-bold">{{ metrics.totalInvoices }}</div>
          </div>
          <div class="bg-white p-4 rounded shadow">
            <div class="text-gray-500 text-sm">Total Collected</div>
            <div class="text-xl font-bold">${{ metrics.totalCollected.toFixed(2) }}</div>
          </div>
          <div class="bg-white p-4 rounded shadow">
            <div class="text-gray-500 text-sm">Total Pending</div>
            <div class="text-xl font-bold">${{ metrics.totalPending.toFixed(2) }}</div>
          </div>
          <div class="bg-white p-4 rounded shadow">
            <div class="text-gray-500 text-sm">Overdue Invoices</div>
            <div class="text-xl font-bold">{{ metrics.overdue }}</div>
          </div>
        </div>

        <!-- Chart -->
        <div class="bg-white p-6 rounded shadow">
          <h3 class="text-lg font-semibold mb-4 text-gray-700">Monthly Revenue</h3>
          <Bar :data="chartData" :key="chartData.labels.join('-')" />
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
              <tr v-for="invoice in recentInvoices" :key="invoice.id" class="border-b">
                <td class="py-2">
                  <Link :href="route('invoices.show', invoice.id)" class="text-blue-600 hover:underline">
                    {{ invoice.invoice_number }}
                  </Link>
                </td>
                <td class="py-2">{{ invoice.client?.name || '—' }}</td>
                <td class="py-2">${{ invoice.total.toFixed(2) }}</td>
                <td class="py-2 capitalize">{{ invoice.status }}</td>
                <td class="py-2">{{ invoice.date ? new Date(invoice.date).toLocaleDateString() : 'N/A' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
