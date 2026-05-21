# Diagrama 1 — Arquitectura General del Sistema BioVital

Vista de alto nivel del sistema: capas, componentes principales y dependencias.

```mermaid
graph TB
    subgraph Cliente["Cliente / Navegador"]
        Browser[Navegador Web]
        JS[Archivos JS<br/>jQuery + Bootstrap + AdminLTE]
        CSRF_JS[csrf.js / csrf_helper.js]
    end

    subgraph Servidor["Servidor Apache + PHP"]
        HT[.htaccess<br/>mod_rewrite]
        IDX[index.php<br/>Front Controller]

        subgraph Config["Configuración"]
            APP[config/app.php]
            ROUTES[config/routes.php]
        end

        subgraph Controladores["Capa de Controladores"]
            AUTH[AuthController]
            REG[RegistroController]
            PERF[PerfilController]
            PAN[PanelController]
            PAG[PageController]
            PAC_C[PacienteController]
            MED_C[MedicoController]
            ADM_C[AdministradorController]
            CON_C[ConsultorioController]
            UBI_C[UbicacionController]
            CSRF_C[CSRFController]
            REC_C[RecetaController]:::missing
            ASI_C[AsistenteController]:::missing
        end

        subgraph Modelos["Capa de Modelos"]
            CONX[Conexion.php<br/>PDO MySQL]
            SEC[Security.php<br/>CSRF + Sanitización]
            PAC_M[Paciente / LoginPaciente]
            MED_M[Medico / LoginMedico]
            ASI_M[Asistente / LoginAsistente]
            ADM_M[Administrador / LoginAdministrador]
            CONSU[Consultorio]
            REC_M[Receta]
        end

        subgraph Vistas["Capa de Vistas"]
            V_PUB[Vistas Públicas<br/>home, login, registro]
            V_PAC[vista/paciente]
            V_MED[vista/medico]
            V_ASI[vista/asistente]
            V_ADM[vista/administrador]
            V_LAY[vista/layouts<br/>base, header, footer, nav]
        end
    end

    subgraph BD["Base de Datos MySQL"]
        DB[(biovital<br/>localhost:3306)]
    end

    Browser --> HT
    HT --> IDX
    IDX --> APP
    IDX --> ROUTES
    ROUTES --> Controladores
    Controladores --> Modelos
    Controladores --> Vistas
    Modelos --> CONX
    CONX --> DB
    JS -.AJAX/JSON.-> IDX
    CSRF_JS -.token.-> CSRF_C

    classDef missing fill:#ffdddd,stroke:#cc0000,stroke-width:2px,color:#900
```

## Leyenda

- **Bloques rojos (`:::missing`)**: controladores referenciados en `config/routes.php` pero **inexistentes** en `/controlador/`. Sólo existen versiones obsoletas en `/controlador/antiguos/`. Cualquier ruta que los invoque devuelve error 500.
- **Flechas continuas**: dependencia síncrona (request HTTP / include PHP).
- **Flechas punteadas**: comunicación asíncrona (AJAX desde el navegador).

## Capas

| Capa | Responsabilidad | Tecnología |
|------|----------------|------------|
| Cliente | Render, validación frontend, AJAX | HTML5 + jQuery + Bootstrap 4 + AdminLTE 3 |
| Front Controller | Enrutamiento, autoload, sesión, CSRF | PHP 7+, Apache mod_rewrite |
| Controladores | Lógica de negocio, orquestación | PHP OOP |
| Modelos | Acceso a datos, reglas de dominio | PDO + MySQL |
| Vistas | Presentación | PHP embebido (templating manual) |
| Base de Datos | Persistencia | MySQL 5.7+ / MariaDB |
