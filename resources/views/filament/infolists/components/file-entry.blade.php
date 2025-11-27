<div class="space-y-2">
    @foreach ($entry->getFiles() as $file)
        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
            <x-heroicon-o-document class="w-5 h-5 text-gray-500"/>
            <a href="{{ $file->getUrl() }}" target="_blank" class="text-sm text-primary-600 hover:underline">
                {{ $file->file_name }}
            </a>
        </div>
    @endforeach
</div>
