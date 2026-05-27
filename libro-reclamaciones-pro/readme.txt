=== Libro de Reclamaciones PRO ===
Contributors: machadata, carlosmacha
Tags: libro de reclamaciones, reclamos, peru, indecopi, formulario
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Libro de Reclamaciones Digital profesional para WordPress. Configurable por rubro, con plantillas, notificaciones por correo y panel de gestión.

== Description ==

Plugin profesional para implementar un **Libro de Reclamaciones Digital** en sitios WordPress, preparado para Perú y modular para otros países.

Características del MVP:

* Configuración general del negocio (razón social, RUC, logo, etc.).
* Selector de rubro con plantillas listas (Retail, Ecommerce, Restaurante, Hotel, Clínica, Laboratorio, Veterinaria, Educación, Inmobiliaria, etc.).
* Formulario público vía shortcode `[libro_reclamaciones_pro]`.
* Vista previa del formulario en el panel.
* Generación de código único correlativo (LR-AAAA-NNNNNN).
* Guardado en tablas propias con `dbDelta`.
* Notificaciones por correo a la empresa y confirmación al consumidor.
* Panel administrativo con dashboard, lista de reclamos, detalle, historial y cambio de estados.
* Exportación CSV con filtros.
* Captura de IP, user agent, fecha/hora y fecha límite de respuesta.
* Seguridad: nonces, sanitización, escape, prepared statements, capabilities, validación de archivos.

== Installation ==

1. Sube la carpeta `libro-reclamaciones-pro` a `/wp-content/plugins/`.
2. Activa el plugin desde el menú **Plugins** en WordPress.
3. Ve a **Libro de Reclamaciones → Configuración** y completa los datos del negocio.
4. Selecciona la plantilla por rubro en **Plantillas**.
5. Configura los correos en **Correos y notificaciones**.
6. Inserta el shortcode `[libro_reclamaciones_pro]` en la página `/libro-de-reclamaciones`.

== Changelog ==

= 1.0.0 =
* Versión inicial MVP: configuración, plantillas, formulario público, correos, panel admin, exportación CSV.

== Author ==

Desarrollado por **MachaData · Carlos Macha** — https://machadata.com
