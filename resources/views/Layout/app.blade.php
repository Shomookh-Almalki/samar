<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'سمر · أبيات شعرية')</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%E2%9C%A6%3C/text%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans text-ink antialiased">
    <x-navbar :page="$page ?? ''" />

    <main>
        @yield('content')
    </main>

    <footer class="mx-auto max-w-6xl px-4 py-10 text-center text-sm text-ink/60">
        <p class="font-brand text-2xl text-lagoon-deep">سمر</p>
        <p class="font-verse text-xl text-gold-deep">بين بيت وبيت يحلو السمر</p>
        <p class="mt-1">شعر عربي يتشاركه محبّوه.</p>
    </footer>
</body>

</html>
