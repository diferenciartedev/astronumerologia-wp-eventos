# Nodoa · Arquitectura de producto

> **Nodoa** (empresa) → **TrustHunter** (motor de confianza / API) → **TrustFile** (producto que usan las organizaciones).

Documento maestro de arquitectura. Idioma de producto: **español**.

---

## 1. Resumen del producto

**TrustFile** ayuda a las organizaciones a **recuperar la confianza en sus documentos y credenciales**.
Resuelve un problema de negocio real: emitir certificados y documentos que generen confianza real,
reduzcan el fraude, mejoren el control y hagan la validación sencilla.

El usuario debe sentir: *esto es fácil, me ahorra tiempo, reduce el fraude, me da control,
me ayuda a emitir certificados confiables y la verificación es simple.*

La tecnología (blockchain, hashing, identidad digital, validación de integridad) es **infraestructura
de confianza que vive por detrás**. Nunca es el relato del producto.

### Arquitectura de marca
- **Nodoa** — la empresa que desarrolla la plataforma.
- **TrustHunter** — el motor/API: identidad digital, sellado de documentos, registro en blockchain,
  validación de integridad y servicios de confianza.
- **TrustFile** — el producto final donde las organizaciones emiten, gestionan, almacenan, validan
  y controlan certificados y documentos confiables.

---

## 2. Arquitectura de producto (capas)

```
┌──────────────────────────────────────────────────────────────────┐
│  TrustFile Web (Next.js)         Portal del destinatario           │
│  Panel de la organización        Página pública de verificación    │
└───────────────┬──────────────────────────────┬─────────────────────┘
                │  (BFF / API routes)            │  (verificación pública)
┌───────────────▼──────────────────────────────▼─────────────────────┐
│                       TrustFile API (dominio)                       │
│  Organizaciones · Plantillas · Certificados · Destinatarios ·       │
│  Emisión · Auditoría · Firmas · Roles/permisos                      │
└───────────────┬─────────────────────────────────────────────────────┘
                │   (capa de integración con el motor)
┌───────────────▼─────────────────────────────────────────────────────┐
│                    TrustHunter API (motor de confianza)             │
│  Identidad digital · Sellado de documentos · Anclaje blockchain ·   │
│  Verificación · Servicios de firma · Historial de eventos de confianza │
└───────────────┬─────────────────────────────────────────────────────┘
                │
        ┌───────┴───────┬───────────────┬───────────────┐
   PDF service   Notification svc   Object storage   Blockchain anchor
```

**Principio clave:** TrustFile nunca habla con la blockchain directamente. Habla con TrustHunter
mediante una **capa de integración** (`trust-engine`) con interfaz estable. En el MVP esa capa tiene
una implementación *mock/local* (sello = hash SHA-256 + referencia simulada) que luego se reemplaza
por llamadas reales a TrustHunter sin tocar la UI.

---

## 3. Stack técnico (recomendado y justificado)

| Capa | Elección | Justificación |
|---|---|---|
| Frontend / BFF | **Next.js (App Router) + TypeScript** | SSR + RSC para tablas grandes y páginas públicas rápidas; un solo lenguaje en todo el front. |
| Estilos | **Tailwind CSS v4 + design tokens** | Sistema de diseño consistente, bajo coste de mantenimiento, look premium. |
| Backend dominio | **Next API routes (MVP) → NestJS** | Empezar full-stack en una base de código; extraer a NestJS cuando se necesite escalar/separar. |
| Base de datos | **PostgreSQL + Prisma** | Relacional, auditable, transaccional; Prisma da tipado end-to-end. |
| Cola / jobs | **Redis + BullMQ** | Emisión masiva, generación de PDF, sellado y anclaje son procesos pesados → asíncronos. |
| Almacenamiento | **S3-compatible** | PDFs y assets de marca. |
| Auth | **Auth con sesiones + RBAC** (NextAuth/Lucia o propio) | Multi-tenant, roles por organización. |
| PDF | **Servicio de render (Playwright/HTML→PDF)** | Plantillas HTML → PDF de alta fidelidad. |
| QR | **Generación de QR** apuntando a la página pública de verificación. |
| Email | **Proveedor transaccional** (Resend/SES) | Envío de credenciales y solicitudes de firma. |
| Motor de confianza | **TrustHunter API (NestJS)** | Servicio independiente, vendible como API a terceros. |

> En esta primera entrega el frontend corre **con datos mock en memoria**, sin Postgres ni Redis,
> para poder navegar el producto de inmediato (`pnpm dev`). El esquema Prisma y la capa de integración
> ya están definidos para conectar el backend real sin reescribir la UI.

---

## 4. Estructura de monorepo

```
nodoa/
├── apps/
│   ├── trustfile-web/         # Producto principal (Next.js) — ESTA ENTREGA
│   └── trusthunter-console/   # Panel interno del motor (Fase 2)
├── services/
│   ├── trusthunter-api/       # Motor de confianza (Fase 2)
│   ├── pdf-service/           # Render HTML → PDF (Fase 2)
│   └── notification-service/  # Email / notificaciones (Fase 2)
├── packages/
│   ├── ui-system/             # Componentes compartidos (se extrae de trustfile-web)
│   ├── shared-types/          # Tipos de dominio compartidos
│   └── trust-engine-client/   # SDK de integración TrustFile ↔ TrustHunter
└── docs/                      # Arquitectura, modelo de datos, API, plan
```

En esta entrega vive todo dentro de `apps/trustfile-web` (design system + dominio mock) para iterar
rápido; los `packages/` se extraen cuando se estabilicen.

---

## 5. Roles y permisos (RBAC multi-tenant)

| Rol | Puede |
|---|---|
| **Propietario (owner)** | Todo, incluida facturación y borrado de organización. |
| **Administrador** | Gestionar equipo, plantillas, emitir, revocar, configuración. |
| **Emisor** | Crear y emitir certificados, gestionar destinatarios. |
| **Revisor** | Aprobar/rechazar borradores antes de emitir. |
| **Firmante** | Firmar documentos asignados. |
| **Visor** | Solo lectura de repositorio y auditoría. |

---

## 6. Eventos de confianza (auditoría)

Cada acción relevante genera un **evento de confianza** inmutable: `creado`, `editado`, `emitido`,
`firmado`, `sellado`, `anclado`, `verificado`, `descargado`, `reenviado`, `revocado`, `reemplazado`.
Estos eventos alimentan: (a) la línea de tiempo del certificado, (b) el log de auditoría de la
organización y (c) el historial de eventos de TrustHunter.

---

## 7. Tono y lenguaje de producto

Usar lenguaje de negocio claro: *Emitir certificado, Verificar certificado, Historial del certificado,
Documento confiable, Destinatario, Estado de validación, Auditoría, Firmar documento, Plantillas.*

Evitar en la UI principal: *hash, ancla criptográfica, prueba descentralizada* — sólo en paneles
"Detalle técnico avanzado".
</content>
</invoke>
