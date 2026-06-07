# Modelo de datos (propuesta)

Multi-tenant: todo cuelga de `Organization`. Esquema pensado para Prisma/PostgreSQL.

## Entidades principales

### Organization
`id, name, slug, type (universidad|instituto|escuela|academia|centro_formacion|certificadora|empresa),
country, contactEmail, contactPhone, branding(jsonb: logoUrl, primaryColor, secondaryColor, font),
trustIdentityId (FK → TrustIdentity), plan, createdAt`

### User
`id, name, email, passwordHash, status, createdAt`

### Membership  (User ↔ Organization, N:M con rol)
`id, userId, organizationId, role (owner|admin|issuer|reviewer|signer|viewer), invitedAt, acceptedAt`

### Template
`id, organizationId, name, description, status (borrador|activo|archivado),
design(jsonb: layout, colors, typography, blocks[]), variables(jsonb: [{key,label,required}]),
hasQr, hasSignatureBlock, hasSeal, version, createdBy, createdAt, updatedAt`

### Recipient
`id, organizationId, fullName, email, externalId, documentId, metadata(jsonb), createdAt`

### Certificate
`id, organizationId, templateId, recipientId, code (público, único),
title, status (borrador|emitido|revocado|reemplazado|expirado),
issueDate, expirationDate, metadata(jsonb), pdfUrl, verificationUrl,
trustRecordId (FK → TrustRecord), replacedByCertificateId, issuedBy, createdAt, updatedAt`

### IssuanceBatch  (emisión masiva)
`id, organizationId, templateId, source (csv|manual), total, succeeded, failed,
status (procesando|completado|con_errores), createdBy, createdAt`

### Signer / SignatureRequest
`Signer: id, organizationId, name, email, role`
`SignatureRequest: id, certificateId, signerId, status (pendiente|firmado|rechazado),
requestedAt, signedAt, evidence(jsonb)`

### AuditEvent  (eventos de confianza)
`id, organizationId, certificateId?, actorId?, actorType (usuario|sistema|público),
action (creado|editado|emitido|firmado|sellado|anclado|verificado|descargado|reenviado|revocado|reemplazado),
metadata(jsonb), ip, userAgent, createdAt`

## Entidades de TrustHunter (motor)

### TrustIdentity
Identidad digital confiable para organización / emisor / documento.
`id, subjectType (organizacion|emisor|documento), subjectRef, publicRef, status, createdAt`

### TrustRecord  (sello de integridad de un documento)
`id, organizationId, certificateCode, fingerprint (sha-256 del PDF), sealRef,
anchorStatus (pendiente|anclado|fallido), anchorRef (tx/lote blockchain abstraído),
anchoredAt, createdAt`

### TrustEvent
Espejo de eventos relevantes para el motor: `id, recordId, type, payload(jsonb), createdAt`

## Relaciones clave

```
Organization 1─N Membership N─1 User
Organization 1─N Template 1─N Certificate N─1 Recipient
Certificate  1─1 TrustRecord   (sello + anclaje)
Certificate  1─N SignatureRequest N─1 Signer
Certificate  1─N AuditEvent
Organization 1─1 TrustIdentity
```

## Estados del certificado (máquina de estados)

```
borrador ──emitir──▶ emitido ──revocar──▶ revocado
                       │
                       ├──reemplazar──▶ reemplazado (genera nuevo certificado)
                       └──(fecha)─────▶ expirado
```
</content>
