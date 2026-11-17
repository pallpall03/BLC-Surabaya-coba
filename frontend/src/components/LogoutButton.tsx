"use client";

import { useRouter } from "next/navigation";
import { useState } from "react";

export default function LogoutButton() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);

  async function onClick() {
    setLoading(true);
    await fetch("/api/auth/logout", { method: "POST" }).catch(() => undefined);
    router.replace("/login");
    router.refresh();
  }

  return (
    <button
      type="button"
      onClick={onClick}
      disabled={loading}
      className="rounded-2xl border-2 border-blc-navy px-6 py-2.5 font-bold text-blc-navy transition hover:bg-blc-navy hover:text-white disabled:opacity-60"
    >
      {loading ? "Keluar…" : "Keluar"}
    </button>
  );
}
