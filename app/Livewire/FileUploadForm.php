<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Livewire\Component;
use App\Models\File;
use Coderflex\FilamentTurnstile\Forms\Components\Turnstile;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class FileUploadForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public string $uploadDirectory;

    public function mount(): void
    {
        $this->uploadDirectory = Str::random(32);
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('タイトル')
                    ->autocomplete(false)
                    ->required(),
                Textarea::make('description')
                    ->label('説明')
                    ->required()
                    ->autocomplete(false)
                    ->autosize(),
                FileUpload::make('file')
                    ->label('ファイル')
                    ->disk('local')
                    ->directory($this->uploadDirectory)
                    ->storeFileNamesIn('attachment_file_name')
                    ->required(),
                Turnstile::make('captcha')
                    ->theme('light')
                    ->size('normal')
                    ->required()
                    ->visible(config('turnstile.turnstile_enabled')),
            ])
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        $file = File::create([
            'uuid' => Str::uuid(),
            'title' => $data['title'],
            'description' => $data['description'],
            'directory' => $this->uploadDirectory,
            'file_name' => $data['attachment_file_name'],
        ]);

        Notification::make()
            ->icon('heroicon-o-arrow-up-tray')
            ->iconColor('success')
            ->title('ファイルがアップロードされました！')
            ->body('ダウンロードページに移動するには、以下のリンクをクリックしてください。')
            ->actions([
                Action::make('download')
                    ->label('ダウンロード')
                    ->url("/download?uuid={$file->uuid}")
                    ->openUrlInNewTab(),
            ])
            ->persistent()
            ->send();

        DiscordAlert::message('', [
            [
                'title' => $data['title'],
                'description' => $data['description'].'\nダウンロードページに移動するには、以下のリンクをクリックしてください。\n'.env('APP_URL')."/download?uuid={$file->uuid}",
                'color' => '#0ec9cc',
            ]
        ]);

        $this->uploadDirectory = Str::random(32);
        $this->form->fill();
        $this->dispatch('reset-captcha');
    }

    public function render()
    {
        return view('livewire.file-upload-form');
    }
}
