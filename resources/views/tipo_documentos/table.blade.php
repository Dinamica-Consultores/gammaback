<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="tipo_documentos-table">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Cantidad Dias Preaviso</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tipoDocumentos as $tipoDocumento)
                <tr>
                    <td>{{ $tipoDocumento->nombre }}</td>
                    <td>{{ $tipoDocumento->cantidad_dias_preaviso }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['tipo_documentos.destroy', $tipoDocumento->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('tipo_documentos.show', [$tipoDocumento->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('tipo_documentos.edit', [$tipoDocumento->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $tipoDocumentos])
        </div>
    </div>
</div>
