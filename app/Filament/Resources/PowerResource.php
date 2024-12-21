<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PowerResource\Pages;
use App\Filament\Resources\PowerResource\RelationManagers;
use App\Models\Power;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class PowerResource extends Resource
{
    protected static ?string $model = Power::class;

    protected static ?string $navigationIcon = 'heroicon-o-power';

    protected static ?string $navigationGroup = 'Latency';

    protected static ?string $modelLabel = 'Latency';

    protected static ?string $pluralModelLabel = 'Latency';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Latency';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("key_pressed")
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make("location")
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make("sent_at")
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('diff_readable')
                    ->label('Waktu Latensi (Milidetik)')
                    ->state(function(Power $record) {
                        $createdAt = Carbon::parse($record->created_at);
                        $sentAt = Carbon::parse($record->sent_at);

                        $diff = $sentAt->diffInMilliseconds($createdAt);

                        return $diff;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('strftime("%f", created_at) - strftime("%f", sent_at) ' . $direction);
                    })
                    ->searchable()
                    ->badge(),
                TextColumn::make('created_by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPowers::route('/'),
            // 'create' => Pages\CreatePower::route('/create'),
            // 'edit' => Pages\EditPower::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string {
        return static::getModel()::count() > 88 ? 'primary' : 'warning';
    }

    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
