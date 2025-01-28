<!-- Descripcion Field -->
<div class="col-sm-12">
    {!! Form::label('descripcion', 'Descripcion:') !!}
    <p>{{ $bitacora->descripcion }}</p>
</div>

<!-- Id Grupoeconomico Field -->
<div class="col-sm-12">
    {!! Form::label('id_grupoeconomico', 'Red Comercial:') !!}
    <p>{{ $bitacora->grupoeconomicos->nombre }}</p>
</div>

<!-- Persona Agrega Field -->
<div class="col-sm-12">
    {!! Form::label('persona_agrega', 'Persona Agrega:') !!}
    <p>{{ $bitacora->persona_agrega }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-12">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $bitacora->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-12">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $bitacora->updated_at }}</p>
</div>

