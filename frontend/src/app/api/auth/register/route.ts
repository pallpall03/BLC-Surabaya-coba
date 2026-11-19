import { NextRequest, NextResponse } from "next/server";
import { API_URL, TOKEN_COOKIE, upstreamHeaders } from "@/lib/laravel";

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();

    const res = await fetch(`${API_URL}/auth/register`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
      body: JSON.stringify(body),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
      return NextResponse.json(data, { status: res.status });
    }

    // Set token in httpOnly cookie
    const response = NextResponse.json(data);
    response.cookies.set(TOKEN_COOKIE, data.token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === "production",
      sameSite: "lax",
      maxAge: data.expires_in,
      path: "/",
    });

    return response;
  } catch {
    return NextResponse.json(
      { message: "Tidak dapat terhubung ke server. Periksa koneksi internetmu." },
      { status: 503 }
    );
  }
}