@extends('Layout.app')

@section('title', 'إنشاء حساب · سمر')
@section('content')
    <x-auth-shell title="أنشئ حسابك" subtitle="انضم إلى سمر وشارك الأبيات التي تحبها.">
        <form action="{{ route('signup') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="label">الاسم</label>
                <input id="name" name="name" type="text" dir="auto" value="{{ old('name') }}"
                    class="input peer user-invalid:border-red-500" required minlength="2" maxlength="50"
                    pattern="[A-Za-z\u0600-\u06FF ]+">
                <p class="mt-1 text-xs text-gray-500 peer-user-invalid:text-red-600">من 2 إلى 50 حرفًا، حروف فقط.</p>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

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

            <button type="submit" class="btn-primary w-full">إنشاء الحساب</button>

            <p class="text-center text-sm text-ink/70">
                لديك حساب؟
                <a href="{{ route('login') }}" class="font-semibold text-lagoon hover:underline">تسجيل الدخول</a>
            </p>
        </form>
    </x-auth-shell>
@endsection
