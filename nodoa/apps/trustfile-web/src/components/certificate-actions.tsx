"use client";

import { useState } from "react";
import {
  Download,
  Send,
  MoreHorizontal,
  Ban,
  RefreshCcw,
  Copy,
  Link2,
  BadgeCheck,
} from "lucide-react";
import { Button, ButtonLink } from "@/components/ui/button";
import type { Certificate } from "@/lib/types";
import { cn } from "@/lib/cn";

export function CertificateActions({ certificate }: { certificate: Certificate }) {
  const [menuOpen, setMenuOpen] = useState(false);
  const [confirmRevoke, setConfirmRevoke] = useState(false);
  const [toast, setToast] = useState<string | null>(null);

  const revocable = certificate.status === "emitido";

  function fakeAction(message: string) {
    setMenuOpen(false);
    setToast(message);
    setTimeout(() => setToast(null), 2600);
  }

  return (
    <div className="flex items-center gap-2">
      <Button variant="secondary" onClick={() => fakeAction("PDF descargado (demo).")}>
        <Download className="h-4 w-4" /> Descargar PDF
      </Button>
      <ButtonLink href={`/verificar/${certificate.code}`} target="_blank">
        <BadgeCheck className="h-4 w-4" /> Ver validación
      </ButtonLink>

      {/* Menú */}
      <div className="relative">
        <Button variant="secondary" size="icon" onClick={() => setMenuOpen((o) => !o)} aria-label="Más acciones">
          <MoreHorizontal className="h-4 w-4" />
        </Button>
        {menuOpen && (
          <>
            <div className="fixed inset-0 z-10" onClick={() => setMenuOpen(false)} />
            <div className="absolute right-0 z-20 mt-1.5 w-56 overflow-hidden rounded-[var(--radius-md)] border border-border bg-surface py-1 shadow-[var(--shadow-lg)]">
              <MenuItem icon={Send} label="Reenviar al destinatario" onClick={() => fakeAction("Certificado reenviado por correo (demo).")} />
              <MenuItem icon={Link2} label="Regenerar enlace de acceso" onClick={() => fakeAction("Enlace de acceso regenerado (demo).")} />
              <MenuItem icon={Copy} label="Duplicar" onClick={() => fakeAction("Borrador duplicado (demo).")} />
              <MenuItem icon={RefreshCcw} label="Reemplazar" onClick={() => fakeAction("Flujo de reemplazo iniciado (demo).")} />
              <div className="my-1 h-px bg-border" />
              <MenuItem
                icon={Ban}
                label="Revocar certificado"
                danger
                disabled={!revocable}
                onClick={() => {
                  setMenuOpen(false);
                  setConfirmRevoke(true);
                }}
              />
            </div>
          </>
        )}
      </div>

      {/* Confirmación de revocación */}
      {confirmRevoke && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-ink/40 p-4" onClick={() => setConfirmRevoke(false)}>
          <div
            className="w-full max-w-md rounded-[var(--radius-lg)] border border-border bg-surface p-6 shadow-[var(--shadow-lg)] tf-fade-up"
            onClick={(e) => e.stopPropagation()}
          >
            <span className="flex h-11 w-11 items-center justify-center rounded-full bg-danger-50 text-danger-700">
              <Ban className="h-5 w-5" />
            </span>
            <h3 className="mt-4 text-lg font-semibold text-ink">Revocar este certificado</h3>
            <p className="mt-1.5 text-sm text-muted">
              El certificado de <span className="font-medium text-ink-soft">{certificate.recipientName}</span> dejará de
              ser válido y la página pública lo mostrará como revocado. Esta acción queda registrada en la auditoría.
            </p>
            <div className="mt-6 flex justify-end gap-2">
              <Button variant="secondary" onClick={() => setConfirmRevoke(false)}>Cancelar</Button>
              <Button variant="danger" onClick={() => { setConfirmRevoke(false); fakeAction("Certificado revocado (demo)."); }}>
                Sí, revocar
              </Button>
            </div>
          </div>
        </div>
      )}

      {/* Toast */}
      {toast && (
        <div className="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-[var(--radius-md)] border border-border bg-ink px-4 py-2.5 text-sm font-medium text-white shadow-[var(--shadow-lg)] tf-fade-up">
          {toast}
        </div>
      )}
    </div>
  );
}

function MenuItem({
  icon: Icon,
  label,
  onClick,
  danger,
  disabled,
}: {
  icon: typeof Send;
  label: string;
  onClick: () => void;
  danger?: boolean;
  disabled?: boolean;
}) {
  return (
    <button
      onClick={onClick}
      disabled={disabled}
      className={cn(
        "flex w-full items-center gap-2.5 px-3.5 py-2 text-left text-sm transition-colors disabled:opacity-40",
        danger ? "text-danger-700 hover:bg-danger-50" : "text-ink-soft hover:bg-surface-2"
      )}
    >
      <Icon className="h-4 w-4" /> {label}
    </button>
  );
}
