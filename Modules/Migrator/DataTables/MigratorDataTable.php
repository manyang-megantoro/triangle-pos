<?php

namespace Modules\Migrator\DataTables;

use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MigratorDataTable extends DataTable
{

    public function dataTable($query) {
        return datatables()->collection($query)
        ->addColumn('action', function ($data) {
            return view('migrator::partials.actions', [
                'classModel' => $data[__('Class Name')]
            ]);
        });
    }

    public function query() {
        $all_models = $this->getAllModel();
        $listCollection = [];
        if(!empty($all_models)){
            foreach ($all_models as $modelName => $modelClass) {
                $newCollection = [
                    __('Modal Name') => $modelName,
                    __('Class Name') => $modelClass
                ];

                $listCollection[] = $newCollection;
            }
        }
        $collection = collect($listCollection);
        return $collection;
    }

    public function getAllModel()
    {
        $modules = Module::all();
        $module_names = array_keys($modules);
        $models = [];
        $module_path = Module::getPath();
        if(!empty($module_names)){
            foreach ($module_names as $name) {
                $new_models = $this->getModelsByPath($module_path.'/'.$name.'/Entities', $name);
                $models = array_merge($models,$new_models);
            }
        }
        return $models;
    }

    public function getModelsByPath($modelsPath, $moduleName)
    {
        $models = [];
        $modelFiles = File::allFiles($modelsPath);
        foreach ($modelFiles as $modelFile) {
            $modelClass = $modelFile->getFilenameWithoutExtension();
            $models[$moduleName.'\\'.$modelClass] = '\Modules\\'.$moduleName.'\Entities\\' . $modelClass;
        }

        return $models;
    }

    public function html() {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(1)
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns() {
        return [
            Column::computed(__('Modal Name'))
                ->className('text-left align-middle'),

            Column::make( __('Class Name') )
                ->className('text-left align-middle'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

        ];
    }

    protected function filename() {
        return 'Users_' . date('YmdHis');
    }
}
