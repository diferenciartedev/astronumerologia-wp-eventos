# MVP, rutas, API y plan de construcción

## Mapa de rutas (TrustFile)

### Público / marketing
- `/` — Landing de TrustFile (relato: confianza, control, validación fácil).
- `/login`, `/registro`, `/recuperar` — autenticación.
- `/verificar` — buscar por código.
- `/verificar/[codigo]` — **página pública de verificación** (válido / revocado / expirado).
- `/portal/[token]` — portal del destinatario (acceso a su certificado).

### Aplicación (organización) — prefijo `/app`
- `/app` — Dashboard (widgets + actividad + acciones rápidas).
- `/app/certificados` — Repositorio (búsqueda, filtros, orden, exportar).
- `/app/certificados/[id]` — Detalle (preview, datos, línea de tiempo, acciones).
- `/app/emitir` — Flujo de emisión (individual y masiva por CSV).
- `/app/plantillas` — Lista de plantillas.
- `/app/plantillas/[id]` — Editor de plantilla.
- `/app/firmantes` — Firmantes y solicitudes de firma.
- `/app/auditoria` — Log de auditoría.
- `/app/configuracion` — Marca, equipo, roles, verificación, motor de confianza.
- `/onboarding` — Alta de la organización (asistente).

## API de dominio (REST, prefijo `/api`)

```
POST   /api/auth/login | /signup | /reset
GET    /api/org                          # organización actual
PATCH  /api/org/branding
GET    /api/templates  · POST · GET/:id · PATCH/:id
GET    /api/certificates?status=&q=&template=&from=&to=&page=
POST   /api/certificates                 # emitir individual
POST   /api/certificates/bulk            # emisión masiva (CSV)
GET    /api/certificates/:id
POST   /api/certificates/:id/revoke
POST   /api/certificates/:id/replace
POST   /api/certificates/:id/resend
GET    /api/audit?certificateId=
POST   /api/signatures/request · /signatures/:id/sign
GET    /api/verify/:code                 # público
```

## API de TrustHunter (motor, prefijo `/v1`)

```
POST /v1/identities                      # crear identidad digital
POST /v1/seals        { fileHash }       # sellar documento → sealRef
POST /v1/anchors      { sealRef }        # anclar en blockchain (async) → anchorRef
GET  /v1/verify/:code                    # estado de confianza + integridad
POST /v1/signatures                      # eventos de firma
GET  /v1/events                          # historial de eventos de confianza
Webhooks: anchor.confirmed, seal.created, signature.completed
```

Respuestas simples y no técnicas hacia TrustFile:
```json
{ "estado": "valido", "confianza": "alta", "selladoEn": "2026-05-01T10:00:00Z",
  "anclado": true, "emisor": "Universidad Demo" }
```

---

## Alcance MVP

### TrustFile MVP
- [x] Alta de organización + marca
- [x] Gestión de plantillas
- [x] Emisión individual de certificados
- [x] Emisión masiva (CSV)
- [x] Repositorio con búsqueda y filtros
- [x] Página pública de verificación + QR
- [x] Auditoría
- [x] Acceso del destinatario
- [x] Revocación / reemplazo
- [x] Registro de integridad abstraído (vía TrustHunter)

### TrustHunter MVP
- [x] Generación de sello de documento
- [x] Abstracción de registro de prueba en blockchain
- [x] Endpoint de verificación
- [x] Registro de confianza por certificado
- [x] Log de eventos
- [x] Consola admin básica

---

## Orden de construcción (build order)

**Tanda 1 (ESTA ENTREGA) — TrustFile navegable con datos mock**
1. Design system (tokens, Button, Card, Badge, Input, Table, Stat, EmptyState…)
2. Layouts (shell de app: sidebar + topbar; layout público)
3. Landing + login + onboarding
4. Dashboard
5. Plantillas (lista)
6. Emisión (individual + masiva)
7. Repositorio + detalle de certificado
8. Página pública de verificación + QR
9. Auditoría, firmantes, configuración (estructura + estados vacíos)

**Tanda 2 — Backend real**
- Prisma + PostgreSQL, auth real, RBAC, API routes sobre la base de datos.
- Servicio de PDF y de email. Portal del destinatario funcional.

**Tanda 3 — TrustHunter**
- Servicio NestJS del motor, sellado + anclaje reales, consola interna, API pública, webhooks.

---

## Flujos UX clave (world-class)
- **A** Crear y emitir un certificado
- **B** Emitir muchos certificados (CSV)
- **C** Verificar un certificado existente
- **D** Revocar o reemplazar un certificado
- **E** Destinatario accede y comparte su certificado
- **F** Admin monitorea auditoría y estado de emisión
</content>
