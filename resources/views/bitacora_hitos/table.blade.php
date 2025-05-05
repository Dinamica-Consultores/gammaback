<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="bitacora_hitos-table">
            <thead>
            <tr>
            <th>Numero</th>
                <th>Titulo</th>
                <th>Description</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($allHitos as $bitacoraHitos)
                <tr>
                <td>{{ $bitacoraHitos->numero }}</td>
                    <td>{{ $bitacoraHitos->titulo }}</td>
                    <td>{{ $bitacoraHitos->description }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['bitacora_hitos.destroy', [$id2,$bitacora->id,$bitacoraHitos->id]], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('bitacora_hitos.show', [$id2,$bitacora->id,$bitacoraHitos->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('bitacora_hitos.edit', [$id2,$bitacora->id,$bitacoraHitos->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $allHitos])
        </div>
    </div>
</div>
