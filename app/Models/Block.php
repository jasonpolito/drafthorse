<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Block extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'uuid',
        'data',
        'markup',
    ];
    protected $guarded = ['id'];

    protected $casts = [
        'blocks' => 'array',
        'data' => 'array',
    ];

    public const FIELD_TYPES = [
        'text' => [
            'Filament\Forms\Components\TextInput',
            'Filament\Forms\Components\Textarea',
            'Filament\Forms\Components\Select',
            'FilamentTiptapEditor\TiptapEditor',
        ],
        'image' => [
            'Awcodes\Curator\Components\Forms\CuratorPicker',
            'Filament\Forms\Components\FileUpload',
        ],
        'color' => [
            'Filament\Forms\Components\ColorPicker'
        ],
    ];


    public static function boot()
    {
        parent::boot();
        self::saved(
            function (Model $model) {
                Log::info($model->getOriginal());
                Log::info($model);
            }
        );
    }

    public function getFieldOptions($type = false): array
    {
        $options = [];
        if ($this->fields) {
            foreach ($this->fields as $field) {
                $snakeName = Str::snake($field['name']);
                if ($type) {
                    if (in_array($field['type'], self::FIELD_TYPES[$type])) {
                        $options["{{ $snakeName }}"] = $field['name'];
                    }
                } else {
                    $options["{{ $snakeName }}"] = $field['name'];
                }
            }
        }
        return $options;
    }

    public function cleanseContent($content): string
    {
        $content = Str::replace('<p><h', '<h', $content);
        $content = Str::replace('1></p>', '1>', $content);
        $content = Str::replace('2></p>', '2>', $content);
        $content = Str::replace('3></p>', '3>', $content);
        $content = Str::replace('4></p>', '4>', $content);
        $content = Str::replace('5></p>', '5>', $content);
        $content = Str::replace('<p><p', '<p', $content);
        $content = Str::replace('</p></p>', '</p>', $content);
        $content = Str::replace('<p><div', '<div', $content);
        $content = Str::replace('</div></p> ', '</div>', $content);
        return $content;
    }
}
