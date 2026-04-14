# 🔐 Documentación de Protección de Rutas y Roles

**Última actualización:** 12 de abril de 2026  
**Importante para:** Frontend (React Native + Expo)

---

## 📚 Tabla de Contenidos

1. [Resumen de Estrategia](#resumen-de-estrategia)
2. [Autenticación](#autenticación)
3. [Rutas Públicas](#rutas-públicas)
4. [Rutas Autenticadas](#rutas-autenticadas)
5. [Rutas Solo ADMIN](#rutas-solo-admin)
6. [Estrategia Frontend](#estrategia-frontend)
7. [Gestión de Errores](#gestión-de-errores)

---

## 🎯 Resumen de Estrategia

### Flujo de Seguridad Backend

```
REQUEST → ¿Es público? → SÍ → Procesado
            ↓ NO
         ¿Tiene token válido? → NO → 401 Unauthorized
            ↓ SÍ
         ¿Requiere ADMIN? → SÍ → ¿Es admin? → NO → 403 Forbidden
            ↓ NO                     ↓ SÍ
         Procesado              Procesado
```

### Niveles de Acceso

| Nivel           | Descripción            | Acceso                                             |
| --------------- | ---------------------- | -------------------------------------------------- |
| **PÚBLICO**     | Sin token requerido    | Registro, Login, Versión                           |
| **AUTENTICADO** | Token válido requerido | Lectura catálogos, transacciones, datos personales |
| **ADMIN**       | Token + rol ADMIN      | CRUD completo, gestión de sistema                  |

---

## 🔓 Autenticación

### Login

**Endpoint:** `POST /login`  
**Autenticación:** Pública  
**Body:**

```json
{
    "email": "usuario@empresa.com",
    "password": "contraseña"
}
```

**Response (200 OK):**

```json
{
    "message": "Login exitoso",
    "data": {
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "user": {
            "id": 1,
            "nombre_completo": "Juan Pérez",
            "email": "juan@empresa.com",
            "rol": "admin",
            "rol_label": "Administrador",
            "puntos_saldo": 500,
            "codigo_referido": "ABC123XYZ",
            "patrocinador_id": null
        }
    }
}
```

### Registro

**Endpoint:** `POST /registro`  
**Autenticación:** Pública  
**Body:**

```json
{
    "nombre_completo": "Maria García",
    "email": "maria@empresa.com",
    "password": "contraseña",
    "codigo_patrocinador": "ABC123XYZ" // OPCIONAL
}
```

**Response (201 Created):**

```json
{
  "message": "Usuario registrado exitosamente",
  "data": {
    "token": "...",
    "user": { ... }
  }
}
```

### Logout

**Endpoint:** `POST /logout`  
**Autenticación:** ✅ Requerida  
**Body:** (vacío)

**Response (200 OK):**

```json
{
    "message": "Sesión cerrada exitosamente"
}
```

---

## 🔓 Rutas Públicas

| Endpoint    | Verbo | Descripción                   |
| ----------- | ----- | ----------------------------- |
| `/registro` | POST  | Crear nueva cuenta            |
| `/login`    | POST  | Iniciar sesión                |
| `/version`  | GET   | Verificar estado del servidor |

**Nota:** Estas son las ÚNICAS rutas sin autenticación. Todas las demás REQUIEREN token Bearer.

---

## 🔐 Rutas Autenticadas (Todos los usuarios con rol válido)

### Datos Personales

| Endpoint              | Verbo | Descripción                             |
| --------------------- | ----- | --------------------------------------- |
| `/perfil`             | GET   | Ver perfil del usuario actual           |
| `/perfil`             | PUT   | Actualizar perfil                       |
| `/cambiar-contraseña` | POST  | Cambiar contraseña                      |
| `/logout`             | POST  | Cerrar sesión                           |
| `/dashboard`          | GET   | Ver resumen (puntos, comisiones, links) |

### Catálogos (Lectura)

| Endpoint             | Verbo | Descripción                  |
| -------------------- | ----- | ---------------------------- |
| `/productos`         | GET   | Listar productos             |
| `/productos/{id}`    | GET   | Ver detalle de producto      |
| `/videos`            | GET   | Listar videos                |
| `/videos/{id}`       | GET   | Ver video                    |
| `/premios`           | GET   | Listar premios               |
| `/premios/{id}`      | GET   | Ver detalle de premio        |
| `/materiales`        | GET   | Listar materiales            |
| `/materiales/{id}`   | GET   | Ver detalle de material      |
| `/categorias`        | GET   | Listar categorías            |
| `/categoria-premios` | GET   | Listar categorías de premios |
| `/categoria-videos`  | GET   | Listar categorías de videos  |
| `/tipo-materiales`   | GET   | Listar tipos de materiales   |
| `/configuraciones`   | GET   | Listar configuraciones       |

### Notificaciones

| Endpoint                    | Verbo | Descripción                         |
| --------------------------- | ----- | ----------------------------------- |
| `/notificaciones`           | GET   | Listar notificaciones (paginado 15) |
| `/notificaciones/unread`    | GET   | Solo notificaciones no leídas       |
| `/notificaciones/{id}/read` | PUT   | Marcar una como leída               |
| `/notificaciones/read-all`  | PUT   | Marcar todas como leídas            |

### Transacciones - Pedidos

| Endpoint                       | Verbo | Descripción               | Restricción                  |
| ------------------------------ | ----- | ------------------------- | ---------------------------- |
| `/pedidos`                     | GET   | Ver MIS pedidos           | Solo del usuario autenticado |
| `/pedidos`                     | POST  | Crear pedido/compra       | Cualquier usuario            |
| `/pedidos/{id}`                | GET   | Ver detalle de MI pedido  | Solo del propietario o admin |
| `/pedidos/{id}/confirmar-pago` | POST  | Subir comprobante de pago | Solo del propietario         |

### Transacciones - Canjes

| Endpoint              | Verbo | Descripción                  | Restricción                  |
| --------------------- | ----- | ---------------------------- | ---------------------------- |
| `/canje-premios`      | GET   | Ver MIS canjes               | Solo del usuario             |
| `/canje-premios`      | POST  | Crear canje (canjear puntos) | Cualquier usuario            |
| `/canje-premios/{id}` | GET   | Ver detalle de MI canje      | Solo del propietario o admin |

### Comisiones - Lectura

| Endpoint           | Verbo | Descripción                | Restricción                  |
| ------------------ | ----- | -------------------------- | ---------------------------- |
| `/comisiones`      | GET   | Ver MIS comisiones         | Solo de vendedor o admin     |
| `/comisiones/{id}` | GET   | Ver detalle de MI comisión | Solo del propietario o admin |

### Historial de Puntos - Lectura

| Endpoint            | Verbo | Descripción      | Restricción      |
| ------------------- | ----- | ---------------- | ---------------- |
| `/historial-puntos` | GET   | Ver MI historial | Solo del usuario |

### Referidos

| Endpoint          | Verbo | Descripción                 |
| ----------------- | ----- | --------------------------- |
| `/referidos`      | GET   | Ver MIS referidos directos  |
| `/referidos/{id}` | GET   | Ver detalles de UN referido |

---

## 🔒 Rutas Solo ADMIN (Requiere middleware 'admin')

### Gestión de Productos

| Endpoint          | Verbo  | Descripción       |
| ----------------- | ------ | ----------------- |
| `/productos`      | POST   | Crear producto    |
| `/productos/{id}` | PUT    | Editar producto   |
| `/productos/{id}` | DELETE | Eliminar producto |

### Gestión de Videos

| Endpoint       | Verbo  | Descripción    |
| -------------- | ------ | -------------- |
| `/videos`      | POST   | Crear video    |
| `/videos/{id}` | PUT    | Editar video   |
| `/videos/{id}` | DELETE | Eliminar video |

### Gestión de Premios

| Endpoint        | Verbo  | Descripción     |
| --------------- | ------ | --------------- |
| `/premios`      | POST   | Crear premio    |
| `/premios/{id}` | PUT    | Editar premio   |
| `/premios/{id}` | DELETE | Eliminar premio |

### Gestión de Materiales

| Endpoint           | Verbo  | Descripción       |
| ------------------ | ------ | ----------------- |
| `/materiales`      | POST   | Crear material    |
| `/materiales/{id}` | PUT    | Editar material   |
| `/materiales/{id}` | DELETE | Eliminar material |

### Gestión de Categorías

| Endpoint                  | Verbo  | Descripción                   |
| ------------------------- | ------ | ----------------------------- |
| `/categorias`             | POST   | Crear categoría               |
| `/categorias/{id}`        | PUT    | Editar categoría              |
| `/categorias/{id}`        | DELETE | Eliminar categoría            |
| `/categoria-premios`      | POST   | Crear categoría de premios    |
| `/categoria-premios/{id}` | PUT    | Editar categoría de premios   |
| `/categoria-premios/{id}` | DELETE | Eliminar categoría de premios |
| `/categoria-videos`       | POST   | Crear categoría de videos     |
| `/categoria-videos/{id}`  | PUT    | Editar categoría de videos    |
| `/categoria-videos/{id}`  | DELETE | Eliminar categoría de videos  |
| `/tipo-materiales`        | POST   | Crear tipo de material        |
| `/tipo-materiales/{id}`   | PUT    | Editar tipo de material       |
| `/tipo-materiales/{id}`   | DELETE | Eliminar tipo de material     |

### Gestión de Configuraciones

| Endpoint                | Verbo  | Descripción            |
| ----------------------- | ------ | ---------------------- |
| `/configuraciones`      | POST   | Crear configuración    |
| `/configuraciones/{id}` | PUT    | Editar configuración   |
| `/configuraciones/{id}` | DELETE | Eliminar configuración |

### Gestión de Pedidos

| Endpoint               | Verbo | Descripción                 |
| ---------------------- | ----- | --------------------------- |
| `/pedidos/{id}/estado` | PATCH | Cambiar estado (ADMIN ONLY) |

### Gestión de Comisiones

| Endpoint           | Verbo  | Descripción           |
| ------------------ | ------ | --------------------- |
| `/comisiones`      | POST   | Crear comisión manual |
| `/comisiones/{id}` | PUT    | Editar comisión       |
| `/comisiones/{id}` | DELETE | Eliminar comisión     |

### Gestión de Historial de Puntos

| Endpoint            | Verbo | Descripción                       |
| ------------------- | ----- | --------------------------------- |
| `/historial-puntos` | POST  | Crear movimiento manual de puntos |

### Gestión de Canjes

| Endpoint              | Verbo  | Descripción            |
| --------------------- | ------ | ---------------------- |
| `/canje-premios/{id}` | PUT    | Aprobar/rechazar canje |
| `/canje-premios/{id}` | DELETE | Eliminar canje         |

---

## 💻 Estrategia Frontend

### 1. Almacenamiento de Token y Datos de Usuario

```javascript
// React Native + Expo
import AsyncStorage from "@react-native-async-storage/async-storage";

// Guardar después del login
const handleLogin = async (email, password) => {
    const response = await fetch(`${API_URL}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (response.ok) {
        // Guardar token
        await AsyncStorage.setItem("auth_token", data.data.token);

        // Guardar datos de usuario
        await AsyncStorage.setItem("user", JSON.stringify(data.data.user));

        return data.data.user;
    }

    throw new Error(data.message);
};

// Recuperar al abrir la app
const rehydrateUser = async () => {
    const token = await AsyncStorage.getItem("auth_token");
    const userStr = await AsyncStorage.getItem("user");

    if (token && userStr) {
        const user = JSON.parse(userStr);
        return { token, user };
    }

    return null;
};
```

### 2. Configurar Cliente HTTP con Token

```javascript
// En un archivo de configuración (api.js)
import axios from "axios";
import AsyncStorage from "@react-native-async-storage/async-storage";

const API_URL = "https://api.naturebiolife.com/api";

const apiClient = axios.create({
    baseURL: API_URL,
    timeout: 10000,
});

// Interceptor para agregar token automáticamente
apiClient.interceptors.request.use(
    async (config) => {
        const token = await AsyncStorage.getItem("auth_token");

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => Promise.reject(error),
);

// Interceptor para manejar 401/403
apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            // Token expiró o inválido
            await AsyncStorage.removeItem("auth_token");
            await AsyncStorage.removeItem("user");
            // Redirigir a login
        }

        if (error.response?.status === 403) {
            // Usuario no tiene permisos (no es admin)
            console.error("No tienes permisos para esta acción");
        }

        return Promise.reject(error);
    },
);

export default apiClient;
```

### 3. Componente para Proteger Rutas

```javascript
// components/ProtectedRoute.js
import React, { useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";

export const ProtectedRoute = ({
    children,
    requiredRole = null, // 'admin', 'vendedor', null (cualquiera)
    fallback = null,
}) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const checkAuth = async () => {
            const userStr = await AsyncStorage.getItem("user");

            if (userStr) {
                setUser(JSON.parse(userStr));
            }

            setLoading(false);
        };

        checkAuth();
    }, []);

    if (loading) {
        return <LoadingScreen />;
    }

    // No hay usuario = no autenticado
    if (!user) {
        return fallback || <LoginScreen />;
    }

    // Si requiere rol específico
    if (requiredRole && user.rol !== requiredRole) {
        return fallback || <UnauthorizedScreen />;
    }

    return children;
};
```

### 4. Ocultar Opciones según Rol

```javascript
// Ejemplo de menú condicionado
import { useContext } from "react";
import { AuthContext } from "./AuthContext";

export const NavigationMenu = () => {
    const { user } = useContext(AuthContext);
    const isAdmin = user?.rol === "admin";

    return (
        <View>
            {/* Accesible para todos */}
            <TouchableOpacity onPress={() => navigate("Dashboard")}>
                <Text>📊 Dashboard</Text>
            </TouchableOpacity>

            <TouchableOpacity onPress={() => navigate("Productos")}>
                <Text>📦 Productos</Text>
            </TouchableOpacity>

            <TouchableOpacity onPress={() => navigate("Pedidos")}>
                <Text>🛒 Mis Pedidos</Text>
            </TouchableOpacity>

            <TouchableOpacity onPress={() => navigate("Referidos")}>
                <Text>👥 Mis Referidos</Text>
            </TouchableOpacity>

            {/* Solo ADMIN */}
            {isAdmin && (
                <>
                    <TouchableOpacity
                        onPress={() => navigate("AdminProductos")}
                    >
                        <Text>⚙️ Gestionar Productos</Text>
                    </TouchableOpacity>

                    <TouchableOpacity
                        onPress={() => navigate("AdminComisiones")}
                    >
                        <Text>💰 Gestionar Comisiones</Text>
                    </TouchableOpacity>

                    <TouchableOpacity
                        onPress={() => navigate("AdminCategorias")}
                    >
                        <Text>📋 Gestionar Categorías</Text>
                    </TouchableOpacity>
                </>
            )}
        </View>
    );
};
```

### 5. Context de Autenticación

```javascript
// contexts/AuthContext.js
import React, { createContext, useState, useEffect } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import apiClient from "../api/api";

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(null);
    const [loading, setLoading] = useState(true);

    // Rehydratar al iniciar app
    useEffect(() => {
        rehydrate();
    }, []);

    const rehydrate = async () => {
        try {
            const savedToken = await AsyncStorage.getItem("auth_token");
            const savedUser = await AsyncStorage.getItem("user");

            if (savedToken && savedUser) {
                setToken(savedToken);
                setUser(JSON.parse(savedUser));
            }
        } catch (error) {
            console.error("Error rehydrating:", error);
        } finally {
            setLoading(false);
        }
    };

    const login = async (email, password) => {
        const response = await apiClient.post("/login", { email, password });
        const { token, user } = response.data.data;

        await AsyncStorage.setItem("auth_token", token);
        await AsyncStorage.setItem("user", JSON.stringify(user));

        setToken(token);
        setUser(user);

        return user;
    };

    const logout = async () => {
        try {
            await apiClient.post("/logout");
        } finally {
            await AsyncStorage.removeItem("auth_token");
            await AsyncStorage.removeItem("user");

            setToken(null);
            setUser(null);
        }
    };

    const isAdmin = user?.rol === "admin";

    return (
        <AuthContext.Provider
            value={{ user, token, loading, login, logout, isAdmin }}
        >
            {children}
        </AuthContext.Provider>
    );
};
```

---

## 🚨 Gestión de Errores

### Respuestas de Error Comunes

```javascript
// 400 Bad Request
{
  "message": "Validación fallida",
  "errors": {
    "email": ["El email ya está registrado"]
  }
}

// 401 Unauthorized
{
  "message": "No autenticado. Token inválido o expirado."
}

// 403 Forbidden
{
  "message": "No autorizado. Se requiere rol de ADMIN."
}

// 404 Not Found
{
  "message": "Recurso no encontrado"
}

// 422 Unprocessable Entity
{
  "message": "No hay suficiente stock para el producto: Proteína Whey"
}

// 500 Internal Server Error
{
  "message": "Error del servidor. Intenta más tarde."
}
```

### Manejo Recomendado

```javascript
const handleApiError = (error) => {
    const status = error.response?.status;
    const message = error.response?.data?.message || "Error desconocido";

    switch (status) {
        case 401:
            // Ir a login
            navigation.replace("Login");
            break;

        case 403:
            // Mostrar "No tienes permisos"
            showAlert("Error", "No tienes permisos para esta acción");
            break;

        case 422:
            // Validación fallida
            const errors = error.response.data.errors;
            showAlert("Validación", Object.values(errors).flat().join("\n"));
            break;

        default:
            showAlert("Error", message);
    }
};
```

### Actualizar Perfil

```javascript
// Actualizar datos personales del usuario
const updateProfile = async (updatedData) => {
    try {
        const response = await apiClient.put("/perfil", {
            nombre_completo: updatedData.nombre_completo,
            email: updatedData.email,
            telefono: updatedData.telefono,
            dni: updatedData.dni,
            direccion: updatedData.direccion,
        });

        // Guardar datos actualizados en AsyncStorage
        await AsyncStorage.setItem("user", JSON.stringify(response.data.user));

        // Notificar cambio en AuthContext
        setUser(response.data.user);

        showAlert("Éxito", response.data.message);
    } catch (error) {
        handleApiError(error);
    }
};
```

### Cambiar Contraseña

```javascript
// Cambiar contraseña del usuario
const changePassword = async (
    currentPassword,
    newPassword,
    newPasswordConfirmation,
) => {
    try {
        const response = await apiClient.post("/cambiar-contraseña", {
            current_password: currentPassword,
            new_password: newPassword,
            new_password_confirmation: newPasswordConfirmation,
        });

        showAlert("Éxito", response.data.message);

        // Opcional: Solicitar al usuario que se registre de nuevo
        // navigation.replace("Login");
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors;
            showAlert(
                "Error de Validación",
                Object.values(errors).flat().join("\n"),
            );
        } else {
            handleApiError(error);
        }
    }
};
```

---

## 📋 Checklist para Frontend

- [ ] Implementar AuthContext con token management
- [ ] Configurar axios interceptors con Bearer token
- [ ] Crear componente ProtectedRoute para validaciones
- [ ] Condicionar UI según `user.rol` (mostrar/ocultar menús admin)
- [ ] Manejar 401 (redirigir a login) y 403 (mostrar error)
- [ ] Implementar refresh de datos al volver del background
- [ ] Persistir token y usuario en AsyncStorage
- [ ] Implementar logout que limpie almacenamiento
- [ ] Validar permisos antes de mostrar botones de acción
- [ ] Mostrar loading mientras se verifica autenticación

---

## 🔄 Flujo Completo de Autenticación

```
1. Usuario abre app
   ↓
2. App intenta rehydratar desde AsyncStorage
   ├─ Token existe → Navega a Home
   └─ Token no existe → Navega a Login
   ↓
3. Usuario hace login
   ├─ POST /login con email/password
   ├─ Recibe token + user
   ├─ Guarda en AsyncStorage
   ├─ Configura header Authorization
   └─ Navega a Home
   ↓
4. App usa token automáticamente en todas las peticiones
   ├─ GET /dashboard → Con token
   ├─ POST /pedidos → Con token
   └─ DELETE /admin/producto → Con token + middleware admin
   ↓
5. Token expira o es inválido
   ├─ API devuelve 401
   ├─ Interceptor borra AsyncStorage
   ├─ Redirige a Login
   └─ Vuelve al paso 1
```

---

## 📞 Notas Importantes

- **Nunca guardes el token en el código** - siempre en AsyncStorage seguro
- **El token se envía automáticamente** - no lo agregues manualmente en cada request
- **Valida permisos en el frontend** - es UX, pero la verdadera validación es backend
- **No confíes solo en frontend** - el backend siempre valida roles
- **Maneja errores 403** - significa que el usuario existe pero NO tiene permisos
- **AsyncStorage no es 100% seguro** - considera usar Keychain/Keystore para producción
