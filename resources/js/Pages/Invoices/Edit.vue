<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InvoiceForm from './Partials/InvoiceForm.vue'; // ✅ Must exist

const props = defineProps({
  invoice: Object,
  user: Object,
  clients: Array, // ✅ Added clients prop
});

const form = useForm({
  ...props.invoice,
  status: props.invoice.status || 'draft',
  items: props.invoice.items || [],
});

const submit = () => {
  form.put(`/invoices/${props.invoice.id}`);
};
</script>

<template>
  <Head title="Edit Invoice" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Edit Invoice</h2>
    </template>
    <div class="py-10">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
          <InvoiceForm
            :form="form"
            :user="user"
            :clients="props.clients" 
            :isEdit="true"
            :onSubmit="submit"
            :errors="form.errors"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
