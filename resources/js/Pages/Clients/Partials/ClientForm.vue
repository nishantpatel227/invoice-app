<script setup>
import { computed } from 'vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  form: Object,
  errors: Object,
  isEdit: Boolean,
  onSubmit: Function,
})

const isBusiness = computed(() => props.form.type === 'business')
</script>

<template>
  <form @submit.prevent="onSubmit" class="space-y-8 max-w-4xl mx-auto p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Client Type -->
      <div>
        <InputLabel for="type" value="Client Type" />
        <select
          v-model="form.type"
          id="type"
          class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="individual">Individual</option>
          <option value="business">Business</option>
        </select>
        <InputError :message="errors.type" class="mt-1" />
      </div>

      <!-- Name -->
      <div>
        <InputLabel for="name" value="Name" />
        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
        <InputError :message="errors.name" class="mt-1" />
      </div>

      <!-- Company Name (for business only) -->
      <div v-if="isBusiness" class="md:col-span-2">
        <InputLabel for="company_name" value="Company Name" />
        <TextInput id="company_name" v-model="form.company_name" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.company_name" class="mt-1" />
      </div>

      <!-- Contact Person -->
      <div>
        <InputLabel for="contact_person" value="Contact Person" />
        <TextInput id="contact_person" v-model="form.contact_person" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.contact_person" class="mt-1" />
      </div>

      <!-- Email -->
      <div>
        <InputLabel for="email" value="Email" />
        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
        <InputError :message="errors.email" class="mt-1" />
      </div>

      <!-- Phone Personal -->
      <div>
        <InputLabel for="phone_personal" value="Phone (Personal)" />
        <TextInput id="phone_personal" v-model="form.phone_personal" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.phone_personal" class="mt-1" />
      </div>

      <!-- Phone Business -->
      <div>
        <InputLabel for="phone_business" value="Phone (Business)" />
        <TextInput id="phone_business" v-model="form.phone_business" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.phone_business" class="mt-1" />
      </div>

      <!-- Extension -->
      <div>
        <InputLabel for="phone_extension" value="Extension" />
        <TextInput id="phone_extension" v-model="form.phone_extension" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.phone_extension" class="mt-1" />
      </div>

      <!-- Tax Number -->
      <div>
        <InputLabel for="tax_number" value="Tax Number" />
        <TextInput id="tax_number" v-model="form.tax_number" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.tax_number" class="mt-1" />
      </div>

      <!-- Currency -->
      <div>
        <InputLabel for="currency" value="Currency" />
        <TextInput id="currency" v-model="form.currency" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.currency" class="mt-1" />
      </div>
    </div>

    <!-- Billing Address -->
    <h3 class="text-xl font-semibold text-gray-800 mt-10 mb-4 border-b pb-2">Billing Address</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <TextInput v-model="form.billing_address_line1" placeholder="Address Line 1" class="w-full" />
      <TextInput v-model="form.billing_address_line2" placeholder="Address Line 2" class="w-full" />
      <TextInput v-model="form.billing_city" placeholder="City" class="w-full" />
      <TextInput v-model="form.billing_state" placeholder="State/Province" class="w-full" />
      <TextInput v-model="form.billing_postal_code" placeholder="Postal Code" class="w-full" />
      <TextInput v-model="form.billing_country" placeholder="Country" class="w-full" />
    </div>

    <!-- Shipping Address -->
    <h3 class="text-xl font-semibold text-gray-800 mt-10 mb-4 border-b pb-2">
  Shipping Address
</h3>

<!-- Address Line 1 & 2 -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <TextInput
    v-model="form.shipping_address_line1"
    placeholder="Address Line 1"
    class="w-full"
  />
  <TextInput
    v-model="form.shipping_address_line2"
    placeholder="Address Line 2"
    class="w-full"
  />
</div>

<!-- City & State -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
  <TextInput
    v-model="form.shipping_city"
    placeholder="City"
    class="w-full"
  />
  <TextInput
    v-model="form.shipping_state"
    placeholder="State / Province"
    class="w-full"
  />
</div>

<!-- Postal Code & Country -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
  <TextInput
    v-model="form.shipping_postal_code"
    placeholder="Postal Code"
    class="w-full"
  />
  <TextInput
    v-model="form.shipping_country"
    placeholder="Country"
    class="w-full"
  />
</div>


    <!-- Notes -->
    <div class="mt-10">
      <InputLabel for="notes" value="Notes" />
      <textarea
        id="notes"
        v-model="form.notes"
        rows="4"
        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 resize-y focus:outline-none focus:ring-2 focus:ring-blue-500"
      ></textarea>
      <InputError :message="errors.notes" class="mt-1" />
    </div>

    <!-- Preferences -->
    <div class="mt-6 flex flex-wrap gap-6">
      <label class="flex items-center space-x-2">
        <input
          type="checkbox"
          v-model="form.receive_email"
          class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
        />
        <span class="text-sm text-gray-700">Receive emails</span>
      </label>
      <label class="flex items-center space-x-2">
        <input
          type="checkbox"
          v-model="form.is_active"
          class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
        />
        <span class="text-sm text-gray-700">Active</span>
      </label>
    </div>

    <!-- Submit Button -->
    <div class="pt-10 text-right">
      <PrimaryButton type="submit" class="px-6 py-2 text-base">
        {{ isEdit ? 'Update' : 'Create' }} Client
      </PrimaryButton>
    </div>
  </form>
</template>
