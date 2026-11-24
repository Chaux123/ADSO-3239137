
import { useCallback, useEffect, useState } from 'react';

import {
  createFamily,
  getFamilies,
  getFamily
} from '../services/api.js';

const INITIAL_FORM = {
  full_name: '',
  document_number: ''
};

export default function FamilyPage() {
  const [families, setFamilies] = useState([]);
  const [form, setForm] = useState(INITIAL_FORM);

  const [searchInput, setSearchInput] = useState('');
  const [search, setSearch] = useState('');

  const [selectedFamily, setSelectedFamily] = useState(null);

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [loadingDetails, setLoadingDetails] = useState(false);

  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const [page, setPage] = useState(1);
  const [hasNextPage, setHasNextPage] = useState(false);
  const [refreshKey, setRefreshKey] = useState(0);

  const refreshFamilies = useCallback(() => {
    setRefreshKey((current) => current + 1);
  }, []);

  useEffect(() => {
    let cancelled = false;

    async function loadFamilies() {
      setLoading(true);
      setError('');

      try {
        const params = { page };

        if (search.trim()) {
          params.q = search.trim();
        }

        const response = await getFamilies(params);

        if (cancelled) return;

        const items = Array.isArray(response)
          ? response
          : response?.data ?? [];

        setFamilies(items);

        const currentPage = Number(
          response?.meta?.current_page ??
          response?.current_page ??
          page
        );

        const lastPage = Number(
          response?.meta?.last_page ??
          response?.last_page ??
          currentPage
        );

        setHasNextPage(currentPage < lastPage);
      } catch (err) {
        if (!cancelled) {
          setFamilies([]);
          setHasNextPage(false);
          setError(
            err.message || 'No se pudieron cargar las familias.'
          );
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    }

    loadFamilies();

    return () => {
      cancelled = true;
    };
  }, [search, page, refreshKey]);

  function handleInputChange(event) {
    const { name, value } = event.target;

    setForm((previous) => ({
      ...previous,
      [name]: value
    }));
  }

  function handleSearch(event) {
    event.preventDefault();
    setPage(1);
    setSearch(searchInput.trim());
  }

  function clearSearch() {
    setSearchInput('');
    setSearch('');
    setPage(1);
  }

  async function handleCreateFamily(event) {
    event.preventDefault();
    setError('');
    setSuccess('');

    const fullName = form.full_name.trim();
    const documentNumber = form.document_number.trim();

    if (!fullName || !documentNumber) {
      setError('Debes completar todos los campos.');
      return;
    }

    setSaving(true);

    try {
      await createFamily({
        full_name: fullName,
        document_number: documentNumber
      });

      setSuccess('Familia registrada correctamente.');
      setForm(INITIAL_FORM);

      setPage(1);
      setSearch('');
      setSearchInput('');
      refreshFamilies();
    } catch (err) {
      if (err.status === 409) {
        setError('Ya existe una familia con ese documento.');
      } else {
        setError(
          err.message || 'No se pudo registrar la familia.'
        );
      }
    } finally {
      setSaving(false);
    }
  }

  async function handleViewFamily(id) {
    setError('');
    setLoadingDetails(true);
    setSelectedFamily(null);

    try {
      const response = await getFamily(id);
      setSelectedFamily(response?.data ?? response);
    } catch (err) {
      setError(
        err.message || 'No se pudo consultar la familia.'
      );
    } finally {
      setLoadingDetails(false);
    }
  }

  return (
    <section className="page-content">
      <h2>Gestión de familias</h2>

      <p>
        Registra familias y consulta su información
        en la biblioteca comunitaria.
      </p>

      {error && (
        <div className="error-message" role="alert">
          {error}
        </div>
      )}

      {success && (
        <div className="success-message" role="status">
          {success}
        </div>
      )}

      <div className="card" style={{ marginBottom: '28px' }}>
        <h3>Registrar nueva familia</h3>

        <form onSubmit={handleCreateFamily}>
          <div className="form-group">
            <label htmlFor="family-name">
              Nombre de la familia o responsable
            </label>

            <input
              id="family-name"
              name="full_name"
              type="text"
              value={form.full_name}
              onChange={handleInputChange}
              maxLength={200}
              placeholder="Nombre completo"
              required
              disabled={saving}
            />
          </div>

          <div className="form-group">
            <label htmlFor="family-document">
              Número de documento
            </label>

            <input
              id="family-document"
              name="document_number"
              type="text"
              value={form.document_number}
              onChange={handleInputChange}
              maxLength={30}
              placeholder="Documento de identificación"
              required
              disabled={saving}
            />
          </div>

          <button
            type="submit"
            className="btn btn-primary"
            disabled={saving}
          >
            {saving ? 'Guardando...' : 'Registrar familia'}
          </button>
        </form>
      </div>

      <div className="card">
        <h3>Consultar familias</h3>

        <form
          onSubmit={handleSearch}
          style={{
            display: 'flex',
            gap: '12px',
            flexWrap: 'wrap',
            marginBottom: '20px'
          }}
        >
          <div
            className="form-group"
            style={{
              flex: '1 1 240px',
              marginBottom: 0
            }}
          >
            <label htmlFor="family-search">
              Buscar familia
            </label>

            <input
              id="family-search"
              type="search"
              value={searchInput}
              maxLength={200}
              placeholder="Nombre o documento"
              onChange={(event) =>
                setSearchInput(event.target.value)
              }
            />
          </div>

          <div
            style={{
              display: 'flex',
              gap: '8px',
              alignItems: 'flex-end'
            }}
          >
            <button
              type="submit"
              className="btn btn-primary"
            >
              Buscar
            </button>

            <button
              type="button"
              className="btn btn-secondary"
              onClick={clearSearch}
            >
              Limpiar
            </button>
          </div>
        </form>

        {loading ? (
          <p role="status">Cargando familias...</p>
        ) : families.length === 0 ? (
          <p>No se encontraron familias registradas.</p>
        ) : (
          <>
            <div className="card-grid">
              {families.map((family) => (
                <article className="card" key={family.id}>
                  <h4>{family.full_name}</h4>

                  <p>
                    <strong>Documento:</strong>{' '}
                    {family.document_number}
                  </p>

                  <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={() =>
                      handleViewFamily(family.id)
                    }
                    disabled={loadingDetails}
                  >
                    Ver detalles
                  </button>
                </article>
              ))}
            </div>

            <div
              style={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                gap: '16px',
                marginTop: '24px'
              }}
            >
              <button
                type="button"
                className="btn btn-secondary"
                disabled={page === 1}
                onClick={() =>
                  setPage((current) =>
                    Math.max(1, current - 1)
                  )
                }
              >
                Anterior
              </button>

              <span>Página {page}</span>

              <button
                type="button"
                className="btn btn-primary"
                disabled={!hasNextPage}
                onClick={() =>
                  setPage((current) => current + 1)
                }
              >
                Siguiente
              </button>
            </div>
          </>
        )}
      </div>

      {selectedFamily && (
        <div
          className="card"
          style={{ marginTop: '24px' }}
        >
          <h3>Detalles de la familia</h3>

          <p>
            <strong>Nombre:</strong>{' '}
            {selectedFamily.full_name}
          </p>

          <p>
            <strong>Documento:</strong>{' '}
            {selectedFamily.document_number}
          </p>

          <p>
            <strong>Préstamos activos:</strong>{' '}
            {selectedFamily.active_loans ?? 0}
          </p>

          <button
            type="button"
            className="btn btn-secondary"
            onClick={() => setSelectedFamily(null)}
          >
            Cerrar detalles
          </button>
        </div>
      )}
    </section>
  );
}
