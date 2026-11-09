@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border focus:border-primary focus:ring-primary rounded-md shadow-sm']) }}>
