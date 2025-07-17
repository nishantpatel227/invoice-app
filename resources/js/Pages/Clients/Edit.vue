<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ClientForm from './Partials/ClientForm.vue';

const props = defineProps({
  client: Object,
});

const form = useForm({
  type: props.client.type,
  name: props.client.name,
  company_name: props.client.company_name,
  contact_person: props.client.contact_person,
  email: props.client.email,
  phone_personal: props.client.phone_personal,
  phone_business: props.client.phone_business,
  phone_extension: props.client.phone_extension,
  billing_address_line1: props.client.billing_address_line1,
  billing_address_line2: props.client.billing_address_line2,
  billing_city: props.client.billing_city,
  billing_state: props.client.billing_state,
  billing_postal_code: props.client.billing_postal_code,
  billing_country: props.client.billing_country,
  shipping_address_line1: props.client.shipping_address_line1,
  shipping_address_line2: props.client.shipping_address_line2,
  shipping_city: props.client.shipping_city,
  shipping_state: props.client.shipping_state,
  shipping_postal_code: props.client.shipping_postal_code,
  shipping_country: props.client.shipping_country,
  tax_number: props.client.tax_number,
  currency: props.client.currency,
  notes: props.client.notes,
  receive_email: props.client.receive_email,
  is_active: props.client.is_active,
});

const submit = () => {
  form.put(route('clients.update', props.client.id));
};
</script>

<template>
  <Head :title="`Edit Client: ${props.client.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Edit Client</h2>
    </template>

    <div class="py-10">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
          <ClientForm
            :form="form"
            :errors="form.errors"
            :isEdit="true"
            :onSubmit="submit"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
