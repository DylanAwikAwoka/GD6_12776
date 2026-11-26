import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router';
import FormPesan from '../components/FormPesan';
import Pemberitahuan from '../components/Pemberitahuan';
import { getKonser } from '../services/api';
import { formatRupiah, formatTanggal } from '../utils/format';

export default function DetailKonser() {
  const { id } = useParams();
  const [konser, setKonser] = useState(null);
  const [memuat, setMemuat] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    let abaikan = false;
    setMemuat(true);
    setError('');

    getKonser(id)
      .then((data) => {
        if (!abaikan) setKonser(data);
      })
      .catch((err) => {
        if (abaikan) return;
        setError(err.status === 404 ? 'Konser tidak ditemukan.' : err.message);
      })
      .finally(() => {
        if (!abaikan) setMemuat(false);
      });

    return () => {
      abaikan = true;
    };
  }, [id]);

  // Dipanggil FormPesan setelah pesanan berhasil, agar sisa tiket ikut turun.
  function kurangiSisa(pesanan) {
    setKonser((lama) => ({ ...lama, sisa: lama.sisa - pesanan.jumlah }));
  }

  if (memuat) return <Pemberitahuan>Memuat konser...</Pemberitahuan>;
  if (error) {
    return (
      <>
        <Pemberitahuan jenis="error">{error}</Pemberitahuan>
        <Link to="/">Kembali ke daftar konser</Link>
      </>
    );
  }

  return (
    <section>
      <Link to="/">&larr; Kembali ke daftar konser</Link>
      <h1>{konser.nama}</h1>
      <p className="meta">
        {konser.kota} &middot; {formatTanggal(konser.tanggal)} &middot;
        Promotor: {konser.promotor}
      </p>
      <p className="harga">{formatRupiah(konser.harga)}</p>
      <p>Sisa tiket: {konser.sisa}</p>

      {konser.sisa === 0 && (
        <Pemberitahuan jenis="error">Tiket habis.</Pemberitahuan>
      )}
      <FormPesan konser={konser} onBerhasil={kurangiSisa} />
    </section>
  );
}
