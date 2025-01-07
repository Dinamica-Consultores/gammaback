<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="setup_analises-table">
            <thead>
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Id Company</th>
                <th>Id Excel</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($setupAnalises as $setupAnalisis)
                <tr>
                    <td>{{ $setupAnalisis->codigo }}</td>
                    <td>{{ $setupAnalisis->nombre }}</td>
                    <td>{{ $setupAnalisis->excel->company->razon_social}}</td>
                    <td>{{ $setupAnalisis->excel->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['setup_analises.destroy', $setupAnalisis->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('setup_analises.show', [$setupAnalisis->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('setup_analises.edit', [$setupAnalisis->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $setupAnalises])
        </div>
    </div>
</div>
