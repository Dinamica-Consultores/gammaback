<!-- Cuenta Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cuenta', 'Cuenta:') !!}
    {!! Form::text('cuenta', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre', 'Nombre:') !!}
    {!! Form::text('nombre', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Origen Field -->
<div class="form-group col-sm-6">
    {!! Form::label('origen', 'Origen:') !!}
    {!! Form::text('origen', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Nivel 1 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nivel_1', 'Nivel 1:') !!}
    {!! Form::text('nivel_1', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Nivel 2 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nivel_2', 'Nivel 2:') !!}
    {!! Form::text('nivel_2', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Nivel 3 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nivel_3', 'Nivel 3:') !!}
    {!! Form::text('nivel_3', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Nivel 4 Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nivel_4', 'Nivel 4:') !!}
    {!! Form::text('nivel_4', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Posicion Moneda Field -->
<div class="form-group col-sm-6">
    {!! Form::label('posicion_moneda', 'Posicion Moneda:') !!}
    {!! Form::text('posicion_moneda', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Posicion Fiscal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('posicion_fiscal', 'Posicion Fiscal:') !!}
    {!! Form::text('posicion_fiscal', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Posicion Fiscal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('posicion_socios', 'Posicion Socios:') !!}
    {!! Form::text('posicion_socios', null, ['class' => 'form-control', 'required']) !!}
</div>
<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Version:') !!}
    {!! Form::select('id_excel', $excels, null, ['class' => 'form-control custom-select']) !!}
</div>