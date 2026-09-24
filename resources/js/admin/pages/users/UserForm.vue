<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';
import FormField from '../../components/FormField.vue';
import { notify } from '../../toast';
import { errorMessage, validationErrors } from '../../utils';

const props = defineProps({
    id: { type: String, default: null },
});

const router = useRouter();
const loading = ref(!!props.id);
const saving = ref(false);
const errors = ref({});

const form = reactive({
    name: '',
    email: '',
    password: '',
    is_admin: false,
});

onMounted(async () => {
    if (!props.id) return;

    try {
        const { data } = await api.get(`/users/${props.id}`);
        Object.assign(form, { name: data.name, email: data.email, is_admin: data.is_admin });
    } catch (error) {
        notify(errorMessage(error), 'error');
        router.push({ name: 'users.index' });
    } finally {
        loading.value = false;
    }
});

async function submit() {
    saving.value = true;
    errors.value = {};

    try {
        if (props.id) {
            await api.put(`/users/${props.id}`, form);
            notify('User updated.');
        } else {
            await api.post('/users', form);
            notify('User created.');
        }
        router.push({ name: 'users.index' });
    } catch (error) {
        errors.value = validationErrors(error);
        notify(errorMessage(error), 'error');
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div v-if="loading" class="text-sm text-gray-500">Loading…</div>

    <form v-else class="max-w-xl space-y-6" @submit.prevent="submit">
        <div class="space-y-5 rounded-xl border border-gray-200 bg-white p-6">
            <FormField id="name" label="Name" :error="errors.name">
                <input id="name" v-model="form.name" type="text" class="form-input" required />
            </FormField>

            <FormField id="email" label="Email" :error="errors.email">
                <input id="email" v-model="form.email" type="email" class="form-input" required />
            </FormField>

            <FormField id="password" label="Password" :error="errors.password" :hint="props.id ? 'Leave empty to keep the current password.' : null">
                <input id="password" v-model="form.password" type="password" class="form-input" autocomplete="new-password" :required="!props.id" />
            </FormField>

            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                <input v-model="form.is_admin" type="checkbox" class="rounded border-gray-300 text-brand-600" />
                Administrator (can access this admin panel)
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Save user' }}</button>
            <RouterLink :to="{ name: 'users.index' }" class="btn btn-secondary">Cancel</RouterLink>
        </div>
    </form>
</template>
