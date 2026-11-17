import { cookies } from "next/headers";
import { API_URL, TOKEN_COOKIE, type SessionUser } from "@/lib/laravel";

/** Membaca sesi dari cookie dan memvalidasinya ke Laravel. Mengembalikan null kalau belum login. */
export async function getSession(): Promise<SessionUser | null> {
  const token = (await cookies()).get(TOKEN_COOKIE)?.value;
  if (!token) return null;

  try {
    const res = await fetch(`${API_URL}/auth/me`, {
      headers: { Accept: "application/json", Authorization: `Bearer ${token}` },
      cache: "no-store",
    });
    if (!res.ok) return null;

    const data = (await res.json()) as { user: SessionUser };
    return data.user;
  } catch {
    return null;
  }
}
