<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Filament\Forms\Form;
use Filament\Forms\Components\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Livewire\Component;
use App\Models\File;

class FileDownloadForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $uuid;
    public ?File $file = null;
    public ?array $data = [];
    public $fileName;

    public function mount(): void
    {
        $this->uuid = request()->query('uuid');
        if ($this->uuid) {
            $this->file = File::where('uuid', $this->uuid)->first();
            if (!$this->file) {
                abort(404);
                return;
            }
            $directory = $this->file->directory;
            $files = Storage::disk('local')->files($directory);
            if (!empty($files)) {
                $this->fileName = basename($files[0]);
            }
            $this->form->fill([
                'title' => $this->file->title,
                'description' => $this->file->description,
            ]);
        } else {
            abort(404);
            return;
        }
    }

    public function form(Form $form): Form
    {
        if (!$this->file) {
            return $form->schema([]);
        }
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('タイトル')
                    ->readOnly(),
                Textarea::make('description')
                    ->label('説明')
                    ->autosize()
                    ->readOnly(),
                Actions::make([
                    Action::make('download')
                        ->label("{$this->file->file_name}をダウンロード")
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(route('file.download', ['uuid' => $this->uuid]))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->statePath('data');
    }

    public function render()
    {
        return view('livewire.file-download-form');
    }
}
