"use client";

import { useRouter } from "next/navigation";
import { useState, type FormEvent } from "react";

type Captcha = { id: string; question: string };

export default function RegisterForm() {
  const router = useRouter();
  const [name, setName] = useState("");
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [phone, setPhone] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [captcha, setCaptcha] = useState<Captcha | null>(null);
  const [captchaAnswer, setCaptchaAnswer] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  async function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      const res = await fetch("/api/auth/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name,
          username,
          email,
          password,
          password_confirmation: confirmPassword,
          phone,
          captcha_id: captcha?.id ?? null,
          captcha_answer: captcha ? captchaAnswer : null,
        }),
      });
      const data = await res.json().catch(() => ({}));

      if (res.ok) {
        router.replace("/dashboard");
        router.refresh();
        return;
      }

      setError(data.message ?? "Terjadi kesalahan. Coba lagi.");
      setConfirmPassword("");
      setCaptchaAnswer("");
      // Captcha baru dari server; kalau akun terkunci (429), captcha tidak dikirim.
      setCaptcha(data.captcha ?? null);
    } catch {
      setError("Tidak dapat terhubung ke server. Periksa koneksi internetmu.");
    } finally {
      setLoading(false);
    }
  }

  const inputClass =
    "mt-1.5 w-full rounded-2xl border border-orange-100 bg-white px-4 py-3 outline-none transition focus:border-blc-orange focus:ring-4 focus:ring-orange-100";

  return (
    <form onSubmit={onSubmit} className="space-y-5" noValidate>
      <div>
        <label htmlFor="name" className="text-sm font-bold text-blc-navy">
          Nama Lengkap
        </label>
        <input
          id="name"
          name="name"
          type="text"
          autoComplete="name"
          required
          value={name}
          onChange={(e) => setName(e.target.value)}
          className={inputClass}
          placeholder="Masukkan nama lengkap"
        />
      </div>

      <div>
        <label htmlFor="username" className="text-sm font-bold text-blc-navy">
          Username
        </label>
        <input
          id="username"
          name="username"
          type="text"
          autoComplete="username"
          required
          value={username}
          onChange={(e) => setUsername(e.target.value)}
          className={inputClass}
          placeholder="Hanya huruf, angka, strip, dan underscore"
          pattern="^[a-zA-Z0-9_-]+$"
        />
      </div>

      <div>
        <label htmlFor="email" className="text-sm font-bold text-blc-navy">
          Email
        </label>
        <input
          id="email"
          name="email"
          type="email"
          autoComplete="email"
          required
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          className={inputClass}
          placeholder="contoh@email.com"
        />
      </div>

      <div>
        <label htmlFor="password" className="text-sm font-bold text-blc-navy">
          Password
        </label>
        <div className="relative">
          <input
            id="password"
            name="password"
            type={showPassword ? "text" : "password"}
            autoComplete="new-password"
            required
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            className={`${inputClass} pr-24`}
            minLength={8}
          />
          <button
            type="button"
            onClick={() => setShowPassword((v) => !v)}
            aria-pressed={showPassword}
            className="absolute right-3 top-1/2 mt-0.5 -translate-y-1/2 rounded-lg px-3 py-1.5 text-sm font-bold text-blc-orange-dark hover:bg-orange-50"
          >
            {showPassword ? "Sembunyikan" : "Tampilkan"}
          </button>
        </div>
        <p className="mt-1 text-xs text-slate-500">
          Minimal 8 karakter: huruf besar, huruf kecil, angka, dan simbol.
        </p>
      </div>

      <div>
        <label htmlFor="confirmPassword" className="text-sm font-bold text-blc-navy">
          Konfirmasi Password
        </label>
        <input
          id="confirmPassword"
          name="confirmPassword"
          type={showPassword ? "text" : "password"}
          autoComplete="new-password"
          required
          value={confirmPassword}
          onChange={(e) => setConfirmPassword(e.target.value)}
          className={inputClass}
        />
      </div>

      <div>
        <label htmlFor="phone" className="text-sm font-bold text-blc-navy">
          Nomor Telepon (Opsional)
        </label>
        <input
          id="phone"
          name="phone"
          type="tel"
          autoComplete="tel"
          value={phone}
          onChange={(e) => setPhone(e.target.value)}
          className={inputClass}
          placeholder="08xx-xxxx-xxxx atau +628xx-xxxx-xxxx"
        />
      </div>

      {captcha && (
        <div className="rounded-2xl bg-blc-cream p-4">
          <label htmlFor="captcha" className="text-sm font-bold text-blc-navy">
            Verifikasi: {captcha.question}
          </label>
          <input
            id="captcha"
            name="captcha"
            type="text"
            inputMode="numeric"
            autoComplete="off"
            required
            value={captchaAnswer}
            onChange={(e) => setCaptchaAnswer(e.target.value)}
            className={inputClass}
          />
        </div>
      )}

      <p role="alert" aria-live="polite" className={error ? "rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700" : "sr-only"}>
        {error}
      </p>

      <button
        type="submit"
        disabled={loading}
        className="w-full rounded-2xl bg-blc-orange px-6 py-3.5 font-bold text-blc-navy shadow-lg shadow-orange-500/30 transition hover:-translate-y-0.5 hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-60"
      >
        {loading ? "Memproses…" : "Daftar Gratis"}
      </button>

      <p className="text-center text-sm text-slate-500">
        Sudah punya akun?{" "}
        <a href="/login" className="font-bold text-blc-orange-dark hover:underline">
          Masuk di sini
        </a>
      </p>
    </form>
  );
}