<div class="container mx-auto flex flex-col items-center justify-center gap-12 text-center">
    <!-- Main Headline -->
    <div class="max-w-prose">
        <h1 class="text-3xl font-bold">Akhiri Kerumitan, Sambut Program Magang yang Bermakna.</h1>
    </div>

    <!-- 3-Column Feature Grid -->
    <div class="grid grid-cols-1 gap-10 text-center md:grid-cols-3 md:gap-8">
        <!-- Column 1: The Problem -->
        <div class="flex flex-col items-center">
            <div class="mb-4 text-4xl">🧩</div>
            <h3 class="text-lg font-semibold">Memahami Kerumitan Anda</h3>
            <p class="mt-2 text-sm text-base-content/70">
                Kami tahu mengelola program magang terasa seperti menyatukan ribuan keping puzzle
                yang rumit.
            </p>
        </div>

        <!-- Column 2: The Solution -->
        <div class="flex flex-col items-center">
            <div class="mb-4 text-4xl">🎓</div>
            <h3 class="text-lg font-semibold">Solusi Cerdas Kami</h3>
            <p class="mt-2 text-sm text-base-content/70">
                Internara hadir sebagai partner Anda, menata setiap detail agar Anda bisa fokus
                membimbing masa depan siswa.
            </p>
        </div>

        <!-- Column 3: The Journey -->
        <div class="flex flex-col items-center">
            <div class="mb-4 text-4xl">🚀</div>
            <h3 class="text-lg font-semibold">Perjalanan Dimulai</h3>
            <p class="mt-2 text-sm text-base-content/70">
                Proses setup ini adalah langkah pertama Anda menuju program magang yang lebih
                terorganisir dan berdampak.
            </p>
        </div>
    </div>

    <!-- Call to Action Button -->
    <div>
        <x-ui::button class="btn-primary" label="Mulai Instalasi" wire:click="nextStep" />
    </div>
</div>
