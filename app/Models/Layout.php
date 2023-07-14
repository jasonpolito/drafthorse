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
        'uuid',
        'data',
        'markup',
    ];

    protected $casts = [
        'data' => 'array',
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
        if (count($parts) > 1) {
            $start = self::attachDataToBlocks($parts[0]);
            $end = self::attachDataToBlocks($parts[1]);
        }
        return (object) [
            'start' => $start ?? '',
            'end' => $end ?? '',
        ];
    }

    /**
     * Get the "raw" blade markup from a layout and record
     *
     * @param Layout $layout
     * @param object $record
     * @return string
     */
    public static function getBladeMarkup(string $layoutMarkup, object $record): string
    {
        $recordData = Record::getData($record);
        $source = Record::renderMarkup($layoutMarkup, ['data' => $recordData]);
        $parts = self::getLayoutParts($source);
        $start = Record::renderMarkup($parts->start, ['data' => $recordData]);
        $end = Record::renderMarkup($parts->end, ['data' => $recordData]);
        $content = $record->data['content'];
        // dd($content);
        $pageSource = self::renderBlocks($content['value'], $record);
        // dd($pageSource);

        $pageMarkup = Record::renderMarkup($pageSource, ['data' => $recordData]);
        $markup = Arr::join([
            $start,
            $pageMarkup,
            $end,
        ], '');
        return $markup;
    }

    public static function renderBlocks($blocks, $data): string
    {
        $markup = '';
        foreach ($blocks as $blockData) {
            $data = Record::getValueData(json_decode(json_encode($blockData['data'])));
            $block = Block::firstWhere('uuid', $blockData['block_uuid']);
            if (!$block) {
                $partial = Partial::firstWhere('uuid', $blockData['block_uuid']);
                if ($partial) {
                    $markup .= self::renderBlocks($partial['data']['content']['value'], ['data' => $data], true);
                }
            }
            if ($block) {
                $markup .= Record::renderMarkup($block->markup, ['data' => $data], true);
            }
        }
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
        $layoutMarkup = self::renderBlocks($layout->data['layout']['value'], $record);
        $pageMarkup = self::getBladeMarkup($layoutMarkup, $record);
        $data = json_decode(json_encode(Record::getData($record)));
        return  Blade::render($pageMarkup, [
            'record' => $record,
            'data' => $data,
        ]);
    }
}
