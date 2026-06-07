import type {
  AuditEvent,
  Certificate,
  IssuanceBatch,
  Member,
  Organization,
  Template,
} from "./types";

/* ============================================================
   Capa de datos mock (en memoria).
   Sustituible por Prisma/PostgreSQL sin tocar la UI: las
   funciones `get*` son el contrato que consumirán los componentes.
   ============================================================ */

export const organization: Organization = {
  id: "org_1",
  name: "Instituto Aurora",
  slug: "instituto-aurora",
  type: "instituto",
  country: "México",
  contactEmail: "registro@institutoaurora.mx",
  branding: {
    logoText: "Instituto Aurora",
    primaryColor: "#3f49d4",
    secondaryColor: "#12b76a",
  },
};

export const members: Member[] = [
  { id: "u1", name: "Lucía Fernández", email: "lucia@institutoaurora.mx", role: "owner", lastActive: "Hace 2 h" },
  { id: "u2", name: "Marco Díaz", email: "marco@institutoaurora.mx", role: "admin", lastActive: "Ayer" },
  { id: "u3", name: "Sofía Ramírez", email: "sofia@institutoaurora.mx", role: "issuer", lastActive: "Hace 3 h" },
  { id: "u4", name: "Daniel Ortega", email: "daniel@institutoaurora.mx", role: "reviewer", lastActive: "Hace 1 día" },
  { id: "u5", name: "Dra. Elena Soto", email: "elena@institutoaurora.mx", role: "signer", lastActive: "Hace 5 días" },
];

export const templates: Template[] = [
  {
    id: "tpl_diploma",
    name: "Diploma de finalización",
    description: "Diploma oficial para cursos y diplomados completados.",
    status: "activo",
    accent: "#3f49d4",
    variables: [
      { key: "programa", label: "Programa", required: true },
      { key: "horas", label: "Horas lectivas", required: true },
      { key: "promedio", label: "Promedio", required: false },
    ],
    hasQr: true,
    hasSignature: true,
    hasSeal: true,
    certificatesIssued: 248,
    updatedAt: "2026-05-28",
  },
  {
    id: "tpl_constancia",
    name: "Constancia de estudios",
    description: "Constancia simple de participación o asistencia.",
    status: "activo",
    accent: "#12b76a",
    variables: [
      { key: "curso", label: "Curso", required: true },
      { key: "periodo", label: "Periodo", required: true },
    ],
    hasQr: true,
    hasSignature: false,
    hasSeal: true,
    certificatesIssued: 132,
    updatedAt: "2026-06-01",
  },
  {
    id: "tpl_reconocimiento",
    name: "Reconocimiento institucional",
    description: "Reconocimiento a la excelencia y participación destacada.",
    status: "activo",
    accent: "#f79009",
    variables: [
      { key: "motivo", label: "Motivo del reconocimiento", required: true },
    ],
    hasQr: true,
    hasSignature: true,
    hasSeal: true,
    certificatesIssued: 41,
    updatedAt: "2026-05-12",
  },
  {
    id: "tpl_taller",
    name: "Certificado de taller",
    description: "Plantilla en preparación para talleres cortos.",
    status: "borrador",
    accent: "#2e90fa",
    variables: [{ key: "taller", label: "Taller", required: true }],
    hasQr: true,
    hasSignature: false,
    hasSeal: false,
    certificatesIssued: 0,
    updatedAt: "2026-06-05",
  },
];

function trust(seed: string, anclado = true): Certificate["trust"] {
  return {
    confianza: anclado ? "alta" : "pendiente",
    selladoEn: anclado ? "2026-05-30T10:24:00Z" : null,
    anclado,
    fingerprint: "sha256:" + seed.padEnd(12, "0").slice(0, 12) + "…a1f9",
    sealRef: "seal_" + seed,
    anchorRef: anclado ? "anchor_" + seed + "_b7" : null,
  };
}

export const certificates: Certificate[] = [
  {
    id: "cert_1", code: "TF-7QK2-9MX4", title: "Diploma de finalización — Diplomado en Diseño UX",
    templateId: "tpl_diploma", templateName: "Diploma de finalización",
    recipientName: "Andrea Beltrán", recipientEmail: "andrea.beltran@gmail.com",
    status: "emitido", issueDate: "2026-05-30", expirationDate: null, issuedBy: "Sofía Ramírez",
    metadata: { programa: "Diplomado en Diseño UX", horas: "120", promedio: "9.4" },
    trust: trust("7qk29mx4"),
  },
  {
    id: "cert_2", code: "TF-3RP8-2KL9", title: "Constancia de estudios — Inglés B2",
    templateId: "tpl_constancia", templateName: "Constancia de estudios",
    recipientName: "Javier Núñez", recipientEmail: "jnunez@outlook.com",
    status: "emitido", issueDate: "2026-05-29", expirationDate: "2028-05-29", issuedBy: "Sofía Ramírez",
    metadata: { curso: "Inglés Nivel B2", periodo: "Ene–May 2026" },
    trust: trust("3rp82kl9"),
  },
  {
    id: "cert_3", code: "TF-1AA5-7BC0", title: "Reconocimiento institucional — Mejor proyecto final",
    templateId: "tpl_reconocimiento", templateName: "Reconocimiento institucional",
    recipientName: "Carolina Méndez", recipientEmail: "caro.mendez@gmail.com",
    status: "emitido", issueDate: "2026-05-20", expirationDate: null, issuedBy: "Marco Díaz",
    metadata: { motivo: "Mejor proyecto final de la generación 2026" },
    trust: trust("1aa57bc0"),
  },
  {
    id: "cert_4", code: "TF-9ZZ1-4QW7", title: "Diploma de finalización — Programación Web",
    templateId: "tpl_diploma", templateName: "Diploma de finalización",
    recipientName: "Roberto Lima", recipientEmail: "rlima@gmail.com",
    status: "revocado", issueDate: "2026-04-15", expirationDate: null, issuedBy: "Sofía Ramírez",
    metadata: { programa: "Programación Web Full-Stack", horas: "160" },
    trust: trust("9zz14qw7"),
  },
  {
    id: "cert_5", code: "TF-5MM3-8RT2", title: "Constancia de estudios — Excel Avanzado",
    templateId: "tpl_constancia", templateName: "Constancia de estudios",
    recipientName: "Patricia Salas", recipientEmail: "psalas@gmail.com",
    status: "emitido", issueDate: "2026-05-18", expirationDate: null, issuedBy: "Sofía Ramírez",
    metadata: { curso: "Excel Avanzado", periodo: "Abr 2026" },
    trust: trust("5mm38rt2"),
  },
  {
    id: "cert_6", code: "TF-6NN4-1YU8", title: "Diploma de finalización — Marketing Digital (v2)",
    templateId: "tpl_diploma", templateName: "Diploma de finalización",
    recipientName: "Hugo Paredes", recipientEmail: "hugo.p@gmail.com",
    status: "reemplazado", issueDate: "2026-03-10", expirationDate: null, issuedBy: "Marco Díaz",
    metadata: { programa: "Marketing Digital", horas: "90" },
    trust: trust("6nn41yu8"), replacedBy: "cert_7",
  },
  {
    id: "cert_7", code: "TF-8PP6-3IO5", title: "Diploma de finalización — Marketing Digital",
    templateId: "tpl_diploma", templateName: "Diploma de finalización",
    recipientName: "Hugo Paredes", recipientEmail: "hugo.p@gmail.com",
    status: "emitido", issueDate: "2026-05-02", expirationDate: null, issuedBy: "Marco Díaz",
    metadata: { programa: "Marketing Digital", horas: "90" },
    trust: trust("8pp63io5"),
  },
  {
    id: "cert_8", code: "TF-2QW9-6AS1", title: "Constancia de estudios — Diseño Gráfico",
    templateId: "tpl_constancia", templateName: "Constancia de estudios",
    recipientName: "Mariana Cruz", recipientEmail: "mcruz@gmail.com",
    status: "expirado", issueDate: "2024-05-01", expirationDate: "2026-05-01", issuedBy: "Sofía Ramírez",
    metadata: { curso: "Diseño Gráfico", periodo: "2024" },
    trust: trust("2qw96as1"),
  },
  {
    id: "cert_9", code: "TF-4ER7-0PL3", title: "Diploma de finalización — Ciencia de Datos",
    templateId: "tpl_diploma", templateName: "Diploma de finalización",
    recipientName: "Esteban Rivas", recipientEmail: "erivas@gmail.com",
    status: "borrador", issueDate: "2026-06-06", expirationDate: null, issuedBy: "Sofía Ramírez",
    metadata: { programa: "Ciencia de Datos", horas: "200" },
    trust: trust("4er70pl3", false),
  },
  {
    id: "cert_10", code: "TF-0TY2-5KJ6", title: "Reconocimiento institucional — Trayectoria docente",
    templateId: "tpl_reconocimiento", templateName: "Reconocimiento institucional",
    recipientName: "Prof. Alberto Reyes", recipientEmail: "areyes@institutoaurora.mx",
    status: "emitido", issueDate: "2026-04-28", expirationDate: null, issuedBy: "Lucía Fernández",
    metadata: { motivo: "20 años de trayectoria docente" },
    trust: trust("0ty25kj6"),
  },
];

export const auditEvents: AuditEvent[] = [
  { id: "a1", certificateId: "cert_1", certificateCode: "TF-7QK2-9MX4", actor: "Sofía Ramírez", actorType: "usuario", action: "emitido", detail: "Certificado emitido a Andrea Beltrán", createdAt: "2026-05-30T10:24:00Z" },
  { id: "a2", certificateId: "cert_1", certificateCode: "TF-7QK2-9MX4", actor: "Sistema TrustHunter", actorType: "sistema", action: "sellado", detail: "Documento sellado · integridad registrada", createdAt: "2026-05-30T10:24:05Z" },
  { id: "a3", certificateId: "cert_1", certificateCode: "TF-7QK2-9MX4", actor: "Sistema TrustHunter", actorType: "sistema", action: "anclado", detail: "Prueba de integridad anclada", createdAt: "2026-05-30T10:31:00Z" },
  { id: "a4", certificateId: "cert_4", certificateCode: "TF-9ZZ1-4QW7", actor: "Marco Díaz", actorType: "usuario", action: "revocado", detail: "Revocado por solicitud académica", createdAt: "2026-05-25T16:02:00Z" },
  { id: "a5", certificateCode: "TF-3RP8-2KL9", actor: "Verificador anónimo", actorType: "publico", action: "verificado", detail: "Verificación pública desde QR", createdAt: "2026-06-04T09:15:00Z" },
  { id: "a6", certificateId: "cert_6", certificateCode: "TF-6NN4-1YU8", actor: "Marco Díaz", actorType: "usuario", action: "reemplazado", detail: "Reemplazado por TF-8PP6-3IO5", createdAt: "2026-05-02T11:00:00Z" },
  { id: "a7", certificateId: "cert_3", certificateCode: "TF-1AA5-7BC0", actor: "Dra. Elena Soto", actorType: "usuario", action: "firmado", detail: "Firma aplicada al reconocimiento", createdAt: "2026-05-20T14:40:00Z" },
  { id: "a8", certificateId: "cert_2", certificateCode: "TF-3RP8-2KL9", actor: "Andrea Beltrán", actorType: "publico", action: "descargado", detail: "PDF descargado por el destinatario", createdAt: "2026-06-03T18:22:00Z" },
];

export const batches: IssuanceBatch[] = [
  { id: "b1", templateName: "Constancia de estudios", total: 84, succeeded: 84, failed: 0, status: "completado", createdBy: "Sofía Ramírez", createdAt: "2026-05-29T09:00:00Z" },
  { id: "b2", templateName: "Diploma de finalización", total: 52, succeeded: 50, failed: 2, status: "con_errores", createdBy: "Marco Díaz", createdAt: "2026-05-15T12:30:00Z" },
  { id: "b3", templateName: "Constancia de estudios", total: 30, succeeded: 0, failed: 0, status: "procesando", createdBy: "Sofía Ramírez", createdAt: "2026-06-07T08:10:00Z" },
];

/* ---------- Helpers de consulta (contrato de datos) ---------- */

export function getOrganization() {
  return organization;
}
export function getTemplates() {
  return templates;
}
export function getTemplate(id: string) {
  return templates.find((t) => t.id === id) ?? null;
}
export function getCertificates() {
  return certificates;
}
export function getCertificate(id: string) {
  return certificates.find((c) => c.id === id) ?? null;
}
export function getCertificateByCode(code: string) {
  return certificates.find((c) => c.code.toLowerCase() === code.toLowerCase()) ?? null;
}
export function getAuditForCertificate(id: string) {
  return auditEvents
    .filter((e) => e.certificateId === id)
    .sort((a, b) => +new Date(b.createdAt) - +new Date(a.createdAt));
}
export function getAuditEvents() {
  return [...auditEvents].sort((a, b) => +new Date(b.createdAt) - +new Date(a.createdAt));
}
export function getMembers() {
  return members;
}
export function getBatches() {
  return batches;
}

export function getDashboardStats() {
  const emitidos = certificates.filter((c) => c.status === "emitido").length;
  const borradores = certificates.filter((c) => c.status === "borrador").length;
  const revocados = certificates.filter(
    (c) => c.status === "revocado" || c.status === "reemplazado"
  ).length;
  const verificaciones = auditEvents.filter((e) => e.action === "verificado").length + 318;
  return {
    totalEmitidos: emitidos + 420,
    borradores,
    plantillasActivas: templates.filter((t) => t.status === "activo").length,
    revocados,
    verificaciones,
  };
}
