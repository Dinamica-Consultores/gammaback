<!-- Fecha Field -->
<div class="col-sm-12">
    {!! Form::label('fecha', 'Fecha:') !!}
    <p>{{ $tipoCambio->fecha }}</p>
</div>

<!-- Dolar Compra Field -->
<div class="col-sm-12">
    {!! Form::label('dolar_compra', 'Dolar Compra:') !!}
    <p>{{ $tipoCambio->dolar_compra }}</p>
</div>

<!-- Dolar Venta Field -->
<div class="col-sm-12">
    {!! Form::label('dolar_venta', 'Dolar Venta:') !!}
    <p>{{ $tipoCambio->dolar_venta }}</p>
</div>

<!-- Dolar Promedio Field -->
<div class="col-sm-12">
    {!! Form::label('dolar_promedio', 'Dolar Promedio:') !!}
    <p>{{ $tipoCambio->dolar_promedio }}</p>
</div>

<!-- Dolar Promedio Field -->
<div class="col-sm-12">
    {!! Form::label('euro_promedio', 'Euro Promedio:') !!}
    <p>{{ $tipoCambio->euro_promedio }}</p>
</div>

<!-- Dolar Promedio Field -->
<div class="col-sm-12">
    {!! Form::label('francosuizo_promedio', 'Franco Suizo Promedio:') !!}
    <p>{{ $tipoCambio->francosuizo_promedio }}</p>
</div>

<!-- Dolar Promedio Field -->
<div class="col-sm-12">
    {!! Form::label('ui', 'UI:') !!}
    <p>{{ $tipoCambio->ui }}</p>
</div>

<!-- Dolar Promedio Field -->
<div class="col-sm-12">
    {!! Form::label('ipc', 'IPC:') !!}
    <p>{{ $tipoCambio->ipc }}</p>
</div>

<!-- Dolar Promedio Field -->
<div class="col-sm-12">
    {!! Form::label('ipc_empresa', 'IPC Empresa:') !!}
    <p>{{ $tipoCambio->ipc_empresa }}</p>
</div>

<!-- Id Excel Field -->
<div class="col-sm-12">
    {!! Form::label('id_excel', 'Version Excel:') !!}
    <p>{{ $tipoCambio->excel->version }}</p>
</div>