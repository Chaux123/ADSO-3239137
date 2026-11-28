
import { useState } from 'react';
import { Navigate, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext.jsx';

export default function LoginPage() {
  const navigate = useNavigate();

  const {
    signIn,
    isAuthenticated,
    loading,
    error
  } = useAuth();

  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [selectedRole, setSelectedRole] = useState('');

  const [validationError, setValidationError] = useState('');

  if (isAuthenticated) {
    return <Navigate to="/catalogo" replace />;
  }

  async function handleSubmit(event) {
    event.preventDefault();
    setValidationError('');

    if (!selectedRole) {
      setValidationError('Selecciona cómo deseas ingresar.');
      return;
    }

    if (!username.trim() || !password) {
      setValidationError(
        'Debes ingresar tu usuario y contraseña.'
      );
      return;
    }

    try {
      await signIn(username.trim(), password, selectedRole);
      navigate('/catalogo', { replace: true });
    } catch {
      // AuthContext stores the error message.
    }
  }

  return (
    <section className="page-content page-content--narrow">
      <div style={{ maxWidth: '420px', margin: '0 auto' }}>
        <h2>Iniciar sesión</h2>

        <p>
          Ingresa con tu cuenta de Toy Library.
        </p>

        {validationError && (
          <div className="error-message" role="alert">
            {validationError}
          </div>
        )}

        {error && (
          <div className="error-message" role="alert">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label htmlFor="login-role">¿Cómo deseas ingresar?</label>
            <select id="login-role" value={selectedRole} onChange={(event) => { setSelectedRole(event.target.value); setValidationError(''); }} required disabled={loading}>
              <option value="">Selecciona tu tipo de cuenta</option>
              <option value="admin">Administrador</option>
              <option value="customer">Cliente</option>
              <option value="promoter">Promotor</option>
            </select>
          </div>
          <div className="form-group">
            <label htmlFor="username">
              Nombre de usuario
            </label>

            <input
              id="username"
              type="text"
              autoComplete="username"
              value={username}
              onChange={(event) =>
                setUsername(event.target.value)
              }
              placeholder="Ingresa tu usuario"
              maxLength={120}
              required
              disabled={loading}
            />
          </div>

          <div className="form-group">
            <label htmlFor="password">
              Contraseña
            </label>

            <input
              id="password"
              type="password"
              autoComplete="current-password"
              value={password}
              onChange={(event) =>
                setPassword(event.target.value)
              }
              placeholder="Ingresa tu contraseña"
              required
              disabled={loading}
            />
          </div>

          <button
            type="submit"
            className="btn btn-primary"
            disabled={loading}
            style={{ width: '100%' }}
          >
            {loading ? 'Ingresando...' : 'Iniciar sesión'}
          </button>
        </form>
      </div>
    </section>
  );
}
