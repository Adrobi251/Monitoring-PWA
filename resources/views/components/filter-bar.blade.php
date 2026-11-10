@props(['method' => 'GET'])

<form method="{{ $method }}" {{ $attributes->merge(['class' => 'flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end']) }}>
    {{ $slot }}
</form>
