const API_URL = import.meta.env.VITE_API_URL;

// Kesalahan dari API: membawa kode status dan daftar kesalahan per isian (422).
export class ApiError extends Error {
  constructor(status, pesan, errors = {}) {
    super(pesan);
    this.status = status;
    this.errors = errors;
  }
}

async function request(path, options = {}) {
  let response;
  try {
    response = await fetch(`${API_URL}${path}`, {
      ...options,
      headers: {
        Accept: 'application/json',
        ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      },
    });
  } catch {
    // fetch hanya gagal (throw) bila server tidak terjangkau atau diblokir CORS.
    throw new ApiError(
      0,
      'Tidak dapat terhubung ke server. Periksa php artisan serve dan CORS.',
    );
  }

  const body = await response.json().catch(() => null);

  if (!response.ok) {
    throw new ApiError(
      response.status,
      body?.message ?? 'Terjadi kesalahan pada server.',
      body?.errors ?? {},
    );
  }
  return body;
}

export function getDaftarKonser() {
  return request('/konser').then((body) => body.data);
}

export function getKonser(id) {
  return request(`/konser/${id}`).then((body) => body.data);
}

export function buatPesanan(konserId, data) {
  return request(`/konser/${konserId}/pesanan`, {
    method: 'POST',
    body: JSON.stringify(data),
  }).then((body) => body.data);
}
