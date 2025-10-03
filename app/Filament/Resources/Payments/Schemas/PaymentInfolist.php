<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('booking.id')
                    ->label('Booking')
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('subscriptionPlan.name')
                    ->label('Subscription plan')
                    ->placeholder('-'),
                TextEntry::make('payment_method'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('original_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('proration_credit')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('transaction_id')
                    ->placeholder('-'),
                TextEntry::make('payment_details')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('failure_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('external_id')
                    ->placeholder('-'),
                TextEntry::make('commission')
                    ->numeric(),
                TextEntry::make('processed_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
