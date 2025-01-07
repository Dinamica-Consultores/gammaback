<!-- Mes Field -->
<div class="col-sm-12">
    {!! Form::label('mes', 'Mes:') !!}
    <p>{{ $inBalance->mes }}</p>
</div>

<!-- Ano Field -->
<div class="col-sm-12">
    {!! Form::label('ano', 'Ano:') !!}
    <p>{{ $inBalance->ano }}</p>
</div>

<!-- Sucursal Field -->
<div class="col-sm-12">
    {!! Form::label('sucursal', 'Sucursal:') !!}
    <p>{{ $inBalance->sucursal }}</p>
</div>

<!-- Cuenta Master Field -->
<div class="col-sm-12">
    {!! Form::label('cuenta_master', 'Cuenta Master:') !!}
    <p>{{ $inBalance->cuenta_master }}</p>
</div>

<!-- Saldo Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('saldo_uyu', 'Saldo Uyu:') !!}
    <p>{{ $inBalance->saldo_uyu }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $inBalance->excel->version }}</p>
</div>

