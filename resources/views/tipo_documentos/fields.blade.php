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
<div class="form-group col-sm-6">
        <div class="form-check">
            {!! Form::checkbox('is_one', 1, null, ['class' => 'form-check-input', 'id' => 'is_one_checkbox']) !!}
            {!! Form::label('is_one_checkbox', 'Es Unico', ['class' => 'form-check-label']) !!}
        </div>
    </div>