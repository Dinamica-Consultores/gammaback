<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="sucursales-table">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Id Company</th>
                <th>Id Excel</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($sucursale as $sucursales)
                <tr>
                    <td>{{ $sucursales->nombre }}</td>
                    <td>{{ $sucursales->excel->company->razon_social }}</td>
                    <td>{{ $sucursales->excel->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['sucursales.destroy', $sucursales->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('sucursales.show', [$sucursales->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('sucursales.edit', [$sucursales->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $sucursale])
        </div>
    </div>
</div>
