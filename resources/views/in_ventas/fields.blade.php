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

<!-- Codigo Analisis Field -->
<div class="form-group col-sm-6">
    {!! Form::label('codigo_analisis', 'Codigo Analisis:') !!}
    {!! Form::text('codigo_analisis', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Cantidad Venta Unidades Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cantidad_venta_unidades', 'Cantidad Venta Unidades:') !!}
    {!! Form::text('cantidad_venta_unidades', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Cantidad Costo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cantidad_costo', 'Cantidad Costo:') !!}
    {!! Form::text('cantidad_costo', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Cantidad Margen Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cantidad_margen', 'Cantidad Margen:') !!}
    {!! Form::text('cantidad_margen', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Ventas Uyu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ventas_uyu', 'Ventas Uyu:') !!}
    {!! Form::text('ventas_uyu', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Ganancia Bruta Uyu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ganancia_bruta_uyu', 'Ganancia Bruta Uyu:') !!}
    {!! Form::text('ganancia_bruta_uyu', null, ['class' => 'form-control', 'required']) !!}
</div>
<!-- Ventas Uyu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ventas_uyu_prom', 'Ventas Uyu Promedio:') !!}
    {!! Form::text('ventas_uyu', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Ganancia Bruta Uyu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ganancia_bruta_uyu_prom', 'Ganancia Bruta Uyu Promedio:') !!}
    {!! Form::text('ganancia_bruta_uyu', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Costo Uyu Field -->
<div class="form-group col-sm-6">
    {!! Form::label('costo_uyu', 'Costo Uyu:') !!}
    {!! Form::text('costo_uyu', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Id Excel:') !!}
    {!! Form::select('id_excel', $excels, null, ['class' => 'form-control custom-select']) !!}
</div>