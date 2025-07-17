<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  client: Object,
});
</script>

<template>
  <Head :title="`Client: ${client.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">Client Details</h2>
    </template>

    <div class="py-10">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow space-y-6">
          <!-- Basic Info -->
          <section>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Basic Info</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-800">
              <p><strong>Type:</strong> {{ client.type }}</p>
              <p><strong>Name:</strong> {{ client.name }}</p>
              <p><strong>Company:</strong> {{ client.company_name || '-' }}</p>
              <p><strong>Contact Person:</strong> {{ client.contact_person || '-' }}</p>
              <p><strong>Email:</strong> {{ client.email }}</p>
              <p><strong>Personal Phone:</strong> {{ client.phone_personal || '-' }}</p>
              <p><strong>Business Phone:</strong> {{ client.phone_business || '-' }}</p>
              <p><strong>Phone Extension:</strong> {{ client.phone_extension || '-' }}</p>
            </div>
          </section>

          <!-- Billing Address -->
          <section>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Billing Address</h3>
            <address class="not-italic text-gray-800 space-y-1">
              <p>{{ client.billing_address_line1 }}</p>
              <p v-if="client.billing_address_line2">{{ client.billing_address_line2 }}</p>
              <p>
                {{ client.billing_city }}, {{ client.billing_state }}
                {{ client.billing_postal_code }}
              </p>
              <p>{{ client.billing_country }}</p>
            </address>
          </section>

          <!-- Shipping Address -->
          <section>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Shipping Address</h3>
            <address class="not-italic text-gray-800 space-y-1">
              <p>{{ client.shipping_address_line1 }}</p>
              <p v-if="client.shipping_address_line2">{{ client.shipping_address_line2 }}</p>
              <p>
                {{ client.shipping_city }}, {{ client.shipping_state }}
                {{ client.shipping_postal_code }}
              </p>
              <p>{{ client.shipping_country }}</p>
            </address>
          </section>

          <!-- Other Details -->
          <section>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Other Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-800">
              <p><strong>Tax Number:</strong> {{ client.tax_number || '-' }}</p>
              <p><strong>Currency:</strong> {{ client.currency || '-' }}</p>
              <p class="md:col-span-2"><strong>Notes:</strong> {{ client.notes || '-' }}</p>
              <p><strong>Receives Email:</strong> {{ client.receive_email ? 'Yes' : 'No' }}</p>
              <p><strong>Status:</strong> {{ client.is_active ? 'Active' : 'Inactive' }}</p>
            </div>
          </section>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
