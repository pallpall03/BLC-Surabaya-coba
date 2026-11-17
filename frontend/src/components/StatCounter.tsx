"use client";

import { useEffect, useRef, useState } from "react";

export default function StatCounter({
  to,
  suffix = "",
  label,
}: {
  to: number;
  suffix?: string;
  label: string;
}) {
  const ref = useRef<HTMLDivElement>(null);
  const [value, setValue] = useState(0);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const io = new IntersectionObserver(([entry]) => {
      if (!entry.isIntersecting) return;
      io.disconnect();
      const start = performance.now();
      const dur = 1200;
      const tick = (now: number) => {
        const p = Math.min((now - start) / dur, 1);
        setValue(Math.round(to * (1 - Math.pow(1 - p, 3))));
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    });
    io.observe(el);
    return () => io.disconnect();
  }, [to]);

  return (
    <div ref={ref} className="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-orange-100">
      <div className="text-3xl font-extrabold text-blc-navy">
        {value}
        {suffix}
      </div>
      <div className="mt-1 text-sm text-slate-600">{label}</div>
    </div>
  );
}
