<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('booking_id')
                    ->relationship('booking', 'id'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('subscription_plan_id')
                    ->relationship('subscriptionPlan', 'name'),
                TextInput::make('payment_method')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('original_amount')
                    ->numeric(),
                TextInput::make('proration_credit')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('transaction_id'),
                Textarea::make('payment_details')
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
                Textarea::make('failure_reason')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
                TextInput::make('external_id'),
                TextInput::make('gateway_response'),
                TextInput::make('commission')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                DateTimePicker::make('processed_at'),
            ]);
    }
}
