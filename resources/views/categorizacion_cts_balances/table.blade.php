<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="categorizacion_cts_balances-table">
            <thead>
            <tr>
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Origen</th>
                <th>Nivel 1</th>
                <th>Nivel 2</th>
                <th>Nivel 3</th>
                <th>Nivel 4</th>
                <th>Posicion Moneda</th>
                <th>Posicion Fiscal</th>
                <th>Posicion Socios</th>
                <th>Compañia</th>
                <th>Archivo</th>
                      <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($categorizacionCtsBalances as $categorizacionCtsBalance)
                <tr>
                    <td>{{ $categorizacionCtsBalance->cuenta }}</td>
                    <td>{{ $categorizacionCtsBalance->nombre }}</td>
                    <td>{{ $categorizacionCtsBalance->origen }}</td>
                    <td>{{ $categorizacionCtsBalance->nivel_1 }}</td>
                    <td>{{ $categorizacionCtsBalance->nivel_2 }}</td>
                    <td>{{ $categorizacionCtsBalance->nivel_3 }}</td>
                    <td>{{ $categorizacionCtsBalance->nivel_4 }}</td>
                    <td>{{ $categorizacionCtsBalance->posicion_moneda }}</td>
                    <td>{{ $categorizacionCtsBalance->posicion_fiscal }}</td>
                    <td>{{ $categorizacionCtsBalance->posicion_socios }}</td>
                    <td>{{ $categorizacionCtsBalance->excel?->company->razon_social }}</td>
                    <td>{{ $categorizacionCtsBalance->excel?->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['categorizacion_cts_balances.destroy', $categorizacionCtsBalance->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('categorizacion_cts_balances.show', [$categorizacionCtsBalance->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('categorizacion_cts_balances.edit', [$categorizacionCtsBalance->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $categorizacionCtsBalances])
        </div>
    </div>
</div>
