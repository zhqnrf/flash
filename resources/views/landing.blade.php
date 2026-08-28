<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flash Inspire Training Center</title>
    <link rel="shortcut icon" href="{{ asset('storage/icon.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('storage/icon.png') }}" sizes="180x180">
    
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
        /* Floating Animation */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        /* --- ANIMASI MARQUEE / LOGO BERJALAN --- */
        .marquee-wrapper {
            display: flex;
            overflow: hidden;
            user-select: none;
            gap: 1.5rem;
            mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        }
        .marquee-content {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: space-around;
            gap: 1.5rem;
            min-width: 100%;
            animation: scroll 25s linear infinite;
        }
        .marquee-wrapper:hover .marquee-content {
            animation-play-state: paused;
        }
        @keyframes scroll {
            from { transform: translateX(0); }
            to { transform: translateX(calc(-100% - 1.5rem)); }
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased text-gray-800 bg-gray-50 overflow-x-hidden w-full" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- ================= NAVBAR ================= -->
    <nav :class="{'bg-white/90 backdrop-blur-lg shadow-lg py-2': scrolled, 'bg-transparent py-4 sm:py-6': !scrolled}" class="fixed w-full z-50 transition-all duration-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo dengan Wadah Putih -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="bg-white w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center shadow-lg border border-gray-100 overflow-hidden shrink-0">
                        <img src="{{ asset('storage/icon.png') }}" alt="Logo FITC" class="w-full h-full object-contain p-1.5" onerror="this.src='https://ui-avatars.com/api/?name=F&background=1a365d&color=fff'">
                    </div>
                    <span :class="{'text-navy': scrolled, 'text-white drop-shadow-md': !scrolled}" class="font-extrabold text-xl sm:text-2xl md:text-3xl tracking-widest transition-colors duration-300">FITC</span>
                </div>
                <!-- Login Button -->
                <div>
                    <a href="{{ url('/login') }}" :class="{'bg-gradient-to-r from-primary to-ocean text-white shadow-lg shadow-blue-500/30': scrolled, 'bg-white/10 text-white backdrop-blur-md border border-white/30 hover:bg-white hover:text-navy': !scrolled}" class="font-bold text-sm sm:text-base py-2 px-5 sm:py-3 sm:px-8 rounded-full transition-all duration-300 transform hover:scale-105 inline-block">
                        Login 
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ================= HERO SECTION ================= -->
    <section class="relative bg-gradient-to-br from-[#1a365d] to-[#0f203b] pt-36 sm:pt-48 pb-40 sm:pb-52 md:pb-64 flex items-center justify-center overflow-hidden min-h-[90vh] md:min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center relative z-10 flex flex-col items-center">
            
            <div data-aos="fade-down" data-aos-duration="1000" class="inline-block px-4 sm:px-6 py-2 rounded-full border border-white/30 text-white/90 text-xs sm:text-sm font-semibold tracking-wider sm:tracking-[0.15em] mb-6 sm:mb-10 backdrop-blur-sm shadow-sm text-center">
                TERAKREDITASI MADYA (B) KEMENKES RI
            </div>
            
            <h1 data-aos="zoom-in" data-aos-duration="1200" data-aos-delay="200" class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold text-white mb-2 tracking-tight drop-shadow-xl leading-tight">
                Flash Inspire
            </h1>
            <h1 data-aos="zoom-in" data-aos-duration="1200" data-aos-delay="300" class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-[#5bc0de] mb-8 sm:mb-10 tracking-tight drop-shadow-xl leading-tight">
                Training Center
            </h1>
            
            <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500" class="text-base sm:text-xl md:text-2xl text-gray-300 mb-2 sm:mb-3 font-light px-4">
                "Enerjik - Responsif - Inovatif - Lugas"
            </p>
            <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600" class="text-base sm:text-xl md:text-2xl font-bold text-white mb-10 sm:mb-16 tracking-wide px-4">
                Fun - Learn - Agile, Smile - Humble
            </p>
            
            <div data-aos="fade-up" data-aos-duration="1000" data-aos-delay="800" class="w-full sm:w-auto px-4 sm:px-0">
                <a href="#program" class="bg-white text-[#1a365d] font-bold text-base sm:text-lg py-3 sm:py-4 px-8 sm:px-12 rounded-full hover:bg-gray-100 transition-all duration-300 shadow-[0_0_30px_rgba(255,255,255,0.3)] transform hover:-translate-y-1 block sm:inline-block w-full sm:w-auto">
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
    <section class="pt-8 sm:pt-10 pb-20 sm:pb-32 px-4 sm:px-6 bg-gray-50 relative z-10 w-full overflow-hidden">
        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 md:gap-16 items-center">
            
            <!-- Area Foto Direktur -->
            <div data-aos="fade-right" data-aos-duration="1200" class="relative flex justify-center mt-10 md:mt-0 order-2 md:order-1">
                <div class="relative w-56 h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 bg-[#1ba1e2] rounded-[2rem] sm:rounded-[2.5rem] shadow-[0_20px_50px_rgba(27,161,226,0.4)] mt-8 sm:mt-16 group mx-auto md:mx-0">
                    
                    <img src="{{ asset('storage/direktur.png') }}" 
                         alt="dr. Windy" 
                         class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-[120%] max-w-[260px] sm:max-w-[340px] md:max-w-[380px] z-10 drop-shadow-2xl transition-transform duration-500 group-hover:scale-105" 
                         style="min-height: 110%; object-fit: contain; object-position: bottom;"
                         onerror="this.src='https://ui-avatars.com/api/?name=Windy+Ary&size=512&background=transparent&color=fff'">
                    
                    <!-- Floating Name Tag -->
                    <div class="absolute -bottom-6 sm:-bottom-8 left-1/2 md:left-auto transform -translate-x-1/2 md:translate-x-0 md:-right-12 bg-white p-4 sm:p-5 md:p-6 rounded-xl sm:rounded-2xl shadow-2xl border border-gray-100 z-20 animate-float min-w-[200px] sm:min-w-[220px] text-center md:text-left">
                        <p class="font-extrabold text-[#1a365d] text-lg sm:text-xl md:text-2xl">dr. Windy Ary W.</p>
                        <p class="text-sm sm:text-md md:text-lg text-[#1ba1e2] font-bold mt-1">Sp.An-Ti</p>
                    </div>
                </div>
            </div>

            <!-- Teks Sambutan -->
            <div data-aos="fade-left" data-aos-duration="1200" class="space-y-6 sm:space-y-8 md:pl-10 order-1 md:order-2 text-center md:text-left">
                <div>
                    <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-[#1a365d] leading-tight mb-4 sm:mb-6">
                        Membangun SDM Kesehatan yang <span class="text-[#1ba1e2]">Unggul & Kompeten</span>
                    </h2>
                    <div class="w-16 sm:w-24 h-1.5 sm:h-2 bg-[#1ba1e2] rounded-full mx-auto md:mx-0"></div>
                </div>
                
                <p class="text-sm sm:text-lg text-gray-600 leading-relaxed text-justify md:text-left">
                    Flash Inspire Training Center (FITC) hadir sebagai lembaga pelatihan di bawah naungan PT. Flash Emergency Indonesia. Kami berdedikasi untuk terus meningkatkan kualitas dan keterampilan tenaga medis di seluruh Indonesia melalui metode pelatihan yang terintegrasi dan berstandar nasional.
                </p>

                <blockquote class="text-base sm:text-xl text-gray-500 italic leading-relaxed border-l-4 border-[#1ba1e2] pl-4 sm:pl-6 py-2 bg-white shadow-sm rounded-r-lg text-left">
                    "Kami berkomitmen memberikan inovasi pelatihan terbaik untuk menjawab tantangan medis global serta memberikan pelayanan yang profesional."
                </blockquote>
                
                <div class="pt-2 sm:pt-4">
                    <p class="text-[#1a365d] font-bold text-base sm:text-xl">dr. Windy Ary Wijaya, Sp.An-Ti</p>
                    <p class="text-[#1ba1e2] font-semibold tracking-wide uppercase text-[10px] sm:text-sm mt-1">Direktur Utama FITC</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= VISI & MISI ================= -->
    <section class="py-20 sm:py-24 md:py-32 px-4 sm:px-6 bg-white relative border-y border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12 sm:mb-20" data-aos="fade-up">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-navy">Fokus & Tujuan Kami</h2>
                <p class="text-base sm:text-lg text-gray-500 mt-4 sm:mt-6 max-w-2xl mx-auto">Menjadi pionir dalam peningkatan mutu sumber daya manusia kesehatan di Indonesia melalui pelatihan yang inovatif dan terakreditasi.</p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-8 sm:gap-12 items-stretch">
                <!-- Visi -->
                <div data-aos="fade-up" data-aos-delay="100" class="bg-gray-50 rounded-[2rem] sm:rounded-[2.5rem] p-8 sm:p-12 shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 group">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#1a365d] text-white rounded-2xl sm:rounded-3xl flex items-center justify-center text-3xl sm:text-4xl mb-6 sm:mb-8 shadow-xl group-hover:bg-[#1ba1e2] transition-colors duration-300">🎯</div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-navy mb-4 sm:mb-6">Visi Lembaga</h3>
                    <p class="text-lg sm:text-xl text-gray-600 leading-relaxed">
                        Mewujudkan lembaga pelatihan dan peningkatan kompetensi yang profesional, inovatif dan berkualitas di bidang kesehatan dengan mengikuti perkembangan teknologi dan berwawasan global.
                    </p>
                </div>

                <!-- Misi -->
                <div data-aos="fade-up" data-aos-delay="300" class="bg-gradient-to-br from-[#1a365d] to-[#0f203b] rounded-[2rem] sm:rounded-[2.5rem] p-8 sm:p-12 shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/10 backdrop-blur-md text-white border border-white/20 rounded-2xl sm:rounded-3xl flex items-center justify-center text-3xl sm:text-4xl mb-6 sm:mb-8 shadow-xl">🚀</div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-white mb-6 sm:mb-8">Misi Utama</h3>
                    <ul class="space-y-4 sm:space-y-6 text-base sm:text-lg text-gray-200">
                        <li class="flex gap-3 sm:gap-4 items-start"><span class="text-[#1ba1e2] text-xl sm:text-2xl font-bold mt-1 sm:mt-0">✓</span> <span class="leading-relaxed">Melaksanakan pelatihan SDMK terintegrasi & memenuhi standar.</span></li>
                        <li class="flex gap-3 sm:gap-4 items-start"><span class="text-[#1ba1e2] text-xl sm:text-2xl font-bold mt-1 sm:mt-0">✓</span> <span class="leading-relaxed">Meningkatkan kualitas dan profesionalisme SDM Kesehatan.</span></li>
                        <li class="flex gap-3 sm:gap-4 items-start"><span class="text-[#1ba1e2] text-xl sm:text-2xl font-bold mt-1 sm:mt-0">✓</span> <span class="leading-relaxed">Memberikan pelayanan profesional, inovatif dan berkualitas.</span></li>
                        <li class="flex gap-3 sm:gap-4 items-start"><span class="text-[#1ba1e2] text-xl sm:text-2xl font-bold mt-1 sm:mt-0">✓</span> <span class="leading-relaxed">Mengembangkan jejaring kerjasama antar lembaga terkait.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= TIMELINE SEJARAH ================= -->
    <section class="py-20 sm:py-24 md:py-32 px-4 sm:px-6 bg-gray-50 relative overflow-hidden">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16 sm:mb-24" data-aos="fade-up">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-navy">Jejak Langkah Historis</h2>
                <div class="w-16 sm:w-24 h-1.5 bg-[#1ba1e2] mx-auto mt-6 sm:mt-8 rounded-full"></div>
            </div>
            
            <div class="relative">
                <!-- Garis Tengah -->
                <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-1.5 sm:w-2 bg-gray-200 hidden md:block rounded-full"></div>
                
                <div class="space-y-12 sm:space-y-16 md:space-y-32">
                    <!-- Event 1 -->
                    <div class="relative flex md:justify-start justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-6 h-6 sm:w-8 sm:h-8 bg-[#1ba1e2] rounded-full border-4 border-white shadow-xl z-10 group-hover:scale-125 transition-all duration-300"></div>
                        <div data-aos="fade-up" md:data-aos="fade-right" class="bg-white p-6 sm:p-8 md:p-10 rounded-[1.5rem] sm:rounded-[2rem] shadow-lg border border-gray-100 w-full md:w-[45%] hover:shadow-2xl hover:border-[#1ba1e2] transition-all duration-300">
                            <span class="text-[#1ba1e2] font-extrabold text-xs sm:text-sm tracking-widest sm:tracking-[0.2em] uppercase mb-3 sm:mb-4 block">29 September 2023</span>
                            <h4 class="text-xl sm:text-2xl font-bold text-navy mb-3 sm:mb-4">Pendirian Perusahaan</h4>
                            <p class="text-gray-600 text-base sm:text-lg leading-relaxed">Pendirian Perseroan Terbatas Flash Emergency Indonesia melalui Akta Notaris Fibrianto Bimo Setiawan, SH., M.Kn.</p>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="relative flex md:justify-end justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-6 h-6 sm:w-8 sm:h-8 bg-[#1a365d] rounded-full border-4 border-white shadow-xl z-10 group-hover:scale-125 transition-all duration-300"></div>
                        <div data-aos="fade-up" md:data-aos="fade-left" class="bg-white p-6 sm:p-8 md:p-10 rounded-[1.5rem] sm:rounded-[2rem] shadow-lg border border-gray-100 w-full md:w-[45%] hover:shadow-2xl hover:border-[#1a365d] transition-all duration-300">
                            <span class="text-[#1a365d] font-extrabold text-xs sm:text-sm tracking-widest sm:tracking-[0.2em] uppercase mb-3 sm:mb-4 block">30 September 2023</span>
                            <h4 class="text-xl sm:text-2xl font-bold text-navy mb-3 sm:mb-4">Pengesahan Kemenkumham</h4>
                            <p class="text-gray-600 text-base sm:text-lg leading-relaxed">SK Kemenkumham Republik Indonesia tentang Pengesahan Pendirian Badan Hukum PT. Flash Emergency Indonesia.</p>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="relative flex md:justify-start justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-6 h-6 sm:w-8 sm:h-8 bg-[#1ba1e2] rounded-full border-4 border-white shadow-xl z-10 group-hover:scale-125 transition-all duration-300"></div>
                        <div data-aos="fade-up" md:data-aos="fade-right" class="bg-white p-6 sm:p-8 md:p-10 rounded-[1.5rem] sm:rounded-[2rem] shadow-lg border border-gray-100 w-full md:w-[45%] hover:shadow-2xl hover:border-[#1ba1e2] transition-all duration-300">
                            <span class="text-[#1ba1e2] font-extrabold text-xs sm:text-sm tracking-widest sm:tracking-[0.2em] uppercase mb-3 sm:mb-4 block">10 April 2025</span>
                            <h4 class="text-xl sm:text-2xl font-bold text-navy mb-3 sm:mb-4">Pembentukan FITC</h4>
                            <p class="text-gray-600 text-base sm:text-lg leading-relaxed">Surat Keputusan Direktur PT. Flash Emergency Indonesia tentang Pendirian secara resmi Flash Inspire Training Center.</p>
                        </div>
                    </div>

                    <!-- Event 4 (Puncak) -->
                    <div class="relative flex md:justify-end justify-center items-center w-full group">
                        <div data-aos="zoom-in" class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-8 h-8 sm:w-10 sm:h-10 bg-[#5bc0de] rounded-full border-4 border-white shadow-[0_0_20px_rgba(91,192,222,0.8)] z-10 group-hover:scale-125 transition-all duration-300 animate-pulse"></div>
                        <div data-aos="fade-up" md:data-aos="fade-left" class="bg-gradient-to-br from-[#1a365d] to-[#0f203b] p-6 sm:p-8 md:p-10 rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl w-full md:w-[45%] hover:-translate-y-2 transition-all duration-300 text-white relative overflow-hidden">
                            <div class="absolute -right-6 -top-6 sm:-right-10 sm:-top-10 w-24 h-24 sm:w-32 sm:h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <span class="text-[#5bc0de] font-extrabold text-xs sm:text-sm tracking-widest sm:tracking-[0.2em] uppercase mb-3 sm:mb-4 block relative z-10">3 September 2025</span>
                            <h4 class="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-white relative z-10">Akreditasi Kemenkes RI</h4>
                            <p class="text-gray-300 text-base sm:text-lg leading-relaxed relative z-10">Keputusan Dirjen SDM Kesehatan menetapkan FITC terakreditasi Madya (B) sebagai Lembaga Pelatihan Bidang Kesehatan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!-- ================= PROGRAM PELATIHAN (Dibatasi 4 & Pakai Str Limit) ================= -->
    <section id="program" class="py-20 sm:py-24 md:py-32 px-4 sm:px-6 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12 sm:mb-20" data-aos="fade-up">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-navy">Program Pelatihan Unggulan</h2>
                <p class="text-base sm:text-lg text-gray-500 mt-4 sm:mt-6 max-w-3xl mx-auto">Tingkatkan kompetensi medis Anda dengan sertifikasi resmi dan instruktur berpengalaman di bidang gawat darurat dan keperawatan.</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @forelse($events ?? [] as $event)
                    @php
                        // Logika menentukan card populer (misal urutan ke-2 atau sesuai keinginan)
                        $isPopular = $loop->index === 1; 
                        $namaPelatihan = $event->pelatihan->nama_pelatihan ?? $event->nama_event ?? 'Program Pelatihan';
                        $deskripsi = $event->deskripsi ?? 'Pelatihan resmi bersertifikasi untuk tenaga medis dan kesehatan.';
                    @endphp

                    @if($isPopular)
                        <!-- Card Highlight / Populer -->
                        <div data-aos="fade-up" data-aos-delay="{{ ($loop->index + 1) * 100 }}" class="bg-[#1a365d] rounded-[1.5rem] sm:rounded-[2rem] p-6 sm:p-8 md:p-10 shadow-2xl transition-all duration-500 transform hover:-translate-y-2 sm:hover:-translate-y-4 xl:-translate-y-6 group relative overflow-hidden flex flex-col">
                            <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-white/5 rounded-bl-full"></div>
                            <div class="absolute -right-8 top-5 sm:-right-6 sm:top-6 bg-[#1ba1e2] text-white text-[10px] sm:text-xs font-bold px-8 sm:px-10 py-1 transform rotate-45 shadow-md">POPULER</div>
                            
                            <h4 class="text-xl sm:text-2xl font-extrabold text-white mb-3 sm:mb-4 relative z-10 pr-6">{{ $namaPelatihan }}</h4>
                            <p class="text-blue-200 text-sm mb-4 sm:mb-6 flex-grow relative z-10">{{ Str::limit($deskripsi, 80) }}</p>
                            <p class="text-3xl sm:text-4xl font-black text-[#5bc0de] mb-4 sm:mb-6 mt-auto relative z-10">Rp {{ number_format($event->biaya ?? 1500000, 0, ',', '.') }}</p>
                            <div class="mb-6 sm:mb-8 relative z-10"><span class="bg-white/10 text-white text-[10px] sm:text-xs font-bold px-3 sm:px-4 py-1.5 sm:py-2 rounded-full tracking-wider border border-white/20 block text-center sm:inline-block">MIN {{ $event->minimal_peserta ?? 25 }} PESERTA</span></div>
                            <a href="{{ url('/register-event/' . ($event->uuid ?? '#')) }}" class="w-full py-3 sm:py-4 rounded-xl bg-[#1ba1e2] text-white font-bold hover:bg-[#5bc0de] transition-all duration-300 shadow-lg relative z-10 text-center block">Daftar Pelatihan</a>
                        </div>
                    @else
                        <!-- Card Standar -->
                        <div data-aos="fade-up" data-aos-delay="{{ ($loop->index + 1) * 100 }}" class="bg-gray-50 rounded-[1.5rem] sm:rounded-[2rem] p-6 sm:p-8 md:p-10 shadow-md hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 sm:hover:-translate-y-4 border border-gray-200 group flex flex-col">
                            <h4 class="text-xl sm:text-2xl font-extrabold text-navy mb-3 sm:mb-4">{{ $namaPelatihan }}</h4>
                            <p class="text-gray-500 text-sm mb-4 sm:mb-6 flex-grow">{{ Str::limit($deskripsi, 80) }}</p>
                            <p class="text-3xl sm:text-4xl font-black text-[#1ba1e2] mb-4 sm:mb-6 mt-auto">Rp {{ number_format($event->biaya ?? 1500000, 0, ',', '.') }}</p>
                            <div class="mb-6 sm:mb-8"><span class="bg-white border border-gray-200 text-gray-700 text-[10px] sm:text-xs font-bold px-3 sm:px-4 py-1.5 sm:py-2 rounded-full tracking-wider block text-center sm:inline-block">MIN {{ $event->minimal_peserta ?? 25 }} PESERTA</span></div>
                            <a href="{{ url('/register-event/' . ($event->uuid ?? '#')) }}" class="w-full py-3 sm:py-4 rounded-xl bg-white border-2 border-[#1a365d] text-[#1a365d] font-bold group-hover:bg-[#1a365d] group-hover:text-white transition-all duration-300 text-center block">Daftar Pelatihan</a>
                        </div>
                    @endif
                @empty
                    <!-- Fallback Statis jika tabel events kosong -->
                    <div class="bg-gray-50 rounded-[1.5rem] sm:rounded-[2rem] p-6 sm:p-8 md:p-10 shadow-md border border-gray-200 flex flex-col">
                        <h4 class="text-xl sm:text-2xl font-extrabold text-navy mb-3">BTCLS</h4>
                        <p class="text-gray-500 text-sm mb-4 flex-grow">Basic Trauma Cardiac Life Support untuk perawat dan tenaga medis.</p>
                        <p class="text-3xl sm:text-4xl font-black text-[#1ba1e2] mb-4 mt-auto">Rp 1.5jt</p>
                        <div class="mb-6"><span class="bg-white border border-gray-200 text-gray-700 text-[10px] font-bold px-3 py-1.5 rounded-full">MIN 25 PESERTA</span></div>
                        <a href="#" class="w-full py-3 rounded-xl bg-white border-2 border-[#1a365d] text-[#1a365d] font-bold text-center block">Daftar Pelatihan</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ================= FASILITAS PELATIHAN (No Emoji, Pure SVG) ================= -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 bg-[#1a365d] relative overflow-hidden">
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#fff 2px, transparent 2px); background-size: 20px 20px sm:30px sm:30px;"></div>
        
        <div class="max-w-6xl mx-auto relative z-10 text-center">
            <h2 data-aos="fade-up" class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-10 sm:mb-16">Fasilitas yang Anda Dapatkan</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
                <!-- Item 1: Document -->
                <div data-aos="zoom-in" data-aos-delay="100" class="bg-white/5 backdrop-blur-sm p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border border-white/10 text-center hover:bg-white/10 transition-all group">
                    <svg class="w-10 h-10 sm:w-14 sm:h-14 mb-4 mx-auto text-[#1ba1e2] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                    </svg>
                    <p class="text-white text-sm sm:text-base font-medium leading-tight">E-Materi &<br>E-Sertifikat</p>
                </div>
                <!-- Item 2: ID Card -->
                <div data-aos="zoom-in" data-aos-delay="200" class="bg-white/5 backdrop-blur-sm p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border border-white/10 text-center hover:bg-white/10 transition-all group">
                    <svg class="w-10 h-10 sm:w-14 sm:h-14 mb-4 mx-auto text-[#1ba1e2] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"></path>
                    </svg>
                    <p class="text-white text-sm sm:text-base font-medium leading-tight">Kartu<br>Peserta ID</p>
                </div>
                <!-- Item 3: T-Shirt -->
                <div data-aos="zoom-in" data-aos-delay="300" class="bg-white/5 backdrop-blur-sm p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border border-white/10 text-center hover:bg-white/10 transition-all group">
                    <svg class="w-10 h-10 sm:w-14 sm:h-14 mb-4 mx-auto text-[#1ba1e2] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 12.677c-.066.214-.1.437-.1.661z"></path>
                    </svg>
                    <p class="text-white text-sm sm:text-base font-medium leading-tight">T-Shirt &<br>Tas Pelatihan</p>
                </div>
                <!-- Item 4: Book -->
                <div data-aos="zoom-in" data-aos-delay="400" class="bg-white/5 backdrop-blur-sm p-5 sm:p-6 md:p-8 rounded-xl sm:rounded-2xl border border-white/10 text-center hover:bg-white/10 transition-all group">
                    <svg class="w-10 h-10 sm:w-14 sm:h-14 mb-4 mx-auto text-[#1ba1e2] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p class="text-white text-sm sm:text-base font-medium leading-tight">Modul &<br>Blocknote Kit</p>
                </div>
            </div>
            <p data-aos="fade-up" data-aos-delay="500" class="text-blue-200 mt-8 sm:mt-10 text-xs sm:text-sm italic px-4">*Khusus program Preceptorship hanya mendapatkan fasilitas E-Materi & E-Sertifikat.</p>
        </div>
    </section>

    <!-- ================= LEMBAGA MITRA (MARQUEE BERJALAN) ================= -->
    <section class="py-16 sm:py-24 bg-gray-50 border-t border-gray-200 overflow-hidden">
        <div class="max-w-7xl mx-auto text-center px-4">
            <h3 data-aos="fade-up" class="text-xs sm:text-sm font-bold text-gray-400 tracking-widest sm:tracking-[0.3em] uppercase mb-4 sm:mb-8">Kolaborasi & Kemitraan</h3>
            <h2 data-aos="fade-up" class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-navy mb-8 sm:mb-12">Dipercaya Oleh Berbagai Institusi</h2>
        </div>
            
        <!-- Kontainer Marquee -->
        <div class="marquee-wrapper w-full mt-4 sm:mt-10" data-aos="fade-up" data-aos-delay="200">
            <!-- Wrapper 1 -->
            <div class="marquee-content">
                <!-- Card Mitra 1 -->
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-ppni.png') }}" alt="PPNI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=P&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">PPNI</span>
                </div>
                <!-- Card Mitra 2 -->
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-ibi.png') }}" alt="IBI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=I&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">Ikatan Bidan Indonesia</span>
                </div>
                <!-- Card Mitra 3 -->
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-bsmi.png') }}" alt="BSMI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=B&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">BSMI</span>
                </div>
                <!-- Card Mitra 4 -->
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-kilisuci.png') }}" alt="RSUD Kilisuci" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=R&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">RSUD Kilisuci Kediri</span>
                </div>
                <!-- Card Mitra 5 -->
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-sehati.png') }}" alt="SEHATI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=S&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">SEHATI</span>
                </div>
                <!-- Card Mitra 6 -->
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-gema.png') }}" alt="Gema Amal" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=G&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">Gema Amal Nusantara</span>
                </div>
            </div>

            <!-- Wrapper 2 (Duplikat untuk efek infinite scroll tanpa putus) -->
            <div class="marquee-content" aria-hidden="true">
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-ppni.png') }}" alt="PPNI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=P&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">PPNI</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-ibi.png') }}" alt="IBI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=I&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">Ikatan Bidan Indonesia</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-bsmi.png') }}" alt="BSMI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=B&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">BSMI</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-kilisuci.png') }}" alt="RSUD Kilisuci" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=R&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">RSUD Kilisuci Kediri</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-sehati.png') }}" alt="SEHATI" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=S&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">SEHATI</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 px-5 sm:px-8 py-3 sm:py-4 bg-white rounded-2xl shadow-sm border border-gray-100 shrink-0 hover:border-[#1ba1e2] hover:shadow-md transition-all cursor-pointer group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-full border border-gray-100 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('storage/logo-gema.png') }}" alt="Gema Amal" class="w-full h-full object-contain group-hover:scale-110 transition-transform" onerror="this.src='https://ui-avatars.com/api/?name=G&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-gray-600 text-sm sm:text-base whitespace-nowrap group-hover:text-[#1ba1e2]">Gema Amal Nusantara</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= CALL TO ACTION (CTA) ================= -->
    <section class="py-16 sm:py-20 md:py-24 px-4 sm:px-6 bg-white border-t border-gray-100 text-center">
        <div class="max-w-4xl mx-auto" data-aos="zoom-in">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-navy mb-4 sm:mb-6 leading-tight">Siap Meningkatkan Kompetensi Anda?</h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-500 mb-8 sm:mb-10 px-2">Hubungi tim administrasi kami sekarang juga untuk informasi jadwal pelatihan terdekat dan pendaftaran institusi.</p>
            <a href="https://wa.me/6281252259463" target="_blank" class="inline-block w-full sm:w-auto bg-[#1ba1e2] text-white font-bold text-lg sm:text-xl py-4 sm:py-5 px-8 sm:px-12 rounded-full hover:bg-[#1a365d] transition-colors duration-300 shadow-xl transform hover:-translate-y-1">
                Hubungi Kami di WhatsApp
            </a>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-[#0B1120] pt-16 sm:pt-20 md:pt-24 pb-8 sm:pb-12 px-4 sm:px-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#5bc0de] via-[#1ba1e2] to-[#1a365d]"></div>
        
        <div class="max-w-7xl mx-auto grid md:grid-cols-12 gap-10 md:gap-16 border-b border-white/10 pb-12 sm:pb-16 relative z-10">
            <div class="md:col-span-5 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <div class="bg-white w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center shadow-lg overflow-hidden shrink-0">
                        <img src="{{ asset('storage/icon.png') }}" alt="Logo FITC" class="w-full h-full object-contain p-1.5" onerror="this.src='https://ui-avatars.com/api/?name=F&background=1a365d&color=fff'">
                    </div>
                    <span class="font-extrabold text-2xl sm:text-3xl text-white tracking-widest">FITC</span>
                </div>
                <p class="text-gray-400 mb-6 sm:mb-8 text-sm sm:text-md leading-relaxed max-w-sm mx-auto md:mx-0">
                    Lembaga Pelatihan Bidang Kesehatan Terakreditasi Madya (B) Kemenkes RI di bawah naungan PT. Flash Emergency Indonesia.
                </p>
            </div>

            <div class="md:col-span-7 grid sm:grid-cols-2 gap-8 sm:gap-10 text-center sm:text-left">
                <div>
                    <h4 class="text-white font-bold mb-6 sm:mb-8 uppercase tracking-widest text-xs sm:text-sm">Kontak Informasi</h4>
                    <ul class="space-y-4 sm:space-y-6 text-gray-400 text-sm sm:text-md">
                        <li class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4 hover:text-[#1ba1e2] transition-colors">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/5 flex items-center justify-center text-[#1ba1e2] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div> 
                            <span class="font-medium mt-1 sm:mt-2.5">+62 812 5225 9463</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6 sm:mb-8 uppercase tracking-widest text-xs sm:text-sm">Alamat Kantor</h4>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4 text-gray-400 text-sm sm:text-md hover:text-[#1ba1e2] transition-colors">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/5 flex items-center justify-center text-[#1ba1e2] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <p class="leading-relaxed font-medium mt-1">
                            Dusun Sobontoro RT 03 RW II<br>
                            Desa Watudandang, Kec. Prambon<br>
                            Kab. Nganjuk, Jawa Timur
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-6 sm:pt-8 flex flex-col justify-center items-center gap-2 text-xs sm:text-sm text-gray-500 font-medium relative z-10 text-center">
            <p>&copy; 2026 Flash Inspire Training Center.</p>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
        });
    </script>
</body>
</html>