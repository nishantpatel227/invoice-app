<script setup>
import { ref, watch, computed, onMounted } from 'vue';

const props = defineProps({
    form: Object, // This is the useForm object from Inertia
    user: Object,
    clients: Array,
    isEdit: { type: Boolean, default: false },
    onSubmit: Function,
    errors: Object, // Inertia errors
});

// Computed property for selectedClient remains good
const selectedClient = computed(() => props.clients.find(c => c.id === props.form.client_id) || null);

// Reactive state for local validation messages
const dateError = ref('');

// --- Reactive calculation logic ---
const calculateSubtotal = () => {
    let subtotal = 0;
    props.form.items.forEach((item) => {
        const quantity = Number(item.quantity) || 0;
        const rate = Number(item.rate) || 0;
        item.amount = parseFloat((quantity * rate).toFixed(2));
        subtotal += item.amount;
    });
    props.form.subtotal = parseFloat(subtotal.toFixed(2));
};

const calculateTotal = () => {
    calculateSubtotal();

    const tax = (props.form.subtotal * (Number(props.form.tax_percent) || 0)) / 100;
    const discount = (props.form.subtotal * (Number(props.form.discount) || 0)) / 100;
    const shipping = Number(props.form.shipping) || 0;

    props.form.total = parseFloat((props.form.subtotal + tax + shipping - discount).toFixed(2));
};

// --- Watchers for calculations and date validation ---
watch(() => props.form.items, calculateTotal, { deep: true, immediate: true });
watch(() => props.form.tax_percent, calculateTotal);
watch(() => props.form.discount, calculateTotal);
watch(() => props.form.shipping, calculateTotal);

// Watch for date changes to perform inline validation
watch([() => props.form.date, () => props.form.due_date], ([newDate, newDueDate]) => {
    if (newDate && newDueDate && new Date(newDueDate) < new Date(newDate)) {
        dateError.value = "Due date cannot be earlier than the invoice date.";
    } else {
        dateError.value = "";
    }
}, { immediate: true }); // Check on initial load too


// --- Lifecycle hook for initial setup ---
onMounted(() => {
  if (props.isEdit) {
    calculateTotal(); // Ensure totals are calculated for existing invoice
  } else {
    if (!props.form.items || props.form.items.length === 0) { // Check if items is null or empty
        addItem(); // Add one initial item for new invoices
    }
    calculateTotal();
  }
});


// --- Item management functions ---
const addItem = () => {
    props.form.items.push({ description: '', quantity: 1.00, rate: 0.00, amount: 0.00 });
    calculateTotal();
};

const removeItem = (index) => {
    props.form.items.splice(index, 1);
    calculateTotal();
};

// --- Form submission ---
const submit = () => {
    // Perform final date validation before submission
    if (props.form.due_date && props.form.date && new Date(props.form.due_date) < new Date(props.form.date)) {
        alert("Due date cannot be earlier than the invoice date. Please correct it.");
        return;
    }
    calculateTotal();
    props.onSubmit();
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-8 bg-white p-6 rounded shadow">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">From</label>
                <input v-model="props.form.from_name" type="text" class="w-full rounded border-gray-300" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">To</label>
                <select v-model="props.form.client_id" class="w-full rounded border-gray-300">
                    <option disabled value="">Select a client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                        {{ client.name }}
                    </option>
                </select>
                <span v-if="props.errors.client_id" class="text-sm text-red-500"> {{ props.errors.client_id }}</span>
            </div>
            <div v-if="selectedClient" class="p-4 mt-4 bg-gray-100 rounded-xl shadow-md">
                <h2 class="text-lg font-semibold mb-1">{{ selectedClient.name }}</h2>
                <p><strong>Email:</strong> {{ selectedClient.email || 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ selectedClient.phone_personal || selectedClient.phone_business || 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Invoice #</label>
                <input v-model="props.form.invoice_number" type="text" class="w-full rounded border-gray-300" />
                <span v-if="errors.invoice_number" class="text-sm text-red-500">{{ errors.invoice_number }}</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <input v-model="props.form.date" type="date" class="w-full rounded border-gray-300" />
                <span v-if="errors.date" class="text-sm text-red-500">{{ errors.date }}</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                <input v-model="props.form.due_date" type="date" class="w-full rounded border-gray-300" />
                <span v-if="dateError" class="text-sm text-red-500">{{ dateError }}</span>
                <span v-if="errors.due_date" class="text-sm text-red-500">{{ errors.due_date }}</span>
            </div>
        </div>

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
                <input v-model.number="item.quantity" type="number" min="0"   class="rounded border-gray-300 text-right" />
                <input v-model.number="item.rate" type="number" min="0"  class="rounded border-gray-300 text-right" />
                <div class="text-right">${{ Number(item.amount).toFixed(2) }}</div>
                <button type="button" @click="removeItem(index)" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex justify-center items-center">
                    &times;
                </button>
            </div>

            <button type="button" @click="addItem" class="mt-2 text-sm px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                + Add Line Item
            </button>
        </div>

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
                    <div class="text-right">{{ selectedClient?.currency || 'USD' }} {{ Number(props.form.subtotal).toFixed(2) }}</div>
                </div>
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Tax (%)</label>
                    <input v-model.number="props.form.tax_percent" @input="calculateTotal" type="number" step="1" inputmode="decimal" class="w-full text-right rounded border-gray-300" />
                </div>
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Discount (%)</label>
                    <input v-model.number="props.form.discount" @input="calculateTotal" type="number" step="1" inputmode="decimal" class="w-full text-right rounded border-gray-300" />
                </div>
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Shipping</label>
                    <input v-model.number="props.form.shipping" @input="calculateTotal" type="number" step="1" inputmode="decimal" class="w-full text-right rounded border-gray-300" />
                </div>
                <div class="grid grid-cols-2 items-center gap-2 border-t pt-2 font-bold text-lg">
                    <label class="text-right text-gray-800">Total:</label>
                    <div class="text-right">{{ selectedClient?.currency || 'USD' }} {{ Number(props.form.total).toFixed(2) }}</div>
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