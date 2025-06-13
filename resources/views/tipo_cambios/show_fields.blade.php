<!-- Fecha Field -->
<div class="col-sm-12">
    {!! Form::label('fecha', 'Fecha:') !!}
    <p>{{ $tipoCambio->fecha }}</p>
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