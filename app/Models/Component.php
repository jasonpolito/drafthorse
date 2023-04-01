<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Component extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'blocks',
        'is_global',
        'position',
    ];

    protected $casts = [
        'blocks' => 'array'
    ];

    public function scopeGlobal(Builder $query): void
    {
        $query->where('is_global', true);
    }
}
