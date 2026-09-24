/**
 * Dates come from the server as "YYYY-MM-DD HH:mm:ss" in the app timezone.
 */
export function toInputDateTime(value) {
    return value ? value.replace(' ', 'T').slice(0, 16) : '';
}

export function formatDateTime(value) {
    if (!value) return '—';

    const [date, time] = value.split(' ');
    const [y, m, d] = date.split('-').map(Number);

    return new Date(y, m - 1, d).toLocaleDateString(undefined, {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }) + (time ? ` · ${time.slice(0, 5)}` : '');
}

export function validationErrors(error) {
    if (error.response?.status === 422) {
        return error.response.data.errors ?? {};
    }

    return {};
}

export function errorMessage(error) {
    return error.response?.data?.message ?? 'Something went wrong. Please try again.';
}
