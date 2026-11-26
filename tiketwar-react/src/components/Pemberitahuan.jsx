// jenis: 'info' (bawaan), 'error', atau 'sukses'. Isinya dikirim lewat children.
export default function Pemberitahuan({ jenis = 'info', children }) {
  return <div className={`pemberitahuan ${jenis}`}>{children}</div>;
}
