<?php

namespace App\Filament\Resources\BlogPostResource\RelationManagers;

use App\Models\BlogComment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author_name'),

                Tables\Columns\TextColumn::make('body')
                    ->limit(60),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (BlogComment $record): bool => ! $record->is_approved)
                    ->action(fn (BlogComment $record) => $record->update(['is_approved' => true])),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (BlogComment $record): bool => $record->is_approved)
                    ->action(fn (BlogComment $record) => $record->update(['is_approved' => false])),

                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
