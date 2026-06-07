import { AppShell } from "@/components/app-shell";
import { getOrganization, getMembers } from "@/lib/mock-data";

export default function AppLayout({ children }: { children: React.ReactNode }) {
  const org = getOrganization();
  const owner = getMembers()[0];
  return (
    <AppShell orgName={org.name} userName={owner.name}>
      {children}
    </AppShell>
  );
}
