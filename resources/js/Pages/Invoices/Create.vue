<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    user: Object,
    nextInvoiceNumber: Number,
});

const form = useForm({
    from_name: props.user.name || '',
    to_name: '',
    invoice_number: props.nextInvoiceNumber || '',
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

const calculateSubtotal = () => {
    let subtotal = 0;

    form.items.forEach((item) => {
        item.amount = parseFloat((item.quantity * item.rate).toFixed(2));
        subtotal += item.amount;
    });

    form.subtotal = parseFloat(subtotal.toFixed(2));
};

const calculateTotal = () => {
    calculateSubtotal();

    const taxAmount = (form.subtotal * form.tax_percent) / 100;
    const discountAmount = (form.subtotal * form.discount) / 100;

    form.total = parseFloat(
        (form.subtotal + taxAmount + form.shipping - discountAmount).toFixed(2)
    );
};

// Recalculate when items change
watch(() => form.items, calculateTotal, { deep: true });

// Add new item
const addItem = () => {
    form.items.push({ description: '', quantity: 1, rate: 0, amount: 0 });
};

// Remove item
const removeItem = (index) => {
    form.items.splice(index, 1);
};

// Submit
const submit = () => {
    calculateTotal();
    form.post('/invoices');
};

</script>

<template>
    <Head title="Create Invoice" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Invoice
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded shadow">
                    <form @submit.prevent="submit" class="space-y-8">
                        <!-- Header: From / To -->
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">From</label>
                                <input
                                    v-model="form.from_name"
                                    type="text"
                                    :placeholder="props.user?.name ?? 'Your name'"
                                    class="w-full rounded border-gray-300"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">To</label>
                                <input v-model="form.to_name" type="text" class="w-full rounded border-gray-300" />
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="grid grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Invoice #</label>
                                <input
                                    v-model="form.invoice_number"
                                    type="text"
                                    class="w-full rounded border-gray-300"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <input v-model="form.date" type="date" class="w-full rounded border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input v-model="form.due_date" type="date" class="w-full rounded border-gray-300" />
                            </div>
                        </div>

                        <!-- Line Items -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Items</label>

                            <!-- Header Row -->
                            <div class="grid grid-cols-7 gap-2 font-semibold bg-gray-100 p-2 rounded">
                                <div class="col-span-3">Description</div>
                                <div>Qty</div>
                                <div>Rate</div>
                                <div>Amount</div>
                                <div></div> <!-- Placeholder for X button -->
                            </div>

                            <!-- Item Rows -->
                            <div
                                v-for="(item, index) in form.items"
                                :key="index"
                                class="grid grid-cols-7 gap-2 items-center mt-2"
                            >
                                <input
                                    v-model="item.description"
                                    type="text"
                                    placeholder="Description"
                                    class="col-span-3 rounded border-gray-300"
                                />
                                <input
                                    v-model.number="item.quantity"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="rounded border-gray-300"
                                />
                                <input
                                    v-model.number="item.rate"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="rounded border-gray-300"
                                />
                                <div class="text-right">{{ item.amount.toFixed(2) }}</div>
                                <div class="flex justify-end">
                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        class="w-6 h-6 flex items-center justify-center text-white bg-red-500 hover:bg-red-600 rounded-full"
                                        title="Remove item"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <!-- Add Item Button -->
                            <button
                                type="button"
                                class="mt-2 text-sm px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                @click="addItem"
                            >
                                + Add Line Item
                            </button>
                        </div>


                        <!-- Totals -->
                        <div class="grid grid-cols-2 gap-6 mt-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                    <textarea v-model="form.notes" class="w-full rounded border-gray-300"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Terms (Optional)</label>
                                    <textarea v-model="form.terms" class="w-full rounded border-gray-300"></textarea>
                                </div>
                            </div>

                            <div class="space-y-3 w-full max-w-md ml-auto">
                                <div class="grid grid-cols-2 items-center gap-2">
                                    <label class="text-right text-sm text-gray-700">Subtotal:</label>
                                    <input v-model.number="form.subtotal" readonly type="number" class="w-full text-right bg-gray-100 rounded border-gray-300" />
                                </div>

                                <div class="grid grid-cols-2 items-center gap-2">
                                    <label class="text-right text-sm text-gray-700">Tax (%)</label>
                                    <input v-model.number="form.tax_percent" @input="calculateTotal" type="number" class="w-full text-right rounded border-gray-300" />
                                </div>

                                <div class="grid grid-cols-2 items-center gap-2">
                                    <label class="text-right text-sm text-gray-700">Discount (%)</label>
                                    <input v-model.number="form.discount" @input="calculateTotal" type="number" class="w-full text-right rounded border-gray-300" />
                                </div>

                                <div class="grid grid-cols-2 items-center gap-2">
                                    <label class="text-right text-sm text-gray-700">Shipping</label>
                                    <input v-model.number="form.shipping" @input="calculateTotal" type="number" class="w-full text-right rounded border-gray-300" />
                                </div>

                                <div class="grid grid-cols-2 items-center gap-2 border-t pt-2 font-bold text-lg">
                                    <label class="text-right text-gray-800">Total:</label>
                                    <input v-model.number="form.total" readonly type="text" class="w-full text-right bg-gray-100 rounded border-gray-300" />
                                </div>
                            </div>

                        </div>

                        <!-- Submit -->
                        <div class="text-right">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Save Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
