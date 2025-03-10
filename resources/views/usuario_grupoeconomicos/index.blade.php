@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Usuario y Red Comercial</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('usuario_grupoeconomicos.create') }}">
                       Agregar
                    </a>
                </div>
            </div>
            <form action="{{ route('usuario_grupoeconomicos.index') }}" method="GET" >
                <label class="label-control">Buscar:</label>
                <input type="text" name="query" class="form-control" placeholder="Correo del usuario">
            
                <button type="submit" class="btn btn-success">buscar</button>
        </form>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('usuario_grupoeconomicos.table')
        </div>
    </div>

@endsection
