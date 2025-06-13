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