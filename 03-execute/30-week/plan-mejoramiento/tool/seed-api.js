
/**
 * Toy Library - API Seeding Tool
 *
 * Creates sample toys and families through the Laravel API.
 * Intended for development and testing only.
 *
 * Run:
 * npm run seed
 */

const API_BASE_URL = (
  process.env.API_BASE_URL ||
  'http://localhost:8000/api/v1'
).replace(/\/+$/, '');

const ADMIN_USERNAME = process.env.ADMIN_USERNAME;
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD;
const SEED_ENV = process.env.SEED_ENV;

const SAMPLE_FAMILIES = [
  {
    full_name: 'Familia Prueba Uno',
    document_number: 'TEST-FAMILY-001'
  },
  {
    full_name: 'Familia Prueba Dos',
    document_number: 'TEST-FAMILY-002'
  },
  {
    full_name: 'Familia Prueba Tres',
    document_number: 'TEST-FAMILY-003'
  }
];

const SAMPLE_TOYS = [
  {
    name: 'Sonajero de colores',
    brand: 'Toy Library',
    reference: 'TEST-BEBE-001',
    category: 'bebes',
    total_units: 3
  },
  {
    name: 'Bloques de construcción',
    brand: 'Toy Library',
    reference: 'TEST-CONST-001',
    category: 'construccion',
    total_units: 5
  },
  {
    name: 'Rompecabezas educativo',
    brand: 'Toy Library',
    reference: 'TEST-DIDAC-001',
    category: 'didacticos',
    total_units: 4
  },
  {
    name: 'Pelota infantil',
    brand: 'Toy Library',
    reference: 'TEST-EXT-001',
    category: 'exterior',
    total_units: 6
  },
  {
    name: 'Juego de cocina',
    brand: 'Toy Library',
    reference: 'TEST-SIMB-001',
    category: 'simbolicos',
    total_units: 2
  }
];

function validateEnvironment() {
  if (!['development', 'test'].includes(SEED_ENV)) {
    throw new Error(
      'SEED_ENV debe ser development o test.'
    );
  }

  if (!ADMIN_USERNAME || !ADMIN_PASSWORD) {
    throw new Error(
      'Debes configurar ADMIN_USERNAME y ADMIN_PASSWORD.'
    );
  }

  const url = new URL(API_BASE_URL);

  const allowedHosts = [
    'localhost',
    '127.0.0.1',
    '::1'
  ];

  if (
    !['http:', 'https:'].includes(url.protocol) ||
    !allowedHosts.includes(url.hostname) ||
    !url.pathname.replace(/\/$/, '').endsWith('/api/v1')
  ) {
    throw new Error(
      'Por seguridad, la API debe utilizar una dirección local /api/v1.'
    );
  }
}

function normalizeName(value) {
  return String(value || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim()
    .toLowerCase();
}

async function apiRequest(
  endpoint,
  {
    method = 'GET',
    body = null,
    token = null
  } = {}
) {
  const headers = {
    Accept: 'application/json'
  };

  if (body !== null) {
    headers['Content-Type'] = 'application/json';
  }

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const response = await fetch(
    `${API_BASE_URL}${endpoint}`,
    {
      method,
      headers,
      body: body === null
        ? undefined
        : JSON.stringify(body),
      signal: AbortSignal.timeout(15000)
    }
  );

  const contentType =
    response.headers.get('content-type') || '';

  const data = contentType.includes('application/json')
    ? await response.json()
    : await response.text();

  if (!response.ok) {
    const error = new Error(
      data?.error?.message ||
      data?.message ||
      `Error HTTP ${response.status}`
    );

    error.status = response.status;
    error.code =
      data?.error?.code ||
      data?.code ||
      null;

    throw error;
  }

  return data;
}

async function checkApiHealth() {
  console.log('Verificando conexión con Laravel...');

  const response = await apiRequest('/health');

  if (response?.status !== 'ok') {
    throw new Error(
      'La API no devolvió el estado esperado.'
    );
  }

  console.log('API disponible.');
}

async function authenticate() {
  console.log('Iniciando sesión como administrador...');

  const response = await apiRequest('/auth/login', {
    method: 'POST',
    body: {
      username: ADMIN_USERNAME,
      password: ADMIN_PASSWORD
    }
  });

  if (!response?.access_token) {
    throw new Error(
      'La API no devolvió un token de autenticación.'
    );
  }

  if (response.role !== 'admin') {
    throw new Error(
      'La herramienta requiere una cuenta administradora.'
    );
  }

  console.log('Autenticación exitosa.');

  return response.access_token;
}

async function loadCategories() {
  console.log('Consultando categorías...');

  const response = await apiRequest('/categories');

  const categories = Array.isArray(response)
    ? response
    : response?.data;

  if (!Array.isArray(categories)) {
    throw new Error(
      'La API devolvió un formato de categorías inválido.'
    );
  }

  const categoryMap = new Map();

  for (const category of categories) {
    categoryMap.set(
      normalizeName(category.name),
      category.id
    );
  }

  const requiredCategories = [
    'bebes',
    'construccion',
    'didacticos',
    'exterior',
    'simbolicos'
  ];

  for (const name of requiredCategories) {
    if (!categoryMap.has(name)) {
      throw new Error(
        `No existe la categoría requerida: ${name}`
      );
    }
  }

  console.log(
    `${categories.length} categorías encontradas.`
  );

  return categoryMap;
}

async function seedFamilies(token) {
  console.log('\nRegistrando familias de ejemplo...');

  let created = 0;
  let existing = 0;

  for (const family of SAMPLE_FAMILIES) {
    try {
      await apiRequest('/families', {
        method: 'POST',
        token,
        body: family
      });

      created++;

      console.log(
        `[CREADA] ${family.full_name}`
      );
    } catch (error) {
      if (error.status === 409) {
        existing++;

        console.log(
          `[EXISTENTE] ${family.full_name}`
        );

        continue;
      }

      throw error;
    }
  }

  return { created, existing };
}

async function seedToys(token, categoryMap) {
  console.log('\nRegistrando juguetes de ejemplo...');

  let created = 0;
  let existing = 0;

  for (const toy of SAMPLE_TOYS) {
    const categoryId = categoryMap.get(
      toy.category
    );

    if (!categoryId) {
      throw new Error(
        `Categoría no encontrada para ${toy.name}`
      );
    }

    try {
      await apiRequest('/toys', {
        method: 'POST',
        token,
        body: {
          name: toy.name,
          brand: toy.brand,
          reference: toy.reference,
          category_id: categoryId,
          total_units: toy.total_units
        }
      });

      created++;

      console.log(
        `[CREADO] ${toy.name}`
      );
    } catch (error) {
      if (
        error.status === 409 &&
        error.code === 'REFERENCE_TAKEN'
      ) {
        existing++;

        console.log(
          `[EXISTENTE] ${toy.name}`
        );

        continue;
      }

      throw error;
    }
  }

  return { created, existing };
}

async function main() {
  console.log('===================================');
  console.log(' TOY LIBRARY - DEVELOPMENT SEED');
  console.log('===================================');

  validateEnvironment();

  await checkApiHealth();

  const token = await authenticate();

  const categoryMap = await loadCategories();

  const familyResults = await seedFamilies(token);

  const toyResults = await seedToys(
    token,
    categoryMap
  );

  console.log('\n===================================');
  console.log(' RESUMEN DE CARGA');
  console.log('===================================');

  console.log(
    `Familias creadas: ${familyResults.created}`
  );

  console.log(
    `Familias existentes: ${familyResults.existing}`
  );

  console.log(
    `Juguetes creados: ${toyResults.created}`
  );

  console.log(
    `Juguetes existentes: ${toyResults.existing}`
  );

  console.log('\nCarga de datos finalizada.');
}

main().catch((error) => {
  console.error('\nError durante la carga:');
  console.error(error.message);

  process.exitCode = 1;
});
