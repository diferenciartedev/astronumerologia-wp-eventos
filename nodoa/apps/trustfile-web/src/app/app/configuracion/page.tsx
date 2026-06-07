"use client";

import { useState } from "react";
import { Palette, Users, BadgeCheck, Plug, Plus, ShieldCheck } from "lucide-react";
import { PageHeader } from "@/components/ui/misc";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Field, Input, Select } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { getOrganization, getMembers } from "@/lib/mock-data";
import { initials, roleLabel } from "@/lib/format";
import { cn } from "@/lib/cn";

const TABS = [
  { id: "marca", label: "Marca", icon: Palette },
  { id: "equipo", label: "Equipo y roles", icon: Users },
  { id: "verificacion", label: "Verificación", icon: BadgeCheck },
  { id: "motor", label: "Motor de confianza", icon: Plug },
] as const;

type TabId = (typeof TABS)[number]["id"];

export default function ConfiguracionPage() {
  const [tab, setTab] = useState<TabId>("marca");
  const org = getOrganization();
  const members = getMembers();

  return (
    <div className="space-y-6">
      <PageHeader title="Configuración" description="Personaliza tu organización, tu equipo y cómo se verifican tus documentos." />

      <div className="grid gap-6 lg:grid-cols-[220px_1fr]">
        {/* Nav lateral */}
        <nav className="flex gap-1.5 overflow-x-auto lg:flex-col">
          {TABS.map((t) => (
            <button
              key={t.id}
              onClick={() => setTab(t.id)}
              className={cn(
                "flex items-center gap-2.5 whitespace-nowrap rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium transition-colors",
                tab === t.id ? "bg-brand-50 text-brand-700" : "text-ink-soft hover:bg-surface-2"
              )}
            >
              <t.icon className="h-4 w-4" /> {t.label}
            </button>
          ))}
        </nav>

        <div>
          {tab === "marca" && (
            <Card>
              <CardHeader><CardTitle>Identidad de la organización</CardTitle></CardHeader>
              <CardContent className="space-y-4">
                <div className="grid gap-4 sm:grid-cols-2">
                  <Field label="Nombre"><Input defaultValue={org.name} /></Field>
                  <Field label="Tipo">
                    <Select defaultValue={org.type}>
                      <option value="instituto">Instituto</option>
                      <option value="universidad">Universidad</option>
                      <option value="academia">Academia</option>
                      <option value="certificadora">Entidad certificadora</option>
                    </Select>
                  </Field>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <Field label="País"><Input defaultValue={org.country} /></Field>
                  <Field label="Correo de contacto"><Input defaultValue={org.contactEmail} /></Field>
                </div>
                <Field label="Color principal">
                  <div className="flex items-center gap-2">
                    <span className="h-9 w-9 rounded-md border border-border" style={{ backgroundColor: org.branding.primaryColor }} />
                    <Input defaultValue={org.branding.primaryColor} className="font-mono max-w-[160px]" />
                  </div>
                </Field>
                <div className="flex justify-end border-t border-border pt-4">
                  <Button>Guardar cambios</Button>
                </div>
              </CardContent>
            </Card>
          )}

          {tab === "equipo" && (
            <Card>
              <CardHeader className="flex items-center justify-between">
                <CardTitle>Miembros del equipo</CardTitle>
                <Button size="sm"><Plus className="h-4 w-4" /> Invitar</Button>
              </CardHeader>
              <CardContent>
                <ul className="divide-y divide-border">
                  {members.map((m) => (
                    <li key={m.id} className="flex items-center gap-3 py-3">
                      <span className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-[12px] font-semibold text-brand-700">
                        {initials(m.name)}
                      </span>
                      <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-medium text-ink">{m.name}</p>
                        <p className="truncate text-xs text-muted">{m.email}</p>
                      </div>
                      <span className="hidden text-xs text-faint sm:block">{m.lastActive}</span>
                      <Badge tone={m.role === "owner" ? "brand" : "neutral"}>{roleLabel(m.role)}</Badge>
                    </li>
                  ))}
                </ul>
              </CardContent>
            </Card>
          )}

          {tab === "verificacion" && (
            <Card>
              <CardHeader><CardTitle>Página pública de verificación</CardTitle></CardHeader>
              <CardContent className="space-y-4">
                <Field label="Mensaje de confianza" hint="Aparece en la página pública cuando un documento es válido.">
                  <Input defaultValue="La autenticidad de este documento está verificada." />
                </Field>
                <Field label="Dominio de verificación">
                  <Input defaultValue="trustfile.app/verificar" className="font-mono" />
                </Field>
                <SettingRow title="Mostrar datos del destinatario" desc="Nombre y certificado visibles públicamente." on />
                <SettingRow title="Permitir descarga del PDF" desc="Cualquiera con el enlace puede descargar el documento." on />
                <SettingRow title="Registrar verificaciones" desc="Cada verificación queda en la auditoría." on />
                <div className="flex justify-end border-t border-border pt-4">
                  <Button>Guardar cambios</Button>
                </div>
              </CardContent>
            </Card>
          )}

          {tab === "motor" && (
            <Card>
              <CardHeader><CardTitle>Motor de confianza (TrustHunter)</CardTitle></CardHeader>
              <CardContent className="space-y-4">
                <div className="flex items-start gap-3 rounded-[var(--radius-md)] border border-success-100 bg-success-50 p-4">
                  <ShieldCheck className="mt-0.5 h-5 w-5 shrink-0 text-success-600" />
                  <div className="text-sm">
                    <p className="font-medium text-success-700">Conexión activa</p>
                    <p className="text-success-700/80">
                      Tus documentos se sellan y registran automáticamente. No necesitas configurar nada.
                    </p>
                  </div>
                </div>
                <SettingRow title="Sellar integridad al emitir" desc="Cada certificado recibe un registro de integridad." on />
                <SettingRow title="Anclar prueba de forma reforzada" desc="Refuerza la confianza para documentos críticos." on />
                <SettingRow title="Firma digital en documentos" desc="Habilita flujos de firma para certificados que lo requieran." />
                <div className="rounded-[var(--radius-md)] border border-border bg-surface-2 p-4 text-sm text-muted">
                  ¿Eres desarrollador? La API de TrustHunter permite integrar verificación e identidad
                  en tus propios sistemas. <span className="font-medium text-brand-600">Solicitar acceso a la API →</span>
                </div>
              </CardContent>
            </Card>
          )}
        </div>
      </div>
    </div>
  );
}

function SettingRow({ title, desc, on }: { title: string; desc: string; on?: boolean }) {
  return (
    <div className="flex items-center justify-between gap-4 border-t border-border py-3 first:border-0 first:pt-0">
      <div>
        <p className="text-sm font-medium text-ink">{title}</p>
        <p className="text-xs text-muted">{desc}</p>
      </div>
      <span className={`relative h-5 w-9 shrink-0 rounded-full transition-colors ${on ? "bg-brand-600" : "bg-border-strong"}`}>
        <span className={`absolute top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transition-all ${on ? "left-[18px]" : "left-0.5"}`} />
      </span>
    </div>
  );
}
