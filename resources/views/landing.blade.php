<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flash Inspire Training Center</title>
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        navy: '#0B1120', 
                        primary: '#1a365d', 
                        ocean: '#1ba1e2'
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Floating Animation untuk Ornamen */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50 overflow-x-hidden" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- ================= NAVBAR ================= -->
    <nav :class="{'bg-white/90 backdrop-blur-lg shadow-lg py-3': scrolled, 'bg-transparent py-6': !scrolled}" class="fixed w-full z-50 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('storage/icon.png') }}" alt="Logo FITC" class="h-14 w-auto drop-shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=FITC&background=0B1120&color=fff&rounded=true'">
                    <span :class="{'text-navy': scrolled, 'text-white': !scrolled}" class="font-extrabold text-3xl tracking-widest transition-colors duration-300">FITC</span>
                </div>
                <!-- Login Button -->
             <!-- Login Button di Navbar landing.blade.php -->
<div>
    <a href="{{ url('/login') }}" :class="{'bg-gradient-to-r from-primary to-ocean text-white shadow-lg shadow-blue-500/30': scrolled, 'bg-white/10 text-white backdrop-blur-md border border-white/30 hover:bg-white hover:text-navy': !scrolled}" class="font-bold py-3 px-8 rounded-full transition-all duration-300 transform hover:scale-105 inline-block">
        Login 
    </a>
</div>
            </div>
        </div>
    </nav>

    <!-- ================= HERO SECTION ================= -->
    <section class="relative bg-gradient-to-br from-[#1a365d] to-[#0f203b] pt-48 pb-64 flex items-center justify-center overflow-hidden min-h-[90vh]">
        <div class="max-w-5xl mx-auto px-6 text-center relative z-10 flex flex-col items-center">
            
            <div data-aos="fade-down" data-aos-duration="1000" class="inline-block px-6 py-2 rounded-full border border-white/30 text-white/90 text-sm font-semibold tracking-[0.15em] mb-10 backdrop-blur-sm shadow-sm">
                TERAKREDITASI MADYA (B) KEMENKES RI
            </div>
            
            <h1 data-aos="zoom-in" data-aos-duration="1200" data-aos-delay="200" class="text-6xl md:text-7xl lg:text-8xl font-extrabold text-white mb-2 tracking-tight drop-shadow-xl">
                Flash Inspire
            </h1>
            <h1 data-aos="zoom-in" data-aos-duration="1200" data-aos-delay="300" class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-[#5bc0de] mb-10 tracking-tight drop-shadow-xl">
                Training Center
            </h1>
            
            <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500" class="text-xl md:text-2xl text-gray-300 mb-3 font-light">
                "Enerjik - Responsif - Inovatif - Lugas"
            </p>
            <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600" class="text-xl md:text-2xl font-bold text-white mb-16 tracking-wide">
                Fun - Learn - Agile, Smile - Humble
            </p>
            
            <div data-aos="fade-up" data-aos-duration="1000" data-aos-delay="800">
                <a href="#program" class="bg-white text-[#1a365d] font-bold text-lg py-4 px-12 rounded-full hover:bg-gray-100 transition-all duration-300 shadow-[0_0_30px_rgba(255,255,255,0.3)] transform hover:-translate-y-1 inline-block">
                    Jelajahi Program Kami
                </a>
            </div>
        </div>
        
        <!-- White Wave Bottom -->
        <div class="absolute bottom-0 left-0 w-full leading-none z-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto fill-gray-50 drop-shadow-md">
                <path d="M0,256L80,245.3C160,235,320,213,480,218.7C640,224,800,256,960,261.3C1120,267,1280,245,1360,234.7L1440,224L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <!-- ================= SAMBUTAN DIREKTUR ================= -->
    <section class="pt-10 pb-32 px-6 bg-gray-50 relative z-10">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
            
            <!-- Area Foto Direktur (3D Pop-out effect) -->
            <div data-aos="fade-right" data-aos-duration="1200" class="relative flex justify-center mt-10 md:mt-0">
                <!-- Kotak Biru Background -->
                <div class="relative w-72 h-72 md:w-80 md:h-80 bg-[#1ba1e2] rounded-[2.5rem] shadow-[0_20px_50px_rgba(27,161,226,0.4)] mt-16 group">
                    
                    <!-- Foto Direktur Transparan -->
                    <img src="{{ asset('storage/direktur.png') }}" 
                         alt="dr. Windy" 
                         class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-[120%] max-w-[380px] z-10 drop-shadow-2xl transition-transform duration-500 group-hover:scale-105" 
                         style="min-height: 110%; object-fit: contain; object-position: bottom;"
                         onerror="this.src='https://ui-avatars.com/api/?name=Windy+Ary&size=512&background=transparent&color=fff'">
                    
                    <!-- Floating Name Tag -->
                    <div class="absolute -bottom-8 -right-8 md:-right-12 bg-white p-5 md:p-6 rounded-2xl shadow-2xl border border-gray-100 z-20 animate-float min-w-[220px]">
                        <p class="font-extrabold text-[#1a365d] text-xl md:text-2xl">dr. Windy Ary W.</p>
                        <p class="text-md md:text-lg text-[#1ba1e2] font-bold mt-1">Sp.An-Ti</p>
                    </div>
                </div>
            </div>

            <!-- Teks Sambutan -->
            <div data-aos="fade-left" data-aos-duration="1200" class="space-y-8 md:pl-10">
                <div>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-[#1a365d] leading-tight mb-6">
                        Membangun SDM Kesehatan yang <span class="text-[#1ba1e2]">Unggul & Kompeten</span>
                    </h2>
                    <div class="w-24 h-2 bg-[#1ba1e2] rounded-full"></div>
                </div>
                
                <p class="text-lg text-gray-600 leading-relaxed text-justify">
                    Flash Inspire Training Center (FITC) hadir sebagai lembaga pelatihan di bawah naungan PT. Flash Emergency Indonesia. Kami berdedikasi untuk terus meningkatkan kualitas dan keterampilan tenaga medis di seluruh Indonesia melalui metode pelatihan yang terintegrasi dan berstandar nasional.
                </p>

                <blockquote class="text-xl text-gray-500 italic leading-relaxed border-l-4 border-[#1ba1e2] pl-6 py-2 bg-white shadow-sm rounded-r-lg">
                    "Kami berkomitmen memberikan inovasi pelatihan terbaik untuk menjawab tantangan medis global serta memberikan pelayanan yang profesional."
                </blockquote>
                
                <div class="pt-4">
                    <p class="text-[#1a365d] font-bold text-xl">dr. Windy Ary Wijaya, Sp.An-Ti</p>
                    <p class="text-[#1ba1e2] font-semibold tracking-wide uppercase text-sm mt-1">Direktur Utama FITC</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= VISI & MISI ================= -->
    <section class="py-32 px-6 bg-white relative border-y border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-extrabold text-navy">Fokus & Tujuan Kami</h2>
                <p class="text-lg text-gray-500 mt-6 max-w-2xl mx-auto">Menjadi pionir dalam peningkatan mutu sumber daya manusia kesehatan di Indonesia melalui pelatihan yang inovatif dan terakreditasi.</p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12 items-stretch">
                <!-- Visi -->
                <div data-aos="fade-up" data-aos-delay="100" class="bg-gray-50 rounded-[2.5rem] p-12 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 group">
                    <div class="w-20 h-20 bg-[#1a365d] text-white rounded-3xl flex items-center justify-center text-4xl mb-8 shadow-xl group-hover:bg-[#1ba1e2] transition-colors duration-300">🎯</div>
                    <h3 class="text-3xl font-bold text-navy mb-6">Visi Lembaga</h3>
                    <p class="text-xl text-gray-600 leading-relaxed">
                        Mewujudkan lembaga pelatihan dan peningkatan kompetensi yang profesional, inovatif dan berkualitas di bidang kesehatan dengan mengikuti perkembangan teknologi dan berwawasan global.
                    </p>
                </div>

                <!-- Misi -->
                <div data-aos="fade-up" data-aos-delay="300" class="bg-gradient-to-br from-[#1a365d] to-[#0f203b] rounded-[2.5rem] p-12 shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <div class="w-20 h-20 bg-white/10 backdrop-blur-md text-white border border-white/20 rounded-3xl flex items-center justify-center text-4xl mb-8 shadow-xl">🚀</div>
                    <h3 class="text-3xl font-bold text-white mb-8">Misi Utama</h3>
                    <ul class="space-y-6 text-lg text-gray-200">
                        <li class="flex gap-4 items-start"><span class="text-[#1ba1e2] text-2xl font-bold">✓</span> <span class="leading-relaxed">Melaksanakan pelatihan SDMK terintegrasi & memenuhi standar.</span></li>
                        <li class="flex gap-4 items-start"><span class="text-[#1ba1e2] text-2xl font-bold">✓</span> <span class="leading-relaxed">Meningkatkan kualitas dan profesionalisme SDM Kesehatan.</span></li>
                        <li class="flex gap-4 items-start"><span class="text-[#1ba1e2] text-2xl font-bold">✓</span> <span class="leading-relaxed">Memberikan pelayanan profesional, inovatif dan berkualitas.</span></li>
                        <li class="flex gap-4 items-start"><span class="text-[#1ba1e2] text-2xl font-bold">✓</span> <span class="leading-relaxed">Mengembangkan jejaring kerjasama antar lembaga terkait.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= TIMELINE SEJARAH ================= -->
    <section class="py-32 px-6 bg-gray-50 relative">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-24" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-extrabold text-navy">Jejak Langkah Historis</h2>
                <div class="w-24 h-1.5 bg-[#1ba1e2] mx-auto mt-8 rounded-full"></div>
            </div>
            
            <div class="relative">
                <!-- Garis Tengah (Lebih tebal) -->
                <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-2 bg-gray-200 hidden md:block rounded-full"></div>
                
                <div class="space-y-32">
                    <!-- Event 1 -->
                    <div class="relative flex md:justify-start justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-[#1ba1e2] rounded-full border-4 border-white shadow-xl z-10 group-hover:scale-125 transition-all duration-300"></div>
                        <div data-aos="fade-right" class="bg-white p-10 rounded-[2rem] shadow-lg border border-gray-100 w-full md:w-[45%] hover:shadow-2xl hover:border-[#1ba1e2] transition-all duration-300">
                            <span class="text-[#1ba1e2] font-extrabold text-sm tracking-[0.2em] uppercase mb-4 block">29 September 2023</span>
                            <h4 class="text-2xl font-bold text-navy mb-4">Pendirian Perusahaan</h4>
                            <p class="text-gray-600 text-lg leading-relaxed">Pendirian Perseroan Terbatas Flash Emergency Indonesia melalui Akta Notaris Fibrianto Bimo Setiawan, SH., M.Kn.</p>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="relative flex md:justify-end justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-[#1a365d] rounded-full border-4 border-white shadow-xl z-10 group-hover:scale-125 transition-all duration-300"></div>
                        <div data-aos="fade-left" class="bg-white p-10 rounded-[2rem] shadow-lg border border-gray-100 w-full md:w-[45%] hover:shadow-2xl hover:border-[#1a365d] transition-all duration-300">
                            <span class="text-[#1a365d] font-extrabold text-sm tracking-[0.2em] uppercase mb-4 block">30 September 2023</span>
                            <h4 class="text-2xl font-bold text-navy mb-4">Pengesahan Kemenkumham</h4>
                            <p class="text-gray-600 text-lg leading-relaxed">SK Kemenkumham Republik Indonesia tentang Pengesahan Pendirian Badan Hukum PT. Flash Emergency Indonesia.</p>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="relative flex md:justify-start justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-8 h-8 bg-[#1ba1e2] rounded-full border-4 border-white shadow-xl z-10 group-hover:scale-125 transition-all duration-300"></div>
                        <div data-aos="fade-right" class="bg-white p-10 rounded-[2rem] shadow-lg border border-gray-100 w-full md:w-[45%] hover:shadow-2xl hover:border-[#1ba1e2] transition-all duration-300">
                            <span class="text-[#1ba1e2] font-extrabold text-sm tracking-[0.2em] uppercase mb-4 block">10 April 2025</span>
                            <h4 class="text-2xl font-bold text-navy mb-4">Pembentukan FITC</h4>
                            <p class="text-gray-600 text-lg leading-relaxed">Surat Keputusan Direktur PT. Flash Emergency Indonesia tentang Pendirian secara resmi Flash Inspire Training Center.</p>
                        </div>
                    </div>

                    <!-- Event 4 (Puncak) -->
                    <div class="relative flex md:justify-end justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-10 h-10 bg-[#5bc0de] rounded-full border-4 border-white shadow-[0_0_20px_rgba(91,192,222,0.8)] z-10 group-hover:scale-125 transition-all duration-300 animate-pulse"></div>
                        <div data-aos="fade-left" class="bg-gradient-to-br from-[#1a365d] to-[#0f203b] p-10 rounded-[2rem] shadow-2xl w-full md:w-[45%] hover:-translate-y-2 transition-all duration-300 text-white relative overflow-hidden">
                            <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <span class="text-[#5bc0de] font-extrabold text-sm tracking-[0.2em] uppercase mb-4 block relative z-10">3 September 2025</span>
                            <h4 class="text-2xl font-bold mb-4 text-white relative z-10">Akreditasi Kemenkes RI</h4>
                            <p class="text-gray-300 text-lg leading-relaxed relative z-10">Keputusan Dirjen SDM Kesehatan menetapkan FITC terakreditasi Madya (B) sebagai Lembaga Pelatihan Bidang Kesehatan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= PROGRAM PELATIHAN ================= -->
    <section id="program" class="py-32 px-6 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-extrabold text-navy">Program Pelatihan Unggulan</h2>
                <p class="text-lg text-gray-500 mt-6 max-w-3xl mx-auto">Tingkatkan kompetensi medis Anda dengan sertifikasi resmi dan instruktur berpengalaman di bidang gawat darurat dan keperawatan.</p>
            </div>
            
            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-8">
                
                <!-- Card 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="bg-gray-50 rounded-[2rem] p-10 shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-gray-200 group flex flex-col">
                    <h4 class="text-2xl font-extrabold text-navy mb-4">BTCLS</h4>
                    <p class="text-gray-500 text-sm mb-6 h-12">Basic Trauma Cardiac Life Support untuk perawat dan tenaga medis.</p>
                    <p class="text-4xl font-black text-[#1ba1e2] mb-6">Rp 1.5<span class="text-xl text-gray-400 font-bold">jt</span></p>
                    <div class="mb-8"><span class="bg-white border border-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded-full tracking-wider">MIN 25 PESERTA</span></div>
                    <button class="w-full py-4 mt-auto rounded-xl bg-white border-2 border-[#1a365d] text-[#1a365d] font-bold group-hover:bg-[#1a365d] group-hover:text-white transition-all duration-300">Daftar Pelatihan</button>
                </div>

                <!-- Card 2 (Best Seller / Highlight) -->
                <div data-aos="fade-up" data-aos-delay="200" class="bg-[#1a365d] rounded-[2rem] p-10 shadow-2xl transition-all duration-500 transform hover:-translate-y-4 xl:-translate-y-6 group relative overflow-hidden flex flex-col">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-bl-full"></div>
                    <div class="absolute -right-6 top-6 bg-[#1ba1e2] text-white text-xs font-bold px-10 py-1 transform rotate-45 shadow-md">POPULER</div>
                    
                    <h4 class="text-2xl font-extrabold text-white mb-4 relative z-10">ACLS For Nurse</h4>
                    <p class="text-blue-200 text-sm mb-6 h-12 relative z-10">Advanced Cardiac Life Support khusus penanganan lanjut keperawatan.</p>
                    <p class="text-4xl font-black text-[#5bc0de] mb-6 relative z-10">Rp 1.55<span class="text-xl text-blue-300 font-bold">jt</span></p>
                    <div class="mb-8 relative z-10"><span class="bg-white/10 text-white text-xs font-bold px-4 py-2 rounded-full tracking-wider border border-white/20">MIN 25 PESERTA</span></div>
                    <button class="w-full py-4 mt-auto rounded-xl bg-[#1ba1e2] text-white font-bold hover:bg-[#5bc0de] transition-all duration-300 shadow-lg relative z-10">Daftar Pelatihan</button>
                </div>

                <!-- Card 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="bg-gray-50 rounded-[2rem] p-10 shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-gray-200 group flex flex-col">
                    <h4 class="text-2xl font-extrabold text-navy mb-4">PPGDON-KKMN</h4>
                    <p class="text-gray-500 text-sm mb-6 h-12">Pertolongan Pertama Gawat Darurat Obstetri Neonatus.</p>
                    <p class="text-4xl font-black text-[#1ba1e2] mb-6">Rp 1.5<span class="text-xl text-gray-400 font-bold">jt</span></p>
                    <div class="mb-8"><span class="bg-white border border-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded-full tracking-wider">MIN 25 PESERTA</span></div>
                    <button class="w-full py-4 mt-auto rounded-xl bg-white border-2 border-[#1a365d] text-[#1a365d] font-bold group-hover:bg-[#1a365d] group-hover:text-white transition-all duration-300">Daftar Pelatihan</button>
                </div>

                <!-- Card 4 -->
                <div data-aos="fade-up" data-aos-delay="400" class="bg-gray-50 rounded-[2rem] p-10 shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 border border-gray-200 group flex flex-col">
                    <h4 class="text-2xl font-extrabold text-navy mb-4">Preceptorship</h4>
                    <p class="text-gray-500 text-sm mb-6 h-12">Pelatihan pembimbing klinik bagi tenaga medis di fasilitas pelayanan.</p>
                    <p class="text-4xl font-black text-[#1ba1e2] mb-6">Rp 800<span class="text-xl text-gray-400 font-bold">rb</span></p>
                    <div class="mb-8"><span class="bg-white border border-gray-200 text-gray-700 text-xs font-bold px-4 py-2 rounded-full tracking-wider">MIN 25 PESERTA</span></div>
                    <button class="w-full py-4 mt-auto rounded-xl bg-white border-2 border-[#1a365d] text-[#1a365d] font-bold group-hover:bg-[#1a365d] group-hover:text-white transition-all duration-300">Daftar Pelatihan</button>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FASILITAS PELATIHAN (SECTION BARU) ================= -->
    <section class="py-24 px-6 bg-[#1a365d] relative overflow-hidden">
        <!-- Pattern background -->
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#fff 2px, transparent 2px); background-size: 30px 30px;"></div>
        
        <div class="max-w-6xl mx-auto relative z-10 text-center">
            <h2 data-aos="fade-up" class="text-3xl md:text-4xl font-extrabold text-white mb-16">Fasilitas yang Anda Dapatkan</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Item 1 -->
                <div data-aos="zoom-in" data-aos-delay="100" class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center hover:bg-white/20 transition-all">
                    <div class="text-4xl mb-4">📄</div>
                    <p class="text-white font-semibold">E-Materi &<br>E-Sertifikat</p>
                </div>
                <!-- Item 2 -->
                <div data-aos="zoom-in" data-aos-delay="200" class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center hover:bg-white/20 transition-all">
                    <div class="text-4xl mb-4">💳</div>
                    <p class="text-white font-semibold">Kartu<br>Peserta ID</p>
                </div>
                <!-- Item 3 -->
                <div data-aos="zoom-in" data-aos-delay="300" class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center hover:bg-white/20 transition-all">
                    <div class="text-4xl mb-4">👕</div>
                    <p class="text-white font-semibold">T-Shirt &<br>Tas Pelatihan</p>
                </div>
                <!-- Item 4 -->
                <div data-aos="zoom-in" data-aos-delay="400" class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center hover:bg-white/20 transition-all">
                    <div class="text-4xl mb-4">📚</div>
                    <p class="text-white font-semibold">Modul &<br>Blocknote Kit</p>
                </div>
            </div>
            <p data-aos="fade-up" data-aos-delay="500" class="text-blue-200 mt-10 text-sm italic">*Khusus program Preceptorship hanya mendapatkan fasilitas E-Materi & E-Sertifikat.</p>
        </div>
    </section>

    <!-- ================= LEMBAGA MITRA ================= -->
    <section class="py-32 bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h3 data-aos="fade-up" class="text-sm font-bold text-gray-400 tracking-[0.3em] uppercase mb-12">Kolaborasi & Kemitraan</h3>
            <h2 data-aos="fade-up" class="text-3xl font-extrabold text-navy mb-16">Dipercaya Oleh Berbagai Institusi</h2>
            
            <div class="flex flex-wrap justify-center items-center gap-6 lg:gap-8">
                <!-- Mitra Data dipisah menjadi blok-blok besar agar menambah panjang -->
                <div data-aos="zoom-in" data-aos-delay="100" class="px-10 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-extrabold text-gray-600 text-lg hover:text-[#1ba1e2] hover:shadow-xl hover:-translate-y-1 transition-all cursor-default min-w-[250px]">PPNI</div>
                <div data-aos="zoom-in" data-aos-delay="200" class="px-10 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-extrabold text-gray-600 text-lg hover:text-[#1ba1e2] hover:shadow-xl hover:-translate-y-1 transition-all cursor-default min-w-[250px]">Ikatan Bidan Indonesia</div>
                <div data-aos="zoom-in" data-aos-delay="300" class="px-10 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-extrabold text-gray-600 text-lg hover:text-[#1ba1e2] hover:shadow-xl hover:-translate-y-1 transition-all cursor-default min-w-[250px]">Bulan Sabit Merah (BSMI)</div>
                <div data-aos="zoom-in" data-aos-delay="400" class="px-10 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-extrabold text-gray-600 text-lg hover:text-[#1ba1e2] hover:shadow-xl hover:-translate-y-1 transition-all cursor-default min-w-[250px]">RSUD Kilisuci Kediri</div>
                <div data-aos="zoom-in" data-aos-delay="500" class="px-10 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-extrabold text-gray-600 text-lg hover:text-[#1ba1e2] hover:shadow-xl hover:-translate-y-1 transition-all cursor-default min-w-[250px]">SEHATI</div>
                <div data-aos="zoom-in" data-aos-delay="600" class="px-10 py-6 bg-white rounded-2xl shadow-sm border border-gray-100 font-extrabold text-gray-600 text-lg hover:text-[#1ba1e2] hover:shadow-xl hover:-translate-y-1 transition-all cursor-default min-w-[250px]">Gema Amal Nusantara</div>
            </div>
        </div>
    </section>

    <!-- ================= CALL TO ACTION (CTA) ================= -->
    <section class="py-24 px-6 bg-white border-t border-gray-100 text-center">
        <div class="max-w-4xl mx-auto" data-aos="zoom-in">
            <h2 class="text-4xl font-extrabold text-navy mb-6">Siap Meningkatkan Kompetensi Anda?</h2>
            <p class="text-xl text-gray-500 mb-10">Hubungi tim administrasi kami sekarang juga untuk informasi jadwal pelatihan terdekat dan pendaftaran institusi.</p>
            <a href="https://wa.me/6281252259463" target="_blank" class="inline-block bg-[#1ba1e2] text-white font-bold text-xl py-5 px-12 rounded-full hover:bg-[#1a365d] transition-colors duration-300 shadow-xl transform hover:-translate-y-1">
                Hubungi Kami di WhatsApp
            </a>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-[#0B1120] pt-24 pb-12 px-6 relative overflow-hidden">
        <!-- Top border gradient -->
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#5bc0de] via-[#1ba1e2] to-[#1a365d]"></div>
        
        <div class="max-w-7xl mx-auto grid md:grid-cols-12 gap-16 border-b border-white/10 pb-16 relative z-10">
            <div class="md:col-span-5">
                <div class="flex items-center gap-4 mb-8">
                    <img src="{{ asset('storage/icon.png') }}" alt="Logo FITC" class="h-14 w-auto">
                    <span class="font-extrabold text-3xl text-white tracking-widest">FITC</span>
                </div>
                <p class="text-gray-400 mb-8 text-md leading-relaxed max-w-sm">
                    Lembaga Pelatihan Bidang Kesehatan Terakreditasi Madya (B) Kemenkes RI di bawah naungan PT. Flash Emergency Indonesia.
                </p>
                <div class="flex gap-4">
                    <a href="https://instagram.com/flashinspire.trainingcenter" class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white text-xl hover:bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 hover:border-transparent transition-all duration-300 transform hover:scale-110 shadow-lg">📸</a>
                </div>
            </div>

            <div class="md:col-span-7 grid sm:grid-cols-2 gap-10">
                <div>
                    <h4 class="text-white font-bold mb-8 uppercase tracking-widest text-sm">Kontak Informasi</h4>
                    <ul class="space-y-6 text-gray-400 text-md">
                        <li class="flex items-center gap-4 hover:text-[#1ba1e2] transition-colors">
                            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-[#1ba1e2]">📞</div> 
                            <span class="font-medium">+62 812 5225 9463</span>
                        </li>
                        <li class="flex items-center gap-4 hover:text-[#1ba1e2] transition-colors">
                            <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-[#1ba1e2]">✉️</div> 
                            <span class="font-medium">ptflashemergencyindonesia<br>@gmail.com</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-8 uppercase tracking-widest text-sm">Alamat Kantor</h4>
                    <div class="flex gap-4 text-gray-400 text-md hover:text-[#1ba1e2] transition-colors">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-[#1ba1e2] shrink-0">📍</div>
                        <p class="leading-relaxed font-medium">
                            Dusun Sobontoro RT 03 RW II<br>
                            Desa Watudandang, Kec. Prambon<br>
                            Kab. Nganjuk, Jawa Timur
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500 font-medium relative z-10">
            <p>&copy; 2026 Flash Inspire Training Center.</p>
            <p>Membangun SDM Kesehatan Unggul di Indonesia</p>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Inisialisasi Animasi Scroll
        AOS.init({
            once: true, // Animasi berjalan sekali
            offset: 120, // Jarak trigger animasi lebih jauh agar mulus
        });
    </script>
</body>
</html>