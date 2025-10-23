<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo + Title -->
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('alarms.index') }}" class="text-lg font-bold text-gray-700">
                        ALARM DEPALLETISER
                    </a>
                </div>

               <!-- Desktop Navigation -->
                <div class="hidden sm:flex sm:space-x-8 sm:ms-10">
                    <button 
                        id="showTutorialBtn"
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 
                            text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                        Show Tutorial
                    </button>
                </div>

<!-- Tambahkan script untuk memanggil tutorial -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('showTutorialBtn');
    if (btn) {
        btn.addEventListener('click', function() {
            // Hapus flag localStorage supaya Shepherd muncul lagi
            localStorage.removeItem('app_seen_tour_v4');
            // Refresh halaman agar tour otomatis dimulai
            location.reload();
        });
    }
});
</script>

            </div>

            <!-- Desktop Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 relative">
                <button id="desktop-dropdown-btn"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none">
                    <div>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" d="M6 8l4 4 4-4" />
                    </svg>
                </button>

                <div id="desktop-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center sm:hidden">
                <button id="hamburger-btn"
                    class="p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <!-- Icon -->
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path id="hamburger-icon" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3">
            <x-nav-link href="showTutorialBtn" id="showTutorialBtn"
                class="block px-4 py-2">
                {{ __('Alarms') }}
            </x-nav-link>
        </div>

        <div class="border-t border-gray-200 bg-gray-50">
            <div class="px-4 py-3">
                <button id="mobile-dropdown-btn" class="font-medium text-base text-gray-800 w-full text-left">
                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                </button>
            </div>

            <div id="mobile-dropdown-menu" class="hidden border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-red-600 font-semibold hover:bg-red-50 focus:bg-red-50">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- JS untuk toggle menu -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const closeIcon = document.getElementById('close-icon');

    const mobileDropdownBtn = document.getElementById('mobile-dropdown-btn');
    const mobileDropdownMenu = document.getElementById('mobile-dropdown-menu');

    const desktopDropdownBtn = document.getElementById('desktop-dropdown-btn');
    const desktopDropdownMenu = document.getElementById('desktop-dropdown-menu');

    // Toggle mobile menu
    hamburgerBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        hamburgerIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });

    // Toggle mobile logout menu
    mobileDropdownBtn.addEventListener('click', () => {
        mobileDropdownMenu.classList.toggle('hidden');
    });

    // Toggle desktop dropdown
    desktopDropdownBtn.addEventListener('click', () => {
        desktopDropdownMenu.classList.toggle('hidden');
    });

    // Klik di luar menu untuk menutup dropdown desktop
    document.addEventListener('click', function(e) {
        if (!desktopDropdownBtn.contains(e.target) && !desktopDropdownMenu.contains(e.target)) {
            desktopDropdownMenu.classList.add('hidden');
        }
    });
});
</script>
