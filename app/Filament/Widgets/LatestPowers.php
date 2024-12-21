<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Power;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class LatestPowers extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Power::latest(),
            )
            ->columns([
                TextColumn::make("key_pressed")
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make("location")
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('arrived_at')
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
                        $arrivedAt = Carbon::parse($record->arrived_at);
                        $sentAt = Carbon::parse($record->sent_at);

                        $diff = $arrivedAt->diffInMilliseconds($sentAt);

                        return $diff;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(julianday(arrived_at) - julianday(sent_at)) * 24 * 60 * 60 * 1000 ' . $direction);
                    })
                    ->searchable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ]);
    }
}
