@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-5">
                    <h1>
                    Ver
                    </h1>
                </div>
                <div class="col-sm-5">
                    <a class="btn btn-success float-right ml-2"
                       href="{{ route('bitacora_hitos.create2',[$id2,$bitacora->id]) }}">
                                                    Agregar Hitos
                                            </a>
                                            <a class="btn btn-success float-right ml-2"
                       href="{{ route('bitacora_enviosemails.create',[$id2,$bitacora->id]) }}">
                                                    Modificar Usuarios Envios
                                            </a>
                </div>
                <div class="col-sm-2">
                    <a class="btn btn-default float-right"
                       href="{{ route('bitacoras.index2',$id2) }}">
                                                    Atras
                                            </a>
                </div>
            </div>
        </div>
    </section>

    @include('flash::message')
    <div class="content px-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('bitacoras.show_fields')
                </div>
                
            </div>
        </div>

        

        <div class="card">
        <h3 class="text-center">Hitos</h3>
            
        <div class="clearfix"></div>
            <div class="card-body">
                <div class="row">
                
            @include('bitacora_hitos.table')
                </div>
                
            </div>
        </div>
    </div>
@endsection
