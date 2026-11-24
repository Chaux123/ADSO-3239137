
import { NavLink, Navigate, Route, Routes } from 'react-router-dom';

function TemporaryPage({ title, description }) {
  return (
    <section className="page-content">
      <h2>{title}</h2>
      <p>{description}</p>
    </section>
  );
}

function App() {
  const links = [
    { to: '/', label: 'Inicio', end: true },
    { to: '/catalogo', label: 'Catálogo' },
    { to: '/familias', label: 'Familias' },
    { to: '/prestamos', label: 'Préstamos' },
    { to: '/reportes', label: 'Reportes' },
    { to: '/login', label: 'Iniciar sesión' }
  ];

  return (
    <div className="app-container">
      <header className="app-header">
        <h1>Toy Library</h1>
        <p>Biblioteca comunitaria de juguetes</p>

        <nav className="main-nav" aria-label="Navegación principal">
          {links.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              end={link.end}
              className={({ isActive }) =>
                isActive ? 'nav-link active' : 'nav-link'
              }
            >
              {link.label}
            </NavLink>
          ))}
        </nav>
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
            element={
              <TemporaryPage
                title="Catálogo de juguetes"
                description="Aquí podrás consultar los juguetes disponibles."
              />
            }
          />

          <Route
            path="/familias"
            element={
              <TemporaryPage
                title="Gestión de familias"
                description="Aquí se registrarán y consultarán las familias."
              />
            }
          />

          <Route
            path="/prestamos"
            element={
              <TemporaryPage
                title="Gestión de préstamos"
                description="Aquí podrás registrar préstamos y devoluciones."
              />
            }
          />

          <Route
            path="/reportes"
            element={
              <TemporaryPage
                title="Reportes"
                description="Aquí podrás consultar los juguetes más prestados."
              />
            }
          />

          <Route
            path="/login"
            element={
              <TemporaryPage
                title="Iniciar sesión"
                description="Aquí los administradores y promotores podrán ingresar al sistema."
              />
            }
          />

          <Route
            path="*"
            element={<Navigate to="/" replace />}
          />
        </Routes>
      </main>

      <footer className="app-footer">
        <p>Toy Library - Biblioteca comunitaria de juguetes</p>
      </footer>
    </div>
  );
}

export default App;
