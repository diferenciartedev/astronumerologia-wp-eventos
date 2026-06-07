"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Check, Building2, Palette, PartyPopper, ArrowRight, ArrowLeft } from "lucide-react";
import { Logo } from "@/components/brand";
import { Button, ButtonLink } from "@/components/ui/button";
import { Field, Input, Select } from "@/components/ui/input";
import { cn } from "@/lib/cn";

const STEPS = [
  { id: 1, label: "Organización", icon: Building2 },
  { id: 2, label: "Marca", icon: Palette },
  { id: 3, label: "Listo", icon: PartyPopper },
];

const PRESET_COLORS = ["#3f49d4", "#12b76a", "#7c3aed", "#0ea5e9", "#e11d48", "#f59e0b"];

export default function OnboardingPage() {
  const router = useRouter();
  const [step, setStep] = useState(1);
  const [name, setName] = useState("");
  const [color, setColor] = useState("#3f49d4");

  return (
    <div className="min-h-screen bg-canvas">
      <header className="border-b border-border bg-surface">
        <div className="mx-auto flex h-16 max-w-3xl items-center justify-between px-5">
          <Logo />
          <ButtonLink href="/login" variant="ghost" size="sm">Guardar y salir</ButtonLink>
        </div>
      </header>

      <div className="mx-auto max-w-2xl px-5 py-10">
        {/* Stepper */}
        <ol className="mb-10 flex items-center">
          {STEPS.map((s, i) => {
            const done = step > s.id;
            const active = step === s.id;
            return (
              <li key={s.id} className={cn("flex items-center", i < STEPS.length - 1 && "flex-1")}>
                <div className="flex items-center gap-2.5">
                  <span
                    className={cn(
                      "flex h-9 w-9 items-center justify-center rounded-full border text-sm font-semibold transition-colors",
                      done && "border-brand-600 bg-brand-600 text-white",
                      active && "border-brand-600 bg-brand-50 text-brand-700",
                      !done && !active && "border-border bg-surface text-faint"
                    )}
                  >
                    {done ? <Check className="h-4 w-4" /> : s.id}
                  </span>
                  <span className={cn("hidden text-sm font-medium sm:block", active || done ? "text-ink" : "text-faint")}>
                    {s.label}
                  </span>
                </div>
                {i < STEPS.length - 1 && (
                  <span className={cn("mx-3 h-px flex-1", step > s.id ? "bg-brand-600" : "bg-border")} />
                )}
              </li>
            );
          })}
        </ol>

        <div className="rounded-[var(--radius-xl)] border border-border bg-surface p-7 shadow-[var(--shadow-sm)] tf-fade-up">
          {step === 1 && (
            <div className="space-y-5">
              <div>
                <h1 className="text-xl font-semibold tracking-tight text-ink">Cuéntanos de tu organización</h1>
                <p className="mt-1 text-sm text-muted">Esta información aparecerá en tus certificados.</p>
              </div>
              <Field label="Nombre de la organización" htmlFor="org">
                <Input id="org" value={name} onChange={(e) => setName(e.target.value)} placeholder="Ej. Instituto Aurora" />
              </Field>
              <div className="grid gap-4 sm:grid-cols-2">
                <Field label="Tipo de organización">
                  <Select defaultValue="instituto">
                    <option value="universidad">Universidad</option>
                    <option value="instituto">Instituto</option>
                    <option value="escuela">Escuela</option>
                    <option value="academia">Academia</option>
                    <option value="centro_formacion">Centro de formación</option>
                    <option value="certificadora">Entidad certificadora</option>
                    <option value="empresa">Empresa</option>
                  </Select>
                </Field>
                <Field label="País">
                  <Select defaultValue="MX">
                    <option value="MX">México</option>
                    <option value="CO">Colombia</option>
                    <option value="AR">Argentina</option>
                    <option value="CL">Chile</option>
                    <option value="PE">Perú</option>
                    <option value="ES">España</option>
                  </Select>
                </Field>
              </div>
              <Field label="Correo de contacto" htmlFor="cmail">
                <Input id="cmail" type="email" placeholder="registro@organizacion.com" />
              </Field>
            </div>
          )}

          {step === 2 && (
            <div className="space-y-5">
              <div>
                <h1 className="text-xl font-semibold tracking-tight text-ink">Dale tu identidad de marca</h1>
                <p className="mt-1 text-sm text-muted">Personaliza el color que verán en tus documentos.</p>
              </div>
              <Field label="Color principal">
                <div className="flex flex-wrap gap-2.5">
                  {PRESET_COLORS.map((c) => (
                    <button
                      key={c}
                      type="button"
                      onClick={() => setColor(c)}
                      className={cn(
                        "h-10 w-10 rounded-full border-2 transition-transform",
                        color === c ? "scale-110 border-ink" : "border-white shadow-[var(--shadow-xs)]"
                      )}
                      style={{ backgroundColor: c }}
                      aria-label={c}
                    />
                  ))}
                </div>
              </Field>

              {/* Vista previa */}
              <div className="rounded-[var(--radius-lg)] border border-border bg-surface-2 p-5">
                <p className="mb-3 text-xs font-medium uppercase tracking-wide text-faint">Vista previa</p>
                <div className="rounded-[var(--radius-md)] border border-border bg-surface px-5 py-6 text-center">
                  <span
                    className="mx-auto flex h-9 w-9 items-center justify-center rounded-lg text-white"
                    style={{ backgroundColor: color }}
                  >
                    {(name || "Mi organización").charAt(0).toUpperCase()}
                  </span>
                  <p className="mt-3 text-sm font-semibold text-ink">{name || "Mi organización"}</p>
                  <p className="text-xs text-muted">Certificado de ejemplo</p>
                  <span
                    className="mt-3 inline-block rounded-full px-3 py-1 text-xs font-medium text-white"
                    style={{ backgroundColor: color }}
                  >
                    Documento verificado
                  </span>
                </div>
              </div>
            </div>
          )}

          {step === 3 && (
            <div className="py-4 text-center">
              <span className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-success-50 text-success-700">
                <PartyPopper className="h-7 w-7" strokeWidth={1.8} />
              </span>
              <h1 className="mt-4 text-xl font-semibold tracking-tight text-ink">¡Todo listo!</h1>
              <p className="mx-auto mt-1.5 max-w-sm text-sm text-muted">
                Tu organización <span className="font-medium text-ink-soft">{name || "está creada"}</span> y lista para
                emitir certificados confiables. Te llevaremos a tu panel.
              </p>
              <ButtonLink href="/app" size="lg" className="mt-6">
                Ir a mi panel <ArrowRight className="h-4 w-4" />
              </ButtonLink>
            </div>
          )}

          {step < 3 && (
            <div className="mt-7 flex items-center justify-between">
              <Button variant="ghost" size="sm" onClick={() => setStep((s) => Math.max(1, s - 1))} disabled={step === 1}>
                <ArrowLeft className="h-4 w-4" /> Atrás
              </Button>
              <Button onClick={() => setStep((s) => s + 1)}>
                Continuar <ArrowRight className="h-4 w-4" />
              </Button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
