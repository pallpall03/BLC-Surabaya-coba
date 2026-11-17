import { cookies } from "next/headers";
import { NextResponse } from "next/server";
import { API_URL, TOKEN_COOKIE, upstreamHeaders } from "@/lib/laravel";

export async function POST(request: Request) {
  const jar = await cookies();
  const token = jar.get(TOKEN_COOKIE)?.value;

  if (token) {
    // Cabut token di Laravel; kalau gagal, cookie tetap dihapus.
    await fetch(`${API_URL}/auth/logout`, {
      method: "POST",
      headers: upstreamHeaders(request, token),
      cache: "no-store",
    }).catch(() => undefined);
  }

  jar.delete(TOKEN_COOKIE);
  return NextResponse.json({ message: "Berhasil keluar." });
}
