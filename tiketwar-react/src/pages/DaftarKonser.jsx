import { useEffect, useState } from 'react';
import KonserCard from '../components/KonserCard';
import Pemberitahuan from '../components/Pemberitahuan';
import { getDaftarKonser } from '../services/api';

export default function DaftarKonser() {
  const [daftar, setDaftar] = useState([]);
  const [memuat, setMemuat] = useState(true);
  const [error, setError] = useState('');
  const [kataKunci, setKataKunci] = useState('');

  // Mengambil data sekali, saat komponen pertama kali tampil.
  useEffect(() => {
    let abaikan = false;

    getDaftarKonser()
      .then((data) => {
        if (!abaikan) setDaftar(data);
      })
      .catch((err) => {
        if (!abaikan) setError(err.message);
      })
      .finally(() => {
        if (!abaikan) setMemuat(false);
      });

    return () => {
      abaikan = true;
    };
  }, []);

  const tampil = daftar.filter((konser) =>
    konser.nama.toLowerCase().includes(kataKunci.toLowerCase()),
  );

  if (memuat) return <Pemberitahuan>Memuat daftar konser...</Pemberitahuan>;
  if (error) return <Pemberitahuan jenis="error">{error}</Pemberitahuan>;

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
      {tampil.length === 0 ? (
        <Pemberitahuan>Tidak ada konser yang cocok.</Pemberitahuan>
      ) : (
        <div className="grid">
          {tampil.map((konser) => (
            <KonserCard key={konser.id} konser={konser} />
          ))}
        </div>
      )}
    </section>
  );
}
