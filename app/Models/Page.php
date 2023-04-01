<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Page extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'seo',
        'taxonomy_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'seo' => 'array',
    ];

    public function getSeo()
    {
        return (object) $this->seo;
    }

    public function getData()
    {
        return (object) $this->data;
    }

    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class);
    }
}
