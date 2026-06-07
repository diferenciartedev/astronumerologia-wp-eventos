"use client";

import { useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  LayoutDashboard,
  FileBadge,
  FilePlus2,
  LayoutTemplate,
  PenLine,
  ScrollText,
  Settings,
  Search,
  BadgeCheck,
  Menu,
  X,
  ChevronDown,
  Plus,
} from "lucide-react";
import { Logo } from "@/components/brand";
import { ButtonLink } from "@/components/ui/button";
import { cn } from "@/lib/cn";
import { initials } from "@/lib/format";

const NAV: { group: string; items: { href: string; label: string; icon: typeof LayoutDashboard }[] }[] = [
  {
    group: "Principal",
    items: [
      { href: "/app", label: "Panel", icon: LayoutDashboard },
      { href: "/app/certificados", label: "Certificados", icon: FileBadge },
      { href: "/app/emitir", label: "Emitir", icon: FilePlus2 },
    ],
  },
  {
    group: "Gestión",
    items: [
      { href: "/app/plantillas", label: "Plantillas", icon: LayoutTemplate },
      { href: "/app/firmantes", label: "Firmantes", icon: PenLine },
      { href: "/app/auditoria", label: "Auditoría", icon: ScrollText },
    ],
  },
  {
    group: "Cuenta",
    items: [{ href: "/app/configuracion", label: "Configuración", icon: Settings }],
  },
];

function isActive(pathname: string, href: string) {
  if (href === "/app") return pathname === "/app";
  return pathname === href || pathname.startsWith(href + "/");
}

export function AppShell({
  children,
  orgName,
  userName,
}: {
  children: React.ReactNode;
  orgName: string;
  userName: string;
}) {
  const pathname = usePathname();
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <div className="min-h-screen lg:grid lg:grid-cols-[256px_1fr]">
      {/* Sidebar */}
      <aside
        className={cn(
          "fixed inset-y-0 left-0 z-40 w-64 flex-col border-r border-border bg-surface transition-transform lg:static lg:flex lg:translate-x-0",
          mobileOpen ? "flex translate-x-0" : "hidden -translate-x-full"
        )}
      >
        <div className="flex h-16 items-center justify-between px-5">
          <Link href="/app" aria-label="TrustFile">
            <Logo />
          </Link>
          <button
            className="lg:hidden text-muted hover:text-ink"
            onClick={() => setMobileOpen(false)}
            aria-label="Cerrar menú"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Selector de organización */}
        <div className="px-3">
          <button className="flex w-full items-center gap-2.5 rounded-[var(--radius-md)] border border-border bg-surface-2 px-3 py-2 text-left transition-colors hover:border-border-strong">
            <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-brand-600 text-[11px] font-semibold text-white">
              {initials(orgName)}
            </span>
            <span className="min-w-0 flex-1">
              <span className="block truncate text-[13px] font-medium text-ink">{orgName}</span>
              <span className="block text-[11px] text-muted">Organización</span>
            </span>
            <ChevronDown className="h-4 w-4 shrink-0 text-faint" />
          </button>
        </div>

        <nav className="mt-4 flex-1 space-y-5 overflow-y-auto px-3 pb-4">
          {NAV.map((section) => (
            <div key={section.group}>
              <p className="px-3 pb-1.5 text-[11px] font-semibold uppercase tracking-wider text-faint">
                {section.group}
              </p>
              <ul className="space-y-0.5">
                {section.items.map((item) => {
                  const active = isActive(pathname, item.href);
                  return (
                    <li key={item.href}>
                      <Link
                        href={item.href}
                        onClick={() => setMobileOpen(false)}
                        className={cn(
                          "group flex items-center gap-3 rounded-[var(--radius-md)] px-3 py-2 text-sm font-medium transition-colors",
                          active
                            ? "bg-brand-50 text-brand-700"
                            : "text-ink-soft hover:bg-surface-2 hover:text-ink"
                        )}
                      >
                        <item.icon
                          className={cn("h-[18px] w-[18px]", active ? "text-brand-600" : "text-muted group-hover:text-ink-soft")}
                          strokeWidth={1.9}
                        />
                        {item.label}
                      </Link>
                    </li>
                  );
                })}
              </ul>
            </div>
          ))}
        </nav>

        <div className="border-t border-border p-3">
          <ButtonLink href="/verificar" variant="secondary" size="sm" className="w-full">
            <BadgeCheck className="h-4 w-4" /> Verificar certificado
          </ButtonLink>
        </div>
      </aside>

      {mobileOpen && (
        <div className="fixed inset-0 z-30 bg-ink/30 lg:hidden" onClick={() => setMobileOpen(false)} />
      )}

      {/* Main */}
      <div className="flex min-h-screen flex-col">
        <header className="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-border bg-surface/85 px-4 backdrop-blur lg:px-8">
          <button
            className="lg:hidden text-ink-soft"
            onClick={() => setMobileOpen(true)}
            aria-label="Abrir menú"
          >
            <Menu className="h-5 w-5" />
          </button>

          <div className="relative hidden max-w-md flex-1 sm:block">
            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-faint" />
            <input
              placeholder="Buscar certificados, destinatarios o códigos…"
              className="h-9 w-full rounded-[var(--radius-md)] border border-border bg-surface-2 pl-9 pr-3 text-sm text-ink placeholder:text-faint focus:border-brand-300 focus:bg-surface focus:outline-none focus:ring-4 focus:ring-brand-100"
            />
          </div>

          <div className="ml-auto flex items-center gap-2">
            <ButtonLink href="/app/emitir" size="sm">
              <Plus className="h-4 w-4" /> Emitir certificado
            </ButtonLink>
            <div className="flex items-center gap-2 rounded-full border border-border bg-surface-2 py-1 pl-1 pr-3">
              <span className="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-[11px] font-semibold text-brand-700">
                {initials(userName)}
              </span>
              <span className="hidden text-[13px] font-medium text-ink-soft sm:block">{userName}</span>
            </div>
          </div>
        </header>

        <main className="flex-1 px-4 py-6 lg:px-8 lg:py-8">
          <div className="mx-auto w-full max-w-6xl tf-fade-up">{children}</div>
        </main>
      </div>
    </div>
  );
}
