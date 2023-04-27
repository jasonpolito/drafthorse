<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;


class Record extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'uuid',
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
                $records = $records->map(function ($item) {
                    $ritit = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($item->data));
                    foreach ($ritit as $leafValue) {
                        $keys = ['data'];
                        foreach (range(0, $ritit->getDepth()) as $depth) {
                            $keys[] = $ritit->getSubIterator($depth)->key();
                        }
                        $item->{join('.', $keys)} = $leafValue;
                    }
                    return $item;
                });
                // dd($records);
                $data[$name] = collect($records);
                $this->data = (object) $data;
            }
        }
    }

    public function getTaxonomyFields()
    {
        $fields = [];
        foreach ($this->taxonomy->fields as $field) {
            $name = Str::snake($field['name']);
            $fields[$name] = $this->data[$name];
        }
        return $fields;
    }

    public function fullSlug()
    {
        if ($this->parent()->exists()) {
            return $this->parent->fullSlug() . '/' . $this->slug;
        } else {
            return $this->slug;
        }
    }

    public function withRelations()
    {
        $data = $this->data;
        foreach ($data as $name => $info) {
            if (is_array($info)) {
                if ($info['type'] == 'relation') {
                    $data[$name]['value'] = Record::whereIn('id', $info['value'])->get();
                }
            }
        }
        $this->data = $data;
    }

    public static function findBySlug($slug)
    {
        $parts = explode('/', $slug);
        $last = $parts[count($parts) - 1];
        $record = self::where('slug', $last)->first();
        if ($record) {
            return $record;
        }
        return false;
    }

    // public function getData()
    // {
    //     $data = $this->data;
    //     foreach ($this->taxonomy->fields as $field) {
    //         $name = Str::snake($field['name']);
    //         if (!isset($this->data[$name])) {
    //             $data[$name] = null;
    //         }
    //     }
    //     return (object) $data;
    // }

    public static function makeVariablesOptional($str)
    {
        $res = Str::replace('}}', " ?? '' }}", $str);
        $res = Str::replace('!!}', " ?? '' !!}", $res);
        return $res;
    }

    public static function passDataToTemplate($str)
    {
        return Str::replace('<x-template', '<x-template :$data ', $str);
    }

    public static function parseContent($str)
    {
        return self::makeVariablesOptional(self::passDataToTemplate($str));
    }

    public static function renderTemplate($markup, $data = ['data' => []], $die = false)
    {
        $markup = self::passDataToTemplate(self::makeVariablesOptional($markup));
        $dataObj = json_decode(json_encode($data['data']));
        return Blade::render($markup, ['data' => $dataObj]);
    }

    public static function getValueData($data)
    {
        $res = [];
        foreach ($data as $name => $info) {
            $res[$name] = $info->value;
        }
        return $res;
    }

    public function getData()
    {
        $res = [];
        $res['name'] = $this->name;
        $res['children'] = $this->children()->get()->map(function ($item) {
            $data = $item->getData();
            $item->full_slug = $item->fullSlug();
            $item->data = $data;
            return $item;
        });
        foreach ($this->data as $name => $info) {
            if (is_array($info)) {
                $res[$name] = [
                    'value' => $info['value'],
                    'type' => $info['type']
                ];
            }
        }
        return $res;
    }
}
