import Link from "next/link";
import {
  CheckCircle2,
  XCircle,
  AlertTriangle,
  ShieldCheck,
  Search,
  RefreshCcw,
} from "lucide-react";
import { Logo } from "@/components/brand";
import { ButtonLink } from "@/components/ui/button";
import { CertificatePreview } from "@/components/certificate-preview";
import { getCertificateByCode, getOrganization, getTemplate } from "@/lib/mock-data";
import { formatDate, formatDateTime } from "@/lib/format";
import type { CertStatus } from "@/lib/types";

type Verdict = "valido" | "revocado" | "expirado" | "reemplazado" | "no_encontrado";

const VERDICTS: Record<
  Verdict,
  { title: string; subtitle: string; icon: typeof CheckCircle2; classes: string; ring: string }
> = {
  valido: {
    title: "Certificado válido",
    subtitle: "Este documento es auténtico y no ha sido alterado.",
    icon: CheckCircle2,
    classes: "bg-success-50 text-success-700 border-success-100",
    ring: "ring-success-100",
  },
  revocado: {
    title: "Certificado revocado",
    subtitle: "La organización emisora anuló este documento. Ya no es válido.",
    icon: XCircle,
    classes: "bg-danger-50 text-danger-700 border-danger-100",
    ring: "ring-danger-100",
  },
  expirado: {
    title: "Certificado expirado",
    subtitle: "Este documento fue auténtico pero su vigencia ya terminó.",
    icon: AlertTriangle,
    classes: "bg-warning-50 text-warning-700 border-warning-100",
    ring: "ring-warning-100",
  },
  reemplazado: {
    title: "Certificado reemplazado",
    subtitle: "Existe una versión más reciente de este documento.",
    icon: RefreshCcw,
    classes: "bg-warning-50 text-warning-700 border-warning-100",
    ring: "ring-warning-100",
  },
  no_encontrado: {
    title: "No encontramos este certificado",
    subtitle: "Revisa el código e inténtalo de nuevo. Quizá fue escrito de forma incorrecta.",
    icon: Search,
    classes: "bg-surface-2 text-ink-soft border-border",
    ring: "ring-border",
  },
};

function statusToVerdict(status: CertStatus): Verdict {
  if (status === "emitido") return "valido";
  if (status === "borrador") return "no_encontrado";
  return status as Verdict;
}

export default async function VerificationResultPage({
  params,
}: {
  params: Promise<{ codigo: string }>;
}) {
  const { codigo } = await params;
  const cert = getCertificateByCode(decodeURIComponent(codigo));
  const verdict: Verdict = cert ? statusToVerdict(cert.status) : "no_encontrado";
  const v = VERDICTS[verdict];
  const org = getOrganization();
  const template = cert ? getTemplate(cert.templateId) : null;

  return (
    <div className="flex min-h-screen flex-col bg-canvas">
      <header className="border-b border-border bg-surface/80 backdrop-blur">
        <div className="mx-auto flex h-16 max-w-3xl items-center justify-between px-5">
          <Link href="/"><Logo /></Link>
          <Link href="/verificar" className="text-sm font-medium text-ink-soft hover:text-ink">
            Verificar otro
          </Link>
        </div>
      </header>

      <main className="mx-auto w-full max-w-2xl flex-1 px-5 py-10 tf-fade-up">
        {/* Veredicto */}
        <div className={`flex items-center gap-4 rounded-[var(--radius-xl)] border p-5 ring-4 ${v.classes} ${v.ring}`}>
          <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/70">
            <v.icon className="h-7 w-7" />
          </span>
          <div>
            <h1 className="text-lg font-semibold">{v.title}</h1>
            <p className="text-sm opacity-90">{v.subtitle}</p>
          </div>
        </div>

        {cert ? (
          <div className="mt-6 space-y-6">
            <div className="grid gap-6 sm:grid-cols-[1fr_240px]">
              {/* Datos */}
              <div className="rounded-[var(--radius-lg)] border border-border bg-surface p-5">
                <h2 className="text-sm font-semibold text-ink">Detalles del documento</h2>
                <dl className="mt-3 space-y-0">
                  <Row label="Emitido por" value={org.name} />
                  <Row label="Otorgado a" value={cert.recipientName} />
                  <Row label="Certificado" value={cert.templateName} />
                  <Row label="Descripción" value={cert.title} />
                  <Row label="Fecha de emisión" value={formatDate(cert.issueDate)} />
                  <Row label="Vigencia" value={cert.expirationDate ? formatDate(cert.expirationDate) : "Sin vencimiento"} />
                  {Object.entries(cert.metadata).map(([k, val]) => (
                    <Row key={k} label={cap(k)} value={val} />
                  ))}
                  <Row label="Código" value={cert.code} mono />
                </dl>
              </div>

              {/* Mini preview */}
              <div>
                <CertificatePreview certificate={cert} orgName={org.name} accent={template?.accent} />
              </div>
            </div>

            {/* Mensaje de confianza */}
            {verdict === "valido" && (
              <div className="flex items-start gap-3 rounded-[var(--radius-lg)] border border-border bg-surface p-4">
                <ShieldCheck className="mt-0.5 h-5 w-5 shrink-0 text-success-600" />
                <div className="text-sm text-ink-soft">
                  <p className="font-medium text-ink">Confianza verificada</p>
                  <p className="mt-0.5 text-muted">
                    La autenticidad e integridad de este documento están registradas y respaldadas por
                    TrustHunter{cert.trust.selladoEn ? `, desde el ${formatDateTime(cert.trust.selladoEn)}` : ""}.
                  </p>
                </div>
              </div>
            )}

            {verdict === "reemplazado" && (
              <div className="rounded-[var(--radius-lg)] border border-warning-100 bg-warning-50 p-4 text-sm text-warning-700">
                Este documento fue sustituido por una versión más reciente. Solicita al titular el certificado vigente.
              </div>
            )}
          </div>
        ) : (
          <div className="mt-6 flex flex-col items-center rounded-[var(--radius-lg)] border border-dashed border-border-strong bg-surface-2 px-6 py-12 text-center">
            <p className="text-sm text-muted">El código <span className="font-mono text-ink-soft">{decodeURIComponent(codigo)}</span> no corresponde a ningún certificado.</p>
            <ButtonLink href="/verificar" variant="secondary" className="mt-4">
              <Search className="h-4 w-4" /> Intentar de nuevo
            </ButtonLink>
          </div>
        )}

        <p className="mt-8 text-center text-xs text-faint">
          Verificación pública · una plataforma de Nodoa
        </p>
      </main>
    </div>
  );
}

function Row({ label, value, mono }: { label: string; value: string; mono?: boolean }) {
  return (
    <div className="flex items-start justify-between gap-4 border-b border-border py-2.5 last:border-0">
      <dt className="text-sm text-muted">{label}</dt>
      <dd className={`text-right text-sm font-medium text-ink-soft ${mono ? "font-mono" : ""}`}>{value}</dd>
    </div>
  );
}

function cap(s: string) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}
