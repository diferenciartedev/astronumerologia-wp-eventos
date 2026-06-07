import { notFound } from "next/navigation";
import Link from "next/link";
import {
  ArrowLeft,
  ExternalLink,
  ShieldCheck,
  ChevronRight,
  Lock,
} from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { StatusBadge, Badge } from "@/components/ui/badge";
import { ButtonLink } from "@/components/ui/button";
import { CertificatePreview } from "@/components/certificate-preview";
import { CertificateActions } from "@/components/certificate-actions";
import { AuditIcon } from "@/components/audit-icon";
import {
  getCertificate,
  getAuditForCertificate,
  getOrganization,
  getTemplate,
} from "@/lib/mock-data";
import { formatDate, formatDateTime } from "@/lib/format";

export default async function CertificateDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const cert = getCertificate(id);
  if (!cert) notFound();

  const org = getOrganization();
  const template = getTemplate(cert.templateId);
  const timeline = getAuditForCertificate(cert.id);

  return (
    <div className="space-y-6">
      {/* Breadcrumb */}
      <Link
        href="/app/certificados"
        className="inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-ink"
      >
        <ArrowLeft className="h-4 w-4" /> Certificados
      </Link>

      <div className="flex flex-wrap items-start justify-between gap-4">
        <div>
          <div className="flex items-center gap-3">
            <h1 className="text-[22px] font-semibold tracking-tight text-ink">{cert.recipientName}</h1>
            <StatusBadge status={cert.status} />
          </div>
          <p className="mt-1 text-sm text-muted">{cert.title}</p>
          <p className="mt-0.5 font-mono text-xs text-faint">{cert.code}</p>
        </div>
        <CertificateActions certificate={cert} />
      </div>

      <div className="grid gap-6 lg:grid-cols-[1fr_340px]">
        {/* Columna principal */}
        <div className="space-y-6">
          {/* Preview */}
          <Card>
            <CardHeader className="flex items-center justify-between">
              <CardTitle>Vista del certificado</CardTitle>
              <ButtonLink href={`/verificar/${cert.code}`} variant="ghost" size="sm" target="_blank">
                Página pública <ExternalLink className="h-3.5 w-3.5" />
              </ButtonLink>
            </CardHeader>
            <CardContent>
              <CertificatePreview certificate={cert} orgName={org.name} accent={template?.accent} />
            </CardContent>
          </Card>

          {/* Línea de tiempo / auditoría */}
          <Card>
            <CardHeader>
              <CardTitle>Historial del certificado</CardTitle>
            </CardHeader>
            <CardContent>
              {timeline.length === 0 ? (
                <p className="text-sm text-muted">Aún no hay eventos registrados.</p>
              ) : (
                <ol className="relative space-y-5 before:absolute before:left-4 before:top-2 before:bottom-2 before:w-px before:bg-border">
                  {timeline.map((e) => (
                    <li key={e.id} className="relative flex gap-3">
                      <AuditIcon action={e.action} className="ring-4 ring-surface" />
                      <div className="pt-1">
                        <p className="text-sm font-medium text-ink">{e.detail}</p>
                        <p className="mt-0.5 text-xs text-faint">
                          {e.actor} · {formatDateTime(e.createdAt)}
                        </p>
                      </div>
                    </li>
                  ))}
                </ol>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Columna lateral */}
        <div className="space-y-6">
          {/* Confianza */}
          <Card>
            <CardContent className="pt-5">
              <div className="flex items-center gap-3">
                <span className="flex h-10 w-10 items-center justify-center rounded-full bg-success-50 text-success-700">
                  <ShieldCheck className="h-5 w-5" />
                </span>
                <div>
                  <p className="text-sm font-semibold text-ink">
                    Confianza {cert.trust.confianza === "alta" ? "alta" : "pendiente"}
                  </p>
                  <p className="text-xs text-muted">
                    {cert.trust.anclado
                      ? "Integridad registrada y verificable."
                      : "Se registrará al emitir el certificado."}
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Detalles */}
          <Card>
            <CardHeader>
              <CardTitle>Detalles</CardTitle>
            </CardHeader>
            <CardContent className="space-y-0">
              <DetailRow label="Destinatario" value={cert.recipientName} />
              <DetailRow label="Correo" value={cert.recipientEmail} />
              <DetailRow label="Plantilla" value={cert.templateName} />
              <DetailRow label="Emitido por" value={cert.issuedBy} />
              <DetailRow label="Fecha de emisión" value={formatDate(cert.issueDate)} />
              <DetailRow
                label="Vigencia"
                value={cert.expirationDate ? formatDate(cert.expirationDate) : "Sin vencimiento"}
              />
              {Object.entries(cert.metadata).map(([k, v]) => (
                <DetailRow key={k} label={capitalize(k)} value={v} />
              ))}
            </CardContent>
          </Card>

          {/* Detalle técnico avanzado (único lugar con jerga) */}
          <details className="group rounded-[var(--radius-lg)] border border-border bg-surface">
            <summary className="flex cursor-pointer list-none items-center justify-between px-5 py-3 text-sm font-medium text-ink-soft">
              <span className="flex items-center gap-2">
                <Lock className="h-4 w-4 text-faint" /> Detalle técnico avanzado
              </span>
              <ChevronRight className="h-4 w-4 text-faint transition-transform group-open:rotate-90" />
            </summary>
            <div className="space-y-2 border-t border-border px-5 py-4 text-xs">
              <TechRow label="Huella de integridad" value={cert.trust.fingerprint} />
              <TechRow label="Referencia de sello" value={cert.trust.sealRef} />
              <TechRow label="Referencia de anclaje" value={cert.trust.anchorRef ?? "—"} />
              <p className="pt-1 text-faint">
                Registro de integridad gestionado por TrustHunter. Esta información es opcional y no es
                necesaria para verificar el documento.
              </p>
            </div>
          </details>
        </div>
      </div>
    </div>
  );
}

function DetailRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex items-start justify-between gap-4 border-b border-border py-2.5 last:border-0">
      <span className="text-sm text-muted">{label}</span>
      <span className="text-right text-sm font-medium text-ink-soft">{value}</span>
    </div>
  );
}

function TechRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex items-center justify-between gap-3">
      <span className="text-muted">{label}</span>
      <span className="font-mono text-ink-soft">{value}</span>
    </div>
  );
}

function capitalize(s: string) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}
