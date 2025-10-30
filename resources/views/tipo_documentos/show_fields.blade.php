<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $tipoDocumento->nombre }}</p>
</div>

<!-- Cantidad Dias Preaviso Field -->
<div class="col-sm-12">
    {!! Form::label('cantidad_dias_preaviso', 'Cantidad Dias Preaviso:') !!}
    <p>{{ $tipoDocumento->cantidad_dias_preaviso }}</p>
</div>
