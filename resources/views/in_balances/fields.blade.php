<!-- Mes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mes', 'Mes:') !!}
    {!! Form::text('mes', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Ano Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ano', 'Ano:') !!}
    {!! Form::text('ano', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Sucursal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sucursal', 'Sucursal:') !!}
    {!! Form::text('sucursal', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Cuenta Master Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cuenta_master', 'Cuenta Master:') !!}
    {!! Form::text('cuenta_master', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Saldo Uyu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('saldo_uyu', 'Saldo Uyu:') !!}
    {!! Form::text('saldo_uyu', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Id Excel:') !!}
    {!! Form::select('id_excel', $excels, null, ['class' => 'form-control custom-select']) !!}
</div>