import { Link } from 'react-router';

export default function Navbar() {
  return (
    <header className="navbar">
      <Link to="/" className="merek">
        TiketWar
      </Link>
    </header>
  );
}
