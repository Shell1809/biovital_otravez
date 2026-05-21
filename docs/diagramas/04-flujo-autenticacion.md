# Diagrama 4 — Flujo de Autenticación y Autorización

Cómo viaja una sesión desde el formulario de login hasta el panel autorizado.

```mermaid
sequenceDiagram
    autonumber
    actor U as Usuario
    participant B as Navegador
    participant H as .htaccess
    participant I as index.php
    participant CTRL as AuthController
    participant SEC as Security.php
    participant LOG as LoginXxx (modelo)
    participant DB as MySQL
    participant V as Vista

    U->>B: Carga /login/paciente
    B->>H: GET /login/paciente
    H->>I: rewrite a index.php
    I->>I: session_start()<br/>autoload classes
    I->>I: parseRoute()
    I->>CTRL: showLoginPaciente()
    CTRL->>V: renderView('login_paciente')
    V-->>B: HTML + token CSRF embebido

    U->>B: Envía formulario (cédula, pass)
    B->>I: POST /login (incluye csrf_token)
    I->>SEC: verificarTokenCSRF()
    alt token inválido o vencido
        SEC-->>I: false
        I-->>B: 403 / redirect a login
    else token válido
        SEC-->>I: true
        I->>CTRL: login()
        CTRL->>LOG: Loguearse(cedula, pass)
        LOG->>DB: SELECT * FROM login_xxx<br/>JOIN registro_xxx
        DB-->>LOG: filas
        LOG->>LOG: password_verify(pass, hash)
        alt credenciales OK
            LOG-->>CTRL: datos del usuario
            CTRL->>CTRL: $_SESSION['usuario'], ['rol'], ['nombre_us']
            CTRL-->>B: redirect /panel/{rol}
        else credenciales mal
            LOG-->>CTRL: false
            CTRL-->>B: JSON {error}
        end
    end

    Note over B,I: Petición posterior a recurso protegido
    U->>B: GET /panel/medico
    B->>I: GET /panel/medico
    I->>I: verifica $_SESSION['rol'] vs route['rol']
    alt rol coincide
        I->>CTRL: PanelController::medico()
        CTRL->>V: render panel
        V-->>B: HTML
    else rol no coincide
        I-->>B: redirect a login
    end
```

## Estado de la implementación

| Aspecto | Estado | Detalle |
|---------|--------|---------|
| Hashing de contraseña | ✅ Correcto | `password_hash` con `PASSWORD_DEFAULT` (bcrypt) y `password_verify`. |
| CSRF token | ⚠️ Doble sistema | `csrf.js` (POST a `/api/csrf/token`) y `csrf_helper.js` (GET a `/api/get_csrf_token.php`) conviven. |
| Expiración de sesión | ❌ No implementada | No hay `session.cookie_lifetime` ni `regenerate_id()` después del login. |
| Brute-force protection | ❌ No implementada | No hay rate-limit ni bloqueo tras N intentos. |
| `httpOnly` / `secure` cookies | ❌ No configurado | Cookies de sesión usan defaults de PHP. |
| Regeneración de session ID | ❌ Falta | Tras login debería llamarse `session_regenerate_id(true)` para evitar fixation. |
| Logout | ✅ Implementado | `AuthController::logout` destruye sesión. |
| Mensajes de error genéricos | ⚠️ Parcial | Algunos endpoints exponen "usuario no existe" vs "contraseña incorrecta" (enumeration). |
