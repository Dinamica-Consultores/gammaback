@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        Editar Tipo Cambios Globales
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">

            {!! Form::model($tipoCambiosGlobal, ['route' => ['tipo_cambios_globals.update', $tipoCambiosGlobal->id], 'method' => 'patch']) !!}

            <div class="card-body">
                <div class="row">
                    @include('tipo_cambios_globals.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('tipo_cambios_globals.index') }}" class="btn btn-default"> Cancelar</a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
