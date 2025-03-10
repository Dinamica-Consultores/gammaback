@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Compañías</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('companies.create') }}">
                        Agregar Nuevo
                    </a>
                </div>
            </div>
            <form action="{{ route('companies.index') }}" method="GET" >
                <label class="label-control">Buscar:</label>
                <input type="text" name="query" class="form-control" placeholder="Razon social o Nombre de la persona de contacto">
                <button type="submit" class="btn btn-success">buscar</button>
        </form>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('companies.table')
        </div>
    </div>

@endsection
