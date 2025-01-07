
<!-- Logo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('file', 'Archivo Excel:') !!}
    {!! Form::file('file', $attributes = array('accept' => '.xls , .xlsx' ),['class' => 'form-control']) !!}
</div>

<!-- Version Field -->
<div class="form-group col-sm-6">
    {!! Form::label('version', 'Version:') !!}
    {!! Form::text('version', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>

<!-- Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date', 'Fecha de Datos:') !!}
    {!! Form::text('date', null, ['class' => 'form-control','id'=>'date','autocomplete'=>'off']) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#date').datepicker()
    </script>
@endpush

<!-- Id Company Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_company', 'Compania Ligada:') !!}
    {!! Form::select('id_company', $company, null, ['class' => 'form-control custom-select']) !!}
</div>