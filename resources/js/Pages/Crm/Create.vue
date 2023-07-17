<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertModal from '@/Components/AlertModal.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const dealStatus = reactive({ data: null });
const accStatus = reactive({ data: null });
const modalView = ref(false)

const dealForm = useForm({
    deal_name: '',
    deal_stage: '',
    acc_name: '',
    acc_website: '',
    acc_phone: ''
});

defineProps({ errors: Object })

const submit = () => {
    dealForm.post(route('create.deal'), {
        wantsJson: true,
        preserveScroll: true,
        onSuccess: (res) => {
            dealForm.reset();
            dealStatus.data = usePage().props.deal_succes;
            accStatus.data = usePage().props.acc_succes;
            modalView.value = true;
        },
    });
};

const closeModal = () => {
    console.log("closing modal")
    modalView.value = false
}
</script>

<template>
    <Head title="Create new" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create new</h2>
        </template>
        <div v-if="dealStatus.data && modalView === true"
            class="fixed inset-0 w-full h-screen flex items-center justify-center bg-slate-500/30">
            <div class="w-full relative max-w-2xl flex flex-col justify-around gap-8 items-center bg-white shadow-lg rounded-lg p-8">
                <div v-on:click="closeModal" class="absolute cursor-pointer aspect-square text-center align-middle w-[30px] bg-slate-300 text-gray-700 text-2xl rounded-md font-black right-4 top-2">X</div>
                <div class="flex flex-col items-center font-semibold gap-2" v-if="dealStatus.data.data && dealStatus.data.data[0].code === 'SUCCESS'">Your new Deal was succesfully created and is available
                    here:
                    <a
                        :href="`https://crm.zoho.eu/crm/org20092426760/tab/Potentials/${dealStatus.data.data[0].details.id}`"
                        target="_blank" >
                        <PrimaryButton class="ml-4">
                            Deal inside ZOHO
                        </PrimaryButton>
                    </a>
                </div>
                <div class="flex flex-col items-center font-semibold gap-2" v-if="accStatus.data.data && accStatus.data.data[0].code === 'SUCCESS'">Your new Account was succesfully created and is available here:
                    <a
                        :href="`https://crm.zoho.eu/crm/org20092426760/tab/Accounts/${accStatus.data.data[0].details.id}`"
                        target="_blank" >
                        <PrimaryButton class="ml-4">
                            Account inside ZOHO
                        </PrimaryButton>
                    </a>
                </div>
                <div class="flex flex-col bg-red-800 rounded-md p-3 font-semibold text-white items-center gap-2" v-if="dealStatus.data.data && dealStatus.data.data[0].code !== 'SUCCESS'">Something went wrong during Deals record creation. Error:
                        <p class="capitalize">{{dealStatus.data.data[0].message}}</p>
                </div>
                <div class="flex flex-col bg-red-800 rounded-md p-3 font-semibold text-white items-center gap-2" v-if="accStatus.data.data && accStatus.data.data[0].code !== 'SUCCESS'">Something went wrong during Account record creation. Error:
                        <p class="capitalize">{{accStatus.data.data[0].message}}</p>
                </div>
                <div class="flex flex-col bg-red-800 rounded-md text-white font-semibold items-center p-3 gap-2" v-if="!dealStatus.data.data">Somethign went wrong during record creation. Error:
                        <p class="capitalize">{{dealStatus.data.message}}</p>
                </div>

            </div>
        </div>
        <div class="flex flex-wrap justify-center mx-5 py-5 gap-5">
            <form @submit.prevent="submit" class="flex basis-full flex-col max-w-lg gap-3">
                <p class="text-2xl text-center">New Deal</p>
                <div class="p-4 sm:p-4 bg-white h-full shadow sm:rounded-lg">
                    <InputLabel for="deal_name" class="text-2xl" value="Deal Name" />

                    <TextInput id="deal_name" type="text" class="mt-1 block w-full" v-model="dealForm.deal_name" required
                        autofocus />

                    <InputError class="mt-2" :message="dealForm.errors.name" />

                    <InputLabel for="deal_stage" class="text-2xl mt-3" value="Deal Stage" />

                    <select id="deal_stage"
                        class="mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
                        v-model="dealForm.deal_stage" required>
                        <option disabled value="">Please Select</option>
                        <option value="Qualification">Qualification</option>
                        <option value="Needs Analysis">Needs Analysis</option>
                        <option value="Value Proposition">Value Proposition</option>
                        <option value="Identify Decision Makers">Identify Decision Makers</option>
                        <option value="Proposal/Price Quote">Proposal/Price Quote</option>
                        <option value="Negotiation/Review">Negotiation/Review</option>
                        <option value="Closed Won">Closed Won</option>
                        <option value="Closed Lost">Closed Lost</option>
                        <option value="Closed-Lost to Competition">Closed-Lost to Competition</option>
                    </select>

                    <InputError class="mt-2" :message="dealForm.errors.deal_stage" />

                </div>

                <p class="text-2xl text-center">New Account</p>
                <div class="p-4 sm:p-4 bg-white shadow sm:rounded-lg">
                    <InputLabel for="acc_name" class="text-2xl" value="Account Name" />

                    <TextInput id="acc_name" type="text" class="mt-1 block w-full" v-model="dealForm.acc_name" required
                        autofocus />

                    <InputError class="mt-2" :message="dealForm.errors.acc_name" />

                    <InputLabel for="acc_phone" class="text-2xl mt-3" value="Account Phone" />

                    <TextInput id="acc_phone" type="text" class="mt-1 block w-full" v-model="dealForm.acc_phone" required
                        autofocus />

                    <InputError class="mt-2" :message="dealForm.errors.acc_phone" />

                    <InputLabel for="acc_website" class="text-2xl mt-3" value="Account Website" />

                    <TextInput id="acc_website" type="text" class="mt-1 block w-full" v-model="dealForm.acc_website"
                        required autofocus />

                    <InputError class="mt-2" :message="dealForm.errors.acc_website" />

                </div>

                <div class="flex items-center justify-center mt-auto">

                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': dealForm.processing }"
                        :disabled="dealForm.processing">
                        Create
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
