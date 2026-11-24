
import {
  NavLink,
  Navigate,
  Route,
  Routes,
  useNavigate
} from 'react-router-dom';

import LoginPage from './pages/LoginPage.jsx';
import CatalogPage from './pages/CatalogPage.jsx';
import { useAuth } from './context/AuthContext.jsx';

function TemporaryPage({ title, description }) {
  return (
    <section className="page-content">
      <h2>{title}</h2>
      <p>{description}</p>
    </section>
  );
}

function ProtectedPage({ children, adminOnly = false }) {
  const { isAuthenticated, isAdmin } = useAuth();

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  if (adminOnly && !isAdmin) {
    return (
      <TemporaryPage
        title="Acceso denegado"
        description="No tienes permisos para consultar esta sección."
      />
    );
  }

  return children;
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
    { to: '/catalogo', label: 'Catálogo' },
    ...(isAuthenticated
      ? [
          { to: '/familias', label: 'Familias' },
          { to: '/prestamos', label: 'Préstamos' }
        ]
      : []),
    ...(isAdmin
      ? [{ to: '/reportes', label: 'Reportes' }]
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
          <p style={{ marginTop: '16px', marginBottom: 0 }}>
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
            element={<CatalogPage />}
          />

          <Route
            path="/login"
            element={<LoginPage />}
          />

          <Route
            path="/familias"
            element={
              <ProtectedPage>
                <TemporaryPage
                  title="Gestión de familias"
                  description="Aquí podrás registrar y consultar las familias."
                />
              </ProtectedPage>
            }
          />

          <Route
            path="/prestamos"
            element={
              <ProtectedPage>
                <TemporaryPage
                  title="Gestión de préstamos"
                  description="Aquí podrás registrar préstamos y devoluciones."
                />
              </ProtectedPage>
            }
          />

          <Route
            path="/reportes"
            element={
              <ProtectedPage adminOnly>
                <TemporaryPage
                  title="Reportes"
                  description="Aquí podrás consultar los juguetes más prestados."
                />
              </ProtectedPage>
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




