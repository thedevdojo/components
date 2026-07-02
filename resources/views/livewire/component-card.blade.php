<div class="self-start shrink-0">
    @if ($published)
        <div class="relative flex items-center gap-1" x-data="{ menu: false }">
            <span class="inline-flex items-center gap-1.5 rounded-md border border-green-500/20 bg-green-500/10 px-2.5 py-1.5 font-mono text-xs font-medium text-green-600 dark:text-green-400">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
                Added
            </span>

            <button type="button" @click="menu = ! menu" aria-label="More options"
                class="inline-flex h-[30px] w-7 items-center justify-center rounded-md border border-foreground/10 bg-background text-foreground/60 transition hover:bg-secondary hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.6" /><circle cx="12" cy="12" r="1.6" /><circle cx="19" cy="12" r="1.6" /></svg>
            </button>

            <div x-show="menu" x-cloak @click.away="menu = false"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute right-0 top-full z-50 mt-1.5 w-52 overflow-hidden rounded-lg border border-foreground/10 bg-popover p-1 text-popover-foreground shadow-lg dark:border-foreground/15">
                <button type="button" wire:click="reAdd" wire:target="reAdd" wire:loading.attr="disabled"
                    @click="menu = false"
                    class="flex w-full items-center gap-2 rounded-md px-2.5 py-1.5 text-left text-sm text-foreground/80 transition hover:bg-secondary hover:text-foreground">
                    <svg wire:loading.remove wire:target="reAdd" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" /><path d="M3 3v5h5" /></svg>
                    <svg wire:loading wire:target="reAdd" class="h-3.5 w-3.5 shrink-0 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    <span>Re-add <span class="text-foreground/45">(overwrite)</span></span>
                </button>
            </div>
        </div>
    @else
        <button type="button" wire:click="add" wire:target="add" wire:loading.attr="disabled"
            class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-md border border-foreground/10 bg-background px-2.5 py-1.5 font-mono text-xs text-foreground/70 transition hover:bg-secondary hover:text-foreground disabled:opacity-60">
            <svg wire:loading.remove wire:target="add" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5v14" /></svg>
            <svg wire:loading wire:target="add" class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
            <span>add {{ $name }}</span>
        </button>
    @endif
</div>
