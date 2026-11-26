import { useState } from 'react';
import { buatPesanan } from '../services/api';
import Pemberitahuan from './Pemberitahuan';
import { formatRupiah } from '../utils/format';

const FORM_AWAL = { nama_pemesan: '', email: '', jumlah: 1 };

export default function FormPesan({ konser, onBerhasil }) {
  const [form, setForm] = useState(FORM_AWAL);
  const [errors, setErrors] = useState({});
  const [pesanError, setPesanError] = useState('');
  const [mengirim, setMengirim] = useState(false);
  const [pesanan, setPesanan] = useState(null);
  const habis = konser.sisa === 0;

  function ubah(e) {
    const { name, value } = e.target;
    setForm((lama) => ({ ...lama, [name]: value }));
  }

  async function kirim(e) {
    e.preventDefault();
    if (mengirim) return;

    setMengirim(true);
    setErrors({});
    setPesanError('');
    setPesanan(null);

    try {
      const hasil = await buatPesanan(konser.id, {
        nama_pemesan: form.nama_pemesan,
        email: form.email,
        jumlah: form.jumlah === '' ? null : Number(form.jumlah),
      });
      setPesanan(hasil);
      setForm(FORM_AWAL);
      onBerhasil(hasil);
    } catch (err) {
      if (err.status === 422) {
        setErrors(err.errors);
      } else {
        setPesanError(err.message);
      }
    } finally {
      setMengirim(false);
    }
  }

  return (
    <form className="form" onSubmit={kirim} noValidate>
      <h2>Pesan Tiket</h2>

      {pesanan && (
        <Pemberitahuan jenis="sukses">
          Pesanan #{pesanan.id} berhasil dibuat. Total{' '}
          {formatRupiah(pesanan.total)}, status {pesanan.status.replace('_', ' ')}.
        </Pemberitahuan>
      )}
      {pesanError && <Pemberitahuan jenis="error">{pesanError}</Pemberitahuan>}

      <label>
        Nama pemesan
        <input
          name="nama_pemesan"
          value={form.nama_pemesan}
          onChange={ubah}
        />
        {errors.nama_pemesan && (
          <small className="salah">{errors.nama_pemesan[0]}</small>
        )}
      </label>

      <label>
        Email
        <input
          type="email"
          name="email"
          value={form.email}
          onChange={ubah}
        />
        {errors.email && (
          <small className="salah">{errors.email[0]}</small>
        )}
      </label>

      <label>
        Jumlah tiket
        <input
          type="number"
          name="jumlah"
          min="1"
          value={form.jumlah}
          onChange={ubah}
        />
        {errors.jumlah && (
          <small className="salah">{errors.jumlah[0]}</small>
        )}
      </label>

      <button type="submit" className="tombol" disabled={mengirim || habis}>
        {mengirim ? 'Mengirim...' : 'Pesan sekarang'}
      </button>
    </form>
  );
}
