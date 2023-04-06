<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Record extends Model implements HasMedia
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

    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'seo' => 'array',
    ];

    public function getSeo()
    {
        return (object) $this->seo;
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
        return $this->belongsTo(Record::class);
    }

    public function children()
    {
        return $this->hasMany(Record::class, 'parent_id');
    }

    public function buildTaxonomy()
    {
        foreach ($this->taxonomy->fields as $field) {
            if (!empty($field['relations'])) {
                $data = $this->data;
                $name = Str::snake($field['name']);
                $ids = isset($data[$name]) ? $data[$name] : [];
                $records = Record::whereIn('id', $ids)->get();
                $data[$name] = $records->toArray();
                $this->data = $data;
            }
        }
    }

    public function getBlocks()
    {
        if ($this->template()->exists()) {
            return $this->template->replaceTokens($this, $this->template->blocks);
        } else {
            return [];
        }
    }

    public function getTaxonomyFields()
    {
        $fields = [];
        foreach ($this->taxonomy->fields as $field) {
            $name = Str::snake($field['name']);
            $fields[$name] = $this->data[$name];
        }
        // dd($fields);
        return $fields;
    }

    public function getData()
    {
        $data = $this->data;
        foreach ($this->taxonomy->fields as $field) {
            $name = Str::snake($field['name']);
            if (!isset($this->data[$name])) {
                $data[$name] = null;
            }
        }
        return $data;
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
