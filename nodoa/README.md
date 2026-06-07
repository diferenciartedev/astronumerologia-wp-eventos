# Nodoa · TrustFile + TrustHunter

Plataforma SaaS para emitir, gestionar y validar **certificados y documentos confiables**.

- **Nodoa** — la empresa.
- **TrustFile** — el producto que usan las organizaciones (esta entrega).
- **TrustHunter** — el motor de confianza que da integridad y verificación por detrás (Fase 2).

> Idioma de producto: **español**. La tecnología (sellado, integridad, blockchain) vive
> por detrás; la experiencia de usuario nunca es técnica.

## Estructura

```
nodoa/
├── apps/
│   └── trustfile-web/     # App principal (Next.js 16 + Tailwind v4) ← navegable
├── docs/
│   ├── ARQUITECTURA.md    # Arquitectura de producto y stack
│   ├── MODELO-DE-DATOS.md # Esquema de datos (Prisma/PostgreSQL)
│   └── MVP-Y-PLAN.md      # Rutas, API, MVP y orden de construcción
└── (Fase 2) services/, packages/
```

## Cómo correr TrustFile

```bash
cd apps/trustfile-web
pnpm install
pnpm dev          # http://localhost:3000
```

La app arranca con **datos mock en memoria** (sin base de datos), para navegar el producto
de inmediato. La capa `src/lib/trust-engine.ts` es la frontera hacia TrustHunter: hoy es mock,
mañana son llamadas HTTP reales sin cambiar la UI.

## Qué está construido (Tanda 1)

Sistema de diseño + estas pantallas, todas en español y navegables:

| Flujo | Ruta |
|---|---|
| Landing de marca | `/` |
| Autenticación | `/login` · `/registro` · `/recuperar` |
| Onboarding de organización | `/onboarding` |
| Panel (dashboard) | `/app` |
| Repositorio de certificados | `/app/certificados` |
| Detalle de certificado | `/app/certificados/[id]` |
| Emisión individual y masiva | `/app/emitir` |
| Plantillas | `/app/plantillas` · `/app/plantillas/[id]` |
| Firmantes | `/app/firmantes` |
| Auditoría | `/app/auditoria` |
| Configuración | `/app/configuracion` |
| Verificación pública | `/verificar` · `/verificar/[codigo]` |

## Siguiente (ver docs/MVP-Y-PLAN.md)

- **Tanda 2:** Prisma + PostgreSQL, auth real con RBAC, servicio de PDF y email, portal del destinatario.
- **Tanda 3:** servicio TrustHunter (NestJS), sellado/anclaje reales, consola interna y API pública.
