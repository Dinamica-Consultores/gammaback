
            @php
    use Carbon\Carbon;
@endphp
<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $documentoCompania->nombre }}</p>
</div>

<!-- Fecha De Vencimiento Field -->
<div class="col-sm-12">
    {!! Form::label('fecha_de_vencimiento', 'Fecha De Vencimiento:') !!}
    <p>{{ Carbon::parse($documentoCompania->fecha_de_vencimiento)->format('d/m/Y')}}</p>
</div>

<!-- Fecha De Generacion Field -->
<div class="col-sm-12">
    {!! Form::label('fecha_de_generacion', 'Fecha De Generacion:') !!}
    <p>{{Carbon::parse($documentoCompania->fecha_de_generacion)->format('d/m/Y') }}</p>
</div>

<!-- Id Tipodocumento Field -->
<div class="col-sm-12">
    {!! Form::label('id_tipodocumento', 'Tipo Documento:') !!}
    <p>{{ $documentoCompania->tipo_documento->nombre }}</p>
</div>

<!-- Id Compania Field -->
<div class="col-sm-12">
    {!! Form::label('id_compania', 'Compañia:') !!}
    <p>{{ $documentoCompania->company->razon_social }}</p>
</div>

<!-- Url Field -->
<div class="col-sm-12">
    {!! Form::label('url', 'Documento:') !!}
    @if ($documentoCompania->url)
                        <a href="{{ route('documento_companias.download', $documentoCompania->id) }}" 
        class="btn btn-info btn-xs"
        title="Descargar {{ $documentoCompania->nombre }}">
        <i class="fas fa-download"></i> 
    </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
</div>

