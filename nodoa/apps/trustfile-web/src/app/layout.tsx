import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";

const geistSans = Geist({ variable: "--font-geist-sans", subsets: ["latin"] });
const geistMono = Geist_Mono({ variable: "--font-geist-mono", subsets: ["latin"] });

export const metadata: Metadata = {
  title: {
    default: "TrustFile · Certificados confiables, fáciles de emitir y verificar",
    template: "%s · TrustFile",
  },
  description:
    "TrustFile ayuda a las organizaciones a emitir, gestionar y validar certificados y documentos confiables. Menos fraude, más control, verificación simple.",
  applicationName: "TrustFile",
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="es" className={`${geistSans.variable} ${geistMono.variable} h-full antialiased`}>
      <body className="min-h-full">{children}</body>
    </html>
  );
}
