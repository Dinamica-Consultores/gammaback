<!-- Id Documento Companias Field -->
<div class="col-sm-12">
    {!! Form::label('id_documento_companias', 'Id Documento Companias:') !!}
    <p>{{ $bitacorasEnviosDocumento->id_documento_companias }}</p>
</div>

<!-- Fecha Envio Field -->
<div class="col-sm-12">
    {!! Form::label('fecha_envio', 'Fecha Envio:') !!}
    <p>{{ $bitacorasEnviosDocumento->fecha_envio }}</p>
</div>

<!-- Correos Field -->
<div class="col-sm-12">
    {!! Form::label('correos', 'Correos:') !!}
    <p>{{ $bitacorasEnviosDocumento->correos }}</p>
</div>

<!-- Texto Data Field -->
<div class="col-sm-12">
    {!! Form::label('texto_data', 'Texto Data:') !!}
    <p>{{ $bitacorasEnviosDocumento->texto_data }}</p>
</div>

