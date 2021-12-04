<a href="{{ route('api.migrator.export', ['class_name' => $classModel]) }}" class="btn btn-info btn-sm">
    <i class="bi bi-cloud-download"></i>
</a>
<button class="btn btn-danger btn-sm btn-migrator-importer" data-classname="{{$classModel}}"><i class="bi bi-cloud-upload"></i></button>
