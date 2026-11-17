import Image from "next/image";
import Reveal from "./Reveal";
import StatCounter from "./StatCounter";

export default function Hero() {
  return (
    <section
      id="beranda"
      className="relative overflow-hidden bg-gradient-to-b from-blc-cream to-background px-4 pb-24 pt-36"
    >
      <div className="pointer-events-none absolute -right-32 -top-24 h-[28rem] w-[28rem] rounded-full bg-blc-orange/25 blur-3xl" />
      <div className="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-blc-navy/10 blur-3xl" />

      <div className="relative mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
        <Reveal>
          <span className="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-blc-navy shadow-sm ring-1 ring-orange-100">
            <span className="h-2 w-2 rounded-full bg-blc-orange" />
            Pelatihan TIK gratis &amp; bersertifikat
          </span>
          <h1 className="mt-6 text-4xl font-extrabold leading-tight text-blc-navy sm:text-5xl lg:text-6xl">
            Belajar komputer &amp; internet,{" "}
            <span className="relative whitespace-nowrap text-blc-orange">
              gratis
              <span className="absolute -bottom-1 left-0 h-1.5 w-full rounded-full bg-blc-orange/30" />
            </span>{" "}
            untuk warga Surabaya
          </h1>
          <p className="mt-5 max-w-xl text-lg text-slate-600">
            Broadband Learning Center menyediakan kelas perkantoran, desain
            grafis, hingga literasi internet. Daftar online, pilih lokasi
            terdekat, dan mulai belajar.
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <a
              href="#cara-daftar"
              className="rounded-2xl bg-blc-orange px-7 py-3.5 font-bold text-blc-navy shadow-lg shadow-orange-500/40 transition hover:-translate-y-1 hover:brightness-105"
            >
              Mulai Daftar
            </a>
            <a
              href="#lokasi"
              className="rounded-2xl border-2 border-blc-navy px-7 py-3.5 font-bold text-blc-navy transition hover:bg-blc-navy hover:text-white"
            >
              Cari Lokasi
            </a>
          </div>
        </Reveal>

        <Reveal delay={150} className="relative mx-auto w-full max-w-md">
          <Image
            src="/logo-blc.png"
            alt="Logo Broadband Learning Center Surabaya"
            width={447}
            height={447}
            priority
            className="animate-float mx-auto w-3/4 drop-shadow-2xl"
          />
          <div className="mt-6 grid grid-cols-2 gap-3">
            <StatCounter to={50} suffix="+" label="Lokasi BLC" />
            <StatCounter to={100} suffix="%" label="Gratis" />
          </div>
        </Reveal>
      </div>
    </section>
  );
}
