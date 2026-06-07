import Link from "next/link";
import {
  ShieldCheck,
  Zap,
  LayoutTemplate,
  Boxes,
  QrCode,
  ScrollText,
  Lock,
  ArrowRight,
  CheckCircle2,
} from "lucide-react";
import { Logo, TrustSeal } from "@/components/brand";
import { ButtonLink } from "@/components/ui/button";

const features = [
  { icon: Zap, title: "Emite en minutos", text: "Crea y entrega certificados individuales o por miles, sin Word ni hojas de cálculo." },
  { icon: LayoutTemplate, title: "Plantillas con tu marca", text: "Diseños elegantes con tu logo, colores y firmas. Listos para reutilizar." },
  { icon: QrCode, title: "Verificación simple", text: "Cualquiera valida la autenticidad con un código o QR, en segundos." },
  { icon: ShieldCheck, title: "Menos fraude", text: "Documentos imposibles de alterar sin que se note. Confianza real para terceros." },
  { icon: ScrollText, title: "Control y trazabilidad", text: "Sabe qué se emitió, a quién, cuándo y por quién. Todo auditado." },
  { icon: Boxes, title: "Todo centralizado", text: "Un solo lugar para emitir, almacenar, revocar y reemplazar documentos." },
];

const problems = [
  "Certificados hechos a mano en Word o PDF, lentos y propensos a errores.",
  "Sin control de qué se emitió, a quién y con qué estado.",
  "Documentos que se copian, alteran o presentan de forma falsa.",
  "Verificar autenticidad requiere correos, llamadas y revisiones manuales.",
];

export default function LandingPage() {
  return (
    <div className="min-h-screen bg-canvas">
      {/* Header */}
      <header className="sticky top-0 z-30 border-b border-border bg-surface/80 backdrop-blur">
        <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
          <Logo />
          <nav className="hidden items-center gap-7 text-sm font-medium text-ink-soft md:flex">
            <a href="#producto" className="hover:text-ink">Producto</a>
            <a href="#como-funciona" className="hover:text-ink">Cómo funciona</a>
            <Link href="/verificar" className="hover:text-ink">Verificar</Link>
          </nav>
          <div className="flex items-center gap-2">
            <ButtonLink href="/login" variant="ghost" size="sm">Iniciar sesión</ButtonLink>
            <ButtonLink href="/registro" size="sm">Comenzar gratis</ButtonLink>
          </div>
        </div>
      </header>

      {/* Hero */}
      <section className="relative overflow-hidden tf-grid-bg">
        <div className="mx-auto max-w-6xl px-5 pb-20 pt-16 text-center sm:pt-24">
          <span className="inline-flex items-center gap-2 rounded-full border border-border bg-surface px-3 py-1 text-xs font-medium text-ink-soft shadow-[var(--shadow-xs)]">
            <span className="h-1.5 w-1.5 rounded-full bg-success-500" />
            Confianza para instituciones educativas y certificadoras
          </span>
          <h1 className="mx-auto mt-6 max-w-3xl text-4xl font-semibold leading-[1.1] tracking-tight text-ink sm:text-[56px]">
            La forma más fácil de emitir y validar{" "}
            <span className="text-brand-600">certificados confiables</span>
          </h1>
          <p className="mx-auto mt-5 max-w-xl text-lg text-muted">
            TrustFile te ayuda a emitir certificados y documentos en minutos, mantener el control total
            y permitir que cualquiera verifique su autenticidad al instante.
          </p>
          <div className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <ButtonLink href="/registro" size="lg" className="w-full sm:w-auto">
              Empieza ahora <ArrowRight className="h-4 w-4" />
            </ButtonLink>
            <ButtonLink href="/app" variant="secondary" size="lg" className="w-full sm:w-auto">
              Ver demo del panel
            </ButtonLink>
          </div>
          <p className="mt-4 text-xs text-faint">Sin tarjeta · Configura tu organización en 2 minutos</p>

          {/* Mockup de certificado */}
          <div className="mx-auto mt-14 max-w-2xl">
            <div className="rounded-[var(--radius-xl)] border border-border bg-surface p-2 shadow-[var(--shadow-lg)]">
              <div className="rounded-[var(--radius-lg)] border border-border bg-gradient-to-b from-surface-2 to-surface px-8 py-10 text-left">
                <div className="flex items-center justify-between">
                  <span className="text-sm font-semibold text-ink">Instituto Aurora</span>
                  <TrustSeal />
                </div>
                <p className="mt-8 text-center text-xs uppercase tracking-[0.25em] text-muted">Certifica que</p>
                <p className="mt-2 text-center text-2xl font-semibold text-ink">Andrea Beltrán</p>
                <p className="mt-2 text-center text-sm text-muted">
                  ha concluido el <span className="font-medium text-ink-soft">Diplomado en Diseño UX</span>
                </p>
                <div className="mt-8 flex items-end justify-between">
                  <div className="text-left">
                    <div className="h-px w-28 bg-border-strong" />
                    <p className="mt-1 text-xs text-muted">Dirección académica</p>
                  </div>
                  <div className="flex h-16 w-16 items-center justify-center rounded-lg border border-border bg-surface-2">
                    <QrCode className="h-9 w-9 text-ink-soft" strokeWidth={1.2} />
                  </div>
                </div>
              </div>
            </div>
            <p className="mt-3 text-center text-xs text-faint">
              Código de verificación TF-7QK2-9MX4 · válido públicamente
            </p>
          </div>
        </div>
      </section>

      {/* Problema */}
      <section className="border-y border-border bg-surface">
        <div className="mx-auto grid max-w-6xl gap-10 px-5 py-16 md:grid-cols-2">
          <div>
            <h2 className="text-2xl font-semibold tracking-tight text-ink">
              Emitir y validar documentos no debería ser tan difícil
            </h2>
            <p className="mt-3 text-muted">
              Hoy las organizaciones pierden tiempo, control y credibilidad con procesos manuales.
              TrustFile lo resuelve de raíz.
            </p>
          </div>
          <ul className="space-y-3">
            {problems.map((p) => (
              <li key={p} className="flex items-start gap-3 rounded-[var(--radius-md)] border border-border bg-surface-2 px-4 py-3">
                <span className="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-danger-50 text-danger-500">✕</span>
                <span className="text-sm text-ink-soft">{p}</span>
              </li>
            ))}
          </ul>
        </div>
      </section>

      {/* Features */}
      <section id="producto" className="mx-auto max-w-6xl px-5 py-20">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-semibold tracking-tight text-ink">Todo lo que necesitas para generar confianza</h2>
          <p className="mt-3 text-muted">Una plataforma clara, ordenada y pensada para personas no técnicas.</p>
        </div>
        <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {features.map((f) => (
            <div key={f.title} className="rounded-[var(--radius-lg)] border border-border bg-surface p-6 shadow-[var(--shadow-sm)] transition-shadow hover:shadow-[var(--shadow-md)]">
              <span className="flex h-11 w-11 items-center justify-center rounded-[var(--radius-md)] bg-brand-50 text-brand-600">
                <f.icon className="h-[22px] w-[22px]" strokeWidth={1.8} />
              </span>
              <h3 className="mt-4 text-base font-semibold text-ink">{f.title}</h3>
              <p className="mt-1.5 text-sm text-muted">{f.text}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Cómo funciona */}
      <section id="como-funciona" className="border-y border-border bg-surface">
        <div className="mx-auto max-w-6xl px-5 py-20">
          <div className="mx-auto max-w-2xl text-center">
            <h2 className="text-3xl font-semibold tracking-tight text-ink">Tan simple como tres pasos</h2>
          </div>
          <div className="mt-12 grid gap-6 md:grid-cols-3">
            {[
              { n: "1", t: "Diseña tu plantilla", d: "Sube tu marca y define los datos del certificado una sola vez." },
              { n: "2", t: "Emite los certificados", d: "Uno a uno o por miles con un archivo CSV. Listo en minutos." },
              { n: "3", t: "Comparte y verifica", d: "Cada documento lleva un código y QR para validarlo al instante." },
            ].map((s) => (
              <div key={s.n} className="relative rounded-[var(--radius-lg)] border border-border bg-surface-2 p-6">
                <span className="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">{s.n}</span>
                <h3 className="mt-4 text-base font-semibold text-ink">{s.t}</h3>
                <p className="mt-1.5 text-sm text-muted">{s.d}</p>
              </div>
            ))}
          </div>

          <div className="mx-auto mt-12 flex max-w-2xl flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-muted">
            {["Reduce el fraude", "Control total", "Verificación pública", "Trazabilidad auditable"].map((b) => (
              <span key={b} className="inline-flex items-center gap-1.5">
                <CheckCircle2 className="h-4 w-4 text-success-500" /> {b}
              </span>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="mx-auto max-w-6xl px-5 py-20">
        <div className="overflow-hidden rounded-[var(--radius-xl)] border border-brand-700 bg-brand-700 px-8 py-14 text-center shadow-[var(--shadow-lg)]">
          <h2 className="mx-auto max-w-2xl text-3xl font-semibold tracking-tight text-white">
            Empieza a emitir certificados confiables hoy
          </h2>
          <p className="mx-auto mt-3 max-w-lg text-brand-100">
            Configura tu organización en minutos y dale a tus documentos la confianza que merecen.
          </p>
          <div className="mt-7 flex justify-center">
            <ButtonLink href="/registro" size="lg" variant="secondary">
              Crear mi cuenta <ArrowRight className="h-4 w-4" />
            </ButtonLink>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-border bg-surface">
        <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-5 py-8 sm:flex-row">
          <Logo />
          <p className="flex items-center gap-2 text-xs text-muted">
            <Lock className="h-3.5 w-3.5" />
            Confianza impulsada por TrustHunter · una plataforma de Nodoa
          </p>
        </div>
      </footer>
    </div>
  );
}
