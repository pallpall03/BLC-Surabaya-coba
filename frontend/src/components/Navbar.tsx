"use client";

import Image from "next/image";
import { useEffect, useState } from "react";

const links = [
  { href: "#beranda", label: "Beranda" },
  { href: "#program", label: "Program" },
  { href: "#cara-daftar", label: "Cara Daftar" },
  { href: "#lokasi", label: "Lokasi" },
  { href: "#faq", label: "FAQ" },
];

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);
  const [active, setActive] = useState("#beranda");

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) setActive(`#${e.target.id}`);
        });
      },
      { rootMargin: "-40% 0px -55% 0px" },
    );
    links.forEach((l) => {
      const el = document.querySelector(l.href);
      if (el) io.observe(el);
    });
    return () => io.disconnect();
  }, []);

  return (
    <header className="fixed inset-x-0 top-4 z-50 flex justify-center px-4">
      <nav
        aria-label="Navigasi utama"
        className={`w-full max-w-5xl rounded-2xl border border-white/60 px-4 py-2.5 transition-all duration-300 ${
          scrolled
            ? "bg-white/85 shadow-lg shadow-orange-900/10 backdrop-blur-xl"
            : "bg-white/60 backdrop-blur-md"
        }`}
      >
        <div className="flex items-center justify-between gap-4">
          <a href="#beranda" aria-label="BLC Surabaya, ke beranda" className="flex items-center gap-2 font-extrabold text-blc-navy">
            <Image src="/logo-blc.png" alt="" width={40} height={40} priority className="h-10 w-10" />
            <span className="leading-tight">
              Surabaya
              <span className="block text-[11px] font-semibold text-slate-500">Broadband Learning Center</span>
            </span>
          </a>

          <ul className="hidden items-center gap-1 md:flex">
            {links.map((l) => (
              <li key={l.href}>
                <a
                  href={l.href}
                  className={`rounded-xl px-3.5 py-2 text-sm font-semibold transition-colors ${
                    active === l.href
                      ? "bg-blc-navy text-white"
                      : "text-slate-600 hover:bg-orange-50 hover:text-blc-navy"
                  }`}
                >
                  {l.label}
                </a>
              </li>
            ))}
          </ul>

          <div className="flex items-center gap-2">
            <a
              href="/login"
              className="hidden rounded-xl border-2 border-blc-navy px-4 py-1.5 text-sm font-bold text-blc-navy transition hover:bg-blc-navy hover:text-white sm:inline-block"
            >
              Masuk
            </a>
            <a
              href="/register"
              className="hidden rounded-xl bg-blc-orange px-4 py-2 text-sm font-bold text-blc-navy shadow-md shadow-orange-500/30 transition hover:-translate-y-0.5 hover:brightness-105 sm:inline-block"
            >
              Daftar Gratis
            </a>
            <button
              type="button"
              aria-label={open ? "Tutup menu" : "Buka menu"}
              aria-expanded={open}
              onClick={() => setOpen((v) => !v)}
              className="grid h-10 w-10 place-items-center rounded-xl text-blc-navy hover:bg-orange-50 md:hidden"
            >
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round">
                {open ? <path d="M6 6l12 12M18 6L6 18" /> : <path d="M4 7h16M4 12h16M4 17h16" />}
              </svg>
            </button>
          </div>
        </div>

        {open && (
          <ul className="mt-3 grid gap-1 border-t border-slate-200 pt-3 md:hidden">
            {links.map((l) => (
              <li key={l.href}>
                <a
                  href={l.href}
                  onClick={() => setOpen(false)}
                  className="block rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-orange-50"
                >
                  {l.label}
                </a>
              </li>
            ))}
            <li>
              <a
                href="/login"
                className="mt-1 block rounded-xl border-2 border-blc-navy px-3 py-2 text-center text-sm font-bold text-blc-navy"
              >
                Masuk
              </a>
            </li>
            <li>
              <a
                href="/register"
                onClick={() => setOpen(false)}
                className="block rounded-xl bg-blc-orange px-3 py-2.5 text-center text-sm font-bold text-blc-navy"
              >
                Daftar Gratis
              </a>
            </li>
          </ul>
        )}
      </nav>
    </header>
  );
}
