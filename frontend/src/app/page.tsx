import Navbar from "@/components/Navbar";
import Hero from "@/components/Hero";
import Programs from "@/components/Programs";
import Steps from "@/components/Steps";
import Locations from "@/components/Locations";
import Faq from "@/components/Faq";
import Footer from "@/components/Footer";

export default function Home() {
  return (
    <>
      <Navbar />
      <main>
        <Hero />
        <Programs />
        <Steps />
        <Locations />
        <Faq />
      </main>
      <Footer />
    </>
  );
}
