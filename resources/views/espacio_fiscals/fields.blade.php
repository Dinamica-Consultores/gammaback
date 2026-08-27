<!-- Ano Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ano', 'Ano:') !!}
    {!! Form::text('ano', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Mes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mes', 'Mes:') !!}
    {!! Form::text('mes', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Cuenta Contable Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nivel_3', 'Nivel 3:') !!}
    {!! Form::text('nivel_3', null, ['class' => 'form-control']) !!}
</div>

<!-- Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ajusta', 'Ajusta:') !!}
    {!! Form::text('ajusta', null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- Tipo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tipo', 'Tipo:') !!}
    {!! Form::text('tipo', null, ['class' => 'form-control', 'maxlength' => 255]) !!}
</div>

<!-- Operador Field -->
<div class="form-group col-sm-6">
    {!! Form::label('operador', 'Operador:') !!}
    {!! Form::text('operador', null, ['class' => 'form-control', 'maxlength' => 15]) !!}
</div>

<!-- Signo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('signo', 'Signo:') !!}
    {!! Form::text('signo', null, ['class' => 'form-control', 'maxlength' => 10]) !!}
</div>

<!-- Monto Valor Field -->
<div class="form-group col-sm-6">
    {!! Form::label('monto_valor', 'Monto Valor:') !!}
    {!! Form::text('monto_valor', null, ['class' => 'form-control']) !!}
</div>

<!-- Sucursal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sucursal', 'Sucursal:') !!}
    {!! Form::text('sucursal', null, ['class' => 'form-control']) !!}
</div>

<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Id Excel:') !!}
    {!! Form::select('id_excel', [], null, ['class' => 'form-control custom-select']) !!}
</div>