import type { Metadata } from "next";
import Image from "next/image";
import Link from "next/link";
import { redirect } from "next/navigation";
import LoginForm from "@/components/LoginForm";
import { getSession } from "@/lib/session";

export const metadata: Metadata = {
  title: "Masuk | BLC Surabaya",
};

export default async function LoginPage() {
  if (await getSession()) redirect("/dashboard");

  return (
    <main className="grid min-h-screen place-items-center bg-gradient-to-b from-blc-cream to-background px-4 py-12">
      <div className="w-full max-w-md">
        <Link href="/" className="mx-auto mb-6 flex w-fit items-center gap-3 font-extrabold text-blc-navy">
          <Image src="/logo-blc.png" alt="" width={48} height={48} priority className="h-12 w-12" />
          <span className="leading-tight">
            BLC Surabaya
            <span className="block text-xs font-semibold text-slate-500">Broadband Learning Center</span>
          </span>
        </Link>

        <div className="rounded-3xl border border-orange-100 bg-white p-8 shadow-xl shadow-orange-900/5">
          <h1 className="text-2xl font-extrabold text-blc-navy">Masuk</h1>
          <p className="mt-1 mb-6 text-sm text-slate-600">
            Satu halaman masuk untuk semua pengguna BLC. Instruktur memakai akun Kantorku.
          </p>
          <LoginForm />
        </div>

        <p className="mt-6 text-center text-sm text-slate-600">
          <Link href="/" className="font-bold text-blc-navy hover:underline">
            ← Kembali ke beranda
          </Link>
        </p>
      </div>
    </main>
  );
}
