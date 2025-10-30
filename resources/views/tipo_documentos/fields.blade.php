<!-- Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre', 'Nombre:') !!}
    {!! Form::text('nombre', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Cantidad Dias Preaviso Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cantidad_dias_preaviso', 'Cantidad Dias Preaviso:') !!}
    {!! Form::number('cantidad_dias_preaviso', null, ['class' => 'form-control', 'required', 'min' => 0]) !!}
</div>
