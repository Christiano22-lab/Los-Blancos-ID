"use client"

import type React from "react"

import { createContext, useContext, useState, useEffect } from "react"
import { useRouter, usePathname } from "next/navigation"

type User = {
  id: string
  name: string
  email: string
}

type AuthContextType = {
  user: User | null
  login: (email: string, password: string) => Promise<void>
  signup: (name: string, email: string, password: string) => Promise<void>
  logout: () => void
  isLoading: boolean
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [isLoading, setIsLoading] = useState(true)
  const router = useRouter()
  const pathname = usePathname()

  // Check if user is logged in on initial load
  useEffect(() => {
    const storedUser = localStorage.getItem("user")
    if (storedUser) {
      setUser(JSON.parse(storedUser))
    }
    setIsLoading(false)
  }, [])

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!isLoading && !user && pathname !== "/login" && pathname !== "/signup") {
      router.push("/login")
    }
  }, [user, isLoading, pathname, router])

  const login = async (email: string, password: string) => {
    // This is a mock implementation - in a real app, you'd call your API
    return new Promise<void>((resolve, reject) => {
      setTimeout(() => {
        // Mock successful login
        const mockUser = {
          id: "user_" + Math.random().toString(36).substr(2, 9),
          name: email.split("@")[0],
          email,
        }

        setUser(mockUser)
        localStorage.setItem("user", JSON.stringify(mockUser))
        resolve()
      }, 1000)
    })
  }

  const signup = async (name: string, email: string, password: string) => {
    // This is a mock implementation - in a real app, you'd call your API
    return new Promise<void>((resolve, reject) => {
      setTimeout(() => {
        // Mock successful signup
        const mockUser = {
          id: "user_" + Math.random().toString(36).substr(2, 9),
          name,
          email,
        }

        setUser(mockUser)
        localStorage.setItem("user", JSON.stringify(mockUser))
        resolve()
      }, 1000)
    })
  }

  const logout = () => {
    setUser(null)
    localStorage.removeItem("user")
    router.push("/login")
  }

  return <AuthContext.Provider value={{ user, login, signup, logout, isLoading }}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider")
  }
  return context
}
