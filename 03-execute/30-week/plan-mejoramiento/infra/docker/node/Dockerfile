
# Toy Library - React Frontend
# Node.js 22 with Vite

FROM node:22-alpine

# Set working directory
WORKDIR /workspace/app

# Configure development environment
ENV NODE_ENV=development

# Expose Vite development server port
EXPOSE 5173

# Start the React development server
CMD ["sh", "-c", "npm install && npm run dev -- --host 0.0.0.0"]
