<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="espacio-fiscals-table">
            <thead>
            <tr>
                <th>Ano</th>
                <th>Mes</th>
                <th>Nivel_3</th>
                <th>Ajusta</th>
                <th>Tipo</th>
                <th>Operador</th>
                <th>Signo</th>
                <th>Monto Valor</th>
                <th>Sucursal</th>
                <th>Id Excel</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($espacioFiscals as $espacioFiscal)
                <tr>
                    <td>{{ $espacioFiscal->ano }}</td>
                    <td>{{ $espacioFiscal->mes }}</td>
                    <td>{{ $espacioFiscal->nivel_3 }}</td>
                    <td>{{ $espacioFiscal->ajusta }}</td>
                    <td>{{ $espacioFiscal->tipo }}</td>
                    <td>{{ $espacioFiscal->operador }}</td>
                    <td>{{ $espacioFiscal->signo }}</td>
                    <td>{{ $espacioFiscal->monto_valor }}</td>
                    <td>{{ $espacioFiscal->sucursal }}</td>
                    <td>{{ $espacioFiscal->id_excel }}</td>
                    <td  style="width: 120px">
                        <div class='btn-group'>
                            <a href="{{ route('espacio-fiscals.show', [$espacioFiscal->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('espacio-fiscals.edit', [$espacioFiscal->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                     </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $espacioFiscals])
        </div>
    </div>
</div>
