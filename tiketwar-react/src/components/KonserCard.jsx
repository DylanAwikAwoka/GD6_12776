import { Link } from 'react-router';
import { formatRupiah, formatTanggal } from '../utils/format';

export default function KonserCard({ konser }) {
  const habis = konser.sisa === 0;

  return (
    <article className="kartu">
      <h3>{konser.nama}</h3>
      <p className="meta">
        {konser.kota} &middot; {formatTanggal(konser.tanggal)}
      </p>
      <p className="harga">{formatRupiah(konser.harga)}</p>
      <p>
        {habis ? (
          <span className="lencana habis">Habis</span>
        ) : (
          <span className="lencana">Sisa {konser.sisa}</span>
        )}
      </p>
      <Link to={`/konser/${konser.id}`} className="tombol">
        Lihat detail
      </Link>
    </article>
  );
}
