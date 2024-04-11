<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Filament\Resources\SpeakerResource\RelationManagers;
use Filament\Forms;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'qualifications'=> 'array'
    ];

    public static function getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->maxLength(255)
                ->required(),
            Forms\Components\FileUpload::make('avatar')
                ->maxSize(1024 * 1024 * 10)
                ->avatar()
                ->imageEditor(),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required(),
            Forms\Components\Textarea::make('bio')
                ->maxLength(65535)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('twitter_handle'),
            CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
            ->options([
                'business=leader' => 'Business Leader',
                'charisma' => 'Charismatic Speaker',
                'first-time' => 'First Time Speaker',
                'hometown-hero' => 'Hometown Hero',
                'humanitarian' => 'Works in Humanitarian Field',
                'laracasts-contributors' => 'Laracasts Contributor',
                'twitter-influencers' => 'Large Twitter Following',
                'youtube-influencers' => 'Large Youtube Following',
                'open-source' => 'Open Source Creator / Maintainer',
                'unique-perspective' => 'Unique Perspective',
            ]
            )->columns(3)

        ];
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany{
        return $this->hasMany(Talk::class);
    }
}
