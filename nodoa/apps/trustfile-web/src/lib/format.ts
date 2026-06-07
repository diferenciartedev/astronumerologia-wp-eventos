/** Utilidades de formato en español (es-MX). */

export function formatDate(iso: string | null, opts?: Intl.DateTimeFormatOptions): string {
  if (!iso) return "—";
  return new Intl.DateTimeFormat("es-MX", opts ?? { day: "numeric", month: "short", year: "numeric" }).format(
    new Date(iso)
  );
}

export function formatDateTime(iso: string): string {
  return new Intl.DateTimeFormat("es-MX", {
    day: "numeric",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(new Date(iso));
}

export function formatNumber(n: number): string {
  return new Intl.NumberFormat("es-MX").format(n);
}

const ROLE_LABELS: Record<string, string> = {
  owner: "Propietario",
  admin: "Administrador",
  issuer: "Emisor",
  reviewer: "Revisor",
  signer: "Firmante",
  viewer: "Visor",
};
export function roleLabel(role: string): string {
  return ROLE_LABELS[role] ?? role;
}

export function initials(name: string): string {
  return name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((w) => w[0])
    .join("")
    .toUpperCase();
}
