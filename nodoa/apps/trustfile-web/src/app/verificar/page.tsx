"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { BadgeCheck, Search, ShieldCheck, QrCode } from "lucide-react";
import { Logo } from "@/components/brand";
import { Button } from "@/components/ui/button";

export default function VerificarPage() {
  const router = useRouter();
  const [code, setCode] = useState("");

  function submit(e: React.FormEvent) {
    e.preventDefault();
    const c = code.trim();
    if (c) router.push(`/verificar/${encodeURIComponent(c)}`);
  }

  return (
    <div className="flex min-h-screen flex-col bg-canvas tf-grid-bg">
      <header className="border-b border-border bg-surface/80 backdrop-blur">
        <div className="mx-auto flex h-16 max-w-3xl items-center justify-between px-5">
          <Link href="/"><Logo /></Link>
          <Link href="/login" className="text-sm font-medium text-ink-soft hover:text-ink">Iniciar sesión</Link>
        </div>
      </header>

      <main className="flex flex-1 items-center justify-center px-5 py-16">
        <div className="w-full max-w-lg text-center tf-fade-up">
          <span className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-600 text-white shadow-[var(--shadow-md)]">
            <BadgeCheck className="h-7 w-7" />
          </span>
          <h1 className="mt-6 text-3xl font-semibold tracking-tight text-ink">Verifica un certificado</h1>
          <p className="mx-auto mt-2 max-w-md text-muted">
            Ingresa el código de verificación que aparece en el documento o escanea su código QR.
          </p>

          <form onSubmit={submit} className="mx-auto mt-8 flex max-w-md gap-2">
            <div className="relative flex-1">
              <Search className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-faint" />
              <input
                value={code}
                onChange={(e) => setCode(e.target.value)}
                placeholder="Ej. TF-7QK2-9MX4"
                className="h-12 w-full rounded-[var(--radius-md)] border border-border-strong bg-surface pl-10 pr-3 font-mono text-sm uppercase text-ink shadow-[var(--shadow-xs)] placeholder:font-sans placeholder:normal-case placeholder:text-faint focus:border-brand-400 focus:outline-none focus:ring-4 focus:ring-brand-100"
              />
            </div>
            <Button type="submit" size="lg">Verificar</Button>
          </form>

          <div className="mx-auto mt-10 grid max-w-md grid-cols-2 gap-3 text-left">
            <Feature icon={ShieldCheck} text="Confirma que el documento es auténtico y no fue alterado." />
            <Feature icon={QrCode} text="Funciona con el código o el QR del certificado." />
          </div>

          <p className="mt-8 text-xs text-faint">
            ¿Eres una organización?{" "}
            <Link href="/registro" className="font-medium text-brand-600 hover:text-brand-700">Emite certificados confiables</Link>
          </p>
        </div>
      </main>
    </div>
  );
}

function Feature({ icon: Icon, text }: { icon: typeof ShieldCheck; text: string }) {
  return (
    <div className="flex items-start gap-2.5 rounded-[var(--radius-md)] border border-border bg-surface px-3.5 py-3">
      <Icon className="mt-0.5 h-4 w-4 shrink-0 text-brand-600" />
      <span className="text-xs text-ink-soft">{text}</span>
    </div>
  );
}
