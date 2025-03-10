@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    Usuarios
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('users.create') }}">
                        Nuevo
                    </a>
                </div>
            </div>
            <form action="{{ route('users.index') }}" method="GET" >
                <label class="label-control">Buscar: </label>
                <input type="text" name="query" class="form-control" placeholder="Nombre o apellido o email">
            
                <button type="submit" class="btn btn-success">buscar</button>
        </form>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            @include('users.table')
        </div>
    </div>

@endsection
