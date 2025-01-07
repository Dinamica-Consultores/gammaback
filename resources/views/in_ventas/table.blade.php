<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="in_ventas-table">
            <thead>
            <tr>
                <th>Mes</th>
                <th>Ano</th>
                <th>Sucursal</th>
                <th>Codigo Analisis</th>
                <th>Cantidad Venta Unidades</th>
                <th>Cantidad Costo</th>
                <th>Cantidad Margen</th>
                <th>Ventas Uyu</th>
                <th>Ganancia Bruta Uyu</th>
                <th>Ventas Uyu Promedio</th>
                <th>Ganancia Bruta Uyu Promedio</th>
                <th>Costo Uyu</th>
                <th>Id Company</th>
                <th>Id Excel</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($inVenta as $inVentas)
                <tr>
                    <td>{{ $inVentas->mes }}</td>
                    <td>{{ $inVentas->ano }}</td>
                    <td>{{ $inVentas->sucursal }}</td>
                    <td>{{ $inVentas->codigo_analisis }}</td>
                    <td>{{ $inVentas->cantidad_venta_unidades }}</td>
                    <td>{{ $inVentas->cantidad_costo }}</td>
                    <td>{{ $inVentas->cantidad_margen }}</td>
                    <td>{{ $inVentas->ventas_uyu }}</td>
                    <td>{{ $inVentas->ganancia_bruta_uyu }}</td>
                    <td>{{ $inVentas->ventas_uyu_prom }}</td>
                    <td>{{ $inVentas->ganancia_bruta_uyu_prom }}</td>
                    <td>{{ $inVentas->costo_uyu }}</td>
                    <td>{{ $inVentas->excel->company->razon_social }}</td>
                    <td>{{ $inVentas->excel->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['in_ventas.destroy', $inVentas->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('in_ventas.show', [$inVentas->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('in_ventas.edit', [$inVentas->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $inVenta])
        </div>
    </div>
</div>
