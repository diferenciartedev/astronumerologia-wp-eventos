import { PenLine, Plus, Clock, CheckCircle2 } from "lucide-react";
import { PageHeader, EmptyState } from "@/components/ui/misc";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { getMembers } from "@/lib/mock-data";
import { initials } from "@/lib/format";

export const metadata = { title: "Firmantes" };

const REQUESTS = [
  { id: "s1", cert: "Reconocimiento — Trayectoria docente", signer: "Dra. Elena Soto", status: "firmado" as const, date: "20 may 2026" },
  { id: "s2", cert: "Diploma — Ciencia de Datos", signer: "Dra. Elena Soto", status: "pendiente" as const, date: "Enviada hoy" },
  { id: "s3", cert: "Diploma — Programación Web", signer: "Marco Díaz", status: "pendiente" as const, date: "Enviada ayer" },
];

export default function FirmantesPage() {
  const signers = getMembers().filter((m) => m.role === "signer" || m.role === "owner" || m.role === "admin");

  return (
    <div className="space-y-6">
      <PageHeader
        title="Firmantes"
        description="Asigna responsables de firma y da seguimiento a las solicitudes de firma de tus documentos."
      >
        <Button><Plus className="h-4 w-4" /> Agregar firmante</Button>
      </PageHeader>

      <div className="grid gap-6 lg:grid-cols-[1fr_360px]">
        {/* Solicitudes de firma */}
        <Card>
          <CardHeader><CardTitle>Solicitudes de firma</CardTitle></CardHeader>
          <CardContent>
            {REQUESTS.length === 0 ? (
              <EmptyState icon={PenLine} title="Sin solicitudes" description="Aún no has enviado documentos a firmar." className="border-0" />
            ) : (
              <ol className="space-y-2.5">
                {REQUESTS.map((r) => (
                  <li key={r.id} className="flex items-center gap-3 rounded-[var(--radius-md)] border border-border bg-surface px-4 py-3">
                    <span className={`flex h-9 w-9 items-center justify-center rounded-full ${r.status === "firmado" ? "bg-success-50 text-success-700" : "bg-warning-50 text-warning-700"}`}>
                      {r.status === "firmado" ? <CheckCircle2 className="h-4 w-4" /> : <Clock className="h-4 w-4" />}
                    </span>
                    <div className="min-w-0 flex-1">
                      <p className="truncate text-sm font-medium text-ink">{r.cert}</p>
                      <p className="text-xs text-muted">{r.signer} · {r.date}</p>
                    </div>
                    {r.status === "firmado" ? <Badge tone="success" dot>Firmado</Badge> : <Badge tone="warning" dot>Pendiente</Badge>}
                  </li>
                ))}
              </ol>
            )}
          </CardContent>
        </Card>

        {/* Firmantes */}
        <Card>
          <CardHeader><CardTitle>Personas autorizadas</CardTitle></CardHeader>
          <CardContent>
            <ul className="space-y-3">
              {signers.map((s) => (
                <li key={s.id} className="flex items-center gap-3">
                  <span className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-[12px] font-semibold text-brand-700">
                    {initials(s.name)}
                  </span>
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-sm font-medium text-ink">{s.name}</p>
                    <p className="truncate text-xs text-muted">{s.email}</p>
                  </div>
                </li>
              ))}
            </ul>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
