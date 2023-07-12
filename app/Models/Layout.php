<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;

class Layout extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'fields',
        'markup',
    ];

    /**
     * Add the $data attribute to the block components
     * to give access to record data
     *
     * @param string $markup
     * @return string
     */
    public static function attachDataToBlocks(string $markup): string
    {
        $markup = Str::replace('<x-block ', '<x-block :$data ', $markup);
        return $markup;
    }

    /**
     * Split layout at @content, typically for header and footer
     *
     * @param string $markup
     * @return object
     */
    public static function getLayoutParts(string $markup): object
    {
        $parts = explode('@content', $markup);
        $start = self::attachDataToBlocks($parts[0]);
        $end = self::attachDataToBlocks($parts[1]);
        return (object) [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get the "raw" blade markup from a layout and record
     *
     * @param Layout $layout
     * @param object $record
     * @return string
     */
    public static function getBladeMarkup(Layout $layout, object $record): string
    {
        $source = $layout->markup;
        $parts = self::getLayoutParts($source);
        $data = Record::getData($record);
        $start = Record::renderMarkup($parts->start, ['data' => $data]);
        $markup = Arr::join([
            $start,
            Record::renderMarkup($record->data['markup'], ['data' => Record::getData($record)]),
            $parts->end,
        ], '');
        return $markup;
    }

    /**
     * Render a layout after building the parts
     * and compiling the resulting blade
     *
     * @param object $record
     * @return string
     */
    public static function render(object $record): string
    {
        $layout = Layout::find($record->data['layout']);
        $markup = self::getBladeMarkup($layout, $record);
        $data = json_decode(json_encode(Record::getData($record)));
        return  Blade::render($markup, [
            'record' => $record,
            'data' => $data,
        ]);
    }
}
