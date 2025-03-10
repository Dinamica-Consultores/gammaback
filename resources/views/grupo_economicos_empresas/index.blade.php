@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Grupo Red Comercial y Empresas</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('grupo_economicos_empresas.create') }}">
                       Nuevo
                    </a>
                </div>
            </div>
            <form action="{{ route('grupo_economicos_empresas.index') }}" method="GET" >
                <label class="label-control">Buscar:</label>
                <input type="text" name="query" class="form-control" placeholder="Red Comercial">
            
                <button type="submit" class="btn btn-success">buscar</button>
        </form>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('grupo_economicos_empresas.table')
        </div>
    </div>

@endsection
