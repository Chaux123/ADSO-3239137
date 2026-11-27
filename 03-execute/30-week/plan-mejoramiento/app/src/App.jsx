
import {
  NavLink,
  Navigate,
  Route,
  Routes,
  useNavigate
} from 'react-router-dom';

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
    <section className="page-content">
      <h2>{title}</h2>
      <p>{description}</p>
    </section>
  );
}

export default function App() {
  const navigate = useNavigate();

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
        <h1>Toy Library</h1>

        <p>Biblioteca comunitaria de juguetes</p>

        <nav
          className="main-nav"
          aria-label="Navegación principal"
        >
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              end={link.end}
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
              onClick={handleLogout}
            >
              Cerrar sesión
            </button>
          ) : (
            <NavLink
              to="/login"
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
          <p
            style={{
              marginTop: '16px',
              marginBottom: 0
            }}
          >
            Sesión iniciada como:{' '}
            {role === 'admin'
              ? 'Administrador'
              : 'Promotor'}
          </p>
        )}
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
                <ToyManagementPage />
              </ProtectedRoute>
            }
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
