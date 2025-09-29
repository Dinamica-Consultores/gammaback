<!-- Fecha Entrega Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fecha_entrega', 'Fecha Entrega:') !!}
    {!! Form::text('fecha_entrega', null, ['class' => 'form-control','id'=>'date','autocomplete'=>'off']) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('fecha_reunion', 'Fecha Reunion:') !!}
    {!! Form::text('fecha_reunion', null, ['class' => 'form-control','id'=>'date2','autocomplete'=>'off']) !!}
</div>
<!-- Id Company Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_company', 'Compañia:') !!}
    {!! Form::select('id_company', $company, null , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'id_company' ,'name' => 'id_company', 'required' => true]) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('usuario', 'Usuario Ligado:') !!}
    {!! Form::select('usuario', $usuariosdata, null , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'usuario' ,'name' => 'usuario', 'required' => true]) !!}
</div>
<!-- Descripcion Entrega Field -->
<div class="form-group col-sm-6">
    {!! Form::label('descripcion_entrega', 'Descripcion Entrega:') !!}
    {!! Form::text('descripcion_entrega', null, ['class' => 'form-control']) !!}
</div>
@push('page_scripts')
    <script type="text/javascript">
        $('#date').datepicker()
        $('#date2').datepicker()
        
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
        var multipleCancelButton = new Choices('#usuario', {
            maxItemCount:1,
            itemSelectText:'Selecciona un item',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona un usuario...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
        
    </script>
@endpush
