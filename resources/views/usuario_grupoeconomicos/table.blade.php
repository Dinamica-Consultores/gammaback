<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="usuario_grupoeconomicos-table">
            <thead>
            <tr>
                <th>Usuarios Nombre</th>
                <th>Usuarios Email</th>
                <th>Cantidad Redes Comercial</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($usuarioGrupoeconomicos as $usuarioGrupoeconomicoss)
                <tr>
                    
                    <td>{{ $usuarioGrupoeconomicoss->name }}</td>
                    <td>{{ $usuarioGrupoeconomicoss->email }}</td>
                    <td>{{ $usuarioGrupoeconomicoss->Cantidad }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['usuario_grupoeconomicos.destroy', $usuarioGrupoeconomicoss->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('usuario_grupoeconomicos.edit', [$usuarioGrupoeconomicoss->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $usuarioGrupoeconomicos])
        </div>
    </div>
</div>
