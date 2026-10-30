@extends('Layout.app')

@section('title', 'تسجيل الدخول · سمر')
@section('content')
    <x-auth-shell title="أهلًا بعودتك" subtitle="سجّل الدخول لتشارك الأبيات وتعجب بما تحب.">
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="label">البريد الإلكتروني</label>
                <input id="email" name="email" type="email" dir="ltr" value="{{ old('email') }}"
                    class="input peer text-start user-invalid:border-red-500" required pattern="[^@\s]+@[^@\s]+\.[A-Za-z]{2,}">
                <p class="mt-1 text-xs text-gray-500 peer-user-invalid:text-red-600">بريد صحيح، مثل name@example.com</p>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="label">كلمة المرور</label>
                <input id="password" name="password" type="password" dir="ltr"
                    class="input peer text-start user-invalid:border-red-500" required minlength="8">
                <p class="mt-1 text-xs text-gray-500 peer-user-invalid:text-red-600">8 أحرف على الأقل.</p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full">تسجيل الدخول</button>

            <p class="text-center text-sm text-ink/70">
                جديد في سمر؟
                <a href="{{ route('signup') }}" class="font-semibold text-lagoon hover:underline">إنشاء حساب</a>
            </p>
        </form>
    </x-auth-shell>
@endsection
