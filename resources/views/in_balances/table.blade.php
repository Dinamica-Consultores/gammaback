<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="in_balances-table">
            <thead>
            <tr>
                <th>Mes</th>
                <th>Ano</th>
                <th>Sucursal</th>
                <th>Cuenta Master</th>
                <th>Saldo Uyu</th>
                <th>Id Company</th>
                <th>Id Excel</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($inBalances as $inBalance)
                <tr>
                    <td>{{ $inBalance->mes }}</td>
                    <td>{{ $inBalance->ano }}</td>
                    <td>{{ $inBalance->sucursal }}</td>
                    <td>{{ $inBalance->cuenta_master }}</td>
                    <td>{{ $inBalance->saldo_uyu }}</td>
                    <td>{{ $inBalance->excel->company->razon_social }}</td>
                    <td>{{ $inBalance->excel->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['in_balances.destroy', $inBalance->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('in_balances.show', [$inBalance->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('in_balances.edit', [$inBalance->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $inBalances])
        </div>
    </div>
</div>
