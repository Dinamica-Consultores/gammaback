
<!-- Dolar Compra Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dolar_compra', 'Dolar Compra:') !!}
    {!! Form::number('dolar_compra', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

<!-- Dolar Venta Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dolar_venta', 'Dolar Venta:') !!}
    {!! Form::number('dolar_venta', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

<!-- Dolar Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dolar_promedio', 'Dolar Promedio:') !!}
    {!! Form::number('dolar_promedio', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

<!-- Euro Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('euro_promedio', 'Euro Promedio:') !!}
    {!! Form::number('euro_promedio', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

<!-- Francosuizo Promedio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('francosuizo_promedio', 'Francosuizo Promedio:') !!}
    {!! Form::number('francosuizo_promedio', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

<!-- Ui Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ui', 'Ui:') !!}
    {!! Form::number('ui', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

<!-- Ipc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ipc', 'Ipc:') !!}
    {!! Form::number('ipc', null, ['class' => 'form-control', 'required', 'step' => '0.00000001']) !!}
</div>

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