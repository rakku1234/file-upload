<div>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">ファイルアップロード</h1>
        <form wire:submit="save">
            @php /** @var \App\Livewire\FileUploadForm $this */ @endphp
            {{ $this->form }}
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    アップロード
                </button>
            </div>
        </form>
    </div>
</div>
