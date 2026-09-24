import axios from 'axios';

const api = axios.create({
    baseURL: '/admin/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
    },
    withCredentials: true,
    withXSRFToken: true,
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status;

        // Session expired or access revoked: send the user back to the login page.
        if (status === 401 || status === 419) {
            window.location.href = '/login';
        }

        return Promise.reject(error);
    },
);

/**
 * Build a FormData payload, used for requests that may include file uploads.
 * Laravel cannot read multipart bodies on PUT, so updates are spoofed via _method.
 */
export function toFormData(data, method = 'POST') {
    const form = new FormData();

    Object.entries(data).forEach(([key, value]) => {
        if (value === null || value === undefined) {
            form.append(key, '');
        } else if (typeof value === 'boolean') {
            form.append(key, value ? '1' : '0');
        } else {
            form.append(key, value);
        }
    });

    if (method !== 'POST') {
        form.append('_method', method);
    }

    return form;
}

export default api;
