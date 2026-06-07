import { notFound } from "next/navigation";
import Link from "next/link";
import { ArrowLeft, QrCode, PenLine, ShieldCheck, Plus } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Field, Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { CertificatePreview } from "@/components/certificate-preview";
import { getTemplate, getOrganization } from "@/lib/mock-data";
import type { Certificate } from "@/lib/types";

export default async function TemplateEditorPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const template = getTemplate(id);
  if (!template) notFound();
  const org = getOrganization();

  const sample: Certificate = {
    id: "sample",
    code: "TF-7QK2-9MX4",
    title: `${template.name} — ${template.variables[0]?.label ?? "Programa de ejemplo"}`,
    templateId: template.id,
    templateName: template.name,
    recipientName: "Andrea Beltrán",
    recipientEmail: "andrea@correo.com",
    status: "emitido",
    issueDate: "2026-05-30",
    expirationDate: null,
    issuedBy: "Demo",
    metadata: { horas: "120" },
    trust: { confianza: "alta", selladoEn: null, anclado: true, fingerprint: "", sealRef: "", anchorRef: null },
  };

  return (
    <div className="space-y-6">
      <Link href="/app/plantillas" className="inline-flex items-center gap-1.5 text-sm font-medium text-muted hover:text-ink">
        <ArrowLeft className="h-4 w-4" /> Plantillas
      </Link>

      <div className="flex flex-wrap items-start justify-between gap-3">
        <div>
          <div className="flex items-center gap-3">
            <h1 className="text-[22px] font-semibold tracking-tight text-ink">{template.name}</h1>
            <Badge tone={template.status === "activo" ? "success" : "warning"} dot>
              {template.status === "activo" ? "Activa" : "Borrador"}
            </Badge>
          </div>
          <p className="mt-1 text-sm text-muted">{template.description}</p>
        </div>
        <div className="flex gap-2">
          <Button variant="secondary">Vista previa</Button>
          <Button>Guardar cambios</Button>
        </div>
      </div>

      <div className="grid gap-6 lg:grid-cols-[380px_1fr]">
        {/* Controles */}
        <div className="space-y-6">
          <Card>
            <CardHeader><CardTitle>Identidad</CardTitle></CardHeader>
            <CardContent className="space-y-4">
              <Field label="Nombre de la plantilla">
                <Input defaultValue={template.name} />
              </Field>
              <Field label="Color de acento">
                <div className="flex items-center gap-2">
                  <span className="h-9 w-9 rounded-md border border-border" style={{ backgroundColor: template.accent }} />
                  <Input defaultValue={template.accent} className="font-mono" />
                </div>
              </Field>
            </CardContent>
          </Card>

          <Card>
            <CardHeader><CardTitle>Elementos de confianza</CardTitle></CardHeader>
            <CardContent className="space-y-1">
              <Toggle icon={QrCode} label="Código QR de verificación" on={template.hasQr} />
              <Toggle icon={PenLine} label="Bloque de firma" on={template.hasSignature} />
              <Toggle icon={ShieldCheck} label="Sello de verificación" on={template.hasSeal} />
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex items-center justify-between">
              <CardTitle>Campos dinámicos</CardTitle>
              <Button variant="ghost" size="sm"><Plus className="h-4 w-4" /> Agregar</Button>
            </CardHeader>
            <CardContent className="space-y-2">
              {template.variables.map((v) => (
                <div key={v.key} className="flex items-center justify-between rounded-[var(--radius-md)] border border-border bg-surface-2 px-3 py-2">
                  <div>
                    <p className="text-sm font-medium text-ink">{v.label}</p>
                    <p className="font-mono text-xs text-faint">{"{{"}{v.key}{"}}"}</p>
                  </div>
                  {v.required ? <Badge tone="brand">Obligatorio</Badge> : <Badge tone="neutral">Opcional</Badge>}
                </div>
              ))}
              {template.variables.length === 0 && (
                <p className="text-sm text-muted">Sin campos dinámicos todavía.</p>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Preview */}
        <div className="lg:sticky lg:top-24 lg:self-start">
          <p className="mb-2 text-xs font-medium uppercase tracking-wide text-faint">Vista previa</p>
          <CertificatePreview certificate={sample} orgName={org.name} accent={template.accent} />
        </div>
      </div>
    </div>
  );
}

function Toggle({ icon: Icon, label, on }: { icon: typeof QrCode; label: string; on: boolean }) {
  return (
    <div className="flex items-center justify-between py-2">
      <span className="flex items-center gap-2.5 text-sm text-ink-soft">
        <Icon className="h-4 w-4 text-muted" /> {label}
      </span>
      <span
        className={`relative h-5 w-9 rounded-full transition-colors ${on ? "bg-brand-600" : "bg-border-strong"}`}
      >
        <span className={`absolute top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-all ${on ? "left-[18px]" : "left-0.5"}`} />
      </span>
    </div>
  );
}
