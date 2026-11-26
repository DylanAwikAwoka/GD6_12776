// Mengubah angka 250000 menjadi "Rp 250.000".
export function formatRupiah(angka) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(angka);
}

// Mengubah "2027-01-30" menjadi "30 Januari 2027".
export function formatTanggal(teks) {
  return new Date(teks).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    timeZone: 'UTC',
  });
}
