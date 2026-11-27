
const API_BASE_URL = (
  import.meta.env.VITE_API_URL || '/api/v1'
).replace(/\/+$/, '');

const TOKEN_KEY = 'toy_library_token';

export function getToken() {
  return sessionStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
  sessionStorage.setItem(TOKEN_KEY, token);
}

export function clearToken() {
  sessionStorage.removeItem(TOKEN_KEY);
}

export class ApiError extends Error {
  constructor(message, status, code = null, details = []) {
    super(message);

    this.name = 'ApiError';
    this.status = status;
    this.code = code;
    this.details = details;
  }
}

export async function apiRequest(
  endpoint,
  { method = 'GET', body, headers = {}, ...options } = {}
) {
  const token = getToken();

  const requestHeaders = new Headers(headers);

  requestHeaders.set('Accept', 'application/json');

  if (token) {
    requestHeaders.set('Authorization', `Bearer ${token}`);
  }

  if (
    body !== undefined &&
    body !== null &&
    !(body instanceof FormData)
  ) {
    requestHeaders.set('Content-Type', 'application/json');
  }

  const response = await fetch(
    `${API_BASE_URL}${endpoint}`,
    {
      ...options,
      method,
      headers: requestHeaders,
      body:
        body === undefined || body === null
          ? undefined
          : body instanceof FormData
            ? body
            : JSON.stringify(body)
    }
  );

  if (response.status === 204) {
    return null;
  }

  const contentType =
    response.headers.get('content-type') || '';

  const data = contentType.includes('application/json')
    ? await response.json()
    : await response.text();

  if (!response.ok) {
    const error = data?.error || {};

    throw new ApiError(
      error.message ||
        data?.message ||
        'Ocurrió un error al comunicarse con el servidor.',
      response.status,
      error.code || data?.code || null,
      error.details || data?.details || []
    );
  }

  return data;
}

// AUTENTICACIÓN

export async function login(username, password) {
  const data = await apiRequest('/auth/login', {
    method: 'POST',
    body: {
      username,
      password
    }
  });

  setToken(data.access_token);

  return data;
}

export function logout() {
  clearToken();
}

// CATÁLOGO

export function getCategories() {
  return apiRequest('/categories');
}

export function getToys(params = {}) {
  const query = new URLSearchParams(params).toString();

  return apiRequest(`/toys${query ? `?${query}` : ''}`);
}

export function getToy(id) {
  return apiRequest(`/toys/${encodeURIComponent(id)}`);
}

export function createToy(data) {
  return apiRequest('/toys', {
    method: 'POST',
    body: data
  });
}

export function updateToy(id, data) {
  return apiRequest(`/toys/${encodeURIComponent(id)}`, {
    method: 'PUT',
    body: data
  });
}

export function deleteToy(id) {
  return apiRequest(`/toys/${encodeURIComponent(id)}`, {
    method: 'DELETE'
  });
}

export function uploadToyCover(id, file) {
  const formData = new FormData();

  formData.append('file', file);
  formData.append('_method', 'PUT');

  return apiRequest(
    `/toys/${encodeURIComponent(id)}/cover`,
    {
      method: 'POST',
      body: formData
    }
  );
}

export function deleteToyCover(id) {
  return apiRequest(
    `/toys/${encodeURIComponent(id)}/cover`,
    {
      method: 'DELETE'
    }
  );
}

export function getToyCoverUrl(id) {
  return `${API_BASE_URL}/toys/${encodeURIComponent(id)}/cover`;
}

// FAMILIAS

export function getFamilies(params = {}) {
  const query = new URLSearchParams(params).toString();

  return apiRequest(
    `/families${query ? `?${query}` : ''}`
  );
}

export function getFamily(id) {
  return apiRequest(
    `/families/${encodeURIComponent(id)}`
  );
}

export function createFamily(data) {
  return apiRequest('/families', {
    method: 'POST',
    body: data
  });
}

// PRÉSTAMOS

export function getLoans(params = {}) {
  const query = new URLSearchParams(params).toString();

  return apiRequest(
    `/loans${query ? `?${query}` : ''}`
  );
}

export function getLoan(id) {
  return apiRequest(
    `/loans/${encodeURIComponent(id)}`
  );
}

export function createLoan(familyId, toyIds) {
  return apiRequest('/loans', {
    method: 'POST',
    body: {
      family_id: familyId,
      toy_ids: toyIds
    }
  });
}

export function returnLoanItem(loanId, itemId) {
  return apiRequest(
    `/loans/${encodeURIComponent(loanId)}/items/${encodeURIComponent(itemId)}/return`,
    {
      method: 'POST'
    }
  );
}

// PROMOTORES

export function createPromoter(data) {
  return apiRequest('/users', {
    method: 'POST',
    body: data
  });
}

// REPORTES

export function getMostLoanedReport(limit = 10, from, to) {
  return apiRequest(
    `/reports/most-loaned?${new URLSearchParams({ limit, from, to })}`
  );
}
