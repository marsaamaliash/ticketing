@props(['status'])

@php
    $colors = [
        'open' => 'gray',
        'forwarded' => 'blue',
        'assigned' => 'indigo',
        'in_progress' => 'yellow',
        'finished' => 'green',
        'verified' => 'emerald',
        'closed' => 'zinc',
        'reopened' => 'orange',
        'cancelled' => 'red',
    ];
    $labels = [
        'open' => 'Open',
        'forwarded' => 'Forwarded',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'finished' => 'Finished',
        'verified' => 'Verified',
        'closed' => 'Closed',
        'reopened' => 'Reopened',
        'cancelled' => 'Cancelled',
    ];
    $color = $colors[$status] ?? 'gray';
    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{$color}-100 text-{$color}-700"]) }}>
    {{ $label }}
</span>
