import { useState } from 'react';
import KonserCard from '../components/KonserCard';

// Data contoh: bentuknya sama dengan data API, tetapi isinya karangan.
const DATA_CONTOH = [
  { id: 1, nama: 'Konser Contoh Satu', kota: 'Jakarta', harga: 750000,
    sisa: 20, tanggal: '2026-12-12' },
  { id: 2, nama: 'Konser Contoh Dua', kota: 'Surabaya', harga: 500000,
    sisa: 0, tanggal: '2026-12-20' },
  { id: 3, nama: 'Malam Akustik Contoh', kota: 'Bandung', harga: 250000,
    sisa: 57, tanggal: '2027-01-30' },
];

export default function DaftarKonser() {
  const [kataKunci, setKataKunci] = useState('');

  const tampil = DATA_CONTOH.filter((konser) =>
    konser.nama.toLowerCase().includes(kataKunci.toLowerCase()),
  );

  return (
    <section>
      <h1>Daftar Konser</h1>
      <input
        type="search"
        className="cari"
        placeholder="Cari nama konser"
        value={kataKunci}
        onChange={(e) => setKataKunci(e.target.value)}
      />
      {tampil.length === 0 && <p>Tidak ada konser yang cocok.</p>}
      <div className="grid">
        {tampil.map((konser) => (
          <KonserCard key={konser.id} konser={konser} />
        ))}
      </div>
    </section>
  );
}
