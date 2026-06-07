import Link from "next/link";
import { ShieldCheck, QrCode, ScrollText } from "lucide-react";
import { Logo } from "@/components/brand";

const POINTS = [
  { icon: ShieldCheck, text: "Documentos confiables, imposibles de falsificar sin que se note." },
  { icon: QrCode, text: "Verificación pública con código o QR, en segundos." },
  { icon: ScrollText, text: "Control total y trazabilidad de cada certificado emitido." },
];

export function AuthShell({
  title,
  subtitle,
  children,
  footer,
}: {
  title: string;
  subtitle: string;
  children: React.ReactNode;
  footer?: React.ReactNode;
}) {
  return (
    <div className="min-h-screen lg:grid lg:grid-cols-2">
      {/* Panel izquierdo (marca) */}
      <div className="relative hidden flex-col justify-between overflow-hidden bg-brand-700 p-10 text-white lg:flex">
        <div className="absolute inset-0 tf-grid-bg opacity-30" />
        <div className="relative">
          <Link href="/">
            <Logo tone="light" />
          </Link>
        </div>
        <div className="relative max-w-md">
          <h2 className="text-3xl font-semibold leading-tight tracking-tight">
            Recupera la confianza en tus documentos.
          </h2>
          <ul className="mt-8 space-y-4">
            {POINTS.map((p) => (
              <li key={p.text} className="flex items-start gap-3">
                <span className="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-[var(--radius-md)] bg-white/15">
                  <p.icon className="h-[18px] w-[18px]" strokeWidth={1.9} />
                </span>
                <span className="text-[15px] text-brand-100">{p.text}</span>
              </li>
            ))}
          </ul>
        </div>
        <p className="relative text-xs text-brand-200">Una plataforma de Nodoa · impulsada por TrustHunter</p>
      </div>

      {/* Panel derecho (formulario) */}
      <div className="flex min-h-screen items-center justify-center px-5 py-12">
        <div className="w-full max-w-sm tf-fade-up">
          <div className="mb-8 lg:hidden">
            <Link href="/">
              <Logo />
            </Link>
          </div>
          <h1 className="text-[26px] font-semibold tracking-tight text-ink">{title}</h1>
          <p className="mt-1.5 text-sm text-muted">{subtitle}</p>
          <div className="mt-7">{children}</div>
          {footer && <div className="mt-6 text-center text-sm text-muted">{footer}</div>}
        </div>
      </div>
    </div>
  );
}
