
import {
  NavLink,
  Navigate,
  Route,
  Routes,
  useNavigate
} from 'react-router-dom';
import { useState } from 'react';

import LoginPage from './pages/LoginPage.jsx';
import CatalogPage from './pages/CatalogPage.jsx';
import FamilyPage from './pages/FamilyPage.jsx';
import LoanPage from './pages/LoanPage.jsx';
import ReportPage from './pages/ReportPage.jsx';
import ToyManagementPage from './pages/ToyManagementPage.jsx';
import PromoterPage from './pages/PromoterPage.jsx';

import ProtectedRoute from './components/ProtectedRoute.jsx';
import { useAuth } from './context/AuthContext.jsx';

function TemporaryPage({ title, description }) {
  return (
    <section className="welcome-hero">
      <div className="welcome-copy">
        <span className="eyebrow">UN MUNDO DE POSIBILIDADES</span>
        <h2>{title}</h2>
        <p>{description} Cada juguete compartido es una nueva aventura para nuestra comunidad.</p>
        <NavLink className="btn btn-primary" to="/catalogo">Explorar el catálogo</NavLink>
      </div>
      <div className="welcome-art" aria-hidden="true">
        <span className="art-star">✦</span><span className="art-ball">●</span>
        <span className="art-toy">🧸</span><span className="art-block">A</span>
        <span className="art-sparkle">✳</span>
      </div>
    </section>
  );
}

export default function App() {
  const navigate = useNavigate();
  const [menuOpen, setMenuOpen] = useState(false);

  const {
    isAuthenticated,
    isAdmin,
    role,
    signOut
  } = useAuth();

  function handleLogout() {
    signOut();
    navigate('/login', { replace: true });
  }

  const links = [
    { to: '/', label: 'Inicio', end: true },

    ...(isAuthenticated
      ? [
          { to: '/catalogo', label: 'Catálogo' },
          { to: '/familias', label: 'Familias' },
          { to: '/prestamos', label: 'Préstamos' }
        ]
      : []),

    ...(isAdmin
      ? [
          {
            to: '/administrar-juguetes',
            label: 'Administrar juguetes'
          },
          { to: '/editar-juguetes', label: 'Editar juguetes' },
          {
            to: '/administrar-promotores',
            label: 'Administrar promotores'
          },
          {
            to: '/reportes',
            label: 'Reportes'
          }
        ]
      : [])
  ];

  return (
    <div className="app-container">
      <header className="app-header">
        <div className="header-inner">
        <div className="brand-lockup"><span className="brand-icon" aria-hidden="true">✦</span><div><h1>Toy Library</h1>
        <p>Jugar nos une</p></div></div>

        <button type="button" className="menu-toggle" aria-expanded={menuOpen} aria-controls="main-navigation" onClick={() => setMenuOpen(!menuOpen)}>
          {menuOpen ? 'Cerrar menú' : 'Menú'}
        </button>

        <nav
          id="main-navigation"
          className={`main-nav${menuOpen ? ' is-open' : ''}`}
          aria-label="Navegación principal"
        >
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              end={link.end}
              onClick={() => setMenuOpen(false)}
              className={({ isActive }) =>
                isActive
                  ? 'nav-link active'
                  : 'nav-link'
              }
            >
              {link.label}
            </NavLink>
          ))}

          {isAuthenticated ? (
            <button
              type="button"
              className="btn btn-secondary"
              onClick={() => { setMenuOpen(false); handleLogout(); }}
            >
              Cerrar sesión
            </button>
          ) : (
            <NavLink
              to="/login"
              onClick={() => setMenuOpen(false)}
              className={({ isActive }) =>
                isActive
                  ? 'nav-link active'
                  : 'nav-link'
              }
            >
              Iniciar sesión
            </NavLink>
          )}
        </nav>

        {isAuthenticated && (
          <p className="session-pill">
            Sesión iniciada como:{' '}
            {role === 'admin'
              ? 'Administrador'
              : 'Promotor'}
          </p>
        )}
        </div>
      </header>

      <main className="main-content">
        <Routes>
          <Route
            path="/"
            element={
              <TemporaryPage
                title="Bienvenido a Toy Library"
                description="Consulta juguetes y conoce nuestro sistema comunitario de préstamos."
              />
            }
          />

          <Route
            path="/catalogo"
            element={<ProtectedRoute><CatalogPage /></ProtectedRoute>}
          />

          <Route
            path="/login"
            element={<LoginPage />}
          />

          <Route
            path="/familias"
            element={
              <ProtectedRoute>
                <FamilyPage />
              </ProtectedRoute>
            }
          />

          <Route
            path="/prestamos"
            element={
              <ProtectedRoute>
                <LoanPage />
              </ProtectedRoute>
            }
          />

          <Route
            path="/administrar-juguetes"
            element={
              <ProtectedRoute adminOnly>
                <ToyManagementPage key="manage" />
              </ProtectedRoute>
            }
          />

          <Route
            path="/editar-juguetes"
            element={<ProtectedRoute adminOnly><ToyManagementPage key="edit" editMode /></ProtectedRoute>}
          />

          <Route
            path="/administrar-promotores"
            element={
              <ProtectedRoute adminOnly>
                <PromoterPage />
              </ProtectedRoute>
            }
          />

          <Route
            path="/reportes"
            element={
              <ProtectedRoute adminOnly>
                <ReportPage />
              </ProtectedRoute>
            }
          />

          <Route
            path="*"
            element={<Navigate to="/" replace />}
          />
        </Routes>
      </main>

      <footer className="app-footer">
        <p>
          Toy Library - Biblioteca comunitaria de juguetes
        </p>
      </footer>
    </div>
  );
}
