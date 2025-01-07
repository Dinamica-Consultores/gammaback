<!-- Cuenta Field -->
<div class="col-sm-12">
    {!! Form::label('cuenta', 'Cuenta:') !!}
    <p>{{ $categorizacionCtsBalance->cuenta }}</p>
</div>

<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $categorizacionCtsBalance->nombre }}</p>
</div>

<!-- Origen Field -->
<div class="col-sm-12">
    {!! Form::label('origen', 'Origen:') !!}
    <p>{{ $categorizacionCtsBalance->origen }}</p>
</div>

<!-- Nivel 1 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_1', 'Nivel 1:') !!}
    <p>{{ $categorizacionCtsBalance->nivel_1 }}</p>
</div>

<!-- Nivel 2 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_2', 'Nivel 2:') !!}
    <p>{{ $categorizacionCtsBalance->nivel_2 }}</p>
</div>

<!-- Nivel 3 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_3', 'Nivel 3:') !!}
    <p>{{ $categorizacionCtsBalance->nivel_3 }}</p>
</div>

<!-- Nivel 4 Field -->
<div class="col-sm-12">
    {!! Form::label('nivel_4', 'Nivel 4:') !!}
    <p>{{ $categorizacionCtsBalance->nivel_4 }}</p>
</div>

<!-- Posicion Moneda Field -->
<div class="col-sm-12">
    {!! Form::label('posicion_moneda', 'Posicion Moneda:') !!}
    <p>{{ $categorizacionCtsBalance->posicion_moneda }}</p>
</div>

<!-- Posicion Fiscal Field -->
<div class="col-sm-12">
    {!! Form::label('posicion_fiscal', 'Posicion Fiscal:') !!}
    <p>{{ $categorizacionCtsBalance->posicion_fiscal }}</p>
</div>
<!-- Posicion Fiscal Field -->
<div class="col-sm-12">
    {!! Form::label('posicion_socios', 'Posicion Socios:') !!}
    <p>{{ $categorizacionCtsBalance->posicion_socios }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $categorizacionCtsBalance->excel->version }}</p>
</div>