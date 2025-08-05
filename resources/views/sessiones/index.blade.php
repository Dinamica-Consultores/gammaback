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
                <div class="form-group col-sm-4">
    {!! Form::label('query', 'Correo:') !!}
    {!! Form::text('query',  request('query') , ['class' => 'form-control']) !!}
</div>
                <div class="form-group col-sm-4">
    {!! Form::label('query1', 'Accion:') !!}
    {!! Form::text('query1',  request('query1') , ['class' => 'form-control']) !!}
</div>
                <div class="form-group col-sm-4">
    {!! Form::label('query3', 'Fecha:') !!}
    {!! Form::text('query3', request('query3') , ['class' => 'form-control','id'=>'query3','autocomplete'=>'off']) !!}
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
        $('#query3').datepicker()
    </script>
@endpush