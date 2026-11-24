
import { useEffect, useState } from 'react';

import {
  getCategories,
  getToys,
  getToyCoverUrl
} from '../services/api.js';

function ToyCard({ toy }) {
  const [imageError, setImageError] = useState(false);

  const availableUnits = Number(toy.available_units ?? 0);
  const totalUnits = Number(toy.total_units ?? 0);

  const isAvailable = availableUnits > 0;

  return (
    <article className="card">
      <div
        style={{
          height: '180px',
          marginBottom: '16px',
          borderRadius: '10px',
          overflow: 'hidden',
          backgroundColor: '#eff6ff',
          display: 'flex',
          justifyContent: 'center',
          alignItems: 'center'
        }}
      >
        {toy.cover_key && !imageError ? (
          <img
            src={getToyCoverUrl(toy.id)}
            alt={`Imagen de ${toy.name}`}
            loading="lazy"
            onError={() => setImageError(true)}
            style={{
              width: '100%',
              height: '100%',
              objectFit: 'cover'
            }}
          />
        ) : (
          <span
            aria-label="Juguete sin imagen"
            role="img"
            style={{ fontSize: '50px' }}
          >
            🧸
          </span>
        )}
      </div>

      <h3 style={{ marginTop: 0 }}>
        {toy.name}
      </h3>

      <p>
        <strong>Marca:</strong> {toy.brand}
      </p>

      <p>
        <strong>Referencia:</strong> {toy.reference}
      </p>

      <p>
        <strong>Categoría:</strong>{' '}
        {toy.category_name || 'Sin categoría'}
      </p>

      <p>
        <strong>Unidades disponibles:</strong>{' '}
        {availableUnits} de {totalUnits}
      </p>

      <span
        style={{
          display: 'inline-block',
          padding: '8px 12px',
          borderRadius: '8px',
          fontWeight: 600,
          backgroundColor: isAvailable
            ? '#dcfce7'
            : '#fee2e2',
          color: isAvailable
            ? '#166534'
            : '#991b1b'
        }}
      >
        {isAvailable ? 'Disponible' : 'No disponible'}
      </span>
    </article>
  );
}

export default function CatalogPage() {
  const [toys, setToys] = useState([]);
  const [categories, setCategories] = useState([]);

  const [searchInput, setSearchInput] = useState('');
  const [search, setSearch] = useState('');
  const [categoryId, setCategoryId] = useState('');

  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const [page, setPage] = useState(1);
  const [hasNextPage, setHasNextPage] = useState(false);

  useEffect(() => {
    let cancelled = false;

    async function loadCategories() {
      try {
        const result = await getCategories();

        if (!cancelled) {
          setCategories(
            Array.isArray(result)
              ? result
              : result?.data ?? []
          );
        }
      } catch (err) {
        if (!cancelled) {
          setError(
            err.message ||
              'No se pudieron cargar las categorías.'
          );
        }
      }
    }

    loadCategories();

    return () => {
      cancelled = true;
    };
  }, []);

  useEffect(() => {
    let cancelled = false;

    async function loadToys() {
      setLoading(true);
      setError('');

      try {
        const params = {
          page
        };

        if (search.trim()) {
          params.q = search.trim();
        }

        if (categoryId) {
          params.category_id = categoryId;
        }

        const result = await getToys(params);

        if (cancelled) {
          return;
        }

        const items = Array.isArray(result)
          ? result
          : result?.data ?? [];

        setToys(items);

        const currentPage = Number(
          result?.meta?.current_page ??
            result?.current_page ??
            page
        );

        const lastPage = Number(
          result?.meta?.last_page ??
            result?.last_page ??
            currentPage
        );

        setHasNextPage(currentPage < lastPage);
      } catch (err) {
        if (!cancelled) {
          setToys([]);
          setHasNextPage(false);

          setError(
            err.message ||
              'No se pudo cargar el catálogo de juguetes.'
          );
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    }

    loadToys();

    return () => {
      cancelled = true;
    };
  }, [search, categoryId, page]);

  function handleSearch(event) {
    event.preventDefault();

    setPage(1);
    setSearch(searchInput);
  }

  function handleCategoryChange(event) {
    setCategoryId(event.target.value);
    setPage(1);
  }

  function clearFilters() {
    setSearchInput('');
    setSearch('');
    setCategoryId('');
    setPage(1);
  }

  return (
    <section className="page-content">
      <h2>Catálogo de juguetes</h2>

      <p>
        Explora los juguetes disponibles en nuestra
        biblioteca comunitaria.
      </p>

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
            flex: '1 1 220px',
            marginBottom: 0
          }}
        >
          <label htmlFor="toy-search">
            Buscar juguete
          </label>

          <input
            id="toy-search"
            type="search"
            value={searchInput}
            maxLength={250}
            onChange={(event) =>
              setSearchInput(event.target.value)
            }
            placeholder="Nombre o marca"
          />
        </div>

        <div
          className="form-group"
          style={{
            flex: '1 1 180px',
            marginBottom: 0
          }}
        >
          <label htmlFor="toy-category">
            Categoría
          </label>

          <select
            id="toy-category"
            value={categoryId}
            onChange={handleCategoryChange}
          >
            <option value="">
              Todas las categorías
            </option>

            {categories.map((category) => (
              <option
                key={category.id}
                value={category.id}
              >
                {category.name}
              </option>
            ))}
          </select>
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
            onClick={clearFilters}
          >
            Limpiar
          </button>
        </div>
      </form>

      {error && (
        <div className="error-message" role="alert">
          {error}
        </div>
      )}

      {loading ? (
        <p role="status">
          Cargando juguetes...
        </p>
      ) : toys.length === 0 ? (
        <p>
          No se encontraron juguetes con los filtros
          seleccionados.
        </p>
      ) : (
        <>
          <div className="card-grid">
            {toys.map((toy) => (
              <ToyCard
                key={toy.id}
                toy={toy}
              />
            ))}
          </div>

          <div
            style={{
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              gap: '16px',
              marginTop: '30px'
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
    </section>
  );
}
