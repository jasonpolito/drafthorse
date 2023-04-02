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
        'parent_id',
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

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function parent()
    {
        return $this->belongsTo(Page::class);
    }

    public function children()
    {
        return $this->hasMany(Page::class);
    }

    public function getBlocks()
    {
        if ($this->template()->exists()) {
            return $this->template->replaceTokens($this);
        } else {
            return [];
        }
    }

    public function getSlug()
    {
        if ($this->parent()->exists()) {
            return $this->parent->getSlug() . '/' . $this->slug;
        } else {
            return $this->slug;
        }
    }
}
