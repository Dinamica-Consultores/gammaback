<!-- Descripcion Field -->
<div class="col-sm-3">
    {!! Form::label('descripcion', 'Descripcion:') !!}
    <p>{{ $bitacora->descripcion }}</p>
</div>


<!-- Persona Agrega Field -->
<div class="col-sm-3">
    {!! Form::label('persona_agrega', 'Persona Agrega:') !!}
    <p>{{ $bitacora->persona_agrega }}</p>
</div>

<!-- Created At Field -->
<div class="col-sm-3">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{{ $bitacora->created_at }}</p>
</div>

<!-- Updated At Field -->
<div class="col-sm-3">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{{ $bitacora->updated_at }}</p>
</div>

