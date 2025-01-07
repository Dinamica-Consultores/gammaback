<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="estudios-table">
            <thead>
            <tr>
                <th>Razon Social</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Campo</th>
                <th>Tipo</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($estudios as $estudioss)
                <tr>
                    <td>{{ $estudioss->razon_social }}</td>
                    <td>{{ $estudioss->per_cont_name }}</td>
                    <td>{{ $estudioss->per_cont_phone }}</td>
                    <td>{{ $estudioss->campo }}</td>
                    <td>{{ $estudioss->es_empresa?'Empresa':'Estudios' }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['estudios.destroy', $estudioss->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('estudios.show', [$estudioss->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('estudios.edit', [$estudioss->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $estudios])
        </div>
    </div>
</div>
