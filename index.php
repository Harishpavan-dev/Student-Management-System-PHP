<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SLIATE ATI Jaffna | Department of IT</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class'
    }
  </script>

  <link rel="icon" href="assets/ati-logo.png">
</head>

<body class="bg-slate-50 text-slate-800 dark:bg-slate-900 dark:text-slate-200">

  <!-- ================= NAVBAR ================= -->
 <header
  class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur border-b border-slate-200 dark:border-slate-700">

  <div
    class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <!-- LEFT: Logos + Title -->
    <div class="flex items-center gap-3">
      <!-- SLIATE Logo -->
      <img src="assets/sliate-logo.png" class="h-10 sm:h-12" alt="SLIATE Logo">

      <!-- Divider (hidden on mobile) -->
      <div class="hidden sm:block h-10 w-px bg-slate-300"></div>

      <!-- ATI Jaffna -->
      <div class="flex items-center gap-2">
        <img src="assets/ati-logo.png" class="h-9 w-9 sm:h-10 sm:w-10 rounded-full"
          alt="ATI Jaffna Logo">
        <div>
          <h1 class="text-base sm:text-lg font-bold leading-tight">
           SLIATE ATI Jaffna
          </h1>
          <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">
            Department of Information Technology
          </p>
        </div>
      </div>
    </div>

    <!-- RIGHT: Actions -->
    <div class="flex items-center gap-3 sm:justify-end">
      <button id="themeToggle"
        class="px-3 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800">
        🌙
      </button>

      <a href="Signup/Student_signup.php"
        class="px-4 py-2 rounded-lg bg-yellow-500 text-black font-semibold hover:bg-yellow-400 transition whitespace-nowrap">
        Student Registration
      </a>
    </div>

  </div>
</header>


  <!-- ================= HERO ================= -->
  <section class="max-w-7xl mx-auto px-6 py-32 grid md:grid-cols-2 gap-16 items-center">

  <!-- LEFT CONTENT -->
  <div>
    <h2 class="text-5xl md:text-6xl font-extrabold leading-tight mb-8 text-slate-900 dark:text-white">
      Advanced Technological Institute<br>
      <span class="text-yellow-500">Jaffna</span>
    </h2>

    <p class="text-xl md:text-2xl text-slate-700 dark:text-slate-300 mb-10">
      Empowering students with industry-ready IT skills through practical learning
      and academic excellence.
    </p>

    <div class="flex flex-wrap gap-6">
      <a href="Login/Student_login.php"
        class="px-8 py-4 rounded-lg bg-yellow-500 text-black font-bold text-lg hover:bg-yellow-400 transition">
        Student Login
      </a>

      <a href="Rep_login/index.php"
        class="px-8 py-4 rounded-lg border-2 border-slate-300 dark:border-slate-600 font-bold text-lg
               hover:bg-slate-100 dark:hover:bg-slate-800 transition">
        REP Login
      </a>

      <a href="Login/hod_login.php"
        class="px-8 py-4 rounded-lg border-2 border-slate-300 dark:border-slate-600 font-bold text-lg
               hover:bg-slate-100 dark:hover:bg-slate-800 transition">
        HOD Login
      </a>
    </div>
  </div>

  <!-- RIGHT LOGO SECTION -->
  <div class="relative flex justify-center">

    <!-- Bigger Glow -->
    <div class="absolute w-96 h-96 bg-yellow-400/25 rounded-full blur-4xl"></div>

    <!-- Logo Card -->
    <div class="relative flex items-center gap-10 bg-white dark:bg-slate-900
                rounded-3xl p-10 shadow-2xl">

      <!-- ATI Logo -->
      <!-- ATI Logo -->
<div class="text-center transform transition duration-500 hover:scale-110 hover:shadow-2xl">
  <img src="assets/ati-logo.png"
       alt="ATI Logo"
       class="w-40 h-40 mx-auto object-contain">
  <p class="mt-3 text-lg font-bold text-slate-800 dark:text-slate-200">
    ATI Jaffna
  </p>
</div>

<!-- Divider -->
<div class="w-px h-28 bg-slate-300 dark:bg-slate-600"></div>

<!-- SLIATE Logo -->
<div class="text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
  <img src="assets/sliate-logo.png"
       alt="SLIATE Logo"
       class="w-28 h-28 mx-auto object-contain">
  <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
    Under SLIATE
  </p>
</div>


    </div>
  </div>

</section>


  <!-- ================= ABOUT ================= -->
  <section class="bg-white dark:bg-slate-800">
    <div class="max-w-6xl mx-auto px-6 py-20 text-center">
      <h3 class="text-3xl font-bold mb-6 text-yellow-500">About SLIATE</h3>
      <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed max-w-4xl mx-auto">
        The Sri Lanka Institute of Advanced Technological Education (SLIATE) was established in 1995 under the SLIATE Act No. 29 of 1995, following the recommendations of a committee appointed by Prof. Wiswa Waranapala, Deputy Minister of Higher Education. In 2001, the institute was renamed as the Sri Lanka Institute of Advanced Technological Education (SLIATE).

SLIATE functions as an autonomous statutory body under the Ministry of Higher Education, responsible for the management of Higher National and National Diploma programmes in Sri Lanka.

The primary objective of SLIATE is to reform and strengthen technical and vocational education to meet the changing needs of national economic development. It focuses on producing skilled technological manpower at technician level, supporting national development strategies and promoting privatization.

SLIATE is mandated to establish Advanced Technological Institutes (ATIs) across all provinces. Currently, it manages 11 ATIs and 7 ATI Sections island-wide. The institute is headed by a Director General, while each ATI and ATI Section is led by a Director and an Academic Coordinator respectively.
      </p>
    </div>
  </section>

  <!-- ================= MISSION & VISION ================= -->
  <section class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-10">

    <div class="bg-white dark:bg-slate-800 p-10 rounded-2xl shadow-lg">
      <h4 class="text-2xl font-bold mb-4 text-yellow-500">Mission</h4>
      <p class="text-slate-600 dark:text-slate-400">
        Creating excellent Higher National and National diplomates with modern
        technology for sustainable development.
      </p>
    </div>

    <div class="bg-white dark:bg-slate-800 p-10 rounded-2xl shadow-lg">
      <h4 class="text-2xl font-bold mb-4 text-yellow-500">Vision</h4>
      <p class="text-slate-600 dark:text-slate-400">
        To become a centre of excellence in technological education.
      </p>
    </div>

  </section>

  <!-- ================= FOOTER ================= -->
  <footer class="bg-slate-900 text-slate-300">
    <div class="max-w-7xl mx-auto px-6 py-12 grid md:grid-cols-3 gap-10">

      <div>
        <h5 class="text-lg font-semibold text-white mb-4">ATI Jaffna</h5>
        <p class="text-sm text-slate-400">
          Department of Information Technology<br>
          HNDIT Program
        </p>
      </div>

      <div>
        <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
        <ul class="space-y-2 text-sm">
          <li><a href="Login/Student_login.php" class="hover:text-yellow-400">Student Login</a></li>
          <li><a href="Rep_login/index.php" class="hover:text-yellow-400">REP Login</a></li>

          <li><a href="Login/hod_login.php" class="hover:text-yellow-400">HOD Login</a></li>
        </ul>
      </div>

      <div>
        <h5 class="text-lg font-semibold text-white mb-4">Developer</h5>
        <p class="text-sm text-slate-400">
          Developed by<br>
          <a href="https://harishpavan-dev.vercel.app" target="_blank"
            class="text-yellow-400 font-semibold hover:underline">
            Bavananthan Harishpavan
          </a>
        </p>
      </div>

    </div>

    <div class="text-center text-xs text-slate-500 border-t border-slate-700 py-4">
      © 2025 ATI Jaffna. All rights reserved.
    </div>
  </footer>

  <!-- ================= DARK MODE SCRIPT ================= -->
  <script>
    const toggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    if (localStorage.getItem('theme') === 'dark') {
      html.classList.add('dark');
    }

    toggle.addEventListener('click', () => {
      html.classList.toggle('dark');
      localStorage.setItem(
        'theme',
        html.classList.contains('dark') ? 'dark' : 'light'
      );
    });
  </script>

</body>

</html>