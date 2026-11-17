import { NextResponse } from "next/server";
import { API_URL, upstreamHeaders } from "@/lib/laravel";

export async function GET(request: Request) {
  try {
    const res = await fetch(`${API_URL}/auth/captcha`, {
      headers: upstreamHeaders(request),
      cache: "no-store",
    });
    const data = await res.json().catch(() => ({}));
    return NextResponse.json(data, { status: res.status });
  } catch {
    return NextResponse.json({ message: "Server tidak dapat dihubungi." }, { status: 502 });
  }
}
