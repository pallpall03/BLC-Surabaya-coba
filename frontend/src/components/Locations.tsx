"use client";

import { useMemo, useState } from "react";
import Reveal from "./Reveal";

// Data contoh. Nanti ganti dengan data dari API Laravel (GET /api/v1/lokasi).
const locations = [
  { name: "BLC Mulyorejo", area: "Timur", slug: "blc-mulyorejo" },
  { name: "BLC Taman Prestasi", area: "Pusat", slug: "blc-taman-prestasi" },
  { name: "BLC Eks. Wisma Barbara", area: "Selatan", slug: "blc-wisma-barbara" },
];
const areas = ["Semua", "Pusat", "Utara", "Selatan", "Timur", "Barat"];

export default function Locations() {
  const [area, setArea] = useState("Semua");
  const [q, setQ] = useState("");

  const list = useMemo(
    () =>
      locations.filter(
        (l) =>
          (area === "Semua" || l.area === area) &&
          l.name.toLowerCase().includes(q.toLowerCase()),
      ),
    [area, q],
  );

  return (
    <section id="lokasi" className="px-4 py-24">
      <div className="mx-auto max-w-6xl">
        <Reveal className="text-center">
          <p className="font-bold text-blc-orange-dark">Lokasi</p>
          <h2 className="mt-2 text-3xl font-extrabold text-blc-navy sm:text-4xl">
            Temukan BLC terdekat
          </h2>
        </Reveal>

        <Reveal className="mt-10 flex flex-col items-center gap-4">
          <input
            type="search"
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Cari nama lokasi…"
            aria-label="Cari lokasi BLC"
            className="w-full max-w-md rounded-2xl border border-orange-100 bg-white px-5 py-3 shadow-sm outline-none transition focus:border-blc-orange focus:ring-4 focus:ring-orange-100"
          />
          <div className="flex flex-wrap justify-center gap-2">
            {areas.map((a) => (
              <button
                key={a}
                type="button"
                onClick={() => setArea(a)}
                aria-pressed={area === a}
                className={`rounded-full px-5 py-2 text-sm font-bold transition ${
                  area === a
                    ? "bg-blc-navy text-white shadow-md"
                    : "bg-white text-slate-600 hover:bg-orange-50"
                }`}
              >
                {a}
              </button>
            ))}
          </div>
        </Reveal>

        <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {list.map((l) => (
            <a
              key={l.slug}
              href={`/lokasi/${l.slug}`}
              className="group rounded-3xl border border-orange-100 bg-white p-6 shadow-sm transition hover:-translate-y-2 hover:border-blc-orange hover:shadow-xl"
            >
              <span className="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-blc-orange-dark">
                Surabaya {l.area}
              </span>
              <h3 className="mt-4 text-lg font-bold text-blc-navy">{l.name}</h3>
              <span className="mt-3 inline-block text-sm font-bold text-blc-navy transition group-hover:translate-x-1">
                Lihat detail →
              </span>
            </a>
          ))}
          {list.length === 0 && (
            <p className="col-span-full text-center text-slate-500">
              Lokasi tidak ditemukan. Coba kata kunci atau wilayah lain.
            </p>
          )}
        </div>
      </div>
    </section>
  );
}
