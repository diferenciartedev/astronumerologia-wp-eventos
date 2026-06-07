import Link from "next/link";
import { LayoutTemplate, Plus, QrCode, PenLine, ShieldCheck, FileBadge } from "lucide-react";
import { PageHeader } from "@/components/ui/misc";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { ButtonLink, Button } from "@/components/ui/button";
import { getTemplates } from "@/lib/mock-data";
import { formatDate, formatNumber } from "@/lib/format";

export const metadata = { title: "Plantillas" };

export default function PlantillasPage() {
  const templates = getTemplates();

  return (
    <div className="space-y-6">
      <PageHeader title="Plantillas" description="Diseña una vez y reutiliza para emitir certificados consistentes y con tu marca.">
        <Button>
          <Plus className="h-4 w-4" /> Nueva plantilla
        </Button>
      </PageHeader>

      <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {templates.map((t) => (
          <Card key={t.id} className="overflow-hidden transition-shadow hover:shadow-[var(--shadow-md)]">
            {/* Mini muestra */}
            <div className="relative h-28 border-b border-border" style={{ background: `linear-gradient(135deg, ${t.accent}14, ${t.accent}06)` }}>
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="w-3/4 rounded-md border border-border bg-surface px-4 py-3 shadow-[var(--shadow-xs)]">
                  <div className="h-1.5 w-10 rounded-full" style={{ backgroundColor: t.accent }} />
                  <div className="mt-2 h-2 w-full rounded-full bg-border" />
                  <div className="mt-1 h-2 w-2/3 rounded-full bg-border" />
                </div>
              </div>
              <span className="absolute right-3 top-3">
                {t.status === "activo" ? (
                  <Badge tone="success" dot>Activa</Badge>
                ) : t.status === "borrador" ? (
                  <Badge tone="warning" dot>Borrador</Badge>
                ) : (
                  <Badge tone="neutral">Archivada</Badge>
                )}
              </span>
            </div>

            <div className="p-5">
              <Link href={`/app/plantillas/${t.id}`} className="block">
                <h3 className="text-base font-semibold text-ink hover:text-brand-700">{t.name}</h3>
              </Link>
              <p className="mt-1 line-clamp-2 text-sm text-muted">{t.description}</p>

              <div className="mt-3 flex flex-wrap gap-1.5">
                {t.hasQr && <Capability icon={QrCode} label="QR" />}
                {t.hasSignature && <Capability icon={PenLine} label="Firma" />}
                {t.hasSeal && <Capability icon={ShieldCheck} label="Sello" />}
              </div>

              <div className="mt-4 flex items-center justify-between border-t border-border pt-3">
                <span className="flex items-center gap-1.5 text-xs text-muted">
                  <FileBadge className="h-3.5 w-3.5" /> {formatNumber(t.certificatesIssued)} emitidos
                </span>
                <span className="text-xs text-faint">Editada {formatDate(t.updatedAt)}</span>
              </div>
            </div>
          </Card>
        ))}

        {/* Tarjeta nueva */}
        <Link
          href="#"
          className="flex min-h-[260px] flex-col items-center justify-center gap-3 rounded-[var(--radius-lg)] border border-dashed border-border-strong bg-surface-2 text-center transition-colors hover:border-brand-300 hover:bg-brand-50/30"
        >
          <span className="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600">
            <LayoutTemplate className="h-6 w-6" strokeWidth={1.7} />
          </span>
          <span className="text-sm font-medium text-ink">Crear nueva plantilla</span>
          <span className="max-w-[60%] text-xs text-muted">Empieza desde cero o duplica una existente</span>
        </Link>
      </div>
    </div>
  );
}

function Capability({ icon: Icon, label }: { icon: typeof QrCode; label: string }) {
  return (
    <span className="inline-flex items-center gap-1 rounded-full border border-border bg-surface-2 px-2 py-0.5 text-[11px] font-medium text-ink-soft">
      <Icon className="h-3 w-3" /> {label}
    </span>
  );
}
