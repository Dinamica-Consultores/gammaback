<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="estudios_usuarios-table">
            <thead>
            <tr>
                <th>Usuario Nombre y Apellido</th>
                <th>Usuario Email</th>
                <th>Estudio</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($estudiosUsuarios as $estudiosUsuarioss)
                <tr>
                    
                    <td>{{ $estudiosUsuarioss->User->name }} {{ $estudiosUsuarioss->User->surname }}</td>
                    <td>{{ $estudiosUsuarioss->User->email }}</td>
                    <td>{{ $estudiosUsuarioss->estudios->razon_social }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['estudios_usuarios.destroy', $estudiosUsuarioss->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>

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
            @include('adminlte-templates::common.paginate', ['records' => $estudiosUsuarios])
        </div>
    </div>
</div>
