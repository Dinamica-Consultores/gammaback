<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="in_presupuestos-table">
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
            @foreach($inPresupuesto as $inPresupuestos)
                <tr>
                    <td>{{ $inPresupuestos->mes }}</td>
                    <td>{{ $inPresupuestos->ano }}</td>
                    <td>{{ $inPresupuestos->sucursal }}</td>
                    <td>{{ $inPresupuestos->cuenta_master }}</td>
                    <td>{{ $inPresupuestos->monto_uyu }}</td>
                    <td>{{ $inPresupuestos->excel->company->razon_social }}</td>
                    <td>{{ $inPresupuestos->excel->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['in_presupuestos.destroy', $inPresupuestos->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('in_presupuestos.show', [$inPresupuestos->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('in_presupuestos.edit', [$inPresupuestos->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $inPresupuesto])
        </div>
    </div>
</div>
