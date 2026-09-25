<div x-data="toastHost()"
     @toast.window="push($event.detail)"
     x-cloak
     class="fixed top-4 right-4 z-[200] flex flex-col gap-2 w-[22rem] max-w-[calc(100vw-2rem)] pointer-events-none">

    <template x-for="t in items" :key="t.id">
        <div x-data="{ shown: false }"
             x-init="$nextTick(() => shown = true)"
             x-show="shown"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-6"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-6"
             :class="{
                 'border-emerald-500/40': t.type === 'success',
                 'border-rose-500/40':    t.type === 'error',
                 'border-amber-500/40':   t.type === 'warning',
                 'border-sky-500/40':     t.type === 'info',
             }"
             class="pointer-events-auto rounded-xl border bg-gray-900/95 shadow-2xl shadow-black/60 backdrop-blur-md p-3.5 flex items-start gap-3"
             role="alert" aria-live="assertive" aria-atomic="true">

            {{-- Icon --}}
            <span class="flex-shrink-0 mt-0.5 w-5 h-5"
                  :class="{
                      'text-emerald-400': t.type === 'success',
                      'text-rose-400':    t.type === 'error',
                      'text-amber-400':   t.type === 'warning',
                      'text-sky-400':     t.type === 'info',
                  }">
                <template x-if="t.type === 'success'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="t.type === 'error'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="t.type === 'warning'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 5a2 2 0 00-3.48 0L3.34 16a2 2 0 001.73 3z"/></svg>
                </template>
                <template x-if="t.type === 'info'">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </span>

            {{-- Body --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white leading-tight" x-text="t.title"></p>
                <p x-show="t.message" class="text-xs text-gray-400 mt-1 leading-snug" x-text="t.message"></p>
            </div>

            {{-- Close --}}
            <button type="button" @click="dismiss(t.id)"
                    class="flex-shrink-0 -mt-1 -mr-1 p-1 rounded-md text-gray-500 hover:text-white hover:bg-gray-800/60 transition-colors"
                    aria-label="Dismiss">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

{{-- Global toast helper — call from anywhere --}}
<script>
    window.toast = {
        show(detail) { window.dispatchEvent(new CustomEvent('toast', { detail })); },
        success(title, message, timeout = 4000) { this.show({ type: 'success', title, message, timeout }); },
        error  (title, message, timeout = 5000) { this.show({ type: 'error',   title, message, timeout }); },
        warning(title, message, timeout = 4500) { this.show({ type: 'warning', title, message, timeout }); },
        info   (title, message, timeout = 4000) { this.show({ type: 'info',    title, message, timeout }); },
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('toastHost', () => ({
            items: [],
            push(detail) {
                const id = Date.now() + Math.random();
                const toast = {
                    id,
                    type:    detail.type    || 'info',
                    title:   detail.title   || 'Notice',
                    message: detail.message || '',
                    timeout: detail.timeout ?? 4000,
                };
                this.items.push(toast);
                if (toast.timeout > 0) setTimeout(() => this.dismiss(id), toast.timeout);
            },
            dismiss(id) { this.items = this.items.filter(t => t.id !== id); },
        }));
    });
</script>

{{-- Auto-bridge Laravel session flash → toast --}}
@foreach (['success' => 'success', 'error' => 'error', 'warning' => 'warning', 'status' => 'info'] as $key => $type)
    @if(session($key))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.toast.{{ $type }}(@json(ucfirst($key)), @json(session($key)));
            });
        </script>
    @endif
@endforeach