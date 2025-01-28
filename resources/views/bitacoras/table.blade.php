<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="bitacoras-table">
            <thead>
            <tr>
                <th>Descripcion</th>
                <th>Red Comercial</th>
                <th>Persona Agrega</th>
                <th colspan="3">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bitacoras as $bitacora)
                <tr>
                    <td>{{ $bitacora->descripcion }}</td>
                    <td>{{ $bitacora->grupoeconomicos->nombre }}</td>
                    <td>{{ $bitacora->persona_agrega }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['bitacoras.destroy', $bitacora->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('bitacoras.show', [$bitacora->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('bitacoras.edit', [$bitacora->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $bitacoras])
        </div>
    </div>
</div>
