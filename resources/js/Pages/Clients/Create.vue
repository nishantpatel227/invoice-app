<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ClientForm from './Partials/ClientForm.vue';

const form = useForm({
  type: 'individual',
  name: '',
  company_name: '',
  contact_person: '',
  email: '',
  phone_personal: '',
  phone_business: '',
  phone_extension: '',
  billing_address_line1: '',
  billing_address_line2: '',
  billing_city: '',
  billing_state: '',
  billing_postal_code: '',
  billing_country: '',
  shipping_address_line1: '',
  shipping_address_line2: '',
  shipping_city: '',
  shipping_state: '',
  shipping_postal_code: '',
  shipping_country: '',
  tax_number: '',
  currency: '',
  notes: '',
  receive_email: true,
  is_active: true,
});

const submit = () => {
  form.post(route('clients.store'));
};
</script>

<template>
  <Head title="Create Client" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Create Client</h2>
    </template>

    <div class="py-10">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
          <ClientForm
            :form="form"
            :isEdit="false"
            :onSubmit="submit"
            :errors="form.errors"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
