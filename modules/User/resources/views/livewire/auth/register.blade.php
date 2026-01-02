<x-ui::card class="border-base-300 w-full max-w-lg rounded-xl border text-center shadow-lg" title="Daftar Akun Siswa"
    subtitle="Buat akun baru untuk siswa.">
    <x-ui::form class="text-start">
        <x-ui::input type="text" label="Nama" placeholder="Nama lengkap" required />
        <x-ui::input type="email" label="Email" placeholder="Alamat email" required />
        <x-ui::input type="password" label="Kata sandi" placeholder="Kata sandi" required />
        <x-ui::input type="password" label="Konfirmasi kata sandi" placeholder="Konfirmasi kata sandi" required />
        @php
            $policyRoute = \Illuminate\Support\Facades\Route::has('privacy-policy') ? route('privacy-policy') : '#';
        @endphp

        <div class="mt-4 flex flex-col gap-8">
            <div class="w-full space-y-2">
                <x-ui::button class="btn-primary w-full" label="Daftar" />

                <p class="text-center text-xs">Dengan menekan tombol <b>Daftar</b>, pengguna otomatis menyetujui <a
                        class="underline" href="{{ $policyRoute }}">Kebijakan Privasi.</a>
                </p>
            </div>

            @if (\Illuminate\Support\Facades\Route::has('login'))
                <p class="text-center text-sm">
                    Sudah punya akun? <a class="font-medium underline" href="{{ route('login') }}"
                        wire:navigate>Login</a>
                </p>
            @endif
        </div>
    </x-ui::form>
</x-ui::card>
