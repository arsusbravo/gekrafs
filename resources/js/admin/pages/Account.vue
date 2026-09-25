<script setup>
import { reactive, ref } from 'vue';
import api from '../api';
import FormField from '../components/FormField.vue';
import { currentUser, isAdmin } from '../currentUser';
import { notify } from '../toast';
import { errorMessage, validationErrors } from '../utils';

const saving = ref(false);
const errors = ref({});

const form = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
});

async function updatePassword() {
    saving.value = true;
    errors.value = {};

    try {
        const { data } = await api.put('/account/password', form);
        Object.assign(form, { current_password: '', password: '', password_confirmation: '' });
        notify(data.message);
    } catch (error) {
        errors.value = validationErrors(error);
        notify(errorMessage(error), 'error');
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="max-w-xl space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="font-semibold">Account details</h2>
            <dl class="mt-4 divide-y divide-gray-100 text-sm">
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-gray-500">Name</dt>
                    <dd class="font-medium">{{ currentUser.name }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="font-medium">{{ currentUser.email }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-2.5">
                    <dt class="text-gray-500">Role</dt>
                    <dd>
                        <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-medium', isAdmin() ? 'bg-brand-100 text-brand-800' : 'bg-gray-100 text-gray-700']">
                            {{ isAdmin() ? 'Administrator' : 'User' }}
                        </span>
                    </dd>
                </div>
            </dl>
            <p class="mt-4 text-xs text-gray-500">To change your name or email address, please contact an administrator.</p>
        </section>

        <form class="space-y-5 rounded-xl border border-gray-200 bg-white p-6" @submit.prevent="updatePassword">
            <h2 class="font-semibold">Change password</h2>

            <FormField id="current_password" label="Current password" :error="errors.current_password">
                <input id="current_password" v-model="form.current_password" type="password" class="form-input" autocomplete="current-password" required />
            </FormField>

            <FormField id="password" label="New password" :error="errors.password" hint="At least 8 characters.">
                <input id="password" v-model="form.password" type="password" class="form-input" autocomplete="new-password" required />
            </FormField>

            <FormField id="password_confirmation" label="Confirm new password">
                <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="form-input" autocomplete="new-password" required />
            </FormField>

            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Update password' }}</button>
        </form>
    </div>
</template>
