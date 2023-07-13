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

    public function parent()
    {
        return $this->belongsTo(Record::class);
    }

    public function children()
    {
        return $this->hasMany(Record::class, 'parent_id');
    }

    public function scopeType($query, $type)
    {
        return $query->whereHas('taxonomy', function ($q) use ($type) {
            return $q->where('name', $type);
        });
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
                    $records = Record::whereIn('id', $info['value'])->get()->toArray();
                    $data[$name] = $records;
                }
            }
        }
        $this->data = $data;
        return $this;
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

    public static function makeVariablesOptional($str)
    {
        $pattern = '/\{{2}\s*\$([a-z\d_(\->)]+)\s*\}{2}/i';
        preg_match_all($pattern, $str, $matches);
        if (count($matches[1])) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $token = $matches[0][$i];
                $varName = Str::replace('->', '→', '$' . $matches[1][$i]);
                $replacement = Str::replace('}}', " ?? '($varName is not defined!)' }}", $token);
                $str = Str::replace($token, $replacement, $str);
            }
        }
        $pattern = '/\{\!\!\s*\$([a-z\d_(\->)]+)\s*\!\!\}/i';
        preg_match_all($pattern, $str, $matches);
        if (count($matches[1])) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $token = $matches[0][$i];
                $varName = Str::replace('->', '→', '$' . $matches[1][$i]);
                $replacement = Str::replace('!!}', " ?? '($varName is not defined!)' !!}", $token);
                $str = Str::replace($token, $replacement, $str);
            }
        }
        return $str;
    }

    public static function passDataToMarkup($str)
    {
        $str = Str::replace('<x-blocks ', '<x-blocks :$data ', $str);
        $str = Str::replace('<x-block ', '<x-block :$data ', $str);
        return $str;
    }

    public static function parseContent($str)
    {
        return self::makeVariablesOptional(self::passDataToMarkup($str));
    }

    public static function renderMarkup($markup, $data = ['data' => []])
    {
        $markup = self::passDataToMarkup(self::makeVariablesOptional($markup));
        $dataObj = json_decode(json_encode($data['data']));
        return Blade::render($markup, ['data' => $dataObj]);
    }

    public static function getValueData($data)
    {
        $res = [];
        foreach ($data as $name => $info) {
            if (is_array($info->value)) {
                if (!empty($info->value)) {
                    $res[$name] = [];
                    foreach ($info->value as $nestedValue) {
                        $nestedMap = [];
                        foreach ($nestedValue->data as $nestedName => $nestedData) {
                            $nestedMap[$nestedName] = $nestedData->value;
                        }
                        $res[$name][] = $nestedMap;
                    }
                }
            } else {
                $res[$name] = $info->value;
            }
        }
        return $res;
    }

    public static function getNestedData()
    {
    }

    public static function getData($record)
    {
        $res = [];
        $res['name'] = $record->name;
        if (false) {
            $res['slug'] = $record->slug;
            $res['markup'] = $record->data['markup'];
            $res['children'] = $record->children()->get()->map(function ($item) {
                $data = self::getData($item);
                $item->full_slug = $item->fullSlug();
                $item->data = $data;
                return $item;
            });
        }
        foreach ($record->data as $name => $info) {
            if (isset($info['value'])) {
                if (is_array($info['value'])) {
                    if ($info['type'] == 'relation') {
                        $res[$name] = Record::whereIn('id', $info['value'])->get()->map(function ($item) {
                            return self::getData($item);
                        });
                    } else {
                        $res[$name] = $info['value'];
                    }
                } else {
                    $res[$name] = $info['value'];
                }
            }
        }
        return $res;
    }

    public function siblings()
    {
        return $this->parent->children()->where('id', '!=', $this->id)->get();
    }
}
