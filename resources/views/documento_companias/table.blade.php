<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table" id="documento_companias-table">
            <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha De Vencimiento</th>
                <th>Fecha De Generacion</th>
                <th>Tipo Documento</th>
                <th>Compañia</th>
                <th>Archivo</th>
                <th colspan="3">Action</th>
            </tr>
            </thead>
            <tbody>
            @php
    use Carbon\Carbon;
@endphp
            @foreach($documentoCompanias as $documentoCompania)
                <tr>
                    <td>{{ $documentoCompania->nombre }}</td>
                    <td>{{ Carbon::parse($documentoCompania->fecha_de_vencimiento)->format('d/m/Y') }}</td>
                    <td>   {{ Carbon::parse($documentoCompania->fecha_de_generacion)->format('d/m/Y') }} </td>
                    <td>{{ $documentoCompania->tipo_documento->nombre }}</td>
                    <td>{{ $documentoCompania->company->razon_social }}</td>
                    <td>
                        @if ($documentoCompania->url)
                        <a href="{{ route('documento_companias.download', $documentoCompania->id) }}" 
        class="btn btn-info btn-xs"
        title="Descargar {{ $documentoCompania->nombre }}">
        <i class="fas fa-download"></i> 
    </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td  style="width: 120px">
                        {!! Form::open(['route' => ['documento_companias.destroy', $documentoCompania->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{{ route('documento_companias.show', [$documentoCompania->id]) }}"
                               class='btn btn-default btn-xs'>
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ route('documento_companias.edit', [$documentoCompania->id]) }}"
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
            @include('adminlte-templates::common.paginate', ['records' => $documentoCompanias])
        </div>
    </div>
</div>
