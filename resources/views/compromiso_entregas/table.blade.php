<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="compromiso_entregas-table">
            <thead>
            <tr>
                <th>Fecha Reunion</th>
                <th>Fecha Entrega</th>
                <th>Descripcion Entrega</th>
                <th>Usuario Encargado</th>
                <th>Compañia</th>
                <th>Fecha Entregado</th>
                <th>Descripcion Entregado</th>
                <th>Dias de Retraso</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($compromisoEntregas as $compromisoEntrega)
                <tr>
                    <td>{{ $compromisoEntrega->fecha_reunion->format('d/m/Y') }}</td>
                    <td>{{ $compromisoEntrega->fecha_entrega->format('d/m/Y') }}</td>
                    <td>{{ $compromisoEntrega->descripcion_entrega }}</td>
                    <td>{{ $compromisoEntrega->user->email }}</td>
                    <td>{{ $compromisoEntrega->company?->razon_social }}</td>
                    <td>{{ $compromisoEntrega->fecha_entregado?->format('d/m/Y') }}</td>
                    <td>{{ $compromisoEntrega->descripcion_entregado }}</td>
                   
@if(isset($compromisoEntrega->fecha_entregado))
@php
    $fechaInicio = $compromisoEntrega->fecha_entrega;
    $fechaFin = $compromisoEntrega->fecha_entregado;
    $diasDiferencia = $fechaInicio->diffInDays($fechaFin, false);
@endphp
<td>{{ $diasDiferencia }}</td>
@else

<td>0</td>
@endif
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['compromiso_entregas.destroy', $compromisoEntrega->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('compromiso_entregas.show', [$compromisoEntrega->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            @if (!isset($compromisoEntrega->fecha_entregado) )
                            <a href="{{ route('compromiso_entregas.edit', [$compromisoEntrega->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-edit"></i>
                            </a>
                            {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
              
                            @endif
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
            @include('adminlte-templates::common.paginate', ['records' => $compromisoEntregas])
        </div>
    </div>
</div>
