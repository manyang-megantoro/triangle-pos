<?php
namespace Modules\Migrator\Builder;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportBuilder implements FromCollection, WithHeadings
{
    public $className = null;

    public function __construct($className)
    {
        $this->className = $className;
    }
    public function collection()
    {
        // return $this->className::all();
        return $this->className::limit(1)->get();
    }

    public function headings(): array
    {
        $the_class = new $this->className();
        $tableName = app($the_class::class)->getTable();
        $columns = DB::select('describe '.$tableName);
        $columns_names = array_map(function($o) { return $o->Field;}, $columns);
        return $columns_names;
        // if($this->collection()->first() == null){
        //     return [];
        // }
        // return array_keys($this->collection()->first()->toArray());
    }
}
