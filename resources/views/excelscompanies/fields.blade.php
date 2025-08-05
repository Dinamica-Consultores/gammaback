
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

<!-- Id Company Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_company', 'Compania Ligada:') !!}
    {!! Form::select('id_company', $company, null , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'id_company' ,'name' => 'id_company', 'required' => true]) !!}
</div>
@push('page_scripts')
    <script type="text/javascript">
        $('#date').datepicker()
        var multipleCancelButton = new Choices('#id_company', {
            maxItemCount:1,
            itemSelectText:'Selecciona un item',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una compañía...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
        
        
    </script>
@endpush
