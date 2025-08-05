<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="grupo_economicos-table">
            <thead>
            <tr>
                <th>Nombre</th>
                
                <th>Logo</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($grupoEconomicos as $grupoEconomicoss)
                <tr>
                    <td>{{ $grupoEconomicoss->nombre }}</td>
                    
                    <td><img src="{{ asset('storage/'.$grupoEconomicoss->logo) }}" style="width:50px;height:50px;"/></td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['grupo_economicos.destroy', $grupoEconomicoss->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('grupo_economicos.show', [$grupoEconomicoss->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('grupo_economicos.edit', [$grupoEconomicoss->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $grupoEconomicos])
        </div>
    </div>
</div>
