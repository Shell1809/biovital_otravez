# Diagrama 3 — Modelo de Base de Datos (ER)

Esquema entidad-relación inferido a partir de los modelos PHP (`/modelo/*.php`).
Base de datos: **`biovital`** en MySQL `localhost:3306`.

```mermaid
erDiagram
    TIPO_PACIENTE ||--o{ REGISTRO_PACIENTE : "clasifica"
    TIPO_PACIENTE ||--o{ REGISTRO_MEDICO : "clasifica"
    TIPO_PACIENTE ||--o{ REGISTRO_ASISTENTE : "clasifica"
    TIPO_PACIENTE ||--o{ REGISTRO_ADMINISTRADOR : "clasifica"

    REGISTRO_PACIENTE ||--|| LOGIN_PACIENTE : "tiene credencial"
    REGISTRO_MEDICO ||--|| LOGIN_MEDICO : "tiene credencial"
    REGISTRO_ASISTENTE ||--|| LOGIN_ASISTENTE : "tiene credencial"
    REGISTRO_ADMINISTRADOR ||--|| LOGIN_ADMINISTRADOR : "tiene credencial"

    REGISTRO_PACIENTE ||--o{ RECETAS : "recibe"
    REGISTRO_MEDICO ||--o{ RECETAS : "emite"

    CONSULTORIOS ||--o{ CONSULTORIO_MEDICOS : "agrupa"
    REGISTRO_MEDICO ||--o{ CONSULTORIO_MEDICOS : "trabaja en"

    ESTADOS ||--o{ CIUDADES : "contiene"
    CIUDADES ||--o{ MUNICIPIOS : "contiene"
    MUNICIPIOS ||--o{ PARROQUIAS : "contiene"

    ESTADOS ||--o{ CONSULTORIOS : "ubicado en"
    CIUDADES ||--o{ CONSULTORIOS : "ubicado en"
    MUNICIPIOS ||--o{ CONSULTORIOS : "ubicado en"
    PARROQUIAS ||--o{ CONSULTORIOS : "ubicado en"

    ESPECIALIDADES ||--o{ REGISTRO_MEDICO : "ejerce"

    TIPO_PACIENTE {
        int id_tipo_us PK
        string nombre_tipo "Paciente|Medico|Asistente|Administrador"
    }

    REGISTRO_PACIENTE {
        int id_paciente PK
        string cedula_paciente UK
        string nombre_paciente
        string apellido_paciente
        date fecha_nacimiento_pac
        string telefono_paciente
        string direccion_paciente
        string correo_paciente
        char sexo_paciente
        text adicional_paciente
        string avatar_paciente
        int paciente_tipo FK
    }

    REGISTRO_MEDICO {
        int id_medico PK
        string cedula_medico UK
        string nombre_medico
        string apellido_medico
        date fecha_nacimiento_medico
        string telefono_medico
        string direccion_medico
        string correo_medico
        char sexo_medico
        text adicional_medico
        string avatar_medico
        int medico_tipo FK
    }

    REGISTRO_ASISTENTE {
        int id_asistente PK
        string cedula_asistente UK
        string nombre_asistente
        string apellido_asistente
        string telefono_asistente
        string correo_asistente
        string avatar_asistente
        int asistente_tipo FK
    }

    REGISTRO_ADMINISTRADOR {
        int id_administrador PK
        string cedula_administrador UK
        string nombre_administrador
        string apellido_administrador
        string telefono_administrador
        string correo_administrador
        string avatar_administrador
        int administrador_tipo FK
    }

    LOGIN_PACIENTE {
        int id_paciente PK_FK
        string password_hash "bcrypt"
        string status
        datetime ultimo_acceso
    }

    LOGIN_MEDICO {
        int id_medico PK_FK
        string password_hash "bcrypt"
        string status
        datetime ultimo_acceso
    }

    LOGIN_ASISTENTE {
        int id_asistente PK_FK
        string password_hash "bcrypt"
        string status
        datetime ultimo_acceso
    }

    LOGIN_ADMINISTRADOR {
        int id_administrador PK_FK
        string password_hash "bcrypt"
        string status
        datetime ultimo_acceso
    }

    CONSULTORIOS {
        int id_consultorio PK
        string nombre
        text descripcion
        time apertura_habitual
        time cierre_habitual
        string telefono
        string email
        int id_estado FK
        int id_ciudad FK
        int id_municipio FK
        int id_parroquia FK
        string direccion_detallada
        boolean activo
    }

    CONSULTORIO_MEDICOS {
        int id_consultorio PK_FK
        int id_medico PK_FK
        boolean activo
    }

    RECETAS {
        int id_receta PK
        int id_paciente FK
        int id_medico FK
        string nombre_medicamento
        string marca
        string cantidad
        string dosis
        text instrucciones
        datetime fecha_receta
        string estado
    }

    ESTADOS {
        int id_estado PK
        string nombre
    }

    CIUDADES {
        int id_ciudad PK
        int id_estado FK
        string nombre
    }

    MUNICIPIOS {
        int id_municipio PK
        int id_ciudad FK
        string nombre
    }

    PARROQUIAS {
        int id_parroquia PK
        int id_municipio FK
        string nombre
    }

    ESPECIALIDADES {
        int id_especialidad PK
        string nombre
    }
```

## Observaciones del modelo

1. **Antipatrón "una tabla por rol"**: existen 4 tablas `registro_*` y 4 tablas `login_*` con la misma forma. Una sola tabla `usuarios` + `credenciales` con un campo `rol` simplificaría toda la capa de autenticación, perfil y administración.
2. **Nombre engañoso**: la tabla `tipo_paciente` clasifica a **todos** los roles (Paciente, Médico, Asistente, Administrador), pero su nombre sugiere lo contrario. Debería renombrarse a `tipo_usuario`.
3. **Denormalización en `consultorios`**: además de los `FK` (`id_estado`, `id_ciudad`…) se guarda el **nombre** de cada nivel geográfico. Práctico para reportes, riesgoso si las tablas maestras cambian.
4. **Recetas**: sin `controlador/RecetaController.php` activo, las tablas existen pero **no son accesibles desde la app**.
5. **Sin auditoría**: ninguna tabla tiene `created_at` / `updated_at` / `created_by`. Para un sistema clínico esto es deuda crítica.
