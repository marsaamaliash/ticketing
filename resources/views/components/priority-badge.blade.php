@props(['priority'])

@php
    $colors = [
        'low' => 'gray',
        'medium' => 'blue',
        'high' => 'orange',
        'urgent' => 'red',
    ];
    $color = $colors[$priority] ?? 'gray';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{$color}-100 text-{$color}-700"]) }}>
    {{ ucfirst($priority) }}
</span>
