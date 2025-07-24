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
const sameAsBilling = computed({
  get: () => props.form.same_as_billing || false,
  set: (value) => {
    props.form.same_as_billing = value

    if (value) {
      props.form.shipping_address_line1 = props.form.billing_address_line1
      props.form.shipping_address_line2 = props.form.billing_address_line2
      props.form.shipping_city = props.form.billing_city
      props.form.shipping_state = props.form.billing_state
      props.form.shipping_postal_code = props.form.billing_postal_code
      props.form.shipping_country = props.form.billing_country
    }
  }
})
watch(
  () => [
    props.form.billing_address_line1,
    props.form.billing_address_line2,
    props.form.billing_city,
    props.form.billing_state,
    props.form.billing_postal_code,
    props.form.billing_country
  ],
  () => {
    if (sameAsBilling.value) {
      props.form.shipping_address_line1 = props.form.billing_address_line1
      props.form.shipping_address_line2 = props.form.billing_address_line2
      props.form.shipping_city = props.form.billing_city
      props.form.shipping_state = props.form.billing_state
      props.form.shipping_postal_code = props.form.billing_postal_code
      props.form.shipping_country = props.form.billing_country
    }
  }
)

</script>

<template>
  <form @submit.prevent="onSubmit" class="space-y-10 max-w-4xl mx-auto p-6 bg-white shadow-md rounded">
    <!-- Type & Name -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <InputLabel for="type" value="Client Type *" />
        <select
          v-model="form.type"
          id="type"
          required
          class="mt-1 block w-full rounded border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="individual">Individual</option>
          <option value="business">Business</option>
        </select>
        <InputError :message="errors.type" class="mt-1" />
      </div>

      <div>
        <InputLabel for="name" value="Name *" />
        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
        <InputError :message="errors.name" class="mt-1" />
      </div>
    </div>

    <!-- Company Name -->
    <div v-if="isBusiness">
      <InputLabel for="company_name" value="Company Name *" />
      <TextInput
        id="company_name"
        v-model="form.company_name"
        type="text"
        class="mt-1 block w-full"
        required
      />
      <InputError :message="errors.company_name" class="mt-1" />
    </div>

    <!-- Contact Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <InputLabel for="contact_person" value="Contact Person" />
        <TextInput id="contact_person" v-model="form.contact_person" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.contact_person" class="mt-1" />
      </div>

      <div>
        <InputLabel for="email" value="Email *" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          required
          class="mt-1 block w-full"
        />
        <InputError :message="errors.email" class="mt-1" />
      </div>

      <div>
        <InputLabel for="phone_personal" value="Phone (Personal)" />
        <TextInput id="phone_personal" v-model="form.phone_personal" type="tel" class="mt-1 block w-full" />
        <InputError :message="errors.phone_personal" class="mt-1" />
      </div>

      <div>
        <InputLabel for="phone_business" value="Phone (Business)" />
        <TextInput id="phone_business" v-model="form.phone_business" type="tel" class="mt-1 block w-full" />
        <InputError :message="errors.phone_business" class="mt-1" />
      </div>

      <div>
        <InputLabel for="phone_extension" value="Extension" />
        <TextInput id="phone_extension" v-model="form.phone_extension" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.phone_extension" class="mt-1" />
      </div>

      <div>
        <InputLabel for="tax_number" value="Tax Number" />
        <TextInput id="tax_number" v-model="form.tax_number" type="text" class="mt-1 block w-full" />
        <InputError :message="errors.tax_number" class="mt-1" />
      </div>

      <div>
        <InputLabel for="currency" value="Currency (e.g. USD, CAD)" />
        <TextInput
          id="currency"
          v-model="form.currency"
          type="text"
          minlength="3"
          maxlength="3"
          class="mt-1 block w-full uppercase"
        />
        <InputError :message="errors.currency" class="mt-1" />
      </div>
    </div>

    <!-- Billing Address -->
    <div>
      <h3 class="text-xl font-semibold text-gray-800 mb-2">Billing Address</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <TextInput v-model="form.billing_address_line1" placeholder="Address Line 1" class="w-full" />
        <TextInput v-model="form.billing_address_line2" placeholder="Address Line 2" class="w-full" />
        <TextInput v-model="form.billing_city" placeholder="City" class="w-full" />
        <TextInput v-model="form.billing_state" placeholder="State/Province" class="w-full" />
        <TextInput v-model="form.billing_postal_code" placeholder="Postal Code" class="w-full" />
        <TextInput v-model="form.billing_country" placeholder="Country" class="w-full" />
      </div>
    </div>

    <!-- Shipping Address -->
    <div>
      <h3 class="text-xl font-semibold text-gray-800 mb-2">Shipping Address</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <TextInput v-model="form.shipping_address_line1" placeholder="Address Line 1" class="w-full" />
        <TextInput v-model="form.shipping_address_line2" placeholder="Address Line 2" class="w-full" />
        <TextInput v-model="form.shipping_city" placeholder="City" class="w-full" />
        <TextInput v-model="form.shipping_state" placeholder="State / Province" class="w-full" />
        <TextInput v-model="form.shipping_postal_code" placeholder="Postal Code" class="w-full" />
        <TextInput v-model="form.shipping_country" placeholder="Country" class="w-full" />
      </div>
    </div>

    <!-- Notes -->
    <div>
      <InputLabel for="notes" value="Notes" />
      <textarea
        id="notes"
        v-model="form.notes"
        rows="3"
        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2 resize-y focus:outline-none focus:ring-2 focus:ring-blue-500"
      ></textarea>
      <InputError :message="errors.notes" class="mt-1" />
    </div>

    <!-- Preferences -->
    <div class="flex flex-wrap gap-6 mt-6">
      <label class="flex items-center gap-2">
        <input
          type="checkbox"
          v-model="form.receive_email"
          class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
        />
        <span class="text-gray-700 text-sm">Receive emails</span>
      </label>
      <label class="flex items-center gap-2">
        <input
          type="checkbox"
          v-model="form.is_active"
          class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
        />
        <span class="text-gray-700 text-sm">Active</span>
      </label>
    </div>

    <!-- Submit -->
    <div class="text-right pt-8">
      <PrimaryButton type="submit" class="px-6 py-2 text-base">
        {{ isEdit ? 'Update' : 'Create' }} Client
      </PrimaryButton>
    </div>
  </form>
</template>
