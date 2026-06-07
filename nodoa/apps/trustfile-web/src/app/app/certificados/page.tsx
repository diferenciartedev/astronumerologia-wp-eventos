"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import { Search, Download, FilePlus2, FileBadge, SlidersHorizontal } from "lucide-react";
import { PageHeader, EmptyState } from "@/components/ui/misc";
import { Card } from "@/components/ui/card";
import { StatusBadge, Badge, type CertStatus } from "@/components/ui/badge";
import { Button, ButtonLink } from "@/components/ui/button";
import { Select } from "@/components/ui/input";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { getCertificates, getTemplates } from "@/lib/mock-data";
import { formatDate } from "@/lib/format";
import { cn } from "@/lib/cn";

const STATUS_TABS: { value: CertStatus | "todos"; label: string }[] = [
  { value: "todos", label: "Todos" },
  { value: "emitido", label: "Emitidos" },
  { value: "borrador", label: "Borradores" },
  { value: "revocado", label: "Revocados" },
  { value: "reemplazado", label: "Reemplazados" },
  { value: "expirado", label: "Expirados" },
];

export default function CertificadosPage() {
  const all = getCertificates();
  const templates = getTemplates();
  const [query, setQuery] = useState("");
  const [status, setStatus] = useState<CertStatus | "todos">("todos");
  const [template, setTemplate] = useState("todas");

  const filtered = useMemo(() => {
    return all.filter((c) => {
      if (status !== "todos" && c.status !== status) return false;
      if (template !== "todas" && c.templateId !== template) return false;
      if (query) {
        const q = query.toLowerCase();
        if (
          !c.recipientName.toLowerCase().includes(q) &&
          !c.code.toLowerCase().includes(q) &&
          !c.title.toLowerCase().includes(q) &&
          !c.recipientEmail.toLowerCase().includes(q)
        )
          return false;
      }
      return true;
    });
  }, [all, status, template, query]);

  const counts = useMemo(() => {
    const map: Record<string, number> = { todos: all.length };
    for (const c of all) map[c.status] = (map[c.status] ?? 0) + 1;
    return map;
  }, [all]);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Certificados"
        description="Busca, filtra y gestiona todos los certificados emitidos por tu organización."
      >
        <Button variant="secondary">
          <Download className="h-4 w-4" /> Exportar
        </Button>
        <ButtonLink href="/app/emitir">
          <FilePlus2 className="h-4 w-4" /> Emitir
        </ButtonLink>
      </PageHeader>

      {/* Tabs de estado */}
      <div className="flex flex-wrap gap-1.5 border-b border-border pb-px">
        {STATUS_TABS.map((t) => (
          <button
            key={t.value}
            onClick={() => setStatus(t.value)}
            className={cn(
              "relative -mb-px flex items-center gap-2 rounded-t-md px-3 py-2 text-sm font-medium transition-colors",
              status === t.value ? "text-brand-700" : "text-muted hover:text-ink"
            )}
          >
            {t.label}
            <Badge tone={status === t.value ? "brand" : "neutral"} className="px-1.5 py-0 text-[11px]">
              {counts[t.value] ?? 0}
            </Badge>
            {status === t.value && <span className="absolute inset-x-2 -bottom-px h-0.5 rounded-full bg-brand-600" />}
          </button>
        ))}
      </div>

      {/* Filtros */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div className="relative flex-1">
          <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-faint" />
          <input
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Buscar por nombre, correo o código…"
            className="h-10 w-full rounded-[var(--radius-md)] border border-border-strong bg-surface pl-9 pr-3 text-sm text-ink placeholder:text-faint focus:border-brand-400 focus:outline-none focus:ring-4 focus:ring-brand-100"
          />
        </div>
        <div className="flex items-center gap-2">
          <SlidersHorizontal className="h-4 w-4 text-faint" />
          <Select value={template} onChange={(e) => setTemplate(e.target.value)} className="w-52">
            <option value="todas">Todas las plantillas</option>
            {templates.map((t) => (
              <option key={t.id} value={t.id}>{t.name}</option>
            ))}
          </Select>
        </div>
      </div>

      {/* Tabla */}
      <Card className="overflow-hidden">
        {filtered.length === 0 ? (
          <EmptyState
            icon={FileBadge}
            title="Sin resultados"
            description="No encontramos certificados con esos filtros. Prueba ajustar la búsqueda."
            className="border-0"
          />
        ) : (
          <Table>
            <THead>
              <TR>
                <TH>Destinatario</TH>
                <TH>Certificado</TH>
                <TH>Emitido</TH>
                <TH>Vigencia</TH>
                <TH>Estado</TH>
                <TH className="text-right">Confianza</TH>
              </TR>
            </THead>
            <TBody>
              {filtered.map((c) => (
                <TR key={c.id} className="cursor-pointer">
                  <TD>
                    <Link href={`/app/certificados/${c.id}`} className="block">
                      <span className="font-medium text-ink hover:text-brand-700">{c.recipientName}</span>
                      <span className="block text-xs text-muted">{c.recipientEmail}</span>
                    </Link>
                  </TD>
                  <TD>
                    <Link href={`/app/certificados/${c.id}`} className="block">
                      <span className="text-sm text-ink-soft">{c.templateName}</span>
                      <span className="block font-mono text-xs text-faint">{c.code}</span>
                    </Link>
                  </TD>
                  <TD className="whitespace-nowrap text-sm">{formatDate(c.issueDate)}</TD>
                  <TD className="whitespace-nowrap text-sm">
                    {c.expirationDate ? formatDate(c.expirationDate) : <span className="text-faint">Sin vencimiento</span>}
                  </TD>
                  <TD><StatusBadge status={c.status} /></TD>
                  <TD className="text-right">
                    {c.trust.anclado ? (
                      <Badge tone="success">Alta</Badge>
                    ) : (
                      <Badge tone="neutral">Pendiente</Badge>
                    )}
                  </TD>
                </TR>
              ))}
            </TBody>
          </Table>
        )}
      </Card>

      <p className="text-xs text-muted">
        Mostrando {filtered.length} de {all.length} certificados.
      </p>
    </div>
  );
}
