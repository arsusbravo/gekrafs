import { onMounted, ref, watch } from 'vue';
import api from '../api';
import { notify } from '../toast';
import { errorMessage } from '../utils';

/**
 * Shared state and actions for paginated, searchable admin list pages.
 */
export function useResourceList(endpoint) {
    const items = ref([]);
    const meta = ref({ current_page: 1, last_page: 1 });
    const search = ref('');
    const loading = ref(false);

    async function load(page = 1) {
        loading.value = true;
        try {
            const { data } = await api.get(endpoint, { params: { page, search: search.value || undefined } });
            items.value = data.data;
            meta.value = data;
        } catch (error) {
            notify(errorMessage(error), 'error');
        } finally {
            loading.value = false;
        }
    }

    async function destroy(item, label) {
        if (!confirm(`Delete "${label}"? This cannot be undone.`)) return;

        try {
            await api.delete(`${endpoint}/${item.id}`);
            notify(`"${label}" was deleted.`);
            const page = items.value.length === 1 && meta.value.current_page > 1 ? meta.value.current_page - 1 : meta.value.current_page;
            await load(page);
        } catch (error) {
            notify(errorMessage(error), 'error');
        }
    }

    let timer;
    watch(search, () => {
        clearTimeout(timer);
        timer = setTimeout(() => load(1), 300);
    });

    onMounted(() => load());

    return { items, meta, search, loading, load, destroy };
}
