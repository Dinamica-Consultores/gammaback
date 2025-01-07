<!-- Codigo Field -->
<div class="col-sm-12">
    {!! Form::label('codigo', 'Codigo:') !!}
    <p>{{ $setupAnalisis->codigo }}</p>
</div>

<!-- Nombre Field -->
<div class="col-sm-12">
    {!! Form::label('nombre', 'Nombre:') !!}
    <p>{{ $setupAnalisis->nombre }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $setupAnalisis->excel->version }}</p>
</div>