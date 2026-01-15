<div>
    <div
        @class([
            'badge',
            $color,
            'badge-'.$size,
            'font-medium uppercase tracking-wider text-[10px]',
        ])
    >
        {{ str_replace('-', ' ', $roleName) }}
    </div>
</div>
