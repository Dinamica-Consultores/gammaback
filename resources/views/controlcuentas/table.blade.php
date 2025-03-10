<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="controlcuentas-table">
            <thead>
            <tr>
                <th>Compañia</th>
                <th>Cantidad Cuentas</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($controlcuentas as $controlcuentass)
                <tr>
                    <td>{{ $controlcuentass->name }}</td>
                    <td>{{ $controlcuentass->cantidad }}</td>
                    <td  style="width: 120px">
                        <div class='btn-group'>
                            <a href="{{ route('controlcuentas.show', [$controlcuentass->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
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
            @include('adminlte-templates::common.paginate', ['records' => $controlcuentas])
        </div>
    </div>
</div>
