<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InvoiceForm from './Partials/InvoiceForm.vue';

const props = defineProps({
  user: Object,
  nextInvoiceNumber: String,
});

const form = useForm({
  from_name: props.user.name ?? '',
  to_name: '',
  invoice_number: props.nextInvoiceNumber,
  date: '',
  due_date: '',
  subtotal: 0,
  tax_percent: 0,
  discount: 0,
  shipping: 0,
  total: 0,
  notes: '',
  terms: '',
  status: 'draft',
  items: [
    { description: '', quantity: 1, rate: 0, amount: 0 },
  ],
});

const submit = () => {
  form.post('/invoices');
};
</script>

<template>
  <Head title="Create Invoice" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Create Invoice</h2>
    </template>

    <div class="py-10">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
          <InvoiceForm
            :form="form"
            :user="user"
            :isEdit="false"
            :onSubmit="submit"
            :errors="form.errors"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>