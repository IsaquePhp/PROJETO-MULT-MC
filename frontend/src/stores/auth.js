import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null
  }),
  
  actions: {
    async register(name, email, password, password_confirmation) {
      try {
        const response = await axios.post('http://localhost:8000/api/register', {
          name,
          email,
          password,
          password_confirmation
        })
        
        this.token = response.data.access_token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        
        return true
      } catch (error) {
        console.error('Registration error:', error)
        return false
      }
    },
    
    async resetPassword(email) {
      try {
        const response = await axios.post('http://localhost:8000/api/forgot-password', {
          email
        })
        
        return true
      } catch (error) {
        console.error('Reset password error:', error)
        return false
      }
    },
    
    async login(email, password) {
      try {
        const response = await axios.post('http://localhost:8000/api/login', {
          email,
          password
        })
        
        this.token = response.data.access_token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        
        return true
      } catch (error) {
        console.error('Login error:', error)
        return false
      }
    },
    
    async logout() {
      try {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        })
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
      }
    }
  }
})
