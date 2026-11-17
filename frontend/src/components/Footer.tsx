import Image from "next/image";

export default function Footer() {
  return (
    <footer className="bg-blc-navy px-4 py-12 text-orange-50/80">
      <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 text-center sm:flex-row sm:text-left">
        <div className="flex items-center gap-3">
          <Image src="/logo-blc.png" alt="Logo BLC" width={48} height={48} className="h-12 w-12" />
          <div>
            <p className="text-lg font-extrabold text-white">BLC Surabaya</p>
            <p className="text-sm">Broadband Learning Center, Pemerintah Kota Surabaya</p>
          </div>
        </div>
        <p className="text-sm">&copy; {new Date().getFullYear()} BLC Surabaya.</p>
      </div>
    </footer>
  );
}
