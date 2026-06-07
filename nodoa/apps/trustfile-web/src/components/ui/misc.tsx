import * as React from "react";
import type { LucideIcon } from "lucide-react";
import { cn } from "@/lib/cn";

/** Encabezado de página con título, descripción y acciones. */
export function PageHeader({
  title,
  description,
  children,
  className,
}: {
  title: string;
  description?: string;
  children?: React.ReactNode;
  className?: string;
}) {
  return (
    <div className={cn("flex flex-wrap items-start justify-between gap-4", className)}>
      <div>
        <h1 className="text-[22px] font-semibold tracking-tight text-ink">{title}</h1>
        {description && <p className="mt-1 max-w-2xl text-sm text-muted">{description}</p>}
      </div>
      {children && <div className="flex items-center gap-2">{children}</div>}
    </div>
  );
}

/** Estado vacío cuidado, con icono, mensaje y acción. */
export function EmptyState({
  icon: Icon,
  title,
  description,
  action,
  className,
}: {
  icon: LucideIcon;
  title: string;
  description?: string;
  action?: React.ReactNode;
  className?: string;
}) {
  return (
    <div
      className={cn(
        "flex flex-col items-center justify-center rounded-[var(--radius-lg)] border border-dashed border-border-strong bg-surface-2 px-6 py-14 text-center",
        className
      )}
    >
      <div className="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600">
        <Icon className="h-6 w-6" strokeWidth={1.75} />
      </div>
      <h3 className="mt-4 text-[15px] font-semibold text-ink">{title}</h3>
      {description && <p className="mt-1 max-w-sm text-sm text-muted">{description}</p>}
      {action && <div className="mt-5">{action}</div>}
    </div>
  );
}

/** Tarjeta de métrica para el dashboard. */
export function StatCard({
  icon: Icon,
  label,
  value,
  delta,
  tone = "brand",
}: {
  icon: LucideIcon;
  label: string;
  value: string | number;
  delta?: { value: string; positive?: boolean };
  tone?: "brand" | "success" | "warning" | "danger" | "info";
}) {
  const toneClasses: Record<string, string> = {
    brand: "bg-brand-50 text-brand-600",
    success: "bg-success-50 text-success-700",
    warning: "bg-warning-50 text-warning-700",
    danger: "bg-danger-50 text-danger-700",
    info: "bg-info-50 text-info-700",
  };
  return (
    <div className="rounded-[var(--radius-lg)] border border-border bg-surface p-5 shadow-[var(--shadow-sm)]">
      <div className="flex items-center justify-between">
        <span className={cn("flex h-9 w-9 items-center justify-center rounded-[var(--radius-md)]", toneClasses[tone])}>
          <Icon className="h-[18px] w-[18px]" strokeWidth={1.9} />
        </span>
        {delta && (
          <span
            className={cn(
              "text-xs font-medium",
              delta.positive ? "text-success-700" : "text-muted"
            )}
          >
            {delta.value}
          </span>
        )}
      </div>
      <div className="mt-4 text-2xl font-semibold tracking-tight text-ink">{value}</div>
      <div className="mt-0.5 text-sm text-muted">{label}</div>
    </div>
  );
}

/** Separador con etiqueta opcional. */
export function Divider({ label, className }: { label?: string; className?: string }) {
  if (!label) return <hr className={cn("border-border", className)} />;
  return (
    <div className={cn("flex items-center gap-3", className)}>
      <hr className="flex-1 border-border" />
      <span className="text-xs font-medium uppercase tracking-wide text-faint">{label}</span>
      <hr className="flex-1 border-border" />
    </div>
  );
}
