import Link from "next/link";
import { Download } from "lucide-react";
import { PageHeader } from "@/components/ui/misc";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { AuditIcon } from "@/components/audit-icon";
import { getAuditEvents } from "@/lib/mock-data";
import { formatDateTime } from "@/lib/format";

export const metadata = { title: "Auditoría" };

const ACTOR_TONE = {
  usuario: "brand",
  sistema: "info",
  publico: "neutral",
} as const;

const ACTOR_LABEL = {
  usuario: "Usuario",
  sistema: "Sistema",
  publico: "Público",
} as const;

export default function AuditoriaPage() {
  const events = getAuditEvents();

  return (
    <div className="space-y-6">
      <PageHeader
        title="Auditoría"
        description="Cada acción sobre tus certificados queda registrada de forma trazable y verificable."
      >
        <Button variant="secondary">
          <Download className="h-4 w-4" /> Exportar registro
        </Button>
      </PageHeader>

      <Card className="p-2">
        <ol className="divide-y divide-border">
          {events.map((e) => (
            <li key={e.id} className="flex items-center gap-4 px-3 py-3.5">
              <AuditIcon action={e.action} />
              <div className="min-w-0 flex-1">
                <p className="text-sm font-medium text-ink">{e.detail}</p>
                <p className="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-muted">
                  <span>{e.actor}</span>
                  {e.certificateCode && (
                    <>
                      <span className="text-faint">·</span>
                      {e.certificateId ? (
                        <Link href={`/app/certificados/${e.certificateId}`} className="font-mono text-brand-600 hover:text-brand-700">
                          {e.certificateCode}
                        </Link>
                      ) : (
                        <span className="font-mono text-faint">{e.certificateCode}</span>
                      )}
                    </>
                  )}
                </p>
              </div>
              <div className="hidden items-center gap-3 sm:flex">
                <Badge tone={ACTOR_TONE[e.actorType]}>{ACTOR_LABEL[e.actorType]}</Badge>
                <span className="whitespace-nowrap text-xs text-faint">{formatDateTime(e.createdAt)}</span>
              </div>
            </li>
          ))}
        </ol>
      </Card>
    </div>
  );
}
