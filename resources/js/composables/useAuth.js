import { reactive, ref } from 'vue'

const STORAGE_KEYS = {
  auth: 'auth:isAuthenticated',
  user: 'auth:savedUser'
}

const isAuthenticated = ref(localStorage.getItem(STORAGE_KEYS.auth) === 'true')
const user = reactive({ username: '', email: '', phone: '', address: '' })

try {
  const saved = localStorage.getItem(STORAGE_KEYS.user)
  if (saved) {
    const parsed = JSON.parse(saved)
    user.username = parsed.username || ''
    user.email = parsed.email || ''
    user.phone = parsed.phone || ''
    user.address = parsed.address || ''
    if (parsed.fullname) user.fullname = parsed.fullname
  }
} catch {}

function login(identifier, password) {
  // demo: pass through
  isAuthenticated.value = true
  localStorage.setItem(STORAGE_KEYS.auth, 'true')
}

function register(payload) {
  try {
    const toSave = {
      username: payload.username,
      email: payload.email,
      phone: payload.phone || '',
      address: payload.address || '',
      fullname: payload.fullname || ''
    }
    localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(toSave))
    user.username = toSave.username
    user.email = toSave.email
    user.phone = toSave.phone
    user.address = toSave.address
    if (toSave.fullname) user.fullname = toSave.fullname
  } catch {}
}

function logout() {
  isAuthenticated.value = false
  localStorage.setItem(STORAGE_KEYS.auth, 'false')
}

export function useAuth() {
  function updateProfile(partial) {
    user.username = partial.username ?? user.username
    user.email = partial.email ?? user.email
    user.phone = partial.phone ?? user.phone
    user.address = partial.address ?? user.address
    try { localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user)) } catch {}
  }

  return { isAuthenticated, user, login, logout, register, updateProfile }
}
