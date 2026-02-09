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
$(document).ready(function() {
        // Tu código de inicialización del datepicker
        $('#date').datepicker({
            format: "dd/mm/yyyy",
            language: "es",
            autoclose: true
        });

        $('#date2').datepicker({
            format: "dd/mm/yyyy",
            language: "es",
            autoclose: true
        });
        
    });
       var multipleCancelButton = new Choices('#id_company', {
            maxItemCount:1,
            itemSelectText:'Selecciona un compañía',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una compañía...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
          var multipleCancelButton2 = new Choices('#usuario', {
            maxItemCount:1,
            itemSelectText:'Selecciona un usuario',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona un usuario...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
       function cargarUsuarios(idCompany, idUsuarioSeleccionado = null) {
        multipleCancelButton2.clearStore(); 

        if (idCompany && idCompany.length > 0) {
            fetch(`/userCompany/${idCompany}`)
                .then(response => response.json())
                .then(data => {
                    const usersMap = data.map(user => ({
                        value: user.id,
                        label: user.nombre_completo + " (" + user.email + ")",
                        selected: (idUsuarioSeleccionado && user.id == idUsuarioSeleccionado) ? true : false,
                        disabled: false
                    }));

                    multipleCancelButton2.setChoices(usersMap, 'value', 'label', true);
                })
                .catch(error => console.error('Error:', error));
        }
    }

    $('#id_company').on('change', function() {
        cargarUsuarios($(this).val());
    });

    // --- CARGA INICIAL (MODO EDICIÓN) ---
    const idCompanyInicial = $('#id_company').val();
    const idUsuarioInicial = $('#usuario').val(); 

    if (idCompanyInicial) {
        cargarUsuarios(idCompanyInicial, idUsuarioInicial);
    }
      

        
    </script>
@endpush
