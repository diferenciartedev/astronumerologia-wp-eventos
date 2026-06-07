"use client";

import { Suspense, useMemo, useState } from "react";
import { useSearchParams } from "next/navigation";
import Link from "next/link";
import {
  FilePlus2,
  Upload,
  Check,
  ArrowRight,
  ArrowLeft,
  FileSpreadsheet,
  ShieldCheck,
  PartyPopper,
  Download,
} from "lucide-react";
import { PageHeader } from "@/components/ui/misc";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button, ButtonLink } from "@/components/ui/button";
import { Field, Input, Select } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { CertificatePreview } from "@/components/certificate-preview";
import { getTemplates, getOrganization } from "@/lib/mock-data";
import type { Certificate } from "@/lib/types";
import { cn } from "@/lib/cn";

export default function EmitirPage() {
  return (
    <Suspense fallback={null}>
      <EmitirInner />
    </Suspense>
  );
}

function EmitirInner() {
  const searchParams = useSearchParams();
  const [mode, setMode] = useState<"individual" | "masivo">(
    searchParams.get("modo") === "masivo" ? "masivo" : "individual"
  );

  return (
    <div className="space-y-6">
      <PageHeader title="Emitir certificados" description="Crea un certificado o emite muchos a la vez desde un archivo.">
        <Link href="/app/certificados" className="text-sm font-medium text-muted hover:text-ink">
          Ver repositorio
        </Link>
      </PageHeader>

      {/* Selector de modo */}
      <div className="grid gap-3 sm:grid-cols-2">
        <ModeCard
          active={mode === "individual"}
          onClick={() => setMode("individual")}
          icon={FilePlus2}
          title="Certificado individual"
          desc="Llena los datos de un destinatario y emite al instante."
        />
        <ModeCard
          active={mode === "masivo"}
          onClick={() => setMode("masivo")}
          icon={Upload}
          title="Emisión masiva (CSV)"
          desc="Sube un archivo con muchos destinatarios y emite en bloque."
        />
      </div>

      {mode === "individual" ? <IndividualFlow /> : <BulkFlow />}
    </div>
  );
}

function ModeCard({
  active,
  onClick,
  icon: Icon,
  title,
  desc,
}: {
  active: boolean;
  onClick: () => void;
  icon: typeof FilePlus2;
  title: string;
  desc: string;
}) {
  return (
    <button
      onClick={onClick}
      className={cn(
        "flex items-start gap-3 rounded-[var(--radius-lg)] border bg-surface p-4 text-left transition-all",
        active ? "border-brand-400 ring-4 ring-brand-100" : "border-border hover:border-border-strong"
      )}
    >
      <span className={cn("flex h-10 w-10 items-center justify-center rounded-[var(--radius-md)]", active ? "bg-brand-600 text-white" : "bg-brand-50 text-brand-600")}>
        <Icon className="h-5 w-5" strokeWidth={1.9} />
      </span>
      <span>
        <span className="block text-sm font-semibold text-ink">{title}</span>
        <span className="mt-0.5 block text-sm text-muted">{desc}</span>
      </span>
    </button>
  );
}

/* ----------------------- Flujo individual ----------------------- */

function IndividualFlow() {
  const templates = getTemplates().filter((t) => t.status === "activo");
  const org = getOrganization();
  const [templateId, setTemplateId] = useState(templates[0]?.id ?? "");
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [issueDate, setIssueDate] = useState("2026-06-07");
  const [meta, setMeta] = useState<Record<string, string>>({});
  const [issued, setIssued] = useState(false);

  const template = templates.find((t) => t.id === templateId);

  const draft: Certificate = useMemo(
    () => ({
      id: "draft",
      code: "TF-XXXX-XXXX",
      title:
        (template?.name ?? "Certificado") +
        (meta.programa ? ` — ${meta.programa}` : meta.curso ? ` — ${meta.curso}` : ""),
      templateId,
      templateName: template?.name ?? "",
      recipientName: name || "Nombre del destinatario",
      recipientEmail: email,
      status: "borrador",
      issueDate,
      expirationDate: null,
      issuedBy: "Tú",
      metadata: meta,
      trust: { confianza: "pendiente", selladoEn: null, anclado: false, fingerprint: "", sealRef: "", anchorRef: null },
    }),
    [template, templateId, name, email, issueDate, meta]
  );

  if (issued) {
    return <IssuedSuccess single recipientName={name} />;
  }

  return (
    <div className="grid gap-6 lg:grid-cols-[1fr_400px]">
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>1 · Elige una plantilla</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="grid gap-2.5 sm:grid-cols-2">
              {templates.map((t) => (
                <button
                  key={t.id}
                  onClick={() => setTemplateId(t.id)}
                  className={cn(
                    "flex items-center gap-3 rounded-[var(--radius-md)] border p-3 text-left transition-colors",
                    templateId === t.id ? "border-brand-400 bg-brand-50" : "border-border hover:border-border-strong"
                  )}
                >
                  <span className="h-8 w-8 shrink-0 rounded-md" style={{ backgroundColor: t.accent }} />
                  <span className="min-w-0">
                    <span className="block truncate text-sm font-medium text-ink">{t.name}</span>
                    <span className="block text-xs text-muted">{t.certificatesIssued} emitidos</span>
                  </span>
                  {templateId === t.id && <Check className="ml-auto h-4 w-4 text-brand-600" />}
                </button>
              ))}
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>2 · Datos del destinatario</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid gap-4 sm:grid-cols-2">
              <Field label="Nombre completo" htmlFor="rn">
                <Input id="rn" value={name} onChange={(e) => setName(e.target.value)} placeholder="Ej. Andrea Beltrán" />
              </Field>
              <Field label="Correo electrónico" htmlFor="re">
                <Input id="re" type="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="andrea@correo.com" />
              </Field>
            </div>
            <div className="grid gap-4 sm:grid-cols-2">
              <Field label="Fecha de emisión" htmlFor="rd">
                <Input id="rd" type="date" value={issueDate} onChange={(e) => setIssueDate(e.target.value)} />
              </Field>
              <Field label="Vigencia (opcional)" htmlFor="rv">
                <Select id="rv" defaultValue="sin">
                  <option value="sin">Sin vencimiento</option>
                  <option value="1">1 año</option>
                  <option value="2">2 años</option>
                  <option value="5">5 años</option>
                </Select>
              </Field>
            </div>

            {template && template.variables.length > 0 && (
              <div className="grid gap-4 border-t border-border pt-4 sm:grid-cols-2">
                {template.variables.map((v) => (
                  <Field key={v.key} label={v.label + (v.required ? "" : " (opcional)")} htmlFor={v.key}>
                    <Input
                      id={v.key}
                      value={meta[v.key] ?? ""}
                      onChange={(e) => setMeta((m) => ({ ...m, [v.key]: e.target.value }))}
                      placeholder={v.label}
                    />
                  </Field>
                ))}
              </div>
            )}
          </CardContent>
        </Card>

        <div className="flex items-center justify-between rounded-[var(--radius-lg)] border border-border bg-surface p-4">
          <span className="flex items-center gap-2 text-sm text-muted">
            <ShieldCheck className="h-4 w-4 text-success-500" />
            Al emitir, el documento se sella y registra automáticamente para su verificación.
          </span>
          <div className="flex gap-2">
            <Button variant="secondary" onClick={() => setIssued(true)}>Guardar borrador</Button>
            <Button onClick={() => setIssued(true)} disabled={!name}>
              Emitir certificado <ArrowRight className="h-4 w-4" />
            </Button>
          </div>
        </div>
      </div>

      {/* Vista previa en vivo */}
      <div className="lg:sticky lg:top-24 lg:self-start">
        <p className="mb-2 text-xs font-medium uppercase tracking-wide text-faint">Vista previa en vivo</p>
        <CertificatePreview certificate={draft} orgName={org.name} accent={template?.accent} />
        <p className="mt-3 text-xs text-muted">
          Así se verá el certificado. El código y QR de verificación se generan al emitir.
        </p>
      </div>
    </div>
  );
}

/* ----------------------- Flujo masivo ----------------------- */

function BulkFlow() {
  const templates = getTemplates().filter((t) => t.status === "activo");
  const [templateId, setTemplateId] = useState(templates[0]?.id ?? "");
  const [uploaded, setUploaded] = useState(false);
  const [issued, setIssued] = useState(false);

  const sampleRows = [
    { name: "María González", email: "maria.g@correo.com", programa: "Diplomado en Diseño UX", horas: "120" },
    { name: "Luis Herrera", email: "luis.h@correo.com", programa: "Diplomado en Diseño UX", horas: "120" },
    { name: "Ana Torres", email: "ana.t@correo.com", programa: "Diplomado en Diseño UX", horas: "120" },
    { name: "Pedro Ramos", email: "pedro.r@correo.com", programa: "Diplomado en Diseño UX", horas: "120" },
  ];

  if (issued) return <IssuedSuccess single={false} count={84} />;

  return (
    <div className="space-y-6">
      <Card>
        <CardHeader>
          <CardTitle>1 · Plantilla a usar</CardTitle>
        </CardHeader>
        <CardContent>
          <Select value={templateId} onChange={(e) => setTemplateId(e.target.value)} className="max-w-sm">
            {templates.map((t) => (
              <option key={t.id} value={t.id}>{t.name}</option>
            ))}
          </Select>
        </CardContent>
      </Card>

      <Card>
        <CardHeader className="flex items-center justify-between">
          <CardTitle>2 · Sube tu archivo de destinatarios</CardTitle>
          <Button variant="ghost" size="sm">
            <Download className="h-4 w-4" /> Descargar plantilla CSV
          </Button>
        </CardHeader>
        <CardContent>
          {!uploaded ? (
            <button
              onClick={() => setUploaded(true)}
              className="flex w-full flex-col items-center justify-center rounded-[var(--radius-lg)] border-2 border-dashed border-border-strong bg-surface-2 px-6 py-12 text-center transition-colors hover:border-brand-300 hover:bg-brand-50/30"
            >
              <span className="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <FileSpreadsheet className="h-6 w-6" strokeWidth={1.7} />
              </span>
              <span className="mt-3 text-sm font-medium text-ink">Arrastra tu archivo CSV o haz clic para subir</span>
              <span className="mt-1 text-xs text-muted">Columnas: nombre, correo y los campos de la plantilla</span>
            </button>
          ) : (
            <div className="space-y-4">
              <div className="flex items-center gap-2 rounded-[var(--radius-md)] border border-success-100 bg-success-50 px-4 py-2.5 text-sm text-success-700">
                <Check className="h-4 w-4" /> destinatarios.csv cargado · 84 filas detectadas, 0 con errores
              </div>
              <div className="overflow-hidden rounded-[var(--radius-md)] border border-border">
                <table className="w-full text-sm">
                  <thead className="bg-surface-2">
                    <tr className="border-b border-border text-left text-xs font-semibold uppercase tracking-wide text-muted">
                      <th className="px-4 py-2">Nombre</th>
                      <th className="px-4 py-2">Correo</th>
                      <th className="px-4 py-2">Programa</th>
                      <th className="px-4 py-2">Horas</th>
                    </tr>
                  </thead>
                  <tbody>
                    {sampleRows.map((r) => (
                      <tr key={r.email} className="border-b border-border last:border-0">
                        <td className="px-4 py-2.5 font-medium text-ink">{r.name}</td>
                        <td className="px-4 py-2.5 text-ink-soft">{r.email}</td>
                        <td className="px-4 py-2.5 text-ink-soft">{r.programa}</td>
                        <td className="px-4 py-2.5 text-ink-soft">{r.horas}</td>
                      </tr>
                    ))}
                    <tr>
                      <td colSpan={4} className="px-4 py-2.5 text-center text-xs text-muted">+ 80 destinatarios más</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </CardContent>
      </Card>

      {uploaded && (
        <div className="flex items-center justify-between rounded-[var(--radius-lg)] border border-border bg-surface p-4">
          <span className="flex items-center gap-2 text-sm text-muted">
            <Badge tone="brand">84 certificados</Badge>
            Se emitirán y enviarán por correo automáticamente.
          </span>
          <Button onClick={() => setIssued(true)}>
            Emitir 84 certificados <ArrowRight className="h-4 w-4" />
          </Button>
        </div>
      )}
    </div>
  );
}

/* ----------------------- Éxito ----------------------- */

function IssuedSuccess({ single, recipientName, count }: { single: boolean; recipientName?: string; count?: number }) {
  return (
    <Card>
      <CardContent className="flex flex-col items-center py-14 text-center">
        <span className="flex h-14 w-14 items-center justify-center rounded-full bg-success-50 text-success-700">
          <PartyPopper className="h-7 w-7" strokeWidth={1.8} />
        </span>
        <h2 className="mt-4 text-xl font-semibold tracking-tight text-ink">
          {single ? "¡Certificado emitido!" : `¡${count} certificados emitidos!`}
        </h2>
        <p className="mt-1.5 max-w-md text-sm text-muted">
          {single
            ? `El certificado de ${recipientName || "tu destinatario"} fue sellado y registrado. Ya puede verificarse públicamente.`
            : "Los certificados se sellaron, registraron y se están enviando por correo a cada destinatario."}
        </p>
        <div className="mt-6 flex gap-2">
          <ButtonLink href="/app/certificados" variant="secondary">Ver en el repositorio</ButtonLink>
          <ButtonLink href="/app/emitir">Emitir otro</ButtonLink>
        </div>
      </CardContent>
    </Card>
  );
}
