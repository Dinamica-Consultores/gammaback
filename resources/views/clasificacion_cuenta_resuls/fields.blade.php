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
<!-- Grupo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('origen', 'Origen:') !!}
    {!! Form::text('origen', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Grupo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('grupo', 'Grupo:') !!}
    {!! Form::text('grupo', null, ['class' => 'form-control', 'required']) !!}
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

<!-- Clasificacion Ratios Financ Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clasificacion_ratios_financ', 'Clasificacion Ratios Financ:') !!}
    {!! Form::text('clasificacion_ratios_financ', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Clasificacion Punto Equilibrio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clasificacion_punto_equilibrio', 'Clasificacion Punto Equilibrio:') !!}
    {!! Form::text('clasificacion_punto_equilibrio', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Clasificacion Cuenta Juridica Legal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clasificacion_cuenta_juridica_legal', 'Clasificacion Cuenta Juridica Legal:') !!}
    {!! Form::text('clasificacion_cuenta_juridica_legal', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Clasificacion Ebit Ebitda Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clasificacion_ebit_ebitda', 'Clasificacion Ebit Ebitda:') !!}
    {!! Form::text('clasificacion_ebit_ebitda', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Clasificacion Ebit Ebitda Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clasificacion_er', 'Clasificacion Estado Resultado:') !!}
    {!! Form::text('clasificacion_er', null, ['class' => 'form-control', 'required']) !!}
</div>
<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Id Excel:') !!}
    {!! Form::select('id_excel', $excels, null, ['class' => 'form-control custom-select']) !!}
</div>