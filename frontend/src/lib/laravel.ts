// Dipakai di sisi server saja (Route Handler dan Server Component).
// Token login disimpan di cookie httpOnly, jadi JavaScript di browser tidak bisa membacanya.

export const API_URL = process.env.NEXT_PUBLIC_API_URL;
export const TOKEN_COOKIE = "blc_token";

export type SessionUser = {
  id: number;
  name: string;
  username: string | null;
  email: string | null;
  role: string;
  role_label: string;
  blcs?: { id: number; name: string; slug: string }[];
};

/** Header untuk meneruskan permintaan ke Laravel, termasuk IP asli pengguna. */
export function upstreamHeaders(request: Request, token?: string): HeadersInit {
  const headers: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
  };

  const forwarded = request.headers.get("x-forwarded-for");
  if (forwarded) headers["X-Forwarded-For"] = forwarded;
  if (token) headers.Authorization = `Bearer ${token}`;

  return headers;
}
