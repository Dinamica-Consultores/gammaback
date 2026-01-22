@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Telemetria de usos</h1>
                </div>
            </div>
        </div>
        <form action="{{ route('sessiones.index') }}" method="GET" >
                <label class="label-control">Buscar:</label>
                <div class="row mb-2">
                <div class="form-group col-sm-6">
    {!! Form::label('query', 'Usuario:') !!}
    {!! Form::select('query', $users, request('query') , ['class' => 'form-control custom-select ','multiple'=>'multiple' ,'id' => 'query' ,'name' => 'query']) !!}
</div>
                <div class="form-group col-sm-6">
    {!! Form::label('query1', 'Tarea:') !!}
{!! Form::select('query1', [
    '' => 'Seleccione una acción...',
    'Login' => 'Login',
    'Logout' => 'Logout',
    'Cambio de compañias' => 'Cambio de compañías',
    'Cambio de Grupo Economico' => 'Cambio de Grupo Económico'
], request('query1'), ['class' => 'form-control', 'id' => 'query1','name' => 'query1']) !!}
</div>
            

            </div>
              
<div class="row mb-2">
<div class="form-group col-sm-6">
    {!! Form::label('query3', 'Fecha Desde:') !!}
    {!! Form::text('query3', request('query3') , ['class' => 'form-control','id'=>'query3','autocomplete'=>'off']) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('query4', 'Fecha Hasta:') !!}
    {!! Form::text('query4', request('query4') , ['class' => 'form-control','id'=>'query4','autocomplete'=>'off']) !!}
</div>
</div>
            
                <button type="submit" class="btn btn-success">buscar</button>
        </form>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('sessiones.table')
        </div>
    </div>

@endsection
@push('page_scripts')
    <script type="text/javascript">
           var multipleCancelButton = new Choices('#query', {
            maxItemCount:1,
            itemSelectText:'Selecciona un usuario',  
            placeholder: true, // ¡Cambia esto a true!
            placeholderValue: 'Selecciona una usuario...',
            removeItemButton: true,
            singleModeForMultiSelect:true,
            searchResultLimit:5,
            renderChoiceLimit:5,
        })
        $('#query3').datepicker()
        $('#query4').datepicker()
    </script>
@endpush