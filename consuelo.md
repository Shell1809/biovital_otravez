# Informe tecnico de estado - Sistema BioVital

## 1. Resumen ejecutivo

BioVital presenta una arquitectura MVC funcional basada en PHP, MySQL, front controller y tabla centralizada de rutas. El sistema cuenta con 11 modulos principales, de los cuales 8 se encuentran operativos, 2 presentan implementacion parcial y 1 permanece bloqueado por ausencia de controlador.

El estado general corresponde a un MVP avanzado: la base funcional existe, el flujo principal de autenticacion/registro opera correctamente y los fallos criticos estan concentrados en componentes identificables. La prioridad tecnica inmediata es cerrar controladores faltantes, reducir exposicion de configuracion sensible y consolidar la estrategia de seguridad.

## 2. Estado funcional por modulo

| # | Modulo | Estado | Observacion tecnica |
|---|--------|--------|---------------------|
| 1 | Autenticacion | Operativo | Login por rol y logout funcionales. Uso de `bcrypt` para hashes de contrasena. |
| 2 | Registro | Operativo | Registro para 4 roles con validacion y proteccion CSRF. |
| 3 | Perfil de usuario | Operativo | Gestion comun para paciente, medico, asistente y administrador. |
| 4 | Paciente | Operativo | Panel, edicion de datos y consulta de recetas disponibles. |
| 5 | Medico | Operativo | Panel, listado de pacientes y estadisticas funcionales. |
| 6 | Asistente | Parcial | Panel y login disponibles; 5 endpoints fallan por ausencia de `AsistenteController.php`. |
| 7 | Administrador | Parcial menor | Funcional en general; `listarUsuarios` retorna arreglo vacio. |
| 8 | Consultorios | Operativo | CRUD, detalle, horarios, asignacion de medicos y estadisticas implementadas. |
| 9 | Recetas | No operativo | Modelo `Receta.php` existe, pero falta `RecetaController.php`; 8 rutas devuelven HTTP 500. |
| 10 | Ubicaciones | Operativo | API jerarquica para estados, ciudades, municipios, parroquias y especialidades. |
| 11 | CSRF / Seguridad | Parcial | Existen dos implementaciones CSRF activas; requiere consolidacion. |

## 3. Metricas de implementacion

### Modulos

```text
Total de modulos:              11
Modulos operativos:             8   (73 %)
Modulos parciales:              2   (18 %)
Modulos no operativos:          1   ( 9 %)
```

### Rutas HTTP

```text
Rutas definidas:               54
Rutas operativas:              40   (74-75 %)
Rutas placeholder/parciales:    1   ( 2 %)
Rutas con error HTTP 500:      13   (23-24 %)
```

Las 13 rutas con error se concentran en los modulos de recetas y asistente. Implementar `RecetaController.php` y `AsistenteController.php` elevaria la cobertura operativa aproximada al 98 %, asumiendo compatibilidad con los modelos existentes.

## 4. Hallazgos criticos

### 4.1 Controlador de recetas ausente

Archivo requerido:

```text
controlador/RecetaController.php
```

Impacto:

- 8 rutas del modulo de recetas devuelven HTTP 500.
- El modelo `modelo/Receta.php` ya existe y contiene la logica principal.
- Existe una version historica en `controlador/antiguos/RecetaController.php` que puede usarse como referencia.

Accion recomendada:

- Crear `RecetaController.php`.
- Adaptarlo al front controller actual.
- Exponer metodos publicos para listar, crear, editar, borrar, obtener, buscar pacientes y consultar recetas del paciente autenticado.

### 4.2 Controlador de asistente ausente

Archivo requerido:

```text
controlador/AsistenteController.php
```

Impacto:

- 5 endpoints internos del modulo asistente devuelven HTTP 500.
- El login y el panel de asistente ya estan disponibles.
- El modelo `Asistente.php` esta implementado.

Accion recomendada:

- Crear `AsistenteController.php`.
- Reutilizar el patron de `PacienteController.php` o `MedicoController.php`.
- Ajustar nombres de campos y metodos segun la estructura del modelo de asistente.

### 4.3 Script sensible expuesto

Archivo identificado:

```text
get_hash.php
```

Impacto:

- Genera o expone el hash de una contrasena conocida.
- No debe permanecer accesible desde la raiz del proyecto.

Accion recomendada:

- Eliminar el archivo del repositorio o moverlo fuera del document root.
- Si se requiere utilidad de generacion de hashes, implementarla como comando interno no accesible por HTTP.

## 5. Riesgos de seguridad

| Riesgo | Severidad | Detalle | Recomendacion |
|--------|-----------|---------|---------------|
| Errores PHP visibles | Alta | `display_errors = 1` y `error_reporting(E_ALL)` en ejecucion. | Controlar por `APP_ENV`; desactivar en produccion. |
| Credenciales hardcodeadas | Alta | Conexion MySQL con `root` y password vacio en `Conexion.php`. | Usar variables de entorno o archivo de configuracion fuera del repositorio. |
| CSRF duplicado | Alta | Coexisten `csrf.js`, `csrf_helper.js`, `/api/csrf/token` y `/api/get_csrf_token.php`. | Mantener una sola fuente de token CSRF. |
| Sesion sin regeneracion de ID | Alta | No se evidencia `session_regenerate_id(true)` tras login exitoso. | Regenerar ID de sesion despues de autenticar. |
| Cookies sin endurecimiento explicito | Alta | Falta configuracion explicita de `httponly`, `secure` y `samesite`. | Definir `session_set_cookie_params` antes de `session_start`. |
| Mensajes de login diferenciados | Media | Posible enumeracion de usuarios si los errores revelan existencia. | Usar mensaje generico para credenciales invalidas. |

## 6. Observaciones de arquitectura y calidad

### 6.1 Arquitectura

La estructura general esta alineada con MVC:

- `index.php` actua como front controller.
- `config/routes.php` centraliza las rutas.
- `controlador/` contiene la capa de controladores.
- `modelo/` concentra acceso a datos mediante PDO.
- `vista/` contiene plantillas por rol y vistas publicas.

La arquitectura es viable para un MVP y permite remediacion incremental sin reescritura completa.

### 6.2 Modelo de usuarios

Actualmente se observan tablas separadas por rol, por ejemplo registros y logins especificos para paciente, medico, asistente y administrador. Este enfoque funciona, pero genera duplicacion y aumenta el costo de mantenimiento.

Recomendacion de mediano plazo:

- Consolidar usuarios en una tabla `usuarios`.
- Separar credenciales en una tabla `credenciales`.
- Usar un campo `rol` para distinguir permisos y vistas.

### 6.3 Separacion de responsabilidades

Algunos modelos reportan resultados mediante `echo`, lo cual acopla la capa de datos con la respuesta HTTP o la presentacion.

Recomendacion:

- Los modelos deben retornar valores, entidades o resultados estructurados.
- Los controladores deben decidir el formato de respuesta HTTP/JSON.
- Los errores deben comunicarse mediante excepciones controladas o estructuras de resultado.

### 6.4 Auditoria

Para un sistema clinico, se recomienda incorporar campos de trazabilidad en tablas operativas:

```text
created_at
updated_at
created_by
updated_by
deleted_at   (si se implementa borrado logico)
```

Esto aplica especialmente a recetas, consultorios, asignaciones y cambios de perfil.

## 7. Calificacion tecnica

| Categoria | Peso | Puntaje | Justificacion |
|-----------|-----:|--------:|---------------|
| Arquitectura | 20 | 16 | MVC y front controller correctamente orientados. |
| Funcionalidad | 25 | 18 | La mayoria de modulos operan; faltan dos controladores clave. |
| Seguridad | 20 | 11 | Existen bcrypt y CSRF, pero faltan endurecimientos de produccion. |
| Calidad de codigo | 15 | 9 | Hay duplicacion por rol y mezcla de responsabilidades en modelos. |
| Base de datos | 10 | 6 | Correcta en geografia; redundante en usuarios/credenciales. |
| Documentacion | 5 | 4 | Existen diagramas y mapa de rutas documentados. |
| Pruebas y despliegue | 5 | 1 | No se identifican tests automatizados ni pipeline de despliegue. |

```text
Calificacion total: 65 / 100
```

Interpretacion: MVP funcional con deuda tecnica controlable. El sistema puede avanzar a piloto controlado luego de resolver los hallazgos criticos y aplicar endurecimientos minimos de seguridad.

## 8. Plan de remediacion

### Sprint 1 - Correccion critica

Objetivo: eliminar rutas HTTP 500 y cerrar exposiciones evidentes.

1. Implementar `controlador/RecetaController.php`.
2. Implementar `controlador/AsistenteController.php`.
3. Eliminar `get_hash.php` del document root.
4. Configurar errores PHP segun entorno.
5. Extraer credenciales de base de datos a configuracion basada en variables de entorno.

Resultado esperado:

- Operatividad aproximada de rutas: 98 %.
- Reduccion inmediata de fallos visibles al usuario.
- Menor exposicion de informacion sensible.

### Sprint 2 - Endurecimiento de seguridad

Objetivo: estabilizar autenticacion, sesiones y CSRF.

1. Consolidar la implementacion CSRF en un unico endpoint/controlador.
2. Eliminar `js/csrf_helper.js` y `/api/get_csrf_token.php` si quedan obsoletos.
3. Agregar `session_regenerate_id(true)` despues de login exitoso.
4. Configurar cookies de sesion con `secure`, `httponly` y `samesite`.
5. Normalizar mensajes de error de login.
6. Completar `AdministradorController::listarUsuarios` con paginacion.

### Sprint 3 - Normalizacion interna

Objetivo: reducir duplicacion y mejorar mantenibilidad.

1. Disenar migracion hacia `usuarios` + `credenciales`.
2. Evaluar renombrado de `tipo_paciente` a `tipo_usuario`.
3. Agregar campos de auditoria en tablas operativas.
4. Refactorizar modelos para retornar datos en lugar de imprimir respuestas.
5. Revisar controladores para respuestas JSON consistentes.

### Sprint 4 - Profesionalizacion del proyecto

Objetivo: preparar el sistema para mantenimiento y despliegue controlado.

1. Crear `composer.json`.
2. Incorporar autoload PSR-4.
3. Documentar reglas de rewrite con `.htaccess`.
4. Agregar pruebas automatizadas con PHPUnit.
5. Preparar entorno reproducible con Docker o docker-compose.
6. Configurar pipeline CI/CD con lint, pruebas y build.

## 9. Acciones tecnicas sugeridas

### Configuracion de errores por entorno

```php
$env = getenv('APP_ENV') ?: 'production';

if ($env === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}
```

### Parametros seguros de sesion

```php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);
```

### Rewrite base recomendado

```apacheconf
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L,QSA]
```

## 10. Referencias internas

| Documento | Proposito |
|---|---|
| [docs/diagramas/01-arquitectura-general.md](docs/diagramas/01-arquitectura-general.md) | Arquitectura general del sistema. |
| [docs/diagramas/02-modulos.md](docs/diagramas/02-modulos.md) | Mapa funcional de modulos. |
| [docs/diagramas/03-base-datos.md](docs/diagramas/03-base-datos.md) | Modelo de datos. |
| [docs/diagramas/04-flujo-autenticacion.md](docs/diagramas/04-flujo-autenticacion.md) | Flujo de autenticacion. |
| [docs/diagramas/05-flujo-receta.md](docs/diagramas/05-flujo-receta.md) | Flujo del modulo de recetas. |
| [docs/diagramas/06-estado-implementacion.md](docs/diagramas/06-estado-implementacion.md) | Estado de implementacion y calificacion. |
| [docs/diagramas/07-plan-remediacion.md](docs/diagramas/07-plan-remediacion.md) | Roadmap de remediacion. |
| [docs/diagramas/08-mapa-rutas.md](docs/diagramas/08-mapa-rutas.md) | Mapa completo de rutas. |

## 11. Conclusion

El sistema BioVital no requiere una reescritura completa. La ruta recomendada es una remediacion incremental enfocada en:

1. Completar controladores faltantes.
2. Endurecer configuracion de seguridad.
3. Reducir duplicacion estructural.
4. Incorporar pruebas y despliegue reproducible.

Con el cierre del primer sprint, el sistema quedaria en condiciones tecnicas razonables para un piloto interno controlado. Con los cuatro sprints, alcanzaria una base mas solida para operacion real, mantenimiento evolutivo y auditoria tecnica.
