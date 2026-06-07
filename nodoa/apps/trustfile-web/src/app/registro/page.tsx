import Link from "next/link";
import { AuthShell } from "@/components/auth-shell";
import { ButtonLink } from "@/components/ui/button";
import { Field, Input } from "@/components/ui/input";

export const metadata = { title: "Crear organización" };

export default function RegistroPage() {
  return (
    <AuthShell
      title="Crea tu organización"
      subtitle="Empieza a emitir certificados confiables en minutos."
      footer={
        <>
          ¿Ya tienes cuenta?{" "}
          <Link href="/login" className="font-medium text-brand-600 hover:text-brand-700">
            Iniciar sesión
          </Link>
        </>
      }
    >
      <form className="space-y-4">
        <Field label="Tu nombre" htmlFor="name">
          <Input id="name" placeholder="Nombre y apellido" autoComplete="name" />
        </Field>
        <Field label="Correo de trabajo" htmlFor="email">
          <Input id="email" type="email" placeholder="tu@organizacion.com" autoComplete="email" />
        </Field>
        <Field label="Contraseña" htmlFor="password" hint="Mínimo 8 caracteres.">
          <Input id="password" type="password" placeholder="Crea una contraseña" autoComplete="new-password" />
        </Field>
        {/* Demo: continúa al onboarding */}
        <ButtonLink href="/onboarding" className="w-full" size="lg">
          Crear cuenta
        </ButtonLink>
        <p className="text-center text-xs text-faint">
          Al continuar aceptas los Términos y la Política de privacidad.
        </p>
      </form>
    </AuthShell>
  );
}
