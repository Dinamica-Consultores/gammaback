<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="clasificacion_cuenta_resuls-table">
            <thead>
            <tr>
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Origen</th>
                <th>Grupo</th>
                <th>Nivel 1</th>
                <th>Nivel 2</th>
                <th>Nivel 3</th>
                <th>Clasificacion Ratios Financ</th>
                <th>Clasificacion Punto Equilibrio</th>
                <th>Clasificacion Cuenta Juridica Legal</th>
                <th>Clasificacion Ebit Ebitda</th>
                <th>Clasificacion Estado Resultado</th>
                <th>Compañia</th>
                <th>Archivo</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clasificacionCuentaResuls as $clasificacionCuentaResul)
                <tr>
                    <td>{{ $clasificacionCuentaResul->cuenta }}</td>
                    <td>{{ $clasificacionCuentaResul->nombre }}</td>
                    <td>{{ $clasificacionCuentaResul->origen }}</td>
                    <td>{{ $clasificacionCuentaResul->grupo }}</td>
                    <td>{{ $clasificacionCuentaResul->nivel_1 }}</td>
                    <td>{{ $clasificacionCuentaResul->nivel_2 }}</td>
                    <td>{{ $clasificacionCuentaResul->nivel_3 }}</td>
                    <td>{{ $clasificacionCuentaResul->clasificacion_ratios_financ }}</td>
                    <td>{{ $clasificacionCuentaResul->clasificacion_punto_equilibrio }}</td>
                    <td>{{ $clasificacionCuentaResul->clasificacion_cuenta_juridica_legal }}</td>
                    <td>{{ $clasificacionCuentaResul->clasificacion_ebit_ebitda }}</td>
                    <td>{{ $clasificacionCuentaResul->clasificacion_er }}</td>
                    <td>{{ $clasificacionCuentaResul->excel?->company->razon_social }}</td>
                    <td>{{ $clasificacionCuentaResul->excel?->version }}</td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['clasificacion_cuenta_resuls.destroy', $clasificacionCuentaResul->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('clasificacion_cuenta_resuls.show', [$clasificacionCuentaResul->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('clasificacion_cuenta_resuls.edit', [$clasificacionCuentaResul->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $clasificacionCuentaResuls])
        </div>
    </div>
</div>
