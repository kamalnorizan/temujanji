@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$sizeClass = [
    'sm' => 'modal-sm',
    'md' => '',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl',
][$maxWidth] ?? 'modal-xl';

$modalClasses = $show ? 'modal fade show d-block' : 'modal fade';
@endphp

<div id="{{ $name }}" class="{{ $modalClasses }}" tabindex="-1" aria-hidden="{{ $show ? 'false' : 'true' }}" @if($show) aria-modal="true" role="dialog" @endif>
    <div class="modal-dialog {{ $sizeClass }} modal-dialog-centered">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>

@if ($show)
    <div class="modal-backdrop fade show"></div>
@endif
