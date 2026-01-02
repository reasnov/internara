<x-ui::card class="border-base-300 w-full max-w-lg rounded-xl border text-center shadow-lg" title="Masuk ke Dasbor"
    subtitle="Masuk menggunakan email atau username.">
    <div class="flex flex-col gap-8">
        <x-ui::form class="text-start">
            <x-ui::input type="text" label="Email/username" placeholder="Email atau username" required />

            <div class="relative w-full">
                <x-ui::input type="password" label="Kata sandi" placeholder="Kata sandi" required />
                <a class="absolute right-0 top-2 text-xs font-medium underline" href="#">Lupa
                    kata sandi?</a>
                @if (\Illuminate\Support\Facades\Route::has('forgot-password'))
                @endif
            </div>

            <x-ui::checkbox label="Ingat saya" />

            <div class="mt-4 flex w-full flex-col gap-8">
                <x-ui::button class="btn-primary w-full" label="Masuk" />

                @if (\Illuminate\Support\Facades\Route::has('register'))
                    <p class="text-center text-sm">
                        Belum punya akun? <a class="font-medium underline" href="{{ route('register') }}">Daftar</a>
                    </p>
                @endif
            </div>
        </x-ui::form>
    </div>
</x-ui::card>
