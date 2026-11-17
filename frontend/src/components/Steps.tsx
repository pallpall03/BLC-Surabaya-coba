import Reveal from "./Reveal";

const steps = [
  { t: "Buat akun", d: "Daftar di situs BLC dengan data diri dan email aktif." },
  { t: "Pilih pelatihan & lokasi", d: "Tentukan kelas, jadwal, dan BLC terdekat dari rumahmu." },
  { t: "Ikuti kelas", d: "Datang sesuai jadwal dan belajar langsung dengan instruktur." },
  { t: "Dapatkan sertifikat", d: "Selesaikan pelatihan dan terima sertifikat kelulusan." },
];

export default function Steps() {
  return (
    <section id="cara-daftar" className="bg-white px-4 py-24">
      <div className="mx-auto max-w-5xl">
        <Reveal className="text-center">
          <p className="font-bold text-blc-orange-dark">Cara Daftar</p>
          <h2 className="mt-2 text-3xl font-extrabold text-blc-navy sm:text-4xl">
            Empat langkah mudah
          </h2>
        </Reveal>

        <ol className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {steps.map((s, i) => (
            <Reveal key={s.t} delay={i * 100}>
              <li className="relative h-full list-none rounded-3xl bg-orange-50 p-6">
                <span className="grid h-11 w-11 place-items-center rounded-full bg-blc-navy text-lg font-extrabold text-white">
                  {i + 1}
                </span>
                <h3 className="mt-4 font-bold text-blc-navy">{s.t}</h3>
                <p className="mt-1 text-sm text-slate-600">{s.d}</p>
              </li>
            </Reveal>
          ))}
        </ol>
      </div>
    </section>
  );
}
