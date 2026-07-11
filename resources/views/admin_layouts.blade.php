<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    {{-- Tailwind + DaisyUI --}} 
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />

</head>
<body class="bg-base-100" data-theme="retro">

    {{-- Sidebar + Navbar + Content --}}
    @include('components.sidebar')

    {{-- Footer --}}
    <footer class="bg-base-200 text-center py-3">
        <p class="text-sm text-gray-600">
            © {{ date('Y') }} MyLaravelApp
        </p>
    </footer>

</body>
</html>
