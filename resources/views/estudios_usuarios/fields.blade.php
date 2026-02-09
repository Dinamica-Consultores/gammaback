<!-- Id Users Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_users', 'Usuarios:') !!}
    {!! Form::select('id_users', $usuarios, null,  ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'id_users' ,'name' => 'id_users', 'required' => true]) !!}
</div>

<!-- Id Estudios Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_estudios', 'Estudio:') !!}
    {!! Form::select('id_estudios', $estudios, null, ['class' => 'form-control custom-select']) !!}
</div>
@push('page_scripts')
    <script type="text/javascript">

       var multipleCancelButton = new Choices('#id_users', {
            maxItemCount:1,
            itemSelectText:'Selecciona un Usuario',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona un Usuario...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
     
      

        
    </script>
@endpush