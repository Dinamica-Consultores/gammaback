<!-- Mes Field -->
<div class="col-sm-12">
    {!! Form::label('mes', 'Mes:') !!}
    <p>{{ $inVentas->mes }}</p>
</div>

<!-- Ano Field -->
<div class="col-sm-12">
    {!! Form::label('ano', 'Ano:') !!}
    <p>{{ $inVentas->ano }}</p>
</div>

<!-- Sucursal Field -->
<div class="col-sm-12">
    {!! Form::label('sucursal', 'Sucursal:') !!}
    <p>{{ $inVentas->sucursal }}</p>
</div>

<!-- Codigo Analisis Field -->
<div class="col-sm-12">
    {!! Form::label('codigo_analisis', 'Codigo Analisis:') !!}
    <p>{{ $inVentas->codigo_analisis }}</p>
</div>

<!-- Cantidad Venta Unidades Field -->
<div class="col-sm-12">
    {!! Form::label('cantidad_venta_unidades', 'Cantidad Venta Unidades:') !!}
    <p>{{ $inVentas->cantidad_venta_unidades }}</p>
</div>

<!-- Cantidad Costo Field -->
<div class="col-sm-12">
    {!! Form::label('cantidad_costo', 'Cantidad Costo:') !!}
    <p>{{ $inVentas->cantidad_costo }}</p>
</div>

<!-- Cantidad Margen Field -->
<div class="col-sm-12">
    {!! Form::label('cantidad_margen', 'Cantidad Margen:') !!}
    <p>{{ $inVentas->cantidad_margen }}</p>
</div>

<!-- Ventas Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('ventas_uyu', 'Ventas Uyu:') !!}
    <p>{{ $inVentas->ventas_uyu }}</p>
</div>

<!-- Ganancia Bruta Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('ganancia_bruta_uyu', 'Ganancia Bruta Uyu:') !!}
    <p>{{ $inVentas->ganancia_bruta_uyu }}</p>
</div>
<!-- Ventas Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('ventas_uyu', 'Ventas Uyu Promedio:') !!}
    <p>{{ $inVentas->ventas_uyu_prom }}</p>
</div>

<!-- Ganancia Bruta Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('ganancia_bruta_uyu', 'Ganancia Bruta Uyu Promedio:') !!}
    <p>{{ $inVentas->ganancia_bruta_uyu_prom }}</p>
</div>

<!-- Costo Uyu Field -->
<div class="col-sm-12">
    {!! Form::label('costo_uyu', 'Costo Uyu:') !!}
    <p>{{ $inVentas->costo_uyu }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $inVentas->excel->version }}</p>
</div>