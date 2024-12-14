<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeployHistoryResource\RelationManagers\DeployHistoriesRelationManager;
use App\Filament\Resources\WebhookResource\Pages;
use App\Filament\Resources\WebhookResource\RelationManagers;
use App\Models\Webhook;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $token = Str::random(32);
        return $form
            ->schema([
                Forms\Components\TextInput::make('application_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('container_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('image_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('token')
                    ->required()
                    ->readOnly()
                    ->default(Str::random(32))
                    ->helperText('This used for trigger webhook ex. '.env('APP_URL').'/api/depploy/webhook/{token}')
                    ->maxLength(255)
                    ->suffixAction(
                        Action::make('copy')
                            ->icon('heroicon-s-clipboard')
                            ->action(function ($livewire, $state) {
                                $livewire->dispatch('copy-to-clipboard', text: $state);
                            })
                    )
                    ->extraAttributes([
                        'x-data' => '{
                            copyToClipboard(text) {
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(text).then(() => {
                                        $tooltip("Copied to clipboard", { timeout: 1500 });
                                    }).catch(() => {
                                        $tooltip("Failed to copy", { timeout: 1500 });
                                    });
                                } else {
                                    const textArea = document.createElement("textarea");
                                    textArea.value = text;
                                    textArea.style.position = "fixed";
                                    textArea.style.opacity = "0";
                                    document.body.appendChild(textArea);
                                    textArea.select();
                                    try {
                                        document.execCommand("copy");
                                        $tooltip("Copied to clipboard", { timeout: 1500 });
                                    } catch (err) {
                                        $tooltip("Failed to copy", { timeout: 1500 });
                                    }
                                    document.body.removeChild(textArea);
                                }
                            }
                        }',
                        'x-on:copy-to-clipboard.window' => 'copyToClipboard($event.detail.text)',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('container_name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->recordUrl(fn (Webhook $record) => Pages\ViewWebhook::getUrl([$record->id]));
    }

    public static function getRelations(): array
    {
        return [
            DeployHistoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhooks::route('/'),
            'create' => Pages\CreateWebhook::route('/create'),
            'edit' => Pages\EditWebhook::route('/{record}/edit'),
            'view' => Pages\ViewWebhook::route('/{record}'),
        ];
    }
}
