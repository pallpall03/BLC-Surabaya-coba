import Reveal from "./Reveal";

const programs = [
  {
    title: "Perkantoran",
    desc: "Pengolah kata, lembar kerja, dan presentasi untuk kebutuhan kerja dan sekolah.",
    color: "bg-orange-100 text-blc-orange-dark",
    icon: "M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6",
  },
  {
    title: "Desain Grafis",
    desc: "Belajar mengolah gambar dan membuat desain dengan aplikasi sumber terbuka.",
    color: "bg-blc-navy text-white",
    icon: "M12 3l3 6 6 .9-4.5 4.3 1 6.3L12 17.6 6.5 20.5l1-6.3L3 9.9 9 9z",
  },
  {
    title: "Internet & Media Sosial",
    desc: "Berinternet dengan aman, membuat blog, dan memanfaatkan media sosial secara bijak.",
    color: "bg-blc-orange text-blc-navy",
    icon: "M12 3a9 9 0 100 18 9 9 0 000-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18",
  },
];

export default function Programs() {
  return (
    <section id="program" className="px-4 py-24">
      <div className="mx-auto max-w-6xl">
        <Reveal className="mx-auto max-w-2xl text-center">
          <p className="font-bold text-blc-orange-dark">Program Pelatihan</p>
          <h2 className="mt-2 text-3xl font-extrabold text-blc-navy sm:text-4xl">
            Pilih kelas sesuai kebutuhanmu
          </h2>
          <p className="mt-3 text-slate-600">
            Materi disusun bertahap dari dasar, cocok untuk pelajar, pekerja, hingga pelaku UMKM.
          </p>
        </Reveal>

        <div className="mt-12 grid gap-6 md:grid-cols-3">
          {programs.map((p, i) => (
            <Reveal key={p.title} delay={i * 100}>
              <article className="group h-full rounded-3xl border border-orange-100 bg-white p-7 shadow-sm transition hover:-translate-y-2 hover:shadow-xl hover:shadow-orange-900/10">
                <div className={`grid h-14 w-14 place-items-center rounded-2xl ${p.color}`}>
                  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">
                    <path d={p.icon} />
                  </svg>
                </div>
                <h3 className="mt-5 text-xl font-bold text-blc-navy">{p.title}</h3>
                <p className="mt-2 text-slate-600">{p.desc}</p>
                <a href="#cara-daftar" className="mt-5 inline-block font-bold text-blc-navy transition group-hover:translate-x-1">
                  Daftar kelas →
                </a>
              </article>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}
