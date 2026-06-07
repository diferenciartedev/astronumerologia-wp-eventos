import Link from "next/link";
import { AuthShell } from "@/components/auth-shell";
import { Button } from "@/components/ui/button";
import { Field, Input } from "@/components/ui/input";

export const metadata = { title: "Recuperar contraseña" };

export default function RecuperarPage() {
  return (
    <AuthShell
      title="Recupera tu acceso"
      subtitle="Te enviaremos un enlace para restablecer tu contraseña."
      footer={
        <Link href="/login" className="font-medium text-brand-600 hover:text-brand-700">
          ← Volver a iniciar sesión
        </Link>
      }
    >
      <form className="space-y-4">
        <Field label="Correo de trabajo" htmlFor="email">
          <Input id="email" type="email" placeholder="tu@organizacion.com" autoComplete="email" />
        </Field>
        <Button type="button" className="w-full" size="lg">
          Enviar enlace de recuperación
        </Button>
      </form>
    </AuthShell>
  );
}
