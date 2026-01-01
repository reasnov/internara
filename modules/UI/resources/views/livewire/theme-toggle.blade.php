<div x-data="{
    theme: localStorage.getItem('theme') || 'light',
    toggle() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.theme);
        document.documentElement.setAttribute('data-theme', this.theme);
    }
}">
    <label class="swap swap-rotate btn btn-ghost">
        <input type="checkbox" @click="toggle()" :checked="theme === 'dark'" />
        
        {{-- Sun icon --}}
        <x-ui::icon name="o-sun" class="swap-on h-5 w-5"/>
        
        {{-- Moon icon --}}
        <x-ui::icon name="o-moon" class="swap-off h-5 w-5"/>
    </label>
</div>
