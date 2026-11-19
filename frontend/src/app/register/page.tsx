"use client";

import { useRouter } from "next/navigation";
import RegisterForm from "@/components/RegisterForm";

export default function RegisterPage() {
  return (
    <div className="min-h-screen flex items-center justify-center p-4">
      <div className="w-full max-w-md rounded-xl bg-white p-8 shadow-lg">
        <h1 className="text-2xl font-bold text-center text-blc-navy mb-6">Daftar Pengguna</h1>
        <p className="text-center text-gray-600 mb-6">
          Daftar sebagai peserta pelatihan atau pengunjung BLC Surabaya.
        </p>
        <RegisterForm />
      </div>
    </div>
  );
}