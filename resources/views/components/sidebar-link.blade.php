@props(['href', 'active' => false])

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => 'block px-6 py-2 rounded hover:bg-green-700 transition-colors duration-150 ' .
                  ($active ? 'bg-green-800 font-bold' : 'font-medium')
   ]) }}>
    {{ $slot }}
</a>
