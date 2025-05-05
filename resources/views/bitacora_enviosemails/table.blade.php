<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="bitacora_enviosemails-table">
            <thead>
            <tr>
                <th>Id Bitacora</th>
                <th>Id User</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bitacoraEnviosemails as $bitacoraEnviosemail)
                <tr>
                    <td>{{ $bitacoraEnviosemail->id_bitacora }}</td>
                    <td>{{ $bitacoraEnviosemail->id_user }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['bitacora_enviosemails.destroy', $bitacoraEnviosemail->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('bitacora_enviosemails.show', [$bitacoraEnviosemail->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('bitacora_enviosemails.edit', [$bitacoraEnviosemail->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $bitacoraEnviosemails])
        </div>
    </div>
</div>
