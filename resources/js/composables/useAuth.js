import { reactive, ref } from 'vue'

const STORAGE_KEYS = {
  auth: 'auth:isAuthenticated',
  user: 'auth:savedUser'
}

const isAuthenticated = ref(localStorage.getItem(STORAGE_KEYS.auth) === 'true')
const user = reactive({ username: '', email: '', phone: '', address: '', roles: [] })

try {
  const saved = localStorage.getItem(STORAGE_KEYS.user)
  if (saved) {
    const parsed = JSON.parse(saved)
    user.username = parsed.username || ''
    user.email = parsed.email || ''
    user.phone = parsed.phone || ''
    user.address = parsed.address || ''
    user.roles = Array.isArray(parsed.roles) ? parsed.roles : []
    if (parsed.fullname) user.fullname = parsed.fullname
  }
} catch {}

function login(identifier, password) {
  // Simple demo auth with special admin rule:
  // - Admin: email ends with "@admin" AND password === "admin123"
  // - Otherwise: regular user
  const trimmed = (identifier || '').toString().trim()
  const isAdminCred = trimmed.endsWith('@admin') && password === 'admin123'

  // Derive username from identifier (before '@' if present)
  const namePart = trimmed.includes('@') ? trimmed.split('@')[0] : trimmed

  // Update reactive user state
  user.username = namePart || user.username
  user.email = trimmed || user.email
  user.roles = isAdminCred ? ['admin'] : (Array.isArray(user.roles) && user.roles.length ? user.roles : ['user'])

  // Mark authenticated and persist
  isAuthenticated.value = true
  try {
    localStorage.setItem(STORAGE_KEYS.auth, 'true')
    const toSave = {
      username: user.username,
      email: user.email,
      phone: user.phone || '',
      address: user.address || '',
      fullname: user.fullname || '',
      roles: user.roles
    }
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(toSave))
  } catch {}

  // Notify listeners (e.g., dashboard) to refetch data
  try { window.dispatchEvent(new CustomEvent('auth:login', { detail: { user: { ...user } } })) } catch {}

  // Return whether this login is admin (handy for callers if needed)
  return { isAdmin: isAdminCred }
}

function register(payload) {
  try {
    const toSave = {
      username: payload.username,
      email: payload.email,
      phone: payload.phone || '',
      address: payload.address || '',
      fullname: payload.fullname || '',
      roles: Array.isArray(payload.roles) ? payload.roles : ['user']
    }
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(toSave))
    user.username = toSave.username
    user.email = toSave.email
    user.phone = toSave.phone
    user.address = toSave.address
    user.roles = toSave.roles
    if (toSave.fullname) user.fullname = toSave.fullname
    try { window.dispatchEvent(new CustomEvent('auth:register', { detail: { user: { ...user } } })) } catch {}
  } catch {}
}

function logout() {
  isAuthenticated.value = false
  localStorage.setItem(STORAGE_KEYS.auth, 'false')
  try { window.dispatchEvent(new Event('auth:logout')) } catch {}
}

export function useAuth() {
  function updateProfile(partial) {
    user.username = partial.username ?? user.username
    user.email = partial.email ?? user.email
    user.phone = partial.phone ?? user.phone
    user.address = partial.address ?? user.address
    if (Array.isArray(partial.roles)) user.roles = partial.roles
    try { localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user)) } catch {}
  }

  function hasRole(role) {
    return Array.isArray(user.roles) && user.roles.includes(role)
  }

  function setRoles(roles) {
    user.roles = Array.isArray(roles) ? roles : []
    try { localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user)) } catch {}
  }

  return { isAuthenticated, user, login, logout, register, updateProfile, hasRole, setRoles }
}
