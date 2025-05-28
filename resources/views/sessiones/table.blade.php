<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="sessiones-table">
            <thead>
            <tr>
                <th>Correo del Usuario</th>
                <th>Accion </th>
                <th colspan="3">Fecha</th>
            </tr>
            </thead>
            <tbody>
            @foreach($sessiones as $sessioness)
                <tr>
                    <td>{{ $sessioness->email }}</td>
                    <td>{{ $sessioness->opcion }}</td>
                    <td  style="width: 120px">
                    {{ $sessioness->created_at ->format('Y-m-d H:i:s')}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer clearfix">
        <div class="float-right">
            @include('adminlte-templates::common.paginate', ['records' => $sessiones])
        </div>
    </div>
    
</div>
