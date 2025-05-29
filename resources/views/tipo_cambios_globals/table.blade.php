<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="tipo_cambios_globals-table">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Dolar Compra</th>
                <th>Dolar Venta</th>
                <th>Dolar Promedio</th>
                <th>Euro Promedio</th>
                <th>Francosuizo Promedio</th>
                <th>Ui</th>
                <th>Ipc</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tipoCambiosGlobals as $tipoCambiosGlobal)
                <tr>
                    <td>{{\Carbon\Carbon::parse( $tipoCambiosGlobal->fecha)->format('Y-m-d') }}</td>
                    <td>{{ $tipoCambiosGlobal->dolar_compra }}</td>
                    <td>{{ $tipoCambiosGlobal->dolar_venta }}</td>
                    <td>{{ $tipoCambiosGlobal->dolar_promedio }}</td>
                    <td>{{ $tipoCambiosGlobal->euro_promedio }}</td>
                    <td>{{ $tipoCambiosGlobal->francosuizo_promedio }}</td>
                    <td>{{ $tipoCambiosGlobal->ui }}</td>
                    <td>{{ $tipoCambiosGlobal->ipc }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['tipo_cambios_globals.destroy', $tipoCambiosGlobal->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('tipo_cambios_globals.show', [$tipoCambiosGlobal->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('tipo_cambios_globals.edit', [$tipoCambiosGlobal->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $tipoCambiosGlobals])
        </div>
    </div>
</div>
