<?php

namespace App\Observers;

use App\Models\Record;
use Illuminate\Support\Facades\Log;

class RecordObserver
{
    /**
     * Handle the Record "created" event.
     */
    public function created(Record $record): void
    {
        //
    }

    /**
     * Handle the Record "saved" event.
     */
    public function saved(Record $record): void
    {
        // $id = $record->id;
        // foreach ($record->data as $key => $info) {
        //     if ($info['type'] == 'relation' && is_array($info['value'])) {
        //         $related = Record::where('id', '!=', $id)
        //             ->whereIn('id', $info['value'])
        //             ->get();
        //         $related->each(function ($item) use ($key, $id) {
        //             $data = $item->data;
        //             if (!isset($data[$key])) {
        //                 $data[$key] = [
        //                     'value' => [],
        //                     'type' => 'relation'
        //                 ];
        //             }
        //             if (!in_array($id, $data[$key]['value'])) {
        //                 array_push($data[$key]['value'], $id);
        //                 $item->data = $data;
        //                 $item->save();
        //             }
        //         });

        //         remove old relations
        //         if (isset($record->getOriginal("data")[$key])) {
        //             $orig = $record->getOriginal("data")[$key]['value'];
        //             $removedIds = array_diff($orig, $info['value']);
        //             if (count($removedIds)) {
        //                 $removed = Record::where('id', '!=', $id)
        //                     ->whereIn('id', $removedIds)
        //                     ->get();
        //                 $removed->each(function ($item) use ($key, $id) {
        //                     $data = $item->data;
        //                     if (!is_array($data[$key]['value'])) {
        //                         $data[$key]['value'] = [];
        //                     }
        //                     if (in_array($id, $data[$key]['value'])) {
        //                         $index = array_search($id, $data[$key]['value']);
        //                         unset($data[$key]['value'][$index]);
        //                         $item->data = $data;
        //                         $item->save();
        //                     }
        //                 });
        //             }
        //         }
        //     }
        // }
    }

    /**
     * Handle the Record "updated" event.
     */
    public function updated(Record $record): void
    {
    }

    /**
     * Handle the Record "deleted" event.
     */
    public function deleted(Record $record): void
    {
        //
    }

    /**
     * Handle the Record "restored" event.
     */
    public function restored(Record $record): void
    {
        //
    }

    /**
     * Handle the Record "force deleted" event.
     */
    public function forceDeleted(Record $record): void
    {
        //
    }
}
