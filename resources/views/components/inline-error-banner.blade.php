@props(['message' => '', 'closeable' => true])

<div x-data="{ show: {{ $message ? 'true' : 'false' }} }" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
    class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded-r dark:bg-red-900/20 dark:border-red-600" role="alert"
    style="display: none;">

    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400 dark:text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
            </svg>
        </div>

        <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-red-800 dark:text-red-200"
                x-text="$el.getAttribute('data-message') || '{{ $message }}'">
                {{ $slot }}
            </p>
        </div>

        @if ($closeable)
            <div class="ml-auto pl-3">
                <button @click="show = false"
                    class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>
