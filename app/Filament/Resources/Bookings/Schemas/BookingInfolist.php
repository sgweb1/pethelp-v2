<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('owner.name')
                    ->label('Owner'),
                TextEntry::make('sitter.name')
                    ->label('Sitter'),
                TextEntry::make('service.title')
                    ->label('Service'),
                TextEntry::make('pet.name')
                    ->label('Pet'),
                TextEntry::make('start_date')
                    ->dateTime(),
                TextEntry::make('end_date')
                    ->dateTime(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('total_price')
                    ->numeric(),
                TextEntry::make('special_instructions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancellation_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('confirmed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('cancelled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
