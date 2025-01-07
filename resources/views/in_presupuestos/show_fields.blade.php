<!-- Mes Field -->
<div class="col-sm-12">
    {!! Form::label('mes', 'Mes:') !!}
    <p>{{ $inPresupuestos->mes }}</p>
</div>

<!-- Ano Field -->
<div class="col-sm-12">
    {!! Form::label('ano', 'Ano:') !!}
    <p>{{ $inPresupuestos->ano }}</p>
</div>

<!-- Sucursal Field -->
<div class="col-sm-12">
    {!! Form::label('sucursal', 'Sucursal:') !!}
    <p>{{ $inPresupuestos->sucursal }}</p>
</div>

<!-- Cuenta Master Field -->
<div class="col-sm-12">
    {!! Form::label('cuenta_master', 'Cuenta Master:') !!}
    <p>{{ $inPresupuestos->cuenta_master }}</p>
</div>

<!-- Monto Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('monto_uyu', 'Monto Uyu:') !!}
    <p>{{ $inPresupuestos->monto_uyu }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $inPresupuestos->excel->version }}</p>
</div>

