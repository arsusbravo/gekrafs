<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { toFormData } from '../../api';
import FormField from '../../components/FormField.vue';
import ImageInput from '../../components/ImageInput.vue';
import RichTextEditor from '../../components/RichTextEditor.vue';
import { notify } from '../../toast';
import { errorMessage, toInputDateTime, validationErrors } from '../../utils';

const props = defineProps({
    id: { type: String, default: null },
});

const router = useRouter();
const loading = ref(!!props.id);
const saving = ref(false);
const errors = ref({});
const currentImage = ref(null);

const form = reactive({
    title: '',
    slug: '',
    description: '',
    location: '',
    starts_at: '',
    ends_at: '',
    is_published: false,
    image: null,
    remove_image: false,
});

onMounted(async () => {
    if (!props.id) return;

    try {
        const { data } = await api.get(`/events/${props.id}`);
        Object.assign(form, {
            title: data.title,
            slug: data.slug,
            description: data.description ?? '',
            location: data.location ?? '',
            starts_at: toInputDateTime(data.starts_at),
            ends_at: toInputDateTime(data.ends_at),
            is_published: data.is_published,
        });
        currentImage.value = data.image_url;
    } catch (error) {
        notify(errorMessage(error), 'error');
        router.push({ name: 'events.index' });
    } finally {
        loading.value = false;
    }
});

async function submit() {
    saving.value = true;
    errors.value = {};

    const payload = { ...form };
    if (!payload.image) delete payload.image;

    try {
        if (props.id) {
            await api.post(`/events/${props.id}`, toFormData(payload, 'PUT'));
            notify('Event updated.');
        } else {
            await api.post('/events', toFormData(payload));
            notify('Event created.');
        }
        router.push({ name: 'events.index' });
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

    <form v-else class="max-w-3xl space-y-6" @submit.prevent="submit">
        <div class="space-y-5 rounded-xl border border-gray-200 bg-white p-6">
            <FormField id="title" label="Title" :error="errors.title">
                <input id="title" v-model="form.title" type="text" class="form-input" required />
            </FormField>

            <FormField id="slug" label="Slug" :error="errors.slug" hint="Leave empty to generate it from the title.">
                <input id="slug" v-model="form.slug" type="text" class="form-input" />
            </FormField>

            <div class="grid gap-5 sm:grid-cols-2">
                <FormField id="starts_at" label="Starts at" :error="errors.starts_at">
                    <input id="starts_at" v-model="form.starts_at" type="datetime-local" class="form-input" required />
                </FormField>
                <FormField id="ends_at" label="Ends at" :error="errors.ends_at">
                    <input id="ends_at" v-model="form.ends_at" type="datetime-local" class="form-input" />
                </FormField>
            </div>

            <FormField id="location" label="Location" :error="errors.location">
                <input id="location" v-model="form.location" type="text" class="form-input" />
            </FormField>

            <FormField id="description" label="Description" :error="errors.description">
                <RichTextEditor id="description" v-model="form.description" placeholder="Describe the event: program, speakers, dress code…" :invalid="!!errors.description" />
            </FormField>

            <ImageInput v-model:file="form.image" v-model:remove="form.remove_image" :current-url="currentImage" :error="errors.image" />

            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                <input v-model="form.is_published" type="checkbox" class="rounded border-gray-300 text-brand-600" />
                Published (visible on the website)
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Save event' }}</button>
            <RouterLink :to="{ name: 'events.index' }" class="btn btn-secondary">Cancel</RouterLink>
        </div>
    </form>
</template>
