<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="tipo_cambios-table">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Dolar Compra</th>
                <th>Dolar Venta</th>
                <th>Dolar Promedio</th>
                <th>Euro Promedio</th>
                <th>Franco Suizo Promedio</th>
                <th>UI</th>
                <th>IPC</th>
                <th>IPC Empresa</th>
                <th>Compañia</th>
                <th>Version</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tipoCambios as $tipoCambio)
                <tr>
                    <td>{{ $tipoCambio->fecha }}</td>
                    <td>{{ $tipoCambio->dolar_compra }}</td>
                    <td>{{ $tipoCambio->dolar_venta }}</td>
                    <td>{{ $tipoCambio->dolar_promedio }}</td>
                    <td>{{ $tipoCambio->euro_promedio }}</td>
                    <td>{{ $tipoCambio->francosuizo_promedio }}</td>
                    <td>{{ $tipoCambio->ui }}</td>
                    <td>{{ $tipoCambio->ipc }}</td>
                    <td>{{ $tipoCambio->ipc_empresa }}</td>
                    <td>{{ $tipoCambio->excel->company->razon_social }}</td>
                    <td>{{ $tipoCambio->excel->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['tipo_cambios.destroy', $tipoCambio->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('tipo_cambios.show', [$tipoCambio->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('tipo_cambios.edit', [$tipoCambio->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $tipoCambios])
        </div>
    </div>
</div>
