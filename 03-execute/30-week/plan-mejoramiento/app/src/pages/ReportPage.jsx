
import { useEffect, useState } from 'react';

import { getMostLoanedReport } from '../services/api.js';
import { useAuth } from '../context/AuthContext.jsx';

export default function ReportPage() {
  const { isAdmin } = useAuth();

  const [report, setReport] = useState([]);
  const [limit, setLimit] = useState(10);
  const [from, setFrom] = useState('2000-01-01');
  const [to, setTo] = useState(() => new Date().toISOString().slice(0, 10));

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [refreshKey, setRefreshKey] = useState(0);

  useEffect(() => {
    if (!isAdmin) {
      setReport([]);
      return;
    }

    let cancelled = false;

    async function loadReport() {
      setLoading(true);
      setError('');

      try {
        const response = await getMostLoanedReport(limit, from, to);

        if (cancelled) return;

        const items = Array.isArray(response)
          ? response
          : response?.data ?? [];

        setReport(items);
      } catch (err) {
        if (!cancelled) {
          setReport([]);
          setError(
            err.message ||
              'No se pudo cargar el reporte.'
          );
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    }

    loadReport();

    return () => {
      cancelled = true;
    };
  }, [isAdmin, limit, from, to, refreshKey]);

  if (!isAdmin) {
    return (
      <section className="page-content">
        <h2>Acceso denegado</h2>

        <p>
          Solo los administradores pueden consultar
          los reportes de Toy Library.
        </p>
      </section>
    );
  }

  function refreshReport() {
    setRefreshKey((current) => current + 1);
  }

  const maxLoans = Math.max(
    1,
    ...report.map((item) =>
      Number(item.total_loans) || 0
    )
  );

  const totalLoans = report.reduce(
    (total, item) =>
      total + (Number(item.total_loans) || 0),
    0
  );

  return (
    <section className="page-content">
      <h2>Reporte de juguetes más prestados</h2>

      <p>
        Consulta los juguetes con mayor cantidad de
        préstamos registrados en Toy Library.
      </p>

      <div
        className="card"
        style={{ marginBottom: '24px' }}
      >
        <div className="report-filters">
          <div className="form-group">
            <label htmlFor="report-limit">
              Cantidad de resultados
            </label>

            <select
              id="report-limit"
              value={limit}
              onChange={(event) =>
                setLimit(Number(event.target.value))
              }
              disabled={loading}
            >
              <option value={5}>5 juguetes</option>
              <option value={10}>10 juguetes</option>
              <option value={20}>20 juguetes</option>
              <option value={50}>50 juguetes</option>
            </select>
          </div>

          <div className="form-group"><label htmlFor="report-from">Desde</label><input id="report-from" type="date" value={from} max={to} onChange={(event) => setFrom(event.target.value)} /></div>
          <div className="form-group"><label htmlFor="report-to">Hasta</label><input id="report-to" type="date" value={to} min={from} onChange={(event) => setTo(event.target.value)} /></div>
          <button
            type="button"
            className="btn btn-primary"
            onClick={refreshReport}
            disabled={loading}
          >
            {loading ? 'Actualizando...' : 'Actualizar reporte'}
          </button>
        </div>
      </div>

      {error && (
        <div className="error-message" role="alert">
          {error}
        </div>
      )}

      {loading ? (
        <p role="status">Cargando reporte...</p>
      ) : report.length === 0 ? (
        <p>
          No hay préstamos registrados para generar
          el reporte.
        </p>
      ) : (
        <>
          <div
            className="card-grid"
            style={{ marginBottom: '24px' }}
          >
            <div className="card">
              <h3>Juguetes en el reporte</h3>

              <p style={{ fontSize: '2rem', fontWeight: 700 }}>
                {report.length}
              </p>
            </div>

            <div className="card">
              <h3>Préstamos contabilizados</h3>

              <p style={{ fontSize: '2rem', fontWeight: 700 }}>
                {totalLoans}
              </p>

              <small>
                Total correspondiente a los juguetes
                incluidos en este reporte.
              </small>
            </div>
          </div>

          <div className="card">
            <h3>Clasificación de juguetes</h3>

            {report.map((item, index) => {
              const count = Number(item.total_loans) || 0;

              const percentage = Math.max(
                0,
                Math.min(100, (count / maxLoans) * 100)
              );

              return (
                <div
                  key={item.toy_id}
                  style={{
                    marginBottom: '22px',
                    paddingBottom: '14px',
                    borderBottom: '1px solid #e2e8f0'
                  }}
                >
                  <div
                    style={{
                      display: 'flex',
                      justifyContent: 'space-between',
                      gap: '12px',
                      flexWrap: 'wrap',
                      marginBottom: '8px'
                    }}
                  >
                    <strong>
                      {index + 1}. {item.name}
                    </strong>

                    <span>
                      {count} {count === 1
                        ? 'préstamo'
                        : 'préstamos'}
                    </span>
                  </div>

                  <div
                    style={{
                      width: '100%',
                      height: '14px',
                      backgroundColor: '#e2e8f0',
                      borderRadius: '8px',
                      overflow: 'hidden'
                    }}
                    role="progressbar"
                    aria-label={`Préstamos de ${item.name}`}
                    aria-valuenow={count}
                    aria-valuemin={0}
                    aria-valuemax={maxLoans}
                  >
                    <div
                      style={{
                        width: `${percentage}%`,
                        height: '100%',
                        backgroundColor: '#2563eb',
                        borderRadius: '8px'
                      }}
                    />
                  </div>

                  <p style={{ marginBottom: 0 }}>
                    <strong>Marca:</strong>{' '}
                    {item.brand || 'Sin marca'}
                    {' | '}
                    <strong>Referencia:</strong>{' '}
                    {item.reference || 'Sin referencia'}
                  </p>
                </div>
              );
            })}
          </div>
        </>
      )}
    </section>
  );
}
