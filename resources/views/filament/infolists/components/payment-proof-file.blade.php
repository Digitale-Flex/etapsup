<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $media = $getRecord()->getFirstMedia('proof');
    @endphp

    <div class="flex items-center justify-content-end space-x-2">
        @if ($media)
            @if (Str::startsWith($media->mime_type, 'image/'))
                <a href="{{ $media->getUrl() }}" target="_blank" class="block flex-shrink-0">
                    <img src="{{ $media->getUrl() }}" alt="{{ $media->file_name }}" class="w-10 h-10 object-cover rounded-full" />
                </a>
            @else
                <a href="{{ $media->getUrl() }}" target="_blank" class="text-primary-600 hover:text-primary-500 flex-shrink-0">
                    @svg('heroicon-s-document-arrow-down', 'w-8 h-8')
                </a>
            @endif
            <a href="{{ $media->getUrl() }}" target="_blank" class="flex flex-col overflow-hidden">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $media->file_name }}">
                    {{ $media->file_name }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $media->human_readable_size }}
                </span>
            </a>
        @else
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Aucun fichier
            </span>
        @endif
    </div>
</x-dynamic-component>
