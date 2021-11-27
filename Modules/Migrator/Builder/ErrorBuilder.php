<?php
namespace Modules\Migrator\Builder;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ErrorBuilder implements FromArray, WithHeadings
{
    public $errorsHeader = [];
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;

    }

    public function array(): array
    {
        return $this->errors;
    }

    public function headings(): array
    {

        $error_default = [
            'Index',
            'Error State',
            'Error Violation',
            'Error Message',
        ];

        $errors = array_merge($error_default, $this->errorsHeader);
        return $errors;
    }
}
