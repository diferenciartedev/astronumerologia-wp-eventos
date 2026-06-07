import { QRCodeSVG } from "qrcode.react";
import type { Certificate } from "@/lib/types";
import { formatDate } from "@/lib/format";
import { cn } from "@/lib/cn";

/** Render visual del certificado, fiel a cómo se vería el PDF emitido. */
export function CertificatePreview({
  certificate,
  orgName,
  accent = "#3f49d4",
  className,
}: {
  certificate: Certificate;
  orgName: string;
  accent?: string;
  className?: string;
}) {
  const verifyUrl = `https://trustfile.app/verificar/${certificate.code}`;
  return (
    <div
      className={cn(
        "relative aspect-[1.414/1] w-full overflow-hidden rounded-[var(--radius-lg)] border border-border bg-surface shadow-[var(--shadow-md)]",
        className
      )}
    >
      {/* Borde decorativo */}
      <div className="absolute inset-3 rounded-[var(--radius-md)] border" style={{ borderColor: accent + "33" }} />
      <div className="absolute inset-x-0 top-0 h-1.5" style={{ backgroundColor: accent }} />

      <div className="relative flex h-full flex-col px-[6%] py-[5%]">
        <div className="flex items-center justify-between">
          <span className="flex items-center gap-2">
            <span
              className="flex h-7 w-7 items-center justify-center rounded-md text-[13px] font-bold text-white"
              style={{ backgroundColor: accent }}
            >
              {orgName.charAt(0)}
            </span>
            <span className="text-[13px] font-semibold text-ink">{orgName}</span>
          </span>
          <span
            className="rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide"
            style={{ backgroundColor: accent + "1a", color: accent }}
          >
            Certificado oficial
          </span>
        </div>

        <div className="flex flex-1 flex-col items-center justify-center text-center">
          <p className="text-[10px] uppercase tracking-[0.3em] text-muted">Se certifica que</p>
          <p className="mt-2 text-2xl font-semibold tracking-tight text-ink sm:text-[28px]">
            {certificate.recipientName}
          </p>
          <p className="mt-3 max-w-[80%] text-sm text-ink-soft">{certificate.title}</p>
          {certificate.metadata.horas && (
            <p className="mt-1 text-xs text-muted">{certificate.metadata.horas} horas lectivas</p>
          )}
        </div>

        <div className="flex items-end justify-between">
          <div className="text-left">
            <p className="text-xs text-muted">Fecha de emisión</p>
            <p className="text-sm font-medium text-ink-soft">{formatDate(certificate.issueDate)}</p>
          </div>
          <div className="text-center">
            <div className="rounded-md border border-border bg-white p-1.5">
              <QRCodeSVG value={verifyUrl} size={56} level="M" fgColor="#171821" />
            </div>
            <p className="mt-1 font-mono text-[9px] text-faint">{certificate.code}</p>
          </div>
        </div>
      </div>
    </div>
  );
}
