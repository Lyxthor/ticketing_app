<div class="drawer lg:drawer-open">
    <input id="drawer" type="checkbox" class="drawer-toggle" />

    {{-- CONTENT --}}
    <div class="drawer-content">
        {{-- Navbar --}}
        <nav class="navbar bg-base-300">
            <label for="drawer" class="btn btn-square btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     class="w-5 h-5"
                     fill="none"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </label>
        </nav>

        {{-- PAGE CONTENT --}}
        @yield('content')
    </div>

    {{-- SIDEBAR --}}
    <div class="drawer-side">
        <label for="drawer" class="drawer-overlay"></label>

        <aside class="menu p-4 w-64 bg-base-200 min-h-screen flex flex-col">

            {{-- Logo --}}
            <div class="mb-6 text-center">
                <img src="{{ asset('assets/images/logo_bengkod.svg') }}"
                     class="mx-auto"
                     alt="Logo">
            </div>

            {{-- Menu --}}
            <ul>
                <li class="{{ request()->routeIs('dashboard') ? 'bg-base-300 rounded' : '' }}">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>

                <li class="{{ request()->routeIs('kategori.*') ? 'bg-base-300 rounded' : '' }}">
                    <a href="{{ route('kategori.index') }}">Manajemen Kategori</a>
                </li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full text-left hover:bg-base-300 rounded px-4 py-2">
            Logout
        </button>
    </form>
        </aside>
    </div>
</div>
