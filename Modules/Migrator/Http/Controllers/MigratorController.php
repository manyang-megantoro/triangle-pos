<?php

namespace Modules\Migrator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Migrator\Builder\ErrorBuilder;
use Modules\Migrator\Builder\ExportBuilder;
use Modules\Migrator\Builder\ImportBuilder;
use Modules\Migrator\DataTables\MigratorDataTable;

class MigratorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MigratorDataTable $dataTable)
    {
        // abort_if(Gate::denies('access_migrator'), 403);
        return $dataTable->render('migrator::index');
    }

    public function export(Request $request)
    {
        $className = $request->class_name;
        $modelName = Str::afterLast($className, '\\');
        return Excel::download(new ExportBuilder($className), $modelName.'-' . now()->toDateTimeString().'.xlsx');
    }

    public function import(Request $request)
    {
        $className = $request->class_name;
        $builder = new ImportBuilder();
        $builder->className = $className;
        $the_class = new $className();
        $tableName = app($the_class::class)->getTable();
        $builder->tableName = $tableName;
        $builder->import($request->file('file_import'));

        // $failures = [];
        // if(!empty($builder->failures())){
        //     foreach ($builder->failures() as $failure) {
        //         $failures['row'] = $failure->row(); // row that went wrong
        //         $failures['attribute'] = $failure->attribute(); // either heading key (if using heading row concern) or column index
        //         $failures['errors'] = $failure->errors(); // Actual error messages from Laravel validator
        //         $failures['values'] = $failure->values(); // The values of the row that has failed.
        //     }
        // }

        $errors = $builder->errors();

        if(empty($errors)){
            return back()->with(['status'=>1]);
        }else{
            $errorArray = [];
            $all_errors = $errors->all();
            $columns = DB::select('describe '.$tableName);
            $columns_names = array_map(function($o) { return $o->Field;}, $columns);
            sort($columns_names);
            // dd($all_errors);
            if(!empty($all_errors)){
                foreach ($all_errors as $rowIndex => $rowCollection) {
                    $rowError = [$rowIndex+1];
                    $rowError = array_merge($rowError,$rowCollection->errorInfo, $rowCollection->getBindings());
                    $errorArray[] = $rowError;
                }
            }
            $errorBuilder = new ErrorBuilder($errorArray);
            $errorBuilder->errorsHeader = $columns_names;
            return Excel::download($errorBuilder, 'Importer_Errors-' . now()->toDateTimeString().'.xlsx');
        }

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('migrator::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('migrator::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('migrator::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
