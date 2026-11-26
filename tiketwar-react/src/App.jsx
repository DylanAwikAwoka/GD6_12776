import { Route, Routes } from 'react-router';
import Navbar from './components/Navbar';
import Pemberitahuan from './components/Pemberitahuan';
import DaftarKonser from './pages/DaftarKonser';
import DetailKonser from './pages/DetailKonser';

export default function App() {
  return (
    <>
      <Navbar />
      <main className="kontainer">
        <Routes>
          <Route path="/" element={<DaftarKonser />} />
          <Route path="/konser/:id" element={<DetailKonser />} />
          <Route
            path="*"
            element={
              <Pemberitahuan jenis="error">Halaman tidak ditemukan.</Pemberitahuan>
            }
          />
        </Routes>
      </main>
    </>
  );
}
