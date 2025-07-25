<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  form: Object,
  user: Object,
  clients: Array,
  isEdit: { type: Boolean, default: false },
  onSubmit: Function,
  errors: Object,
})

const selectedClient = computed(() =>
  props.clients.find(c => c.id === props.form.client_id) || null
)

const dateError = ref('')

const calculateSubtotal = () => {
  let subtotal = 0
  props.form.items.forEach(item => {
    const quantity = Number(item.quantity) || 0
    const rate = Number(item.rate) || 0
    item.amount = +(quantity * rate).toFixed(2)
    subtotal += item.amount
  })
  props.form.subtotal = +subtotal.toFixed(2)
}

const calculateTotal = () => {
  calculateSubtotal()
  const tax = (props.form.subtotal * (+props.form.tax_percent || 0)) / 100
  const discount = (props.form.subtotal * (+props.form.discount || 0)) / 100
  const shipping = +props.form.shipping || 0
  props.form.total = +(props.form.subtotal + tax + shipping - discount).toFixed(2)
}

watch(() => props.form.items, calculateTotal, { deep: true, immediate: true })
watch(() => [props.form.tax_percent, props.form.discount, props.form.shipping], calculateTotal)

watch(
  [() => props.form.date, () => props.form.due_date],
  ([date, due]) => {
    if (date && due && new Date(due) < new Date(date)) {
      dateError.value = 'Due date cannot be earlier than the invoice date.'
    } else {
      dateError.value = ''
    }
  },
  { immediate: true }
)

onMounted(() => {
  if (!props.form.items || props.form.items.length === 0) addItem()
  calculateTotal()
})

const addItem = () =>
  props.form.items.push({ description: '', quantity: 1, rate: 0, amount: 0 })

const removeItem = index => {
  props.form.items.splice(index, 1)
  calculateTotal()
}

const submit = () => {
  if (props.form.due_date && props.form.date && new Date(props.form.due_date) < new Date(props.form.date)) {
    alert('Due date cannot be earlier than the invoice date.')
    return
  }
  calculateTotal()
  props.onSubmit()
}
</script>

<template>
  <form @submit.prevent="submit" class="space-y-8 bg-white p-6 rounded ">
    <div class="grid grid-cols-2 gap-6">
      <!-- From / To -->
      <div>
        <label class="block text-sm font-medium text-gray-700">From</label>
        <input v-model="props.form.from_name" type="text" class="w-full rounded border-gray-300" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">To</label>
        <select v-model="props.form.client_id" class="w-full rounded border-gray-300">
          <option disabled value="">Select a client</option>
          <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
        </select>
        <span v-if="props.errors.client_id" class="text-sm text-red-500">{{ props.errors.client_id }}</span>
      </div>
    </div>

    <!-- Client info -->
    <div v-if="selectedClient" class="p-6 mt-4 bg-gray-100 rounded-xl shadow-md">
      <div class="flex items-center justify-between mb-4">
  <h2 class="text-xl font-semibold">
    {{ selectedClient.company_name || selectedClient.name }}
  </h2>
  <Link
    :href="`/clients/${selectedClient.id}/edit`"
    class="text-sm px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-50"
  >
    Edit Client
  </Link>
</div>

      <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm text-gray-800">
        <div>
          <p><strong>Email:</strong> {{ selectedClient.email || 'N/A' }}</p>
          <p><strong>Phone:</strong> {{ selectedClient.phone_business || selectedClient.phone_personal || 'N/A' }}
            <span v-if="selectedClient.phone_extension">ext. {{ selectedClient.phone_extension }}</span>
          </p>
          <p v-if="selectedClient.contact_person"><strong>Contact:</strong> {{ selectedClient.contact_person }}</p>
          <p v-if="selectedClient.tax_number"><strong>Tax #:</strong> {{ selectedClient.tax_number }}</p>
        </div>
        <div>
          <p><strong>Billing Address:</strong></p>
          <p class="text-gray-600">
            {{ [selectedClient.billing_address_line1, selectedClient.billing_address_line2, selectedClient.billing_city, selectedClient.billing_state, selectedClient.billing_postal_code, selectedClient.billing_country].filter(Boolean).join(', ') || 'N/A' }}
          </p>
          <p class="mt-3"><strong>Shipping Address:</strong></p>
          <p class="text-gray-600">
            {{ [selectedClient.shipping_address_line1, selectedClient.shipping_address_line2, selectedClient.shipping_city, selectedClient.shipping_state, selectedClient.shipping_postal_code, selectedClient.shipping_country].filter(Boolean).join(', ') || 'N/A' }}
          </p>
        </div>
      </div>
      
    </div>

    <!-- Invoice Meta -->
    <div class="grid grid-cols-3 gap-6">
      <div>
        <label class="block text-sm font-medium text-gray-700">Invoice #</label>
        <input v-model="props.form.invoice_number" type="text" class="w-full rounded border-gray-300" />
        <span v-if="props.errors.invoice_number" class="text-sm text-red-500">{{ props.errors.invoice_number }}</span>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Date</label>
        <input v-model="props.form.date" type="date" class="w-full rounded border-gray-300" />
        <span v-if="props.errors.date" class="text-sm text-red-500">{{ props.errors.date }}</span>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Due Date</label>
        <input v-model="props.form.due_date" type="date" class="w-full rounded border-gray-300" />
        <span v-if="dateError" class="text-sm text-red-500">{{ dateError }}</span>
        <span v-if="props.errors.due_date" class="text-sm text-red-500">{{ props.errors.due_date }}</span>
      </div>
    </div>

    <!-- Items -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Items</label>
      <div class="grid grid-cols-7 gap-2 font-semibold bg-gray-100 p-2 rounded">
        <div class="col-span-3">Description</div>
        <div>Qty</div>
        <div>Rate</div>
        <div>Amount</div>
        <div></div>
      </div>

      <div v-for="(item, index) in props.form.items" :key="index" class="grid grid-cols-7 gap-2 items-center mt-2">
        <input v-model="item.description" type="text" class="col-span-3 rounded border-gray-300" />
        <input v-model.number="item.quantity" type="number" min="0" step="any" class="rounded border-gray-300 text-right" />
        <input v-model.number="item.rate" type="number" min="0" step="any" class="rounded border-gray-300 text-right" />
        <div class="text-right">${{ item.amount.toFixed(2) }}</div>
        <button type="button" @click="removeItem(index)" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex justify-center items-center">
          &times;
        </button>
      </div>

      <button type="button" @click="addItem" class="mt-2 text-sm px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
        + Add Line Item
      </button>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 gap-6 mt-6">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Notes <span class="text-gray-400">(optional)</span></label>
          <textarea v-model="props.form.notes" class="w-full rounded border-gray-300"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Terms <span class="text-gray-400">(optional)</span></label>
          <textarea v-model="props.form.terms" class="w-full rounded border-gray-300"></textarea>
        </div>
      </div>

      <div class="space-y-3 w-full max-w-md ml-auto">
        <div class="grid grid-cols-2 items-center gap-2">
          <label class="text-right text-sm text-gray-700">Subtotal:</label>
          <div class="text-right">{{ selectedClient?.currency || 'USD' }} {{ props.form.subtotal.toFixed(2) }}</div>
        </div>
        <div class="grid grid-cols-2 items-center gap-2">
          <label class="text-right text-sm text-gray-700">Tax (%)</label>
          <input v-model.number="props.form.tax_percent" type="number" step="any" class="w-full text-right rounded border-gray-300" />
        </div>
        <div class="grid grid-cols-2 items-center gap-2">
          <label class="text-right text-sm text-gray-700">Discount (%)</label>
          <input v-model.number="props.form.discount" type="number" step="any" class="w-full text-right rounded border-gray-300" />
        </div>
        <div class="grid grid-cols-2 items-center gap-2">
          <label class="text-right text-sm text-gray-700">Shipping</label>
          <input v-model.number="props.form.shipping" type="number" step="any" class="w-full text-right rounded border-gray-300" />
        </div>
        <div class="grid grid-cols-2 items-center gap-2 border-t pt-2 font-bold text-lg">
          <label class="text-right text-gray-800">Total:</label>
          <div class="text-right">{{ selectedClient?.currency || 'USD' }} {{ props.form.total.toFixed(2) }}</div>
        </div>
      </div>
    </div>

    <div class="text-right">
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        {{ isEdit ? 'Update Invoice' : 'Save Invoice' }}
      </button>
    </div>
  </form>
</template>
