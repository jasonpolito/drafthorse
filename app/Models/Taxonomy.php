<?php

namespace App\Models;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxonomy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array'
    ];

    public function getFieldInputs(): array
    {
        $fields = [];
        foreach ($this->fields as $field) {
            array_push($fields, TextInput::make($field['title']));
        }
        return $fields;
    }
}
