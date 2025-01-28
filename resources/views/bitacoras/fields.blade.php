<!-- Descripcion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('descripcion', 'Descripcion:') !!}
    {!! Form::text('descripcion', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Id Grupoeconomico Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_grupoeconomico', 'Red Comercial:') !!}
    {!! Form::select('id_grupoeconomico', $grupoEconomico, null, ['class' => 'form-control custom-select']) !!}
</div>

<!-- Persona Agrega Field -->
<div class="form-group col-sm-6">
    {!! Form::label('persona_agrega', 'Persona Agrega:') !!}
    {!! Form::text('persona_agrega', null, ['class' => 'form-control', 'required']) !!}
</div>