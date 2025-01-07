<!-- Mes Field -->
<div class="col-sm-12">
    {!! Form::label('mes', 'Mes:') !!}
    <p>{{ $inResultado->mes }}</p>
</div>

<!-- Ano Field -->
<div class="col-sm-12">
    {!! Form::label('ano', 'Ano:') !!}
    <p>{{ $inResultado->ano }}</p>
</div>

<!-- Sucursal Field -->
<div class="col-sm-12">
    {!! Form::label('sucursal', 'Sucursal:') !!}
    <p>{{ $inResultado->sucursal }}</p>
</div>

<!-- Cuenta Master Field -->
<div class="col-sm-12">
    {!! Form::label('cuenta_master', 'Cuenta Master:') !!}
    <p>{{ $inResultado->cuenta_master }}</p>
</div>

<!-- Monto Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('monto_uyu', 'Monto Uyu:') !!}
    <p>{{ $inResultado->monto_uyu }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $inResultado->excel->version }}</p>
</div>