
import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';

import App from './App.jsx';
import './styles/global.css';

const rootElement = document.getElementById('root');

if (!rootElement) {
  throw new Error(
    'No se encontró el elemento principal de Toy Library.'
  );
}

ReactDOM.createRoot(rootElement).render(
  <React.StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </React.StrictMode>
);
