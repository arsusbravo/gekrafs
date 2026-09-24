<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api, { toFormData } from '../../api';
import FormField from '../../components/FormField.vue';
import ImageInput from '../../components/ImageInput.vue';
import RichTextEditor from '../../components/RichTextEditor.vue';
import { notify } from '../../toast';
import { errorMessage, formatDateTime, toInputDateTime, validationErrors } from '../../utils';

const props = defineProps({
    id: { type: String, default: null },
});

const router = useRouter();
const loading = ref(!!props.id);
const saving = ref(false);
const errors = ref({});
const currentImage = ref(null);
const events = ref([]);

const form = reactive({
    event_id: '',
    title: '',
    slug: '',
    excerpt: '',
    body: '',
    published_at: '',
    image: null,
    remove_image: false,
});

function publishNow() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    form.published_at = now.toISOString().slice(0, 16);
}

onMounted(async () => {
    api.get('/events/options').then(({ data }) => (events.value = data));

    if (!props.id) return;

    try {
        const { data } = await api.get(`/posts/${props.id}`);
        Object.assign(form, {
            event_id: data.event_id ?? '',
            title: data.title,
            slug: data.slug,
            excerpt: data.excerpt ?? '',
            body: data.body,
            published_at: toInputDateTime(data.published_at),
        });
        currentImage.value = data.image_url;
    } catch (error) {
        notify(errorMessage(error), 'error');
        router.push({ name: 'posts.index' });
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
            await api.post(`/posts/${props.id}`, toFormData(payload, 'PUT'));
            notify('Post updated.');
        } else {
            await api.post('/posts', toFormData(payload));
            notify('Post created.');
        }
        router.push({ name: 'posts.index' });
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

            <FormField id="event_id" label="Event report" :error="errors.event_id" hint="Optional: link this post to the event it reports on.">
                <select id="event_id" v-model="form.event_id" class="form-input">
                    <option value="">— Not linked to an event —</option>
                    <option v-for="event in events" :key="event.id" :value="event.id">{{ event.title }} ({{ formatDateTime(event.starts_at) }})</option>
                </select>
            </FormField>

            <FormField id="excerpt" label="Excerpt" :error="errors.excerpt" hint="A short summary shown on the blog overview.">
                <textarea id="excerpt" v-model="form.excerpt" rows="2" maxlength="500" class="form-input" />
            </FormField>

            <FormField id="body" label="Content" :error="errors.body">
                <RichTextEditor id="body" v-model="form.body" placeholder="Write the news article…" :invalid="!!errors.body" />
            </FormField>

            <ImageInput v-model:file="form.image" v-model:remove="form.remove_image" :current-url="currentImage" :error="errors.image" />

            <FormField id="published_at" label="Publish date" :error="errors.published_at" hint="Leave empty to keep as draft. A future date schedules the post.">
                <div class="flex gap-2">
                    <input id="published_at" v-model="form.published_at" type="datetime-local" class="form-input" />
                    <button type="button" class="btn btn-secondary shrink-0" @click="publishNow">Now</button>
                    <button v-if="form.published_at" type="button" class="btn btn-secondary shrink-0" @click="form.published_at = ''">Clear</button>
                </div>
            </FormField>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving…' : 'Save post' }}</button>
            <RouterLink :to="{ name: 'posts.index' }" class="btn btn-secondary">Cancel</RouterLink>
        </div>
    </form>
</template>
