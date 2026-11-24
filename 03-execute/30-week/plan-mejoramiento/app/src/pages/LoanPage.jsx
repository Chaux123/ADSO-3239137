
import { useCallback, useEffect, useState } from 'react';

import {
  createLoan,
  getFamilies,
  getLoans,
  getToys,
  returnLoanItem
} from '../services/api.js';

function formatDate(value) {
  if (!value) return 'Sin fecha';

  const date = new Date(value);

  if (Number.isNaN(date.getTime())) {
    return 'Fecha no disponible';
  }

  return new Intl.DateTimeFormat('es-CO', {
    dateStyle: 'medium',
    timeStyle: 'short',
    timeZone: 'UTC'
  }).format(date);
}

export default function LoanPage() {
  const [families, setFamilies] = useState([]);
  const [toys, setToys] = useState([]);
  const [loans, setLoans] = useState([]);

  const [familyId, setFamilyId] = useState('');
  const [selectedToyIds, setSelectedToyIds] = useState([]);

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [returningId, setReturningId] = useState(null);

  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const [refreshKey, setRefreshKey] = useState(0);

  const refresh = useCallback(() => {
    setRefreshKey((current) => current + 1);
  }, []);

  useEffect(() => {
    let cancelled = false;

    async function loadData() {
      setLoading(true);
      setError('');

      try {
        const [familiesResult, toysResult, loansResult] =
          await Promise.all([
            getFamilies(),
            getToys(),
            getLoans()
          ]);

        if (cancelled) return;

        setFamilies(
          Array.isArray(familiesResult)
            ? familiesResult
            : familiesResult?.data ?? []
        );

        setToys(
          Array.isArray(toysResult)
            ? toysResult
            : toysResult?.data ?? []
        );

        setLoans(
          Array.isArray(loansResult)
            ? loansResult
            : loansResult?.data ?? []
        );
      } catch (err) {
        if (!cancelled) {
          setError(
            err.message ||
              'No se pudo cargar la información.'
          );
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    }

    loadData();

    return () => {
      cancelled = true;
    };
  }, [refreshKey]);

  function toggleToy(toyId) {
    setError('');
    setSuccess('');

    setSelectedToyIds((current) => {
      if (current.includes(toyId)) {
        return current.filter((id) => id !== toyId);
      }

      if (current.length >= 3) {
        setError(
          'Solo puedes seleccionar hasta 3 juguetes.'
        );
        return current;
      }

      return [...current, toyId];
    });
  }

  async function handleCreateLoan(event) {
    event.preventDefault();

    setError('');
    setSuccess('');

    if (!familyId) {
      setError('Debes seleccionar una familia.');
      return;
    }

    if (
      selectedToyIds.length < 1 ||
      selectedToyIds.length > 3
    ) {
      setError(
        'Debes seleccionar entre 1 y 3 juguetes.'
      );
      return;
    }

    setSaving(true);

    try {
      await createLoan(familyId, selectedToyIds);

      setSuccess('Préstamo registrado correctamente.');
      setFamilyId('');
      setSelectedToyIds([]);
      refresh();
    } catch (err) {
      if (err.status === 409) {
        setError(
          err.message ||
            'No se puede registrar el préstamo.'
        );
      } else if (err.status === 422) {
        setError(
          err.message ||
            'La familia supera el límite permitido.'
        );
      } else {
        setError(
          err.message ||
            'Ocurrió un error al registrar el préstamo.'
        );
      }
    } finally {
      setSaving(false);
    }
  }

  async function handleReturn(loanId, itemId) {
    setError('');
    setSuccess('');
    setReturningId(itemId);

    try {
      await returnLoanItem(loanId, itemId);

      setSuccess('Juguete devuelto correctamente.');
      refresh();
    } catch (err) {
      setError(
        err.message ||
          'No se pudo registrar la devolución.'
      );
    } finally {
      setReturningId(null);
    }
  }

  return (
    <section className="page-content">
      <h2>Gestión de préstamos</h2>

      <p>
        Registra préstamos de juguetes, consulta las
        fechas de devolución y gestiona las entregas.
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
        <h3>Registrar nuevo préstamo</h3>

        <form onSubmit={handleCreateLoan}>
          <div className="form-group">
            <label htmlFor="loan-family">
              Seleccionar familia
            </label>

            <select
              id="loan-family"
              value={familyId}
              onChange={(event) =>
                setFamilyId(event.target.value)
              }
              required
              disabled={saving || loading}
            >
              <option value="">Selecciona una familia</option>

              {families.map((family) => (
                <option
                  key={family.id}
                  value={family.id}
                >
                  {family.full_name} - {family.document_number}
                </option>
              ))}
            </select>
          </div>

          <h4>Seleccionar juguetes (máximo 3)</h4>

          {loading ? (
            <p>Cargando juguetes...</p>
          ) : toys.length === 0 ? (
            <p>No hay juguetes disponibles para consultar.</p>
          ) : (
            <div className="card-grid">
              {toys.map((toy) => {
                const available =
                  Number(toy.available_units) > 0;

                const checked =
                  selectedToyIds.includes(toy.id);

                return (
                  <label
                    key={toy.id}
                    className="card"
                    style={{
                      cursor: available
                        ? 'pointer'
                        : 'not-allowed'
                    }}
                  >
                    <input
                      type="checkbox"
                      checked={checked}
                      disabled={!available || saving}
                      onChange={() => toggleToy(toy.id)}
                    />

                    <strong>{toy.name}</strong>

                    <p>Marca: {toy.brand}</p>

                    <p>
                      Disponibles: {toy.available_units}
                    </p>
                  </label>
                );
              })}
            </div>
          )}

          <p>
            Juguetes seleccionados:{' '}
            <strong>{selectedToyIds.length}/3</strong>
          </p>

          <button
            type="submit"
            className="btn btn-primary"
            disabled={saving || loading}
          >
            {saving ? 'Registrando...' : 'Registrar préstamo'}
          </button>
        </form>
      </div>

      <div className="card">
        <h3>Préstamos registrados</h3>

        {loading ? (
          <p>Cargando préstamos...</p>
        ) : loans.length === 0 ? (
          <p>No hay préstamos registrados.</p>
        ) : (
          <div className="card-grid">
            {loans.map((loan) => (
              <article key={loan.id} className="card">
                <h4>Préstamo</h4>

                <p>
                  <strong>ID:</strong> {loan.id}
                </p>

                <p>
                  <strong>Familia:</strong> {loan.family_id}
                </p>

                <p>
                  <strong>Fecha:</strong>{' '}
                  {formatDate(loan.loaned_at)}
                </p>

                <p>
                  <strong>Estado:</strong>{' '}
                  {loan.status || 'Sin información'}
                </p>

                {Array.isArray(loan.items) &&
                  loan.items.map((item) => (
                    <div
                      key={item.id}
                      style={{
                        marginTop: '12px',
                        paddingTop: '12px',
                        borderTop: '1px solid #e2e8f0'
                      }}
                    >
                      <p>
                        <strong>Juguete:</strong>{' '}
                        {item.toy_name || item.name}
                      </p>

                      <p>
                        <strong>Vencimiento:</strong>{' '}
                        {formatDate(item.due_at)}
                      </p>

                      {item.returned_at ? (
                        <p>
                          Devuelto:{' '}
                          {formatDate(item.returned_at)}
                        </p>
                      ) : (
                        <button
                          type="button"
                          className="btn btn-secondary"
                          disabled={returningId === item.id}
                          onClick={() =>
                            handleReturn(loan.id, item.id)
                          }
                        >
                          {returningId === item.id
                            ? 'Procesando...'
                            : 'Registrar devolución'}
                        </button>
                      )}
                    </div>
                  ))}
              </article>
            ))}
          </div>
        )}
      </div>
    </section>
  );
}
