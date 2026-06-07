import { cn } from "@/lib/cn";

/** Marca de la aplicación: TrustFile. El glifo es un "escudo + check". */
export function Logo({
  className,
  showWordmark = true,
  tone = "default",
}: {
  className?: string;
  showWordmark?: boolean;
  tone?: "default" | "light";
}) {
  return (
    <span className={cn("inline-flex items-center gap-2", className)}>
      <span
        className={cn(
          "relative flex h-8 w-8 items-center justify-center rounded-[10px] shadow-[var(--shadow-sm)]",
          tone === "light" ? "bg-white/15 text-white" : "bg-brand-600 text-white"
        )}
      >
        <svg viewBox="0 0 24 24" className="h-[18px] w-[18px]" fill="none" aria-hidden>
          <path
            d="M12 2.5 19.5 5v6.2c0 4.6-3.1 8.1-7.5 9.8-4.4-1.7-7.5-5.2-7.5-9.8V5L12 2.5Z"
            fill="currentColor"
            opacity="0.25"
          />
          <path
            d="M12 2.5 19.5 5v6.2c0 4.6-3.1 8.1-7.5 9.8-4.4-1.7-7.5-5.2-7.5-9.8V5L12 2.5Z"
            stroke="currentColor"
            strokeWidth="1.4"
            strokeLinejoin="round"
          />
          <path d="m8.7 12 2.2 2.2 4.4-4.6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </span>
      {showWordmark && (
        <span className={cn("text-[17px] font-semibold tracking-tight", tone === "light" ? "text-white" : "text-ink")}>
          Trust<span className="text-brand-600">File</span>
        </span>
      )}
    </span>
  );
}

/** Sello de verificación reutilizable (página pública / preview). */
export function TrustSeal({ className }: { className?: string }) {
  return (
    <span
      className={cn(
        "inline-flex items-center gap-1.5 rounded-full border border-success-100 bg-success-50 px-3 py-1 text-xs font-semibold text-success-700",
        className
      )}
    >
      <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" aria-hidden>
        <path d="M12 2.5 19.5 5v6.2c0 4.6-3.1 8.1-7.5 9.8-4.4-1.7-7.5-5.2-7.5-9.8V5L12 2.5Z" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round" />
        <path d="m8.7 12 2.2 2.2 4.4-4.6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
      Documento verificado
    </span>
  );
}
