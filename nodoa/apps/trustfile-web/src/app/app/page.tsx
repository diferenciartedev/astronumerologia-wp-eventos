import Link from "next/link";
import {
  FileBadge,
  FileEdit,
  LayoutTemplate,
  BadgeCheck,
  Ban,
  FilePlus2,
  Upload,
  PenLine,
  ScrollText,
  ArrowUpRight,
} from "lucide-react";
import { PageHeader, StatCard } from "@/components/ui/misc";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { StatusBadge } from "@/components/ui/badge";
import { ButtonLink } from "@/components/ui/button";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { getCertificates, getDashboardStats, getAuditEvents } from "@/lib/mock-data";
import { formatDate, formatNumber, formatDateTime } from "@/lib/format";
import { AuditIcon } from "@/components/audit-icon";

const QUICK_ACTIONS = [
  { href: "/app/emitir", label: "Emitir certificado", icon: FilePlus2, tone: "bg-brand-50 text-brand-600" },
  { href: "/app/emitir?modo=masivo", label: "Importar y emitir en bloque", icon: Upload, tone: "bg-info-50 text-info-700" },
  { href: "/app/plantillas", label: "Crear plantilla", icon: LayoutTemplate, tone: "bg-success-50 text-success-700" },
  { href: "/verificar", label: "Verificar certificado", icon: BadgeCheck, tone: "bg-warning-50 text-warning-700" },
  { href: "/app/firmantes", label: "Gestionar firmantes", icon: PenLine, tone: "bg-brand-50 text-brand-600" },
  { href: "/app/auditoria", label: "Ver auditoría", icon: ScrollText, tone: "bg-surface-2 text-ink-soft" },
];

export default function DashboardPage() {
  const stats = getDashboardStats();
  const recent = getCertificates().slice(0, 5);
  const activity = getAuditEvents().slice(0, 6);

  return (
    <div className="space-y-7">
      <PageHeader
        title="Panel"
        description="Un vistazo al estado de tus certificados y a la actividad reciente."
      >
        <ButtonLink href="/app/emitir">
          <FilePlus2 className="h-4 w-4" /> Emitir certificado
        </ButtonLink>
      </PageHeader>

      {/* Métricas */}
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard icon={FileBadge} label="Certificados emitidos" value={formatNumber(stats.totalEmitidos)} delta={{ value: "+38 este mes", positive: true }} tone="brand" />
        <StatCard icon={BadgeCheck} label="Verificaciones públicas" value={formatNumber(stats.verificaciones)} delta={{ value: "+12% vs. mayo", positive: true }} tone="success" />
        <StatCard icon={FileEdit} label="Borradores pendientes" value={stats.borradores} tone="warning" />
        <StatCard icon={Ban} label="Revocados / reemplazados" value={stats.revocados} tone="danger" />
      </div>

      {/* Acciones rápidas */}
      <Card>
        <CardHeader className="flex items-center justify-between">
          <CardTitle>Acciones rápidas</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {QUICK_ACTIONS.map((a) => (
              <Link
                key={a.label}
                href={a.href}
                className="group flex items-center gap-3 rounded-[var(--radius-md)] border border-border bg-surface px-4 py-3 transition-all hover:border-brand-200 hover:shadow-[var(--shadow-sm)]"
              >
                <span className={`flex h-9 w-9 items-center justify-center rounded-[var(--radius-md)] ${a.tone}`}>
                  <a.icon className="h-[18px] w-[18px]" strokeWidth={1.9} />
                </span>
                <span className="flex-1 text-sm font-medium text-ink">{a.label}</span>
                <ArrowUpRight className="h-4 w-4 text-faint transition-colors group-hover:text-brand-600" />
              </Link>
            ))}
          </div>
        </CardContent>
      </Card>

      <div className="grid gap-6 lg:grid-cols-3">
        {/* Certificados recientes */}
        <Card className="lg:col-span-2">
          <CardHeader className="flex items-center justify-between">
            <CardTitle>Certificados recientes</CardTitle>
            <Link href="/app/certificados" className="text-sm font-medium text-brand-600 hover:text-brand-700">
              Ver todos
            </Link>
          </CardHeader>
          <Table>
            <THead>
              <TR>
                <TH>Destinatario</TH>
                <TH>Plantilla</TH>
                <TH>Emitido</TH>
                <TH>Estado</TH>
              </TR>
            </THead>
            <TBody>
              {recent.map((c) => (
                <TR key={c.id}>
                  <TD>
                    <Link href={`/app/certificados/${c.id}`} className="block">
                      <span className="font-medium text-ink hover:text-brand-700">{c.recipientName}</span>
                      <span className="block font-mono text-xs text-faint">{c.code}</span>
                    </Link>
                  </TD>
                  <TD className="text-sm">{c.templateName}</TD>
                  <TD className="text-sm">{formatDate(c.issueDate)}</TD>
                  <TD><StatusBadge status={c.status} /></TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </Card>

        {/* Actividad */}
        <Card>
          <CardHeader>
            <CardTitle>Actividad reciente</CardTitle>
          </CardHeader>
          <CardContent>
            <ol className="space-y-4">
              {activity.map((e) => (
                <li key={e.id} className="flex gap-3">
                  <AuditIcon action={e.action} />
                  <div className="min-w-0 flex-1">
                    <p className="text-sm text-ink-soft">{e.detail}</p>
                    <p className="mt-0.5 text-xs text-faint">
                      {e.actor} · {formatDateTime(e.createdAt)}
                    </p>
                  </div>
                </li>
              ))}
            </ol>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
