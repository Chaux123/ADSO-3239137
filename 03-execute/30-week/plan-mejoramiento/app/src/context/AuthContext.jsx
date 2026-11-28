
import {
  createContext,
  useContext,
  useMemo,
  useState
} from 'react';

import {
  login as apiLogin,
  logout as apiLogout,
  getToken
} from '../services/api.js';

const AuthContext = createContext(null);

const ROLE_KEY = 'toy_library_role';

export function AuthProvider({ children }) {
  const [token, setToken] = useState(() => getToken());

  const [role, setRole] = useState(() => {
    if (!getToken()) {
      return null;
    }

    return sessionStorage.getItem(ROLE_KEY);
  });

  const [loading, setLoading] = useState(false);

  const [error, setError] = useState(null);

  const isAuthenticated = Boolean(token);

  const isAdmin = isAuthenticated && role === 'admin';

  const isPromoter =
    isAuthenticated && role === 'promoter';

  async function signIn(username, password, expectedRole) {
    setLoading(true);
    setError(null);

    try {
      const response = await apiLogin(
        username,
        password
      );

      const newToken = response.access_token;
      const newRole = response.role;

      if (expectedRole && newRole !== expectedRole) {
        apiLogout();
        throw new Error('La cuenta no corresponde al tipo de acceso seleccionado.');
      }

      sessionStorage.setItem(ROLE_KEY, newRole);

      setToken(newToken);
      setRole(newRole);

      return response;
    } catch (err) {
      setError(
        err.message || 'No se pudo iniciar sesión.'
      );

      throw err;
    } finally {
      setLoading(false);
    }
  }

  function signOut() {
    apiLogout();

    sessionStorage.removeItem(ROLE_KEY);

    setToken(null);
    setRole(null);
    setError(null);
  }

  const value = useMemo(
    () => ({
      token,
      role,
      loading,
      error,
      isAuthenticated,
      isAdmin,
      isPromoter,
      signIn,
      signOut
    }),
    [
      token,
      role,
      loading,
      error,
      isAuthenticated,
      isAdmin,
      isPromoter
    ]
  );

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error(
      'useAuth debe utilizarse dentro de AuthProvider.'
    );
  }

  return context;
}
