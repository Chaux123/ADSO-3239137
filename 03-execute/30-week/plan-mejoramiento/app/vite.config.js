
import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');

  // Local development: http://localhost:8000
  // Docker development: http://api:8000
  const apiProxyTarget =
    env.API_PROXY_TARGET || 'http://localhost:8000';

  return {
    plugins: [react()],

    server: {
      host: '0.0.0.0',
      port: 5173,
      strictPort: true,

      proxy: {
        '/api': {
          target: apiProxyTarget,
          changeOrigin: true,
          secure: false
        }
      }
    },

    preview: {
      host: '0.0.0.0',
      port: 4173
    },

    build: {
      outDir: 'dist',
      sourcemap: false
    }
  };
});

