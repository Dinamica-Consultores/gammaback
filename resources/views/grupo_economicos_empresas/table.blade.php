<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="grupo_economicos_empresas-table">
            <thead>
            <tr>
                <th>Red Comercial</th>
                <th>Cantidad Empresas</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($grupoEconomicosEmpresas as $grupoEconomicosEmpresass)
                <tr>
                    <td>{{ $grupoEconomicosEmpresass->nombre }}</td>
                    <td>{{ $grupoEconomicosEmpresass->Cantidad }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['grupo_economicos_empresas.destroy', $grupoEconomicosEmpresass->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('grupo_economicos_empresas.edit', [$grupoEconomicosEmpresass->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $grupoEconomicosEmpresas])
        </div>
    </div>
</div>
