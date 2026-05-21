# Diagrama 2 — Mapa de Módulos Funcionales

Distribución de los módulos del sistema, su estado y los roles que los consumen.

```mermaid
graph LR
    subgraph Sistema["Sistema BioVital"]

        subgraph M1["1. Autenticación ✅"]
            M1A[Login Paciente]
            M1B[Login Médico]
            M1C[Login Asistente]
            M1D[Login Administrador]
            M1E[Logout]
        end

        subgraph M2["2. Registro ✅"]
            M2A[Registro Paciente]
            M2B[Registro Médico]
            M2C[Registro Asistente]
            M2D[Registro Administrador]
        end

        subgraph M3["3. Perfil ✅"]
            M3A[Ver Perfil]
            M3B[Editar Datos]
            M3C[Cambiar Foto]
            M3D[Cambiar Contraseña]
        end

        subgraph M4["4. Paciente ✅"]
            M4A[Panel Paciente]
            M4B[Catálogo]
            M4C[Mis Recetas]
            M4D[Mis Estadísticas]
        end

        subgraph M5["5. Médico ✅"]
            M5A[Panel Médico]
            M5B[Listar Pacientes]
            M5C[Mis Estadísticas]
        end

        subgraph M6["6. Asistente ⚠️"]
            M6A[Panel Asistente]
            M6B["API CRUD ❌<br/>(controlador falta)"]
        end

        subgraph M7["7. Administrador ✅"]
            M7A[Panel Admin]
            M7B[Listar Usuarios]
            M7C[Gestión Usuarios]
        end

        subgraph M8["8. Consultorios ✅"]
            M8A[CRUD Consultorios]
            M8B[Asignar Médicos]
            M8C[Horarios]
            M8D[Estadísticas]
        end

        subgraph M9["9. Recetas ❌"]
            M9A["Listar / Crear / Editar<br/>(controlador falta)"]
            M9B["Mis recetas paciente<br/>(controlador falta)"]
        end

        subgraph M10["10. Ubicaciones ✅"]
            M10A[Estados]
            M10B[Ciudades]
            M10C[Municipios]
            M10D[Parroquias]
            M10E[Especialidades]
        end

        subgraph M11["11. Seguridad / CSRF ⚠️"]
            M11A[Generar Token]
            M11B[Verificar Token]
            M11C["Doble sistema JS<br/>csrf.js + csrf_helper.js"]
        end
    end

    PAC((👤 Paciente)) --> M1
    PAC --> M3
    PAC --> M4
    MED((🩺 Médico)) --> M1
    MED --> M3
    MED --> M5
    MED --> M9
    ASI((👩‍⚕️ Asistente)) --> M1
    ASI --> M3
    ASI --> M6
    ASI --> M9
    ADM((🛡️ Administrador)) --> M1
    ADM --> M3
    ADM --> M7
    ADM --> M8
    PUB((🌐 Público)) --> M2
    PUB --> M10

    classDef ok fill:#d4f8d4,stroke:#2e7d32,color:#1b5e20
    classDef warn fill:#fff3cd,stroke:#856404,color:#856404
    classDef bad fill:#f8d7da,stroke:#a02633,color:#721c24

    class M1,M2,M3,M4,M5,M7,M8,M10 ok
    class M6,M11 warn
    class M9 bad
```

## Resumen de Módulos

| # | Módulo | Estado | Notas |
|---|--------|--------|-------|
| 1 | Autenticación | ✅ Funcional | 4 logins por rol + logout. Usa bcrypt. |
| 2 | Registro | ✅ Funcional | 4 registros por rol con validación CSRF. |
| 3 | Perfil | ✅ Funcional | Edición transversal a todos los roles. |
| 4 | Paciente | ✅ Funcional | Panel, catálogo y consulta de recetas. |
| 5 | Médico | ✅ Funcional | Panel, listado de pacientes, estadísticas. |
| 6 | Asistente | ⚠️ Parcial | Login y panel sí, pero **falta `AsistenteController.php`** activo → 5 endpoints rotos. |
| 7 | Administrador | ✅ Funcional | Gestión de usuarios y consultorios. |
| 8 | Consultorios | ✅ Funcional | CRUD completo, asignación de médicos, horarios. |
| 9 | Recetas | ❌ Roto | **Falta `RecetaController.php`** activo → 8 endpoints rotos. |
| 10 | Ubicaciones | ✅ Funcional | APIs jerárquicas (estado → ciudad → municipio → parroquia). |
| 11 | Seguridad / CSRF | ⚠️ Doble sistema | Conviven dos implementaciones JS y dos endpoints. |

### Total

- **11 módulos identificados**
- **8 módulos 100 % funcionales** (≈ 73 %)
- **2 módulos parcialmente funcionales** (Asistente, CSRF)
- **1 módulo roto** (Recetas)
