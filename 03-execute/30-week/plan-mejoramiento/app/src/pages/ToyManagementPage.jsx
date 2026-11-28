
import { useEffect, useRef, useState } from 'react';
import ToyCover from '../components/ToyCover.jsx';

import { useAuth } from '../context/AuthContext.jsx';

import {
  createToy,
  deleteToy,
  deleteToyCover,
  getCategories,
  getToys,
  updateToy,
  uploadToyCover
} from '../services/api.js';

const INITIAL_FORM = {
  name: '',
  brand: '',
  reference: '',
  category_id: '',
  total_units: 1
};

export default function ToyManagementPage({ editMode = false }) {
  const { isAdmin } = useAuth();

  const [toys, setToys] = useState([]);
  const [categories, setCategories] = useState([]);

  const [form, setForm] = useState(INITIAL_FORM);
  const [editingId, setEditingId] = useState(null);
  const modalRef = useRef(null);

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const savingRef = useRef(false);
  savingRef.current = saving;
  const [processingId, setProcessingId] = useState(null);

  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const [page, setPage] = useState(1);
  const [hasNextPage, setHasNextPage] = useState(false);
  const [refreshKey, setRefreshKey] = useState(0);
  const [searchInput, setSearchInput] = useState('');
  const [search, setSearch] = useState('');

  useEffect(() => {
    if (!editingId || !editMode) return;
    const previousFocus = document.activeElement;
    modalRef.current?.focus();
    function handleKeyDown(event) {
      if (event.key === 'Escape' && !savingRef.current) resetForm();
      if (event.key !== 'Tab') return;
      const controls = modalRef.current?.querySelectorAll('button:not(:disabled), input:not(:disabled), select:not(:disabled), [href]');
      if (!controls?.length) return;
      const first = controls[0];
      const last = controls[controls.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
    document.addEventListener('keydown', handleKeyDown);
    return () => { document.removeEventListener('keydown', handleKeyDown); previousFocus?.focus(); };
  }, [editingId, editMode]);

  function refreshToys() {
    setRefreshKey((current) => current + 1);
  }

  useEffect(() => {
    if (!isAdmin) return;

    let cancelled = false;

    async function loadCategories() {
      try {
        const response = await getCategories();

        if (!cancelled) {
          setCategories(
            Array.isArray(response)
              ? response
              : response?.data ?? []
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
  }, [isAdmin]);

  useEffect(() => {
    if (!isAdmin) return;

    let cancelled = false;

    async function loadToys() {
      setLoading(true);

      try {
        const response = await getToys({ page, ...(search ? { q: search } : {}) });

        if (cancelled) return;

        setToys(
          Array.isArray(response)
            ? response
            : response?.data ?? []
        );

        const currentPage = Number(
          response?.pagination?.page ??
          response?.meta?.current_page ??
            response?.current_page ??
            page
        );

        const lastPage = Number(
          response?.pagination?.total_pages ??
          response?.meta?.last_page ??
            response?.last_page ??
            currentPage
        );

        setHasNextPage(currentPage < lastPage);
      } catch (err) {
        if (!cancelled) {
          setToys([]);
          setError(
            err.message ||
              'No se pudieron consultar los juguetes.'
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
  }, [isAdmin, page, refreshKey, search]);

  function handleChange(event) {
    const { name, value } = event.target;

    setForm((previous) => ({
      ...previous,
      [name]: value
    }));
  }

  function resetForm() {
    setForm(INITIAL_FORM);
    setEditingId(null);
  }

  function startEditing(toy) {
    setEditingId(toy.id);

    setForm({
      name: toy.name ?? '',
      brand: toy.brand ?? '',
      reference: toy.reference ?? '',
      category_id: toy.category_id ?? '',
      total_units: toy.total_units ?? 1
    });

    setError('');
    setSuccess('');

  }

  async function handleSubmit(event) {
    event.preventDefault();

    setError('');
    setSuccess('');

    const totalUnits = Number(form.total_units);

    if (
      !form.name.trim() ||
      !form.brand.trim() ||
      !form.category_id ||
      !Number.isInteger(totalUnits) ||
      totalUnits < 1 ||
      (!editingId && !form.reference.trim())
    ) {
      setError('Revisa los campos del formulario.');
      return;
    }

    const data = {
      name: form.name.trim(),
      brand: form.brand.trim(),
      category_id: form.category_id,
      total_units: totalUnits
    };

    if (!editingId) {
      data.reference = form.reference.trim();
    }

    setSaving(true);

    try {
      if (editingId) {
        await updateToy(editingId, data);

        setSuccess('Juguete actualizado correctamente.');
      } else {
        await createToy(data);

        setSuccess('Juguete registrado correctamente.');
      }

      resetForm();
      refreshToys();
    } catch (err) {
      setError(
        err.message ||
          'No se pudo guardar el juguete.'
      );
    } finally {
      setSaving(false);
    }
  }

  async function handleDelete(toy) {
    const confirmed = window.confirm(
      `¿Deseas retirar "${toy.name}" del catálogo?`
    );

    if (!confirmed) return;

    setProcessingId(toy.id);
    setError('');
    setSuccess('');

    try {
      await deleteToy(toy.id);

      setSuccess('Juguete retirado correctamente.');

      if (editingId === toy.id) {
        resetForm();
      }

      refreshToys();
    } catch (err) {
      setError(
        err.message ||
          'No se pudo retirar el juguete.'
      );
    } finally {
      setProcessingId(null);
    }
  }

  async function handleUploadCover(toyId, file) {
    if (!file) return;

    setError('');
    setSuccess('');

    if (
      !['image/jpeg', 'image/png'].includes(file.type)
    ) {
      setError('Solo se permiten imágenes JPG o PNG.');
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      setError('La imagen no puede superar los 2 MB.');
      return;
    }

    setProcessingId(toyId);

    try {
      await uploadToyCover(toyId, file);

      setSuccess('Fotografía actualizada correctamente.');
      refreshToys();
    } catch (err) {
      setError(
        err.message ||
          'No se pudo subir la fotografía.'
      );
    } finally {
      setProcessingId(null);
    }
  }

  async function handleDeleteCover(toyId) {
    const confirmed = window.confirm(
      '¿Deseas eliminar la fotografía de este juguete?'
    );

    if (!confirmed) return;

    setProcessingId(toyId);
    setError('');
    setSuccess('');

    try {
      await deleteToyCover(toyId);

      setSuccess('Fotografía eliminada correctamente.');
      refreshToys();
    } catch (err) {
      setError(
        err.message ||
          'No se pudo eliminar la fotografía.'
      );
    } finally {
      setProcessingId(null);
    }
  }

  if (!isAdmin) {
    return (
      <section className="page-content">
        <h2>Acceso denegado</h2>
        <p>
          Solo los administradores pueden gestionar
          el inventario de juguetes.
        </p>
      </section>
    );
  }

  const selectedToy = toys.find((toy) => toy.id === editingId);

  return (
    <section className="page-content">
      <h2>{editMode ? 'Editar juguetes' : 'Administración de juguetes'}</h2>

      <p>
        {editMode ? 'Busca un juguete para editarlo, retirarlo o administrar su fotografía.' : 'Registra juguetes y consulta el inventario disponible.'}
      </p>

      {error && !editingId && (
        <div className="error-message" role="alert">
          {error}
        </div>
      )}

      {success && (
        <div className="success-message" role="status">
          {success}
        </div>
      )}

      {(!editMode || editingId) && <div className={editMode ? 'toy-modal-backdrop' : undefined}>
      <div
        ref={editMode ? modalRef : undefined}
        role={editMode ? 'dialog' : undefined}
        aria-modal={editMode ? 'true' : undefined}
        aria-labelledby={editMode ? 'toy-modal-title' : undefined}
        tabIndex={editMode ? -1 : undefined}
        className={editMode ? 'card toy-modal' : 'card toy-registration'}
      >
        <h3 id={editMode ? 'toy-modal-title' : undefined}>
          {editingId
            ? 'Editar juguete'
            : 'Registrar nuevo juguete'}
        </h3>
        {editMode && error && <div className="error-message" role="alert">{error}</div>}
        {editMode && success && <div className="success-message" role="status">{success}</div>}

        <form onSubmit={handleSubmit} className="toy-form">
          <div className="form-group">
            <label htmlFor="toy-name">
              Nombre del juguete
            </label>

            <input
              id="toy-name"
              name="name"
              type="text"
              value={form.name}
              onChange={handleChange}
              maxLength={250}
              required
              disabled={saving}
            />
          </div>

          <div className="form-group">
            <label htmlFor="toy-brand">
              Marca
            </label>

            <input
              id="toy-brand"
              name="brand"
              type="text"
              value={form.brand}
              onChange={handleChange}
              maxLength={200}
              required
              disabled={saving}
            />
          </div>

          <div className="form-group">
            <label htmlFor="toy-reference">
              Referencia
            </label>

            <input
              id="toy-reference"
              name="reference"
              type="text"
              value={form.reference}
              onChange={handleChange}
              maxLength={50}
              required={!editingId}
              disabled={saving || Boolean(editingId)}
            />

            {editingId && (
              <small>
                La referencia no se modifica después
                del registro.
              </small>
            )}
          </div>

          <div className="form-group">
            <label htmlFor="toy-category">
              Categoría
            </label>

            <select
              id="toy-category"
              name="category_id"
              value={form.category_id}
              onChange={handleChange}
              required
              disabled={saving}
            >
              <option value="">
                Selecciona una categoría
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

          <div className="form-group">
            <label htmlFor="toy-total">
              Cantidad total
            </label>

            <input
              id="toy-total"
              name="total_units"
              type="number"
              min="1"
              step="1"
              value={form.total_units}
              onChange={handleChange}
              required
              disabled={saving}
            />
          </div>

          <div
            className="toy-form-actions"
            style={{
              display: 'flex',
              gap: '12px',
              flexWrap: 'wrap'
            }}
          >
            <button
              type="submit"
              className="btn btn-primary"
              disabled={saving}
            >
              {saving
                ? 'Guardando...'
                : editingId
                  ? 'Guardar cambios'
                  : 'Registrar juguete'}
            </button>

            {editingId && (
              <button
                type="button"
                className="btn btn-secondary"
                onClick={resetForm}
                disabled={saving}
              >
                Cancelar edición
              </button>
            )}
          </div>
        </form>
        {editMode && selectedToy && <div className="toy-modal-photo">
          <h4>Fotografía del juguete</h4>
          <div className="form-group">
            <label htmlFor="modal-toy-cover">Subir fotografía</label>
            <input id="modal-toy-cover" type="file" accept=".jpg,.jpeg,.png" disabled={processingId === selectedToy.id} onChange={(event) => { const file = event.target.files?.[0]; handleUploadCover(selectedToy.id, file); event.target.value = ''; }} />
          </div>
          {selectedToy.cover_key && <button type="button" className="btn btn-secondary" onClick={() => handleDeleteCover(selectedToy.id)} disabled={processingId === selectedToy.id}>Eliminar fotografía</button>}
        </div>}
      </div></div>}

      <div className="card">
        <h3>Inventario de juguetes</h3>

        {editMode && <form className="toy-search" onSubmit={(event) => { event.preventDefault(); setPage(1); setSearch(searchInput.trim()); }}>
          <div className="form-group"><label htmlFor="edit-toy-search">Buscar por nombre o marca</label><input id="edit-toy-search" type="search" value={searchInput} onChange={(event) => setSearchInput(event.target.value)} placeholder="Buscar juguete" maxLength={250} /></div>
          <button type="submit" className="btn btn-primary">Buscar</button>
        </form>}

        {loading ? (
          <p role="status">Cargando inventario...</p>
        ) : toys.length === 0 ? (
          <p>No se encontraron juguetes registrados.</p>
        ) : (
          <>
            <div className="card-grid">
              {toys.map((toy) => (
                <article
                  className="card toy-inventory-card"
                  key={toy.id}
                >
                  <div className="toy-card-image">
                    <span aria-hidden="true">🧸</span>
                    {toy.cover_key && (
                      <ToyCover
                        toy={toy}
                        alt={`Imagen de ${toy.name}`}
                        loading="lazy"
                      />
                    )}
                  </div>

                  <h4>{toy.name}</h4>

                  <p>
                    <strong>Marca:</strong> {toy.brand}
                  </p>

                  <p>
                    <strong>Referencia:</strong>{' '}
                    {toy.reference}
                  </p>

                  <p>
                    <strong>Disponibles:</strong>{' '}
                    {toy.available_units} de {toy.total_units}
                  </p>

                  {editMode && <div className="toy-card-actions"><button
                    type="button"
                    className="btn btn-secondary toy-card-action"
                    onClick={() => startEditing(toy)}
                    disabled={processingId === toy.id}
                  >
                    Editar
                  </button>

                  <button
                    type="button"
                    className="btn btn-secondary toy-card-action"
                    onClick={() => handleDelete(toy)}
                    disabled={processingId === toy.id}
                  >
                    Retirar
                  </button></div>}

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
    </section>
  );
}
