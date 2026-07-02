@props(['prop'])

@php
    $name = $prop['name'];
    $type = $prop['type'] ?? 'text';
    $default = $prop['default'] ?? '';
@endphp

<div class="grid grid-cols-1 items-center gap-2 border-b border-foreground/10 px-4 py-3 last:border-0 sm:grid-cols-[10rem_1fr_14rem]">
    <div class="flex items-center gap-2">
        <code class="rounded-small bg-secondary px-1.5 py-0.5 font-mono text-[12px] text-foreground/80">{{ $name }}</code>
        <span class="font-mono text-[10px] text-foreground/35">{{ $type }}</span>
    </div>
    <p class="text-xs text-foreground/50">{{ $prop['description'] ?? '' }}</p>
    <div>
        @if ($type === 'select')
            <select x-model="attrs['{{ $name }}']" @change="update()"
                class="w-full rounded-medium border border-input bg-card px-2.5 py-1.5 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                @foreach ($prop['options'] ?? [] as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        @elseif ($type === 'boolean')
            <button type="button" role="switch" x-bind:aria-checked="!!attrs['{{ $name }}']"
                data-knob="{{ $name }}"
                @click="attrs['{{ $name }}'] = ! attrs['{{ $name }}']; update()"
                :class="attrs['{{ $name }}'] ? 'bg-primary' : 'bg-secondary'"
                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full border border-foreground/10 transition-colors">
                <span :class="attrs['{{ $name }}'] ? 'translate-x-5' : 'translate-x-0.5'"
                    class="pointer-events-none block h-5 w-5 rounded-full bg-white shadow-sm transition-transform"></span>
            </button>
        @elseif ($type === 'textarea' || $type === 'array')
            <textarea x-model="attrs['{{ $name }}']" @input.debounce.300ms="update()" rows="2"
                placeholder="{{ is_array($default) ? json_encode($default) : $default }}"
                class="w-full rounded-medium border border-input bg-card px-2.5 py-1.5 font-mono text-xs text-foreground focus:outline-none focus:ring-2 focus:ring-ring"></textarea>
        @elseif ($type === 'number' || $type === 'integer')
            <input type="number" x-model="attrs['{{ $name }}']" @input.debounce.300ms="update()"
                class="w-full rounded-medium border border-input bg-card px-2.5 py-1.5 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring" />
        @else
            <input type="text" x-model="attrs['{{ $name }}']" @input.debounce.300ms="update()"
                placeholder="{{ is_array($default) ? json_encode($default) : $default }}"
                class="w-full rounded-medium border border-input bg-card px-2.5 py-1.5 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring" />
        @endif
    </div>
</div>
