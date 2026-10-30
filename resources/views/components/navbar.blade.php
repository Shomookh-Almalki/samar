@props(['page' => ''])

<header class="sticky top-0 z-50 border-b border-sand/80 bg-cream/80 backdrop-blur-md">
    <nav class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3" aria-label="التنقل الرئيسي">
        <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="سمر - الصفحة الرئيسية">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-linear-to-br from-lagoon to-lagoon-deep text-gold shadow-md shadow-lagoon/30">
                <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <rect x="5" y="5" width="14" height="14" />
                    <rect x="5" y="5" width="14" height="14" transform="rotate(45 12 12)" />
                    <circle cx="12" cy="12" r="2.2" fill="currentColor" stroke="none" />
                </svg>
            </span>
            <span class="leading-none">
                <span class="block font-brand text-3xl font-bold text-lagoon-deep">سمر</span>
                <span class="mt-1 block text-xs font-semibold tracking-[0.3em] text-gold-deep" dir="ltr">SAMAR</span>
            </span>
        </a>

        <ul class="ms-auto flex items-center gap-3">
            @if (($page ?? '') === 'Sign Up')
                <li><a href="{{ route('login') }}" class="btn-ghost">تسجيل الدخول</a></li>
            @elseif (($page ?? '') === 'Log In')
                <li><a href="{{ route('signup') }}" class="btn-primary">إنشاء حساب</a></li>
            @else
                @auth
                    <li class="hidden items-center gap-2 sm:flex">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-linear-to-br from-gold to-coral font-bold text-white shadow" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="font-semibold text-lagoon-deep">{{ auth()->user()->name }}</span>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-ghost">تسجيل الخروج</button>
                        </form>
                    </li>
                @endauth
                @guest
                    <li><a href="{{ route('login') }}" class="btn-ghost">تسجيل الدخول</a></li>
                    <li><a href="{{ route('signup') }}" class="btn-primary">إنشاء حساب</a></li>
                @endguest
            @endif
        </ul>
    </nav>
</header>
