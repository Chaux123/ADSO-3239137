
import { useState } from 'react';

import { useAuth } from '../context/AuthContext.jsx';
import { createPromoter } from '../services/api.js';

const INITIAL_FORM = {
  username: '',
  password: '',
  confirmPassword: ''
};

export default function PromoterPage({ customerMode = false }) {
  const { isAdmin } = useAuth();

  const [form, setForm] = useState(INITIAL_FORM);
  const [saving, setSaving] = useState(false);

  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const [showPassword, setShowPassword] = useState(false);

  function handleChange(event) {
    const { name, value } = event.target;

    setForm((previous) => ({
      ...previous,
      [name]: value
    }));
  }

  async function handleSubmit(event) {
    event.preventDefault();

    setError('');
    setSuccess('');

    const username = form.username.trim();

    if (!username) {
      setError('Debes ingresar un nombre de usuario.');
      return;
    }

    if (form.password.length < 12) {
      setError(
        'La contraseña debe tener mínimo 12 caracteres.'
      );
      return;
    }

    if (form.password !== form.confirmPassword) {
      setError('Las contraseñas no coinciden.');
      return;
    }

    setSaving(true);

    try {
      const response = await createPromoter({
        username,
        password: form.password,
        role: customerMode ? 'customer' : 'promoter'
      });

      setSuccess(
        `El ${customerMode ? 'cliente' : 'promotor'} "${response.username || username}" fue registrado correctamente.`
      );

      setForm(INITIAL_FORM);
      setShowPassword(false);
    } catch (err) {
      if (err.status === 409) {
        setError(
          'El nombre de usuario ya está registrado.'
        );
      } else if (err.status === 403) {
        setError(
          'No tienes permisos para registrar promotores.'
        );
      } else if (err.status === 422) {
        setError(
          err.message || 'Revisa los datos ingresados.'
        );
      } else {
        setError(
          err.message ||
            'No se pudo registrar el promotor.'
        );
      }
    } finally {
      setSaving(false);
    }
  }

  if (!isAdmin) {
    return (
      <section className="page-content">
        <h2>Acceso denegado</h2>

        <p>
          Solo los administradores pueden registrar
          nuevos promotores.
        </p>
      </section>
    );
  }

  return (
    <section className="page-content page-content--medium">
      <div
        style={{
          maxWidth: '600px',
          margin: '0 auto'
        }}
      >
        <h2>Administración de {customerMode ? 'clientes' : 'promotores'}</h2>

        <p>
          {customerMode ? 'Registra clientes para que puedan consultar el catálogo y la disponibilidad.' : 'Registra promotores para gestionar familias, préstamos y devoluciones de juguetes.'}
        </p>

        {error && (
          <div
            className="error-message"
            role="alert"
          >
            {error}
          </div>
        )}

        {success && (
          <div
            className="success-message"
            role="status"
          >
            {success}
          </div>
        )}

        <div className="card">
          <h3>Registrar nuevo {customerMode ? 'cliente' : 'promotor'}</h3>

          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label htmlFor="promoter-username">
                Nombre de usuario
              </label>

              <input
                id="promoter-username"
                name="username"
                type="text"
                value={form.username}
                onChange={handleChange}
                placeholder="Ejemplo: promotor01"
                maxLength={120}
                autoComplete="off"
                required
                disabled={saving}
              />
            </div>

            <div className="form-group">
              <label htmlFor="promoter-password">
                Contraseña
              </label>

              <input
                id="promoter-password"
                name="password"
                type={showPassword ? 'text' : 'password'}
                value={form.password}
                onChange={handleChange}
                placeholder="Mínimo 12 caracteres"
                minLength={12}
                autoComplete="new-password"
                required
                disabled={saving}
              />
            </div>

            <div className="form-group">
              <label htmlFor="promoter-confirm-password">
                Confirmar contraseña
              </label>

              <input
                id="promoter-confirm-password"
                name="confirmPassword"
                type={showPassword ? 'text' : 'password'}
                value={form.confirmPassword}
                onChange={handleChange}
                placeholder="Repite la contraseña"
                minLength={12}
                autoComplete="new-password"
                required
                disabled={saving}
              />
            </div>

            <div
              style={{
                marginBottom: '20px'
              }}
            >
              <label
                style={{
                  display: 'flex',
                  alignItems: 'center',
                  gap: '8px',
                  cursor: 'pointer'
                }}
              >
                <input
                  type="checkbox"
                  checked={showPassword}
                  onChange={(event) =>
                    setShowPassword(event.target.checked)
                  }
                  disabled={saving}
                />

                Mostrar contraseñas
              </label>
            </div>

            <div
              className="card"
              style={{
                backgroundColor: '#eff6ff',
                marginBottom: '20px'
              }}
            >
              <p style={{ margin: 0 }}>
                <strong>Rol asignado:</strong> {customerMode ? 'Cliente' : 'Promotor'}
              </p>

              <small>
                {customerMode ? 'Este usuario podrá consultar el catálogo y la disponibilidad.' : 'Este usuario podrá gestionar familias y préstamos, pero no tendrá permisos de administrador.'}
              </small>
            </div>

            <button
              type="submit"
              className="btn btn-primary"
              disabled={saving}
              style={{ width: '100%' }}
            >
              {saving
                ? 'Registrando...'
                : `Registrar ${customerMode ? 'cliente' : 'promotor'}`}
            </button>
          </form>
        </div>
      </div>
    </section>
  );
}
