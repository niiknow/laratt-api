<?php
namespace Api\Extra\Traits;

use Illuminate\Support\Facades\Config;
use Validator;

/**
 * Add processCsv and saveImport methods
 */
trait ImportableTrait
{
    /**
     * process the csv records
     *
     * @param  array  $csv    the csv rows data
     * @param  array  &$data  the result array
     * @param  array  $vrules the validation rules
     * @return object null or response object if error
     */
    public function processCsv($csv, &$data, $vrules)
    {
        $rowno = 0;
        $limit = config('laratt.import_limit', 999);
        foreach ($csv as $row) {
            $inputs = [];

            // undot the csv array
            foreach ($row as $key => $value) {
                $cell = $value;
                if (!is_string($cell)) {
                    $cell = (string) $cell;
                }
                $cv = trim(mb_strtolower($cell));

                if ($cv === 'null'
                    || $cv === 'nil'
                    || $cv === 'undefined') {
                    $cell = null;
                } elseif (!is_string($value) && is_numeric($cv)) {
                    $cell = $cell + 0;
                }

                // undot array
                \Illuminate\Support\Arr::set($inputs, $key, $cell);
            }

            // validate data
            $validator = Validator::make($inputs, $vrules);

            // capture and provide better error message
            if ($validator->fails()) {
                return [
                    'code'  => 422,
                    'error' => $validator->errors(),
                    'rowno' => $rowno,
                    'row'   => $inputs
                ];
            }

            $data[] = $inputs;
            if ($rowno > $limit) {
                // we must improve a limit due to memory/resource restriction
                return [
                    'code'  => 422,
                    'error' => "Each import must be less than $limit records"
                ];
            }
            $rowno += 1;
        }

        return ['code' => 200];
    }

    /**
     * @param $data
     * @param $spaceId
     * @param null       $idField
     */
    public function saveImport(&$data, $spaceId = null, $idField = 'id')
    {
        // get uid
        // item found, do update, else insert

        $inserted = [];
        $updated  = [];
        $skipped  = [];
        $rowno    = 1;
        $row      = [];

        // insert
        \DB::beginTransaction();
        try {
            // check if it should skip
            foreach ($data as $inputs) {
                $row               = $inputs;
                list($stat, $item) = $this->saveImportItem($inputs, $spaceId, $idField);

                if (null === $item && $stat !== 'skip') {
                    // example usage: if gtin is a local product
                    \DB::rollback();

                    return [
                        'code'  => 422,
                        'error' => 'Error while attempting to import row',
                        'rowno' => $rowno,
                        'row'   => $row
                    ];
                }

                if ($stat === 'insert') {
                    $inserted[] = $item->{$idField};
                } elseif ($stat === 'update') {
                    $updated[] = $item->{$idField};
                } else {
                    $skipped[] = $item->{$idField};
                }

                $rowno += 1;
                $item = null;

                // disable audit for bulk import
                // $start_memory = memory_get_usage();
                // \Log::info("importing: $start_memory");
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            $message = $e->getMessage();
            \Log::error('API import error: ' . $message);
            \Log::error($e->getTrace());

            return [
                'code'  => 422,
                'error' => $message,
                'rowno' => $rowno,
                'row'   => $row
            ];
        }

        return [
            'code'     => 200,
            'count'    => $rowno,
            'inserted' => $inserted,
            'updated'  => $updated,
            'skipped'  => $skipped
        ];
    }

    /**
     * @param $inputs
     * @param $spaceId
     * @param null       $idField
     */
    public function saveImportItem(&$inputs, $spaceId = null, $idField = 'id')
    {
        // wrap import in a transaction
        if (isset($spaceId)) {
            $inputs['space_id'] = $spaceId;
        }

        $model = get_class($this);

        $stat = 'insert';
        $id   = isset($inputs[$idField]) ? $inputs[$idField] : null;
        $item = new $model($inputs);
        if (isset($id)) {
            $inputs[$idField] = $id;

            $item = $this->where($idField, $id)->first();

            // start at 1 because header row is at 0
            if (isset($item)) {
                $item->fill($inputs);
                $stat = 'update';
            } else {
                // rollback transaction
                $item = new $model($inputs);
            }

            // $used_memory = (memory_get_usage() - $start_memory) / 1024 / 1024;
            // $peek_memory = memory_get_peak_usage(true) / 1024 / 1024;
            if ($item->shouldSkip()) {
                $stat = 'skip';

                return [$stat, $item];
            }
        }

        // \Log::info("import #$rowno: $used_memory $peek_memory");
        // $item->setNoAudit(true);

        return [$stat, $item->save() ? $item : null];
    }

    protected function shouldSkip()
    {
        return false;
    }
}
