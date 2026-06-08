@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'dropdown-menu-start',
    default => 'dropdown-menu-end',
};

$menuStyle = match ($width) {
    '48' => 'min-width: 12rem;',
    default => '',
};
@endphp

<div class="dropdown d-inline-block">
    <div data-bs-toggle="dropdown" aria-expanded="false" role="button">
        {{ $trigger }}
    </div>

    <div class="dropdown-menu {{ $alignmentClasses }} {{ $contentClasses }}" style="{{ $menuStyle }}">
        {{ $content }}
    </div>
</div>
