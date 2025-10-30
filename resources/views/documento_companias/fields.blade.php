<!-- Nombre Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nombre', 'Nombre:') !!}
    {!! Form::text('nombre', null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('file', 'Archivo Excel:') !!}
    {!! Form::file('file', $attributes = array( ),['class' => 'form-control']) !!}
</div>
<!-- Id Tipodocumento Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_tipodocumento', 'Tipo documento:') !!}
    {!! Form::select('id_tipodocumento', $tipo_documentos, null , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'id_tipodocumento' ,'name' => 'id_tipodocumento', 'required' => true]) !!}
</div>

<!-- Id Compania Field -->
<div class="form-group col-sm-6">
    {!! Form::label('id_compania', 'Compañia:') !!}  
    {!! Form::select('id_compania', $companies, null , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'id_compania' ,'name' => 'id_compania', 'required' => true]) !!}

</div>
<div class="form-group col-sm-6">
    {!! Form::label('fecha_de_generacion', 'Fecha De Generacion:') !!}
    <div class="input-group">
        {!! Form::text('fecha_de_generacion', isset($documentoCompania) ? \Carbon\Carbon::parse($documentoCompania->fecha_de_generacion)->format('Y-m-d') : null, ['class' => 'form-control', 'id' => 'fecha_de_generacion']) !!}
        <div class="input-group-append">
            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
        </div>
    </div>
</div>

<div class="form-group col-sm-6">
    {!! Form::label('fecha_de_vencimiento', 'Fecha De Vencimiento:') !!}
    <div class="input-group">
        {!! Form::text(
            'fecha_de_vencimiento', 
            isset($documentoCompania) ? \Carbon\Carbon::parse($documentoCompania->fecha_de_vencimiento)->format('Y-m-d') : null, 
            ['class' => 'form-control', 'id' => 'fecha_de_vencimiento']
        ) !!}
        <div class="input-group-append">
            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
        </div>
    </div>
</div>
@push('page_scripts')
    <script type="text/javascript">
        $(function () {
            // Intenta inicializar con la versión básica de Datepicker, especificando el formato
            let dtOptions = {
                dateFormat: 'yy-mm-dd', // El formato para jQuery UI Datepicker
            };
            
            $('#fecha_de_generacion').datepicker(dtOptions);
            $('#fecha_de_vencimiento').datepicker(dtOptions);
        });
    </script>
@endpush

    




@push('page_scripts')
<script type="text/javascript">
        $('#date').datepicker()
        var multipleCancelButton = new Choices('#id_tipodocumento', {
            maxItemCount:1,
            itemSelectText:'Selecciona un item',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una Tipo Documento...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
        var multipleCancelButton = new Choices('#id_compania', {
            maxItemCount:1,
            itemSelectText:'Selecciona un item',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una Tipo Documento...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
    </script>
@endpush