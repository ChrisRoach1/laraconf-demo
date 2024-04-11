<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Filament\Resources\ConferenceResource\RelationManagers;
use Filament\Forms;
class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public static function getForm(): array
    {
        return
            [
                Forms\Components\Section::make('Conference Details')
                    ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->columnSpanFull()
                        ->label('Conference Name')
                        ->maxLength(60)
                        ->required(),
                    Forms\Components\MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_date')
                        ->native(false)
                        ->required(),

                    Forms\Components\Fieldset::make('Status')
                        ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required(),

                        Forms\Components\Toggle::make('is_published')
                            ->required(),
                    ])
                ]),

                Forms\Components\Section::make('Location')
                    ->columns(2)
                ->schema([
                    Forms\Components\Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class),
                    Forms\Components\Select::make('venue_id')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(Venue::getForm())
                        ->editOptionForm(Venue::getForm())
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get): Builder {
                            return $query->where('region', $get('region'));
                        }),
                ]),

                Actions::make([
                    Action::make('star')
                        ->visible(function (string $operation){
                            if ($operation != 'create') {
                                return false;
                            }

                            if(!app()->environment('local')){
                                return false;
                            }

                            return true;
                        })
                        ->label('Fill with factory data')
                        ->icon('heroicon-m-star')
                        ->action(function ($livewire) {
                            $data = Conference::factory()->make()->toArray();
                            unset($data['venue_id']);
                            $livewire->form->fill($data);
                        }),
                ]),
            ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }
}
