<!-- Id Documento Companias Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_documento_companias', 'Id Documento Companias:') !!}
    {!! Form::number('id_documento_companias', null, ['class' => 'form-control']) !!}
</div>

<!-- Fecha Envio Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fecha_envio', 'Fecha Envio:') !!}
    {!! Form::text('fecha_envio', null, ['class' => 'form-control','id'=>'fecha_envio']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#fecha_envio').datepicker()
    </script>
@endpush

<!-- Correos Field -->
<div class="form-group col-sm-6">
    {!! Form::label('correos', 'Correos:') !!}
    {!! Form::text('correos', null, ['class' => 'form-control']) !!}
</div>

<!-- Texto Data Field -->
<div class="form-group col-sm-6">
    {!! Form::label('texto_data', 'Texto Data:') !!}
    {!! Form::text('texto_data', null, ['class' => 'form-control']) !!}
</div>