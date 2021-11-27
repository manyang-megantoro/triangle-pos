<?php

namespace Modules\Migrator\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
// use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
// use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ImportBuilder implements ToModel, WithHeadingRow, SkipsOnError, WithUpserts
{
    use Importable, SkipsErrors;

    public string $className;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $className = $this->className;
        $tableName = $this->tableName;
        $columns = DB::select('describe '.$tableName);
        // $columns_names = array_map(function($o) { return $o->Field;}, $columns);
        $columnsParam = [];
        if(!empty($columns)){
            foreach ($columns as $key => $column) {
                $columnsParam[$column->Field] = $row[$column->Field];
            }
        }

        return new $className($columnsParam);
    }

    public function uniqueBy()
    {
        $tableName = $this->tableName;
        $columnIndexes = DB::select('SHOW INDEX FROM '.$tableName.';');
        $columnsUnique = [];
        if(!empty($columnIndexes)){
            foreach ($columnIndexes as $column) {
                if($column->Non_unique == 0){
                    $columnsUnique[] = $column->Column_name;
                }
            }
        }
        return $columnsUnique;
    }

    // public function batchSize(): int
    // {
    //     return 500;
    // }

    // public function chunkSize(): int
    // {
    //     return 500;
    // }
}
