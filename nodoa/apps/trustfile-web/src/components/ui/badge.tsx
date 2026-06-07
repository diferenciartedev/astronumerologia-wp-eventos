import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/cn";

const badgeVariants = cva(
  "inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium",
  {
    variants: {
      tone: {
        neutral: "border-border bg-surface-2 text-ink-soft",
        brand: "border-brand-200 bg-brand-50 text-brand-700",
        success: "border-success-100 bg-success-50 text-success-700",
        warning: "border-warning-100 bg-warning-50 text-warning-700",
        danger: "border-danger-100 bg-danger-50 text-danger-700",
        info: "border-info-100 bg-info-50 text-info-700",
      },
    },
    defaultVariants: { tone: "neutral" },
  }
);

export interface BadgeProps
  extends React.HTMLAttributes<HTMLSpanElement>,
    VariantProps<typeof badgeVariants> {
  dot?: boolean;
}

export function Badge({ className, tone, dot, children, ...props }: BadgeProps) {
  return (
    <span className={cn(badgeVariants({ tone }), className)} {...props}>
      {dot && <span className="h-1.5 w-1.5 rounded-full bg-current opacity-70" />}
      {children}
    </span>
  );
}

/** Estado de un certificado, con tono y etiqueta en español. */
export type CertStatus = "borrador" | "emitido" | "revocado" | "reemplazado" | "expirado";

const STATUS_MAP: Record<CertStatus, { tone: BadgeProps["tone"]; label: string }> = {
  borrador: { tone: "neutral", label: "Borrador" },
  emitido: { tone: "success", label: "Emitido" },
  revocado: { tone: "danger", label: "Revocado" },
  reemplazado: { tone: "warning", label: "Reemplazado" },
  expirado: { tone: "warning", label: "Expirado" },
};

export function StatusBadge({ status, className }: { status: CertStatus; className?: string }) {
  const { tone, label } = STATUS_MAP[status];
  return (
    <Badge tone={tone} dot className={className}>
      {label}
    </Badge>
  );
}
