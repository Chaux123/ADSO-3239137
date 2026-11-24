
import { Navigate, useLocation } from 'react-router-dom';

import { useAuth } from '../context/AuthContext.jsx';

export default function ProtectedRoute({
  children,
  adminOnly = false
}) {
  const location = useLocation();

  const {
    isAuthenticated,
    isAdmin
  } = useAuth();

  // Redirect users who have not signed in.
  if (!isAuthenticated) {
    return (
      <Navigate
        to="/login"
        state={{ from: location }}
        replace
      />
    );
  }

  // Restrict administrator-only pages.
  if (adminOnly && !isAdmin) {
    return (
      <section className="page-content">
        <h2>Acceso denegado</h2>

        <div className="error-message" role="alert">
          No tienes permisos para acceder a esta sección.
        </div>

        <p>
          Esta función está disponible únicamente
          para los administradores de Toy Library.
        </p>
      </section>
    );
  }

  // Allow access to the requested page.
  return children;
}
