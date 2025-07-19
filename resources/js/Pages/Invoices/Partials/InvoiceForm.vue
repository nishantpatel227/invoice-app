<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    form: Object,
    user: Object,
    clients: Array,
    isEdit: { type: Boolean, default: false },
    onSubmit: Function,
    errors: Object,
});

const form = props.form;
const selectedClient = computed(() => props.clients.find(c => c.id === form.client_id) || null);

const calculateSubtotal = () => {
    let subtotal = 0;
    form.items.forEach((item) => {
        item.amount = parseFloat((Number(item.quantity) * Number(item.rate)).toFixed(2));
        subtotal += item.amount;
    });
    form.subtotal = parseFloat(subtotal.toFixed(2));
};

const calculateTotal = () => {
    calculateSubtotal();
    const tax = (form.subtotal * (Number(form.tax_percent) || 0)) / 100;
    const discount = (form.subtotal * (Number(form.discount) || 0)) / 100;
    const shipping = Number(form.shipping) || 0;
    form.total = parseFloat((form.subtotal + tax + shipping - discount).toFixed(2));
};

watch(() => form.items, calculateTotal, { deep: true });

const addItem = () => {
    form.items.push({ description: '', quantity: 1.00, rate: 0.00, amount: 0.00 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    if (form.due_date < form.date) {
        alert("Due date cannot be earlier than the invoice date.");
        return;
    }
    calculateTotal();
    props.onSubmit();
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-8 bg-white p-6 rounded shadow">
        <!-- From / To -->
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">From</label>
                <input v-model="form.from_name" type="text" class="w-full rounded border-gray-300" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">To</label>
                <select v-model="form.client_id" class="w-full rounded border-gray-300">
                    <option disabled value="">Select a client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                        {{ client.name }}
                    </option>
                </select>
                

            </div>
            <div v-if="selectedClient" class="p-4 mt-4 bg-gray-100 rounded-xl shadow-md">
                    <h2 class="text-lg font-semibold mb-1">{{ selectedClient.name }}</h2>
                    <p><strong>Email:</strong> {{ selectedClient.email || 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ selectedClient.phone_personal || selectedClient.phone_business || 'N/A' }}</p>
                    </div>
        </div>

        <!-- Invoice Info -->
        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Invoice #</label>
                <input v-model="form.invoice_number" type="text" class="w-full rounded border-gray-300" />
                <span v-if="errors.invoice_number" class="text-sm text-red-500">{{ errors.invoice_number }}</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date</label>
                <input v-model="form.date" type="date" class="w-full rounded border-gray-300" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                <input v-model="form.due_date" type="date" class="w-full rounded border-gray-300" />
                <span v-if="errors.due_date" class="text-sm text-red-500">{{ errors.due_date }}</span>
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

            <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-7 gap-2 items-center mt-2">
                <input v-model="item.description" type="text" class="col-span-3 rounded border-gray-300" />
                <input v-model.number="item.quantity" type="number" min="0"  class="rounded border-gray-300 text-right" />
                <input v-model.number="item.rate" type="number" min="0" class="rounded border-gray-300 text-right" />
                <div class="text-right">${{ Number(item.amount).toFixed(2) }}</div>
                <button type="button" @click="removeItem(index)" class="w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex justify-center items-center">
                    &times;
                </button>
            </div>

            <button type="button" @click="addItem" class="mt-2 text-sm px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                + Add Line Item
            </button>
        </div>

        <!-- Totals & Notes -->
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes <span class="text-gray-400">(optional)</span></label>
                    <textarea v-model="form.notes" class="w-full rounded border-gray-300"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Terms <span class="text-gray-400">(optional)</span></label>
                    <textarea v-model="form.terms" class="w-full rounded border-gray-300"></textarea>
                </div>
            </div>
            <div class="space-y-3 w-full max-w-md ml-auto">
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Subtotal:</label>
                    <div class="text-right">${{ form.subtotal.toFixed(2) }}</div>
                </div>
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Tax (%)</label>
                    <input v-model.number="form.tax_percent" @input="calculateTotal" type="number"  class="w-full text-right rounded border-gray-300" />
                </div>
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Discount (%)</label>
                    <input v-model.number="form.discount" @input="calculateTotal" type="number"  class="w-full text-right rounded border-gray-300" />
                </div>
                <div class="grid grid-cols-2 items-center gap-2">
                    <label class="text-right text-sm text-gray-700">Shipping</label>
                    <input v-model.number="form.shipping" @input="calculateTotal" type="number"  class="w-full text-right rounded border-gray-300" />
                </div>
                <div class="grid grid-cols-2 items-center gap-2 border-t pt-2 font-bold text-lg">
                    <label class="text-right text-gray-800">Total:</label>
                    <div class="text-right">${{ form.total.toFixed(2) }}</div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                {{ isEdit ? 'Update Invoice' : 'Save Invoice' }}
            </button>
        </div>
    </form>
</template>
