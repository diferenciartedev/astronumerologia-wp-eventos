import * as React from "react";
import Link from "next/link";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/cn";

const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-[var(--radius-md)] font-medium transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas disabled:pointer-events-none disabled:opacity-50 cursor-pointer select-none",
  {
    variants: {
      variant: {
        primary:
          "bg-brand-600 text-white shadow-[var(--shadow-sm)] hover:bg-brand-700 active:bg-brand-800",
        secondary:
          "bg-surface text-ink border border-border-strong shadow-[var(--shadow-xs)] hover:bg-surface-2 hover:border-faint",
        ghost: "text-ink-soft hover:bg-brand-50 hover:text-brand-700",
        subtle: "bg-brand-50 text-brand-700 hover:bg-brand-100",
        danger:
          "bg-danger-500 text-white shadow-[var(--shadow-sm)] hover:bg-danger-700",
        "danger-soft":
          "bg-danger-50 text-danger-700 hover:bg-danger-100",
      },
      size: {
        sm: "h-8 px-3 text-[13px]",
        md: "h-10 px-4 text-sm",
        lg: "h-11 px-5 text-[15px]",
        icon: "h-10 w-10",
      },
    },
    defaultVariants: { variant: "primary", size: "md" },
  }
);

type ButtonBaseProps = VariantProps<typeof buttonVariants> & {
  className?: string;
  children?: React.ReactNode;
};

export interface ButtonProps
  extends ButtonBaseProps,
    Omit<React.ButtonHTMLAttributes<HTMLButtonElement>, keyof ButtonBaseProps> {}

export function Button({ className, variant, size, ...props }: ButtonProps) {
  return (
    <button className={cn(buttonVariants({ variant, size }), className)} {...props} />
  );
}

export interface ButtonLinkProps
  extends ButtonBaseProps,
    Omit<React.ComponentProps<typeof Link>, keyof ButtonBaseProps> {}

export function ButtonLink({ className, variant, size, ...props }: ButtonLinkProps) {
  return (
    <Link className={cn(buttonVariants({ variant, size }), className)} {...props} />
  );
}

export { buttonVariants };
