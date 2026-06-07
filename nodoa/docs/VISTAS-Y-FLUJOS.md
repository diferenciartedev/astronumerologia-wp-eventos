# Vistas, flujos y wireframes (para bocetar)

Guía para dibujar los bocetos **antes** de construir. Cada vista incluye:
propósito · zonas (wireframe en texto) · componentes · estados · acciones.
Idioma de producto: **español**.

Convención de los bocetos:
```
[ ... ]  bloque/zona      ( btn )  botón       {chip}  badge/estado
| col |  columna          ▦ tabla            ▭ tarjeta        ◯ avatar
```

---

## 0. Mapa de navegación (sitemap)

```
PÚBLICO
 /                     Landing de marca
 /login /registro /recuperar   Autenticación
 /verificar            Buscar certificado por código
 /verificar/[codigo]   Resultado de verificación
 /portal/[token]       Portal del destinatario (Fase 2)

ONBOARDING
 /onboarding           Alta de organización (asistente 3 pasos)

APLICACIÓN  (layout con sidebar)  /app
 /app                  Panel (dashboard)
 /app/certificados     Repositorio
 /app/certificados/[id]  Detalle de certificado
 /app/emitir           Emisión (individual | masiva)
 /app/plantillas       Plantillas (lista)
 /app/plantillas/[id]  Editor de plantilla
 /app/firmantes        Firmantes y solicitudes de firma
 /app/auditoria        Registro de auditoría
 /app/configuracion    Configuración (marca/equipo/verificación/motor)
```

Estructura del **layout de aplicación** (se repite en todo `/app/*`):
```
┌───────────┬───────────────────────────────────────────────┐
│ SIDEBAR   │ TOPBAR: ( buscar… )        ( + Emitir )  ◯ user │
│           ├───────────────────────────────────────────────┤
│ ▭ Org ▾   │                                                 │
│           │                CONTENIDO DE LA VISTA            │
│ Principal │                                                 │
│  Panel    │                                                 │
│  Certif.  │                                                 │
│  Emitir   │                                                 │
│ Gestión   │                                                 │
│  Plantillas                                                 │
│  Firmantes│                                                 │
│  Auditoría│                                                 │
│ Cuenta    │                                                 │
│  Config.  │                                                 │
│ ─────────  │                                                 │
│ (Verificar)│                                                 │
└───────────┴───────────────────────────────────────────────┘
```

---

## 1. Landing `/`

**Propósito:** comunicar el relato (confianza, control, validación fácil) y convertir.

```
[ Header: Logo   Producto · Cómo funciona · Verificar   (Iniciar sesión)(Comenzar) ]
[ HERO  {chip: para instituciones}                                                  ]
[   H1: "La forma más fácil de emitir y validar certificados confiables"            ]
[   sub + ( Empieza ahora )( Ver demo del panel )                                   ]
[   ▭ Mockup de certificado con {Documento verificado} y QR                         ]
[ PROBLEMA: 2 col → texto | lista de 4 dolores                                      ]
[ FEATURES: grid 3x2 (Emite rápido, Marca, Verificación, Menos fraude, Control…)    ]
[ CÓMO FUNCIONA: 3 pasos (Diseña → Emite → Comparte/verifica)                       ]
[ CTA final (banner) + Footer (Nodoa · TrustHunter)                                 ]
```
**Estados:** único. **Acción principal:** → `/registro`.

---

## 2. Autenticación `/login` · `/registro` · `/recuperar`

**Layout split:** izquierda = panel de marca con 3 beneficios; derecha = formulario.

```
┌───────────────────────┬──────────────────────────┐
│  PANEL MARCA (azul)    │  ▭ Formulario             │
│  Logo                  │  H1 + subtítulo           │
│  "Recupera la confianza"│  [ campos ]              │
│  ✓ beneficio 1         │  ( Acción primaria )      │
│  ✓ beneficio 2         │  enlace secundario        │
│  ✓ beneficio 3         │                          │
└───────────────────────┴──────────────────────────┘
```
- **/login:** correo, contraseña, recordarme, "¿olvidaste?". → `/app`.
- **/registro:** nombre, correo, contraseña. → `/onboarding`.
- **/recuperar:** correo → mensaje "enlace enviado".
**Estados:** normal · error de credenciales · enviando · enviado.

---

## 3. Onboarding `/onboarding` (asistente 3 pasos)

```
[ Logo                                   ( Guardar y salir ) ]
[ ①─Organización ──②─Marca ──③─Listo  (stepper) ]
┌──────────────────────────────────────────────────┐
│ Paso 1: nombre · tipo · país · correo de contacto │
│ Paso 2: color principal (swatches) + VISTA PREVIA │
│ Paso 3: ✓ "¡Todo listo!" ( Ir a mi panel )        │
│                              (Atrás)  (Continuar)  │
└──────────────────────────────────────────────────┘
```
**Estados por paso:** vacío/validación · preview en vivo (paso 2) · éxito (paso 3).

---

## 4. Panel `/app` (dashboard)

**Propósito:** estado general + accesos a las acciones más frecuentes.

```
[ H1 Panel                                        ( + Emitir certificado ) ]
[ ▭ Emitidos ][ ▭ Verificaciones ][ ▭ Borradores ][ ▭ Revocados ]   (4 stat cards)
[ ▭ ACCIONES RÁPIDAS  → grid de 6:                                          ]
[   Emitir · Importar y emitir · Crear plantilla · Verificar · Firmantes · Auditoría ]
┌─────────────────────────────┬─────────────────────────────┐
│ ▦ CERTIFICADOS RECIENTES     │ ▭ ACTIVIDAD RECIENTE         │
│ Destinatario·Plantilla·Fecha·{estado} │ ◯ icono + evento + actor·hora │
│ (Ver todos →)                │ (timeline de 6)              │
└─────────────────────────────┴─────────────────────────────┘
```
**Estados:** con datos · **vacío** (primer uso → "Emite tu primer certificado").

---

## 5. Repositorio `/app/certificados`

**Propósito:** encontrar y gestionar cualquier certificado.

```
[ H1 Certificados                          ( Exportar )( + Emitir ) ]
[ Tabs estado: Todos·Emitidos·Borradores·Revocados·Reemplazados·Expirados {conteo} ]
[ ( buscar nombre/correo/código… )           [⚙ filtro plantilla ▾] ]
[ ▦ TABLA:                                                          ]
[  Destinatario | Certificado(+código) | Emitido | Vigencia | {estado} | Confianza ]
[  fila → click → detalle                                          ]
[ "Mostrando X de Y"                                               ]
```
**Columnas ordenables**, filtros combinables (estado + plantilla + búsqueda).
**Estados:** lista · **sin resultados** (filtros) · vacío (sin certificados aún).
**Futuro:** paginación, selección múltiple (acciones en lote), guardar filtros.

---

## 6. Detalle de certificado `/app/certificados/[id]`

**Propósito:** ver todo de un certificado y operarlo.

```
[ ← Certificados ]
[ H1 Nombre destinatario  {estado}     ( Descargar PDF )( Ver validación )(⋯) ]
[ subtítulo (título) · código                                                  ]
┌──────────────────────────────┬───────────────────────┐
│ ▭ VISTA DEL CERTIFICADO        │ ▭ Confianza (alta/pend.)│
│   (preview fiel al PDF)         │ ▭ DETALLES (filas k/v)  │
│   (Página pública →)            │   destinatario, correo, │
│                                 │   plantilla, emisor,    │
│ ▭ HISTORIAL (timeline vertical) │   fechas, metadata…     │
│   ◯ creado/emitido/sellado/     │ ▭ ▸ Detalle técnico     │
│     anclado/verificado…         │   avanzado (colapsado)  │
└──────────────────────────────┴───────────────────────┘
```
**Menú (⋯):** Reenviar · Regenerar enlace · Duplicar · Reemplazar · **Revocar** (confirmación).
**Estados:** emitido · revocado (banner) · reemplazado (link a nuevo) · borrador (CTA emitir).
**Único lugar con jerga técnica:** el panel "Detalle técnico avanzado" (huella, sello, anclaje).

---

## 7. Emisión `/app/emitir`  ★ flujo estrella

**Selector de modo** arriba: `[ Individual ]  [ Masiva (CSV) ]`.

### 7a. Individual (2 columnas: formulario | preview en vivo)
```
┌───────────────────────────────┬────────────────────┐
│ 1· Elige plantilla (grid sel.)  │ VISTA PREVIA EN VIVO│
│ 2· Datos del destinatario:      │  ▭ certificado que  │
│    nombre·correo·fecha·vigencia │  se actualiza al    │
│    + campos dinámicos plantilla │  escribir           │
│ [ℹ se sella al emitir]          │                    │
│   ( Guardar borrador )( Emitir )│                    │
└───────────────────────────────┴────────────────────┘
   → ÉXITO: ✓ "¡Certificado emitido!" (Ver repositorio)(Emitir otro)
```

### 7b. Masiva (CSV)
```
[ 1· Plantilla a usar  ▾ ]
[ 2· Subir archivo:  (Descargar plantilla CSV)                ]
[   ▭ zona drag&drop "arrastra tu CSV"                         ]
[   → cargado: ✓ "84 filas, 0 errores" + ▦ vista previa filas  ]
[ {84 certificados}  ( Emitir 84 certificados )                ]
   → ÉXITO: ✓ "¡84 certificados emitidos!" (se envían por correo)
```
**Estados:** vacío · archivo cargado/validado · **con errores de validación** (filas marcadas) · procesando · éxito.

---

## 8. Plantillas `/app/plantillas` + editor `/[id]`

### Lista (grid de tarjetas)
```
[ H1 Plantillas                         ( + Nueva plantilla ) ]
[ ▭ tarjeta: mini-muestra · nombre · {Activa/Borrador} ·       ]
[   capacidades {QR}{Firma}{Sello} · "N emitidos" · editada    ]
[ ▭ ▭ ▭ … + ▭ "Crear nueva" (dashed)                          ]
```

### Editor (2 columnas: controles | preview)
```
┌───────────────────────┬────────────────────┐
│ ▭ Identidad: nombre·color│ VISTA PREVIA       │
│ ▭ Elementos de confianza │  ▭ certificado de  │
│   ⌧ QR ⌧ Firma ⌧ Sello   │  ejemplo con marca │
│ ▭ Campos dinámicos        │                    │
│   {{programa}} {oblig.}   │                    │
│   ( + Agregar )           │                    │
│        ( Guardar )        │                    │
└───────────────────────┴────────────────────┘
```
**Futuro:** editor de bloques drag&drop, posicionar logo/firma/QR.

---

## 9. Firmantes `/app/firmantes`

```
┌───────────────────────────────┬───────────────────┐
│ ▭ SOLICITUDES DE FIRMA          │ ▭ PERSONAS         │
│  ◯ doc · firmante · fecha {estado}│  AUTORIZADAS       │
│  (firmado / pendiente)          │  ◯ nombre · correo │
│                                 │  ( + Agregar )      │
└───────────────────────────────┴───────────────────┘
```
**Estados:** con solicitudes · vacío. **Flujo:** asignar firmante → enviar solicitud → seguimiento {pendiente→firmado}.

---

## 10. Auditoría `/app/auditoria`

```
[ H1 Auditoría                          ( Exportar registro ) ]
[ ▭ LISTA cronológica:                                         ]
[  ◯ icono-acción · detalle · actor · código(link) {Usuario/Sistema/Público} · fecha ]
```
Acciones con icono+color: creado/editado/emitido/firmado/sellado/anclado/verificado/descargado/reenviado/revocado/reemplazado.
**Futuro:** filtros por acción/actor/fecha, búsqueda.

---

## 11. Configuración `/app/configuracion` (nav lateral interna)

```
[ H1 Configuración ]
┌──────────────┬───────────────────────────────────┐
│ • Marca       │  Panel de la sección seleccionada  │
│ • Equipo y roles                                   │
│ • Verificación │  - Marca: nombre·tipo·país·color   │
│ • Motor de conf.│  - Equipo: ▦ miembros + {rol} (Invitar) │
│              │  - Verificación: mensaje·dominio·toggles│
│              │  - Motor: estado conexión + toggles    │
└──────────────┴───────────────────────────────────┘
```
**Estados:** lectura/edición · guardado. **Motor de confianza** = único lugar que menciona la API/integración.

---

## 12. Verificación pública `/verificar` + `/verificar/[codigo]`  ★ confianza

### Buscar (centrado, fondo con patrón)
```
[ Header simple: Logo            (Iniciar sesión) ]
[            ◯ icono escudo                        ]
[      H1 "Verifica un certificado"                ]
[      ( código TF-XXXX-XXXX )  ( Verificar )      ]
[      2 features (auténtico · QR)                 ]
```

### Resultado
```
[ Header: Logo            (Verificar otro) ]
[ ▭ VEREDICTO grande con color:                        ]
[   ✓ válido / ✕ revocado / ⚠ expirado / ↻ reemplazado / 🔍 no encontrado ]
┌───────────────────────────┬────────────────┐
│ ▭ DETALLES DEL DOCUMENTO    │ ▭ mini-preview │
│  Emitido por · Otorgado a · │   del cert.    │
│  certificado · fechas ·     │                │
│  metadata · código          │                │
└───────────────────────────┴────────────────┘
[ ▭ Mensaje de confianza (si válido) / aviso (si reemplazado) ]
```
**5 estados clave** = los 5 veredictos. Simple, creíble, **sin tecnicismos**.

---

## 13. Portal del destinatario `/portal/[token]` (Fase 2)

```
[ ◯ Logo institución ]
[ "Hola, Andrea" — Tu certificado está listo ]
[ ▭ preview + ( Descargar PDF )( Compartir enlace )( Verificar ) ]
[ {estado: activo/revocado/actualizado} ]
```

---

## FLUJOS CLAVE (paso a paso para bocetar la secuencia)

**A · Emitir un certificado**
`/app` →(+ Emitir)→ `/app/emitir` [Individual] → elegir plantilla → datos (preview en vivo) → ( Emitir ) → ✓ éxito → ( Ver repositorio ) `/app/certificados`.

**B · Emisión masiva**
`/app/emitir` [Masiva] → elegir plantilla → subir CSV → validación (filas/errores) → ( Emitir N ) → ✓ éxito (envío por correo).

**C · Verificar (tercero externo)**
QR/enlace o `/verificar` → escribe código → `/verificar/[codigo]` → veredicto + datos. (Sin login.)

**D · Revocar / reemplazar**
`/app/certificados/[id]` → (⋯) → Revocar → **confirmación** → {revocado} + evento en auditoría. (Reemplazar genera nuevo cert y enlaza el anterior.)

**E · Destinatario accede y comparte** (Fase 2)
Correo → `/portal/[token]` → descarga / comparte / verifica.

**F · Admin monitorea**
`/app` (métricas) → `/app/auditoria` (trazabilidad) → detalle de cert (historial).

---

## Notas transversales para los bocetos

- **Responsive:** sidebar colapsa a menú hamburguesa (<lg); grids 2-col pasan a 1-col; tablas con scroll horizontal.
- **Estados a dibujar siempre:** vacío · cargando (skeleton) · con datos · error · éxito.
- **Jerarquía:** 1 acción primaria por vista (botón sólido azul); resto secundarias.
- **Badges de estado** con color consistente: emitido=verde, borrador=neutro, revocado=rojo, reemplazado/expirado=ámbar.
- **Lo técnico (hash/sello/anclaje)** solo en: panel "Detalle técnico avanzado" y sección "Motor de confianza". Nunca en flujos principales.
```
```
