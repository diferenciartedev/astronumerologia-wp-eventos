import {
  FilePlus2,
  FileEdit,
  Send,
  PenLine,
  ShieldCheck,
  Link2,
  BadgeCheck,
  Download,
  RefreshCcw,
  Ban,
  type LucideIcon,
} from "lucide-react";
import type { AuditAction } from "@/lib/types";
import { cn } from "@/lib/cn";

const MAP: Record<AuditAction, { icon: LucideIcon; tone: string }> = {
  creado: { icon: FilePlus2, tone: "bg-brand-50 text-brand-600" },
  editado: { icon: FileEdit, tone: "bg-surface-2 text-ink-soft" },
  emitido: { icon: Send, tone: "bg-success-50 text-success-700" },
  firmado: { icon: PenLine, tone: "bg-brand-50 text-brand-600" },
  sellado: { icon: ShieldCheck, tone: "bg-info-50 text-info-700" },
  anclado: { icon: Link2, tone: "bg-info-50 text-info-700" },
  verificado: { icon: BadgeCheck, tone: "bg-success-50 text-success-700" },
  descargado: { icon: Download, tone: "bg-surface-2 text-ink-soft" },
  reenviado: { icon: Send, tone: "bg-surface-2 text-ink-soft" },
  revocado: { icon: Ban, tone: "bg-danger-50 text-danger-700" },
  reemplazado: { icon: RefreshCcw, tone: "bg-warning-50 text-warning-700" },
};

export function AuditIcon({ action, className }: { action: AuditAction; className?: string }) {
  const { icon: Icon, tone } = MAP[action];
  return (
    <span className={cn("flex h-8 w-8 shrink-0 items-center justify-center rounded-full", tone, className)}>
      <Icon className="h-4 w-4" strokeWidth={1.9} />
    </span>
  );
}
