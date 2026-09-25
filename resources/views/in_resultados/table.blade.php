<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="in_resultados-table">
            <thead>
            <tr>
                <th>Mes</th>
                <th>Ano</th>
                <th>Sucursal</th>
                <th>Cuenta Master</th>
                <th>Monto Uyu</th>
                <th>Id Company</th>
                <th>Id Excel</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($inResultados as $inResultado)
                <tr>
                    <td>{{ $inResultado->mes }}</td>
                    <td>{{ $inResultado->ano }}</td>
                    <td>{{ $inResultado->sucursal }}</td>
                    <td>{{ $inResultado->cuenta_master }}</td>
                    <td>{{ $inResultado->monto_uyu }}</td>
                    <td>{{ $inResultado->excel?->company?->razon_social }}</td>
                    <td>{{ $inResultado->excel?->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['in_resultados.destroy', $inResultado->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('in_resultados.show', [$inResultado->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('in_resultados.edit', [$inResultado->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $inResultados])
        </div>
    </div>
</div>
