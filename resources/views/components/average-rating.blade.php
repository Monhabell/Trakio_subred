@php
    $stats = Illuminate\Support\Facades\Cache::remember('site_rating_stats', 60, function () {
        return [
            'average' => \App\Models\SiteRating::avg('rating') ?? 0,
            'count'   => \App\Models\SiteRating::count()
        ];
    });
    $rating = number_format($stats['average'], 1);
    $fullStars = round($stats['average']);
@endphp

<div {{ $attributes->merge(['class' => 'p-2  d-flex align-items-center justify-content-between']) }}>
    <div class="d-flex align-items-center border-end pe-3 me-3">
        <span class="fw-bold fs-3 text-dark">{{ $rating }}</span>
        <span class="text-muted ms-1" style="font-size: 0.7rem; margin-top: 5px;">/5</span>
    </div>

    <div class="flex-grow-1 text-start">
        <div class="text-warning lh-1 mb-1" style="letter-spacing: -2px;">
            @for ($i = 1; $i <= 5; $i++)
                <span>{{ $i <= $fullStars ? '★' : '☆' }}</span>
            @endfor
        </div>
        <div class="text-muted lh-1" style="font-size: 0.75rem;">
            {{ $stats['count'] }} opiniones
        </div>
    </div>
</div>