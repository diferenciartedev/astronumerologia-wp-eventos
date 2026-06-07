# Flujos paso a paso (click-by-click)

Recorrido detallado de cada flujo clave, pantalla por pantalla. Para cada paso:
**Ve** (qué muestra la pantalla) · **Hace** (acción del usuario) · **Pasa** (resultado/estado).

Notación:
```
▸ PANTALLA (ruta)        🟦 acción primaria      ⏳ estado de carga
✅ éxito   ⚠️ error   {badge}   →  navega a
```

Leyenda de actores:
- **Emisor/Admin** = usuario de la organización (con sesión).
- **Verificador** = tercero externo, sin sesión.
- **Destinatario** = quien recibe el certificado.

---

## FLUJO A · Emitir un certificado individual
**Actor:** Emisor · **Meta:** entregar 1 certificado confiable. · **Pantallas:** 4 (+1 éxito).

**A1 ▸ Panel `/app`**
- **Ve:** métricas, acciones rápidas, certificados recientes.
- **Hace:** clic en 🟦 **( + Emitir certificado )** (topbar o acción rápida).
- **Pasa:** → `/app/emitir`.

**A2 ▸ Emitir `/app/emitir` — selector de modo**
- **Ve:** dos tarjetas: **Individual** (seleccionada) | **Masiva (CSV)**.
- **Hace:** deja **Individual**.
- **Pasa:** muestra el formulario de 2 columnas (form | preview en vivo).

**A3 ▸ Paso 1 · Elegir plantilla**
- **Ve:** grid de plantillas activas (Diploma, Constancia, Reconocimiento…).
- **Hace:** clic en **Diploma de finalización**.
- **Pasa:** la tarjeta se marca {✓}; el **preview** adopta el color/acento de esa plantilla; aparecen sus **campos dinámicos** (programa, horas…).

**A4 ▸ Paso 2 · Datos del destinatario**
- **Ve:** campos nombre · correo · fecha de emisión · vigencia + campos dinámicos.
- **Hace:** escribe "Andrea Beltrán", correo, "Diplomado en Diseño UX", 120 horas.
- **Pasa:** ✨ el **preview de la derecha se actualiza en vivo** (nombre, título, horas). El botón **Emitir** se habilita al tener nombre.

**A5 ▸ Decisión: borrador o emitir**
- **Ve:** aviso "ℹ al emitir, el documento se sella y registra automáticamente".
- **Hace (opción 1):** ( Guardar borrador ) → cert queda {Borrador}, sin sellar.
- **Hace (opción 2):** 🟦 **( Emitir certificado )**.
- **Pasa:** ⏳ se genera código + QR, se **sella** (TrustHunter) y se registra evento.

**A6 ✅ Éxito**
- **Ve:** "¡Certificado emitido!" + "ya puede verificarse públicamente".
- **Hace:** ( Ver en el repositorio ) o ( Emitir otro ).
- **Pasa:** → `/app/certificados` (con el nuevo arriba) o reinicia el formulario.

**Errores/estados:** nombre vacío → botón deshabilitado · correo inválido → validación de campo · fallo de sellado → reintentar (queda {Borrador}).

---

## FLUJO B · Emisión masiva (CSV)
**Actor:** Emisor · **Meta:** emitir muchos a la vez. · **Pantallas:** 3 (+éxito).

**B1 ▸ `/app/emitir` → modo Masiva**
- **Ve:** selector de modo. **Hace:** clic en **Masiva (CSV)**.
- **Pasa:** cambia a la vista de carga por archivo.

**B2 ▸ Paso 1 · Plantilla**
- **Ve:** desplegable de plantilla. **Hace:** elige "Constancia de estudios".
- **Pasa:** define qué columnas se esperan en el CSV.

**B3 ▸ Paso 2 · Subir archivo**
- **Ve:** zona drag&drop + ( Descargar plantilla CSV ).
- **Hace (recomendado):** descarga la plantilla CSV, la llena, la arrastra.
- **Hace:** suelta `destinatarios.csv`.
- **Pasa:** ⏳ se valida → ✅ "84 filas detectadas, 0 con errores" + **vista previa** de las primeras filas (nombre, correo, campos).

**B3b ⚠️ Estado con errores (ramificación)**
- **Pasa:** "82 válidas · 2 con errores" + filas marcadas (correo faltante, campo vacío).
- **Hace:** corrige el CSV y vuelve a subir, **o** emite solo las válidas.

**B4 ▸ Confirmar emisión**
- **Ve:** {84 certificados} + "se enviarán por correo automáticamente".
- **Hace:** 🟦 **( Emitir 84 certificados )**.
- **Pasa:** ⏳ se procesa en lote (sella + registra + encola correos).

**B5 ✅ Éxito**
- **Ve:** "¡84 certificados emitidos!" — se están enviando por correo.
- **Hace:** ( Ver en el repositorio ).
- **Pasa:** aparecen los 84; el lote queda en historial (`procesando → completado`).

---

## FLUJO C · Verificar un certificado (tercero externo)
**Actor:** Verificador (sin sesión) · **Meta:** confirmar autenticidad. · **Pantallas:** 1–2.

**C0 · Punto de entrada**
- Desde el **QR** del documento → abre directo `/verificar/TF-7QK2-9MX4`, **o**
- Desde la web → `/verificar`.

**C1 ▸ Buscar `/verificar`**
- **Ve:** caja "código TF-XXXX-XXXX" + 2 mensajes de confianza.
- **Hace:** escribe el código y 🟦 **( Verificar )**.
- **Pasa:** → `/verificar/[codigo]`.

**C2 ▸ Resultado `/verificar/[codigo]` — 5 veredictos posibles**
- **✅ Válido:** banner verde "Certificado válido" + emisor, destinatario, certificado, fechas, metadata + **mensaje "Confianza verificada"** + mini-preview.
- **✕ Revocado:** banner rojo "Certificado revocado · ya no es válido".
- **⚠️ Expirado:** banner ámbar "fue auténtico pero su vigencia terminó".
- **↻ Reemplazado:** banner ámbar + aviso "existe una versión más reciente".
- **🔍 No encontrado:** "No encontramos este certificado" + ( Intentar de nuevo ).
- **Hace:** (opcional) ( Verificar otro ) → vuelve a `/verificar`.
- **Pasa:** la verificación queda registrada en la auditoría de la organización (actor: Público).

**Clave UX:** todo en lenguaje simple; cero tecnicismos; carga en < 1 s.

---

## FLUJO D · Revocar o reemplazar un certificado
**Actor:** Admin/Emisor · **Meta:** anular o sustituir un documento. · **Pantallas:** 2 + modal.

**D1 ▸ Llegar al certificado**
- **Hace:** en `/app/certificados` busca a la persona → clic en la fila.
- **Pasa:** → `/app/certificados/[id]`.

**D2 ▸ Detalle `/app/certificados/[id]`**
- **Ve:** preview, detalles, historial, botones + menú **(⋯)**.
- **Hace:** clic en **(⋯)** → se despliega menú: Reenviar · Regenerar enlace · Duplicar · Reemplazar · **Revocar**.

**D3a ▸ Revocar (modal de confirmación)**
- **Hace:** clic en **Revocar certificado**.
- **Ve:** modal "Revocar este certificado" — explica consecuencia (deja de ser válido, queda en auditoría).
- **Hace:** 🟦 **( Sí, revocar )** (o Cancelar).
- **Pasa:** estado → {Revocado}; se crea evento "revocado"; la **página pública** ahora muestra "revocado"; toast de confirmación.

**D3b ▸ Reemplazar (ramificación)**
- **Hace:** clic en **Reemplazar**.
- **Pasa:** se abre el flujo de emisión precargado con los datos; al emitir, el viejo queda {Reemplazado} y enlaza al nuevo (`replacedBy`). La verificación del viejo muestra "reemplazado · hay versión más reciente".

**Otras acciones del (⋯):** Reenviar (correo al destinatario) · Regenerar enlace de acceso · Duplicar (crea borrador). Cada una registra su evento.

---

## FLUJO E · El destinatario accede y comparte (Fase 2)
**Actor:** Destinatario · **Meta:** obtener, descargar y compartir su certificado. · **Pantallas:** 1.

**E1 · Recibe el correo**
- **Ve:** correo de la institución "Tu certificado está listo" + ( Ver mi certificado ).
- **Hace:** clic en el botón.
- **Pasa:** → `/portal/[token]` (acceso seguro por enlace, sin contraseña).

**E2 ▸ Portal `/portal/[token]`**
- **Ve:** "Hola, Andrea" + preview del certificado + {estado activo} + acciones.
- **Hace:** ( Descargar PDF ) · ( Compartir enlace de verificación ) · ( Verificar ).
- **Pasa:** descarga el PDF / copia el enlace público / abre la verificación. Cada descarga queda en auditoría.

**Estados:** activo · revocado (aviso) · actualizado (link a la nueva versión).

---

## FLUJO F · El admin monitorea (control y trazabilidad)
**Actor:** Admin · **Meta:** entender qué pasó y con qué estado. · **Pantallas:** 3.

**F1 ▸ Panel `/app`**
- **Ve:** métricas (emitidos, verificaciones, borradores, revocados) + actividad reciente.
- **Hace:** detecta algo (p. ej. pico de verificaciones) → clic en ( Ver auditoría ).

**F2 ▸ Auditoría `/app/auditoria`**
- **Ve:** línea de tiempo de eventos con icono, actor, código (link) y {Usuario/Sistema/Público} + fecha.
- **Hace:** clic en un código → va al detalle del certificado.
- **Pasa:** → `/app/certificados/[id]`.

**F3 ▸ Detalle → Historial**
- **Ve:** la línea de tiempo específica de ese documento (creado→emitido→sellado→anclado→verificado…).
- **Hace:** (opcional) abre "Detalle técnico avanzado" para auditoría profunda.
- **Pasa:** confirma la trazabilidad completa de un extremo a otro.

---

## Diagrama de navegación entre flujos

```
                 ┌────────────── /app (Panel) ──────────────┐
                 │        │            │           │         │
          (+Emitir)   (Certificados) (Auditoría) (Plantillas)(Config)
                 │        │            │
      ┌──────────┘        │            └─────► F: ver evento ─► código ─┐
      ▼                   ▼                                              ▼
 /app/emitir        /app/certificados                         /app/certificados/[id]
  ├ Individual ─►✅  └─ fila ─────────────────────────────────►  ├ (⋯) Revocar ─► modal ─► {Revocado}
  └ Masiva ────►✅                                                ├ (⋯) Reemplazar ─► /app/emitir (precargado)
                                                                  └ ( Ver validación ) ─► /verificar/[codigo]

  PÚBLICO (sin sesión):  QR / enlace ─► /verificar ─► /verificar/[codigo] ─► {veredicto}
  DESTINATARIO:          correo ─► /portal/[token] ─► descargar / compartir / verificar
```

---

## Checklist de pantallas/estados a prototipar (resumen)

| Flujo | Pantallas | Estados especiales a dibujar |
|---|---|---|
| A Emitir | 4 + éxito | preview en vivo, botón deshabilitado, sellado ⏳, éxito |
| B Masiva | 3 + éxito | drag&drop vacío, validado OK, **con errores**, procesando, éxito |
| C Verificar | 1–2 | 5 veredictos (válido/revocado/expirado/reemplazado/no encontrado) |
| D Revocar/Reemplazar | 2 + modal | menú (⋯), modal confirmación, toast, estado resultante |
| E Destinatario | 1 | activo / revocado / actualizado |
| F Monitorear | 3 | timeline general y por-certificado |
