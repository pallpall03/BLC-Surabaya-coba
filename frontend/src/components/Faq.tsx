"use client";

import { useState } from "react";
import Reveal from "./Reveal";

const faqs = [
  { q: "Apakah pelatihan BLC benar-benar gratis?", a: "Ya. Pelatihan BLC gratis dan bersertifikat untuk warga Surabaya." },
  { q: "Siapa saja yang boleh mengikuti?", a: "Warga Surabaya dari berbagai usia dan latar belakang, baik perorangan maupun kelompok." },
  { q: "Apakah saya perlu membawa laptop?", a: "Fasilitas komputer disediakan di lokasi BLC. Cek detail tiap lokasi untuk memastikan." },
  { q: "Bagaimana mendapatkan sertifikat?", a: "Sertifikat diberikan setelah kamu menyelesaikan seluruh sesi pelatihan." },
];

export default function Faq() {
  const [open, setOpen] = useState<number | null>(0);

  return (
    <section id="faq" className="bg-white px-4 py-24">
      <div className="mx-auto max-w-3xl">
        <Reveal className="text-center">
          <p className="font-bold text-blc-orange-dark">FAQ</p>
          <h2 className="mt-2 text-3xl font-extrabold text-blc-navy sm:text-4xl">
            Pertanyaan yang sering diajukan
          </h2>
        </Reveal>

        <div className="mt-10 space-y-3">
          {faqs.map((f, i) => {
            const isOpen = open === i;
            return (
              <div key={f.q} className="rounded-2xl border border-orange-100 bg-orange-50/60">
                <button
                  type="button"
                  aria-expanded={isOpen}
                  onClick={() => setOpen(isOpen ? null : i)}
                  className="flex w-full items-center justify-between gap-4 px-6 py-4 text-left font-bold text-blc-navy"
                >
                  {f.q}
                  <span className={`text-xl text-blc-orange-dark transition-transform ${isOpen ? "rotate-45" : ""}`}>+</span>
                </button>
                {isOpen && <p className="px-6 pb-5 text-slate-600">{f.a}</p>}
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
