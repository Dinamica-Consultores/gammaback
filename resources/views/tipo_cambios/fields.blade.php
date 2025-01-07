<!-- Fecha Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fecha', 'Fecha:') !!}
    {!! Form::text('fecha', null, ['class' => 'form-control','id'=>'fecha']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#fecha').datepicker()
    </script>
@endpush

<!-- Dolar Compra Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dolar_compra', 'Dolar Compra:') !!}
    {!! Form::text('dolar_compra', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Venta Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dolar_venta', 'Dolar Venta:') !!}
    {!! Form::text('dolar_venta', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dolar_promedio', 'Dolar Promedio:') !!}
    {!! Form::text('dolar_promedio', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('euro_promedio', 'Euro Promedio:') !!}
    {!! Form::text('euro_promedio', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('francosuizo_promedio', 'Franco Suizo Promedio:') !!}
    {!! Form::text('francosuizo_promedio', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ui', 'UI:') !!}
    {!! Form::text('ui', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ipc', 'IPC:') !!}
    {!! Form::text('ipc', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ipc_empresa', 'IPC empresa:') !!}
    {!! Form::text('ipc_empresa', null, ['class' => 'form-control', 'required']) !!}
</div>
<!-- Id Excel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_excel', 'Id Excel:') !!}
    {!! Form::select('id_excel', $excels, null, ['class' => 'form-control custom-select']) !!}
</div>