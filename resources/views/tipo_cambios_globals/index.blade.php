@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-4">
                    <h1>Tipo Cambios Globales</h1>
                </div>
                <div class="col-sm-4" class="float-left">
                   <form action="{{ route('tipo_cambios_global.uploadfile') }}" method="POST" enctype="multipart/form-data">
    @csrf
 {!! Form::label('file', 'Archivo Excel de Envio Global:') !!}
    {!! Form::file('file', $attributes = array('accept' => '.xls , .xlsx' ),['class' => 'form-control']) !!}
                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
</form>
                </div>
                <div class="col-sm-4">
                    <a class="btn btn-primary float-right"
                       href="{{ route('tipo_cambios_globals.create') }}">
                        Nuevo
                    </a>
                </div>
                  
                

            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('tipo_cambios_globals.table')
        </div>
    </div>

@endsection
