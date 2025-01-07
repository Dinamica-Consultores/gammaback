<!-- Cuenta Field -->
<div class="col-sm-12">
    {!! Form::label('cuenta', 'Cuenta:') !!}
    <p>{{ $clasificacionCuentaResul->cuenta }}</p>
</div>

<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $clasificacionCuentaResul->nombre }}</p>
</div>
<!-- Grupo Field -->
<div class="col-sm-12">
    {!! Form::label('origen', 'Origen:') !!}
    <p>{{ $clasificacionCuentaResul->origen }}</p>
</div>
<!-- Grupo Field -->
<div class="col-sm-12">
    {!! Form::label('grupo', 'Grupo:') !!}
    <p>{{ $clasificacionCuentaResul->grupo }}</p>
</div>

<!-- Nivel 1 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_1', 'Nivel 1:') !!}
    <p>{{ $clasificacionCuentaResul->nivel_1 }}</p>
</div>

<!-- Nivel 2 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_2', 'Nivel 2:') !!}
    <p>{{ $clasificacionCuentaResul->nivel_2 }}</p>
</div>

<!-- Nivel 3 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_3', 'Nivel 3:') !!}
    <p>{{ $clasificacionCuentaResul->nivel_3 }}</p>
</div>

<!-- Clasificacion Ratios Financ Field -->
<div class="col-sm-12">
    {!! Form::label('clasificacion_ratios_financ', 'Clasificacion Ratios Financ:') !!}
    <p>{{ $clasificacionCuentaResul->clasificacion_ratios_financ }}</p>
</div>

<!-- Clasificacion Punto Equilibrio Field -->
<div class="col-sm-12">
    {!! Form::label('clasificacion_punto_equilibrio', 'Clasificacion Punto Equilibrio:') !!}
    <p>{{ $clasificacionCuentaResul->clasificacion_punto_equilibrio }}</p>
</div>

<!-- Clasificacion Cuenta Juridica Legal Field -->
<div class="col-sm-12">
    {!! Form::label('clasificacion_cuenta_juridica_legal', 'Clasificacion Cuenta Juridica Legal:') !!}
    <p>{{ $clasificacionCuentaResul->clasificacion_cuenta_juridica_legal }}</p>
</div>

<!-- Clasificacion Ebit Ebitda Field -->
<div class="col-sm-12">
    {!! Form::label('clasificacion_ebit_ebitda', 'Clasificacion Ebit Ebitda:') !!}
    <p>{{ $clasificacionCuentaResul->clasificacion_ebit_ebitda }}</p>
</div>
<!-- Clasificacion Ebit Ebitda Field -->
<div class="col-sm-12">
    {!! Form::label('clasificacion_er', 'Clasificacion Estado resultado:') !!}
    <p>{{ $clasificacionCuentaResul->clasificacion_er }}</p>
</div>
<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $clasificacionCuentaResul->excel->version }}</p>
</div>