import Link from "next/link";
import { AuthShell } from "@/components/auth-shell";
import { ButtonLink } from "@/components/ui/button";
import { Field, Input } from "@/components/ui/input";

export const metadata = { title: "Iniciar sesión" };

export default function LoginPage() {
  return (
    <AuthShell
      title="Bienvenido de nuevo"
      subtitle="Inicia sesión para gestionar tus certificados."
      footer={
        <>
          ¿No tienes cuenta?{" "}
          <Link href="/registro" className="font-medium text-brand-600 hover:text-brand-700">
            Crear organización
          </Link>
        </>
      }
    >
      <form className="space-y-4">
        <Field label="Correo de trabajo" htmlFor="email">
          <Input id="email" type="email" placeholder="tu@organizacion.com" autoComplete="email" />
        </Field>
        <Field label="Contraseña" htmlFor="password">
          <Input id="password" type="password" placeholder="••••••••" autoComplete="current-password" />
        </Field>
        <div className="flex items-center justify-between">
          <label className="flex items-center gap-2 text-sm text-ink-soft">
            <input type="checkbox" className="h-4 w-4 rounded border-border-strong text-brand-600 focus:ring-brand-300" />
            Recordarme
          </label>
          <Link href="/recuperar" className="text-sm font-medium text-brand-600 hover:text-brand-700">
            ¿Olvidaste tu contraseña?
          </Link>
        </div>
        {/* Demo: navega directo al panel */}
        <ButtonLink href="/app" className="w-full" size="lg">
          Iniciar sesión
        </ButtonLink>
      </form>
    </AuthShell>
  );
}
