/* ============================================================
   Capa de integración TrustFile ↔ TrustHunter (el "motor").
   Esta es la FRONTERA: la UI nunca habla de blockchain ni hashes
   directamente; consume estas funciones con respuestas simples.

   En el MVP la implementación es local/mock. En producción, cada
   función hará una llamada HTTP a TrustHunter API sin cambiar la
   firma ni la UI que la consume.
   ============================================================ */

export interface SealResult {
  sealRef: string;
  fingerprint: string;
  selladoEn: string;
}

export interface AnchorResult {
  anchorRef: string;
  ancladoEn: string;
}

export interface VerificationResult {
  /** Estado de confianza en lenguaje de negocio, no técnico. */
  estado: "valido" | "revocado" | "expirado" | "no_encontrado";
  confianza: "alta" | "media" | "pendiente";
  emisor: string;
  selladoEn: string | null;
  anclado: boolean;
}

/** Genera un sello de integridad para un documento (mock). */
export async function sealDocument(content: string): Promise<SealResult> {
  const fingerprint = "sha256:" + simpleHash(content);
  return {
    sealRef: "seal_" + simpleHash(content).slice(0, 10),
    fingerprint,
    selladoEn: new Date().toISOString(),
  };
}

/** Ancla la prueba de integridad (mock async). */
export async function anchorSeal(sealRef: string): Promise<AnchorResult> {
  return {
    anchorRef: "anchor_" + sealRef.replace("seal_", "") + "_b7",
    ancladoEn: new Date().toISOString(),
  };
}

/** Hash determinista ligero solo para la demo (NO criptográfico). */
function simpleHash(input: string): string {
  let h = 0x811c9dc5;
  for (let i = 0; i < input.length; i++) {
    h ^= input.charCodeAt(i);
    h = Math.imul(h, 0x01000193);
  }
  return (h >>> 0).toString(16).padStart(8, "0").repeat(2).slice(0, 16);
}
