<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="controlcuentas-table">
            <thead>
            <tr>
                <th>Cuenta</th>
                <th>Tipo</th>
            </tr>
            </thead>
            <tbody>
            @foreach($controlcuentas as $controlcuentass)
                <tr>
                    <td>{{ $controlcuentass->cuenta }}</td>
                    <td>{{ $controlcuentass->tipo }}</td>
                
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
