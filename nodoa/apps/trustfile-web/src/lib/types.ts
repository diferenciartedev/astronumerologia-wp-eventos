/** Tipos de dominio de TrustFile (compartibles a packages/shared-types). */

export type OrgType =
  | "universidad"
  | "instituto"
  | "escuela"
  | "academia"
  | "centro_formacion"
  | "certificadora"
  | "empresa";

export type Role = "owner" | "admin" | "issuer" | "reviewer" | "signer" | "viewer";

export type CertStatus = "borrador" | "emitido" | "revocado" | "reemplazado" | "expirado";

export type AuditAction =
  | "creado"
  | "editado"
  | "emitido"
  | "firmado"
  | "sellado"
  | "anclado"
  | "verificado"
  | "descargado"
  | "reenviado"
  | "revocado"
  | "reemplazado";

export interface Branding {
  logoText: string;
  primaryColor: string;
  secondaryColor: string;
}

export interface Organization {
  id: string;
  name: string;
  slug: string;
  type: OrgType;
  country: string;
  contactEmail: string;
  branding: Branding;
}

export interface Member {
  id: string;
  name: string;
  email: string;
  role: Role;
  lastActive?: string;
}

export interface Template {
  id: string;
  name: string;
  description: string;
  status: "borrador" | "activo" | "archivado";
  accent: string;
  variables: { key: string; label: string; required: boolean }[];
  hasQr: boolean;
  hasSignature: boolean;
  hasSeal: boolean;
  certificatesIssued: number;
  updatedAt: string;
}

export interface TrustInfo {
  /** Confianza percibida (no técnica) para la UI principal. */
  confianza: "alta" | "media" | "pendiente";
  selladoEn: string | null;
  anclado: boolean;
  /** Detalle técnico avanzado (oculto por defecto en la UI). */
  fingerprint: string;
  sealRef: string;
  anchorRef: string | null;
}

export interface Certificate {
  id: string;
  code: string;
  title: string;
  templateId: string;
  templateName: string;
  recipientName: string;
  recipientEmail: string;
  status: CertStatus;
  issueDate: string;
  expirationDate: string | null;
  issuedBy: string;
  metadata: Record<string, string>;
  trust: TrustInfo;
  replacedBy?: string;
}

export interface AuditEvent {
  id: string;
  certificateId?: string;
  certificateCode?: string;
  actor: string;
  actorType: "usuario" | "sistema" | "publico";
  action: AuditAction;
  detail: string;
  createdAt: string;
}

export interface IssuanceBatch {
  id: string;
  templateName: string;
  total: number;
  succeeded: number;
  failed: number;
  status: "procesando" | "completado" | "con_errores";
  createdBy: string;
  createdAt: string;
}
