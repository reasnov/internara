<div>
    <x-mary-header title="{{ $user ? __('Edit User') : __('New User') }}" subtitle="{{ $user ? __('Update user details and roles.') : __('Create a new system user.') }}" separator progress-indicator />

    <x-mary-form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <x-mary-card shadow>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-mary-input label="{{ __('Full Name') }}" wire:model="name" icon="o-user" required />
                        <x-mary-input label="{{ __('Username') }}" wire:model="username" icon="o-at-symbol" required />
                        <x-mary-input label="{{ __('Email Address') }}" wire:model="email" icon="o-envelope" type="email" required />
                        <x-mary-input label="{{ __('Password') }}" wire:model="password" icon="o-key" type="password" 
                            hint="{{ $user ? __('Leave blank to keep current password.') : '' }}" />
                        
                        <x-mary-select
                            label="{{ __('Account Status') }}"
                            wire:model="status"
                            :options="[
                                ['id' => 'active', 'name' => __('Active')],
                                ['id' => 'pending', 'name' => __('Pending')],
                                ['id' => 'inactive', 'name' => __('Inactive')],
                            ]"
                            icon="o-swatches"
                            required
                        />

                        @if(in_array('teacher', $selectedRoles))
                            <x-mary-input label="{{ __('NIP') }}" wire:model="nip" icon="o-identification" hint="{{ __('Required for Teachers') }}" />
                        @endif

                        @if(in_array('student', $selectedRoles))
                            <x-mary-input label="{{ __('NISN') }}" wire:model="nisn" icon="o-academic-cap" hint="{{ __('Required for Students') }}" />
                        @endif
                    </div>

                    <div class="mt-6">
                        <x-mary-choices
                            label="{{ __('Assign Roles') }}"
                            wire:model="selectedRoles"
                            :options="$roles"
                            option-label="name"
                            option-value="name"
                            icon="o-shield-check"
                            hint="{{ __('Select one or more roles for this user.') }}"
                            compact
                        />
                    </div>
                </x-mary-card>
            </div>

            <div class="lg:col-span-1">
                <x-mary-card title="{{ __('Avatar') }}" shadow>
                    <x-mary-file wire:model="avatar" accept="image/*" crop-after-change>
                        <img src="{{ $avatar?->temporaryUrl() ?? $user?->avatar_url ?? '/avatar.png' }}" class="h-40 rounded-lg" />
                    </x-mary-file>
                    <div class="text-xs opacity-50 mt-2">
                        {{ __('Recommended: Square image, max 1MB.') }}
                    </div>
                </x-mary-card>
            </div>
        </div>

        <x-slot:actions>
            <x-mary-button label="{{ __('Cancel') }}" link="/users" class="btn-ghost" />
            <x-mary-button label="{{ __('Save User') }}" type="submit" icon="o-check" class="btn-primary" spinner="save" />
        </x-slot:actions>
    </x-mary-form>
</div>
