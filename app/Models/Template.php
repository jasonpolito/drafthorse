<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'blocks' => 'array',
    ];

    public const FIELD_TYPES = [
        'text' => [
            'Filament\Forms\Components\TextInput',
            'Filament\Forms\Components\Textarea',
            'Filament\Forms\Components\Select',
            'FilamentTiptapEditor\TiptapEditor',
        ],
        'image' => [
            'Filament\Forms\Components\FileUpload'
        ],
        'color' => [
            'Filament\Forms\Components\ColorPicker'
        ],
    ];

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

    public function replaceTokens(Page $page): array
    {
        $jsonString = json_encode($this->blocks);
        foreach ($page->taxonomy->fields as $field) {
            $token = Str::snake($field['name']);
            $content = $page->data[$token] ?? false;
            if (Str::contains('<script', $content)) {
                $replace = addcslashes($content, '"');
            } else {
                $replace = addcslashes(preg_replace('/\v+|\\\r\\\n/Ui', '<br/>', $content), '"');
            }
            if ($content) {
                $jsonString = Str::replace("{{ $token }}", $replace, $jsonString);
            }
        }

        $cleansed = $this->cleanseContent($jsonString);

        $blocks = (array) json_decode($cleansed, true);
        return $blocks;
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
