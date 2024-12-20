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
                    ->label('Waktu Latensi')
                    ->state(function(Power $record) {
                        $createdAt = Carbon::parse($record->created_at);
                        $sentAt = Carbon::parse($record->sent_at);

                        $diff = $createdAt->diffForHumans($sentAt, CarbonInterface::DIFF_ABSOLUTE, true, 6);
                        
                        return $diff;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('strftime("%s", created_at) - strftime("%s", sent_at) ' . $direction);
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
            ]);
    }
}
