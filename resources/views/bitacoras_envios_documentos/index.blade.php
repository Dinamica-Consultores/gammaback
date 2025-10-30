@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bitacoras Envios Documentos</h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
            <form action="{{ route('bitacoras_envios_documentos.index') }}" method="GET" >
                <label class="label-control">Buscar:</label>
                <div class="row mb-2">
                <div class="form-group col-sm-6">
    {!! Form::label('query', 'Empresa:') !!}
    {!! Form::select('query', $companies, request('query') , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'query' ,'name' => 'query', 'required' => false]) !!}
</div>
                <div class="form-group col-sm-6">
    {!! Form::label('query1', 'Tipo Documento:') !!}
    {!! Form::select('query1', $tipo_documentos, request('query1') , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'query1' ,'name' => 'query1', 'required' => false]) !!}
</div>
           

            </div>
              
<div class="row mb-2">
<div class="form-group col-sm-3">
    {!! Form::label('query3', 'Fecha de Vencimientos desde:') !!}
    {!! Form::text('query3', request('query3') , ['class' => 'form-control','id'=>'query3','autocomplete'=>'off']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('query4', 'Fecha de Vencimiento Hasta:') !!}
    {!! Form::text('query4', request('query4') , ['class' => 'form-control','id'=>'query4','autocomplete'=>'off']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('query5', 'Fecha de Envios desde:') !!}
    {!! Form::text('query5', request('query5') , ['class' => 'form-control','id'=>'query5','autocomplete'=>'off']) !!}
</div>
<div class="form-group col-sm-3">
    {!! Form::label('query6', 'Fecha de Envios Hasta:') !!}
    {!! Form::text('query6', request('query6') , ['class' => 'form-control','id'=>'query6','autocomplete'=>'off']) !!}
</div>
</div>
            
                <button type="submit" class="btn btn-success">Buscar</button>
        </form>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('bitacoras_envios_documentos.table')
        </div>
    </div>

@endsection
@push('page_scripts')
    <script type="text/javascript">
           var multipleCancelButton = new Choices('#query', {
            maxItemCount:1,
            itemSelectText:'Selecciona una compañia',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una compañia...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
        var multipleCancelButton2 = new Choices('#query1', {
            maxItemCount:1,
            itemSelectText:'Selecciona una Tipo Documento',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una Tipo Documento...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
        $(function () {
            // Intenta inicializar con la versión básica de Datepicker, especificando el formato
            let dtOptions = {
                dateFormat: 'dd/mm/yy', // El formato para jQuery UI Datepicker
            };
            $('#query3').datepicker(dtOptions)
        $('#query4').datepicker(dtOptions)
        $('#query5').datepicker(dtOptions)
        $('#query6').datepicker(dtOptions)
        })
        
    </script>
@endpush