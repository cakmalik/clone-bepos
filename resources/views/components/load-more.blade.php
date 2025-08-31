@props(['data' => null])

@if ($data->hasMorePages())
    <div x-data x-intersect="$wire.loadMore">
        Loading...
    </div>
@endif
