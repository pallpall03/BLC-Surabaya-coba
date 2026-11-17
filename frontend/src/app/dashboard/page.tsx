import type { Metadata } from "next";
import { redirect } from "next/navigation";
import LogoutButton from "@/components/LogoutButton";
import { getSession } from "@/lib/session";

export const metadata: Metadata = {
  title: "Dashboard | BLC Surabaya",
};

export default async function DashboardPage() {
  const user = await getSession();
  if (!user) redirect("/login");

  return (
    <main className="mx-auto w-full max-w-3xl px-4 py-16">
      <p className="font-bold text-blc-orange-dark">{user.role_label}</p>
      <h1 className="mt-1 text-3xl font-extrabold text-blc-navy">Halo, {user.name}</h1>

      {user.blcs && user.blcs.length > 0 && (
        <p className="mt-3 text-slate-600">BLC: {user.blcs.map((b) => b.name).join(", ")}</p>
      )}

      <p className="mt-6 rounded-2xl bg-blc-cream p-5 text-slate-600">
        Halaman ini masih sementara. Dashboard tiap role akan dibuat pada tahap berikutnya.
      </p>

      <div className="mt-8">
        <LogoutButton />
      </div>
    </main>
  );
}
