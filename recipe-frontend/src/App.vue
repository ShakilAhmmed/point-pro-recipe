<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-container">
        <div class="nav-content">
          <div class="nav-brand">
            <span class="brand-text">Recipe Management</span>
          </div>

          <div class="nav-links desktop">
            <router-link v-if="authState" to="/home" class="nav-link">Home</router-link>
            <button v-if="authState" class="nav-link btn-danger" @click="logout">Logout</button>
            <router-link v-if="!authState" to="/login" class="nav-link">Login</router-link>
            <router-link v-if="!authState" to="/register" class="nav-link">Register</router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <router-view/>
    </main>
  </div>
</template>

<script>
import {authState, clearToken} from './helpers/auth.js'
import axiosRequest from './helpers/axios.js'

export default {
  name: 'App',
  data() {
    return {mobileMenuOpen: false}
  },
  setup() {
    return {authState}
  },
  methods: {
    async logout() {
      try {
        await axiosRequest.post('/v1/auth/logout')
      } catch (e) {
      }
      clearToken()
      this.$router.push('/login')
    }
  },
  watch: {
  }
}
</script>

<style>
/* Global Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  background-color: #f3f4f6;
}

#app {
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Navbar Styles */
.navbar {
  background-color: #2563eb;
  color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  width: 100%;
}

.nav-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 1rem;
  width: 100%;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 4rem;
  width: 100%;
}

.nav-brand {
  display: flex;
  align-items: center;
}

.brand-text {
  font-size: 1.5rem;
  font-weight: bold;
}

.nav-links.desktop {
  display: none;
  gap: 1rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  color: white;
  text-decoration: none;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
  white-space: nowrap;
}

.nav-link:hover {
  background-color: #1d4ed8;
}

.nav-link.router-link-active {
  background-color: #1d4ed8;
}

.nav-icon {
  font-size: 1.2rem;
}

.mobile-menu-button {
  display: block;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
}

.mobile-menu {
  background-color: #1d4ed8;
  padding: 0.5rem;
  width: 100%;
}

.mobile-nav-links {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  color: white;
  text-decoration: none;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.mobile-nav-link:hover {
  background-color: #1e40af;
}

.mobile-nav-link.router-link-active {
  background-color: #1e40af;
}

/* Main Content */
.main-content {
  flex: 1;
  width: 100%;
  min-height: calc(100vh - 4rem);
}

/* Desktop Styles */
@media (min-width: 768px) {
  .nav-links.desktop {
    display: flex;
  }

  .mobile-menu-button {
    display: none;
  }

  .mobile-menu {
    display: none;
  }
}
</style>