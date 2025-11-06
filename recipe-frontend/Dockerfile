FROM node:20-alpine

# Set working directory inside the container
WORKDIR /app

# Copy dependency manifests first for caching
COPY package*.json ./

# Install dependencies (npm ci is faster and more reproducible)
RUN npm ci

# Copy rest of the project files
COPY . .

# Expose Vite default dev port
EXPOSE 5173

# Start Vite dev server (HMR accessible externally)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]