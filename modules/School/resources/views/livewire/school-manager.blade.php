<div class="flex size-full flex-col items-center justify-center">
    <x-ui::card class="border-base-300 w-full max-w-prose rounded-xl border text-center shadow-lg" title="Data Sekolah"
        subtitle="Perbarui data sekolah.">
        <x-ui::form class="flex w-full flex-col gap-8" wire:submit="save">
            <div class="grid grid-cols-1 gap-4 text-start lg:grid-cols-2">
                <x-ui::input class="col-span-full" type="text" label="Nama Sekolah" placeholder="Nama Sekolah" required
                    autofocus wire:model="form.name" />
                <x-ui::textarea class="col-span-full" label="Alamat Sekolah" placeholder="Alamat Sekolah"
                    wire:model="form.address" />
                <x-ui::input class="col-span-full" type="email" label="Email Sekolah" placeholder="Email Sekolah"
                    wire:model="form.email" />
                <x-ui::input type="tel" label="Telepon Sekolah" placeholder="Telepon Sekolah" wire:model="form.phone" />
                <x-ui::input type="tel" label="Fax. Sekolah" placeholder="Fax. Sekolah" wire:model="form.fax" />
                <x-ui::input class="col-span-full" type="text" label="Nama Kepala Sekolah"
                    placeholder="Nama Kepala Sekolah" wire:model="form.principal_name" />
                <x-ui::file-upload class="col-span-full" label="Logo Sekolah"
                    hint="(Ukuran berkas maks. 2 MB dengan tipe JPG, PNG atau WEBP)" wire:model="form.logo_file"
                    :preview="$form->logo_url" />
            </div>

            <div class="flex flex-col items-center gap-8">
                <x-ui::button class="btn-primary w-full" label="Simpan" type="submit" />
            </div>
        </x-ui::form>
    </x-ui::card>
</div>
