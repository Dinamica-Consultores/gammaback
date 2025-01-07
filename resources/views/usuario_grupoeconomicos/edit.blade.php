@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>
                        Editar Usuario por Red Comercial
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('adminlte-templates::common.errors')

        <div class="card">
        @if(isset($infoEdit))
        {!! Form::model($infoEdit, ['route' => ['usuario_grupoeconomicos.update', $infoEdit['id_users']], 'method' => 'patch']) !!}
@else
{!! Form::model($usuarioGrupoeconomico, ['route' => ['usuario_grupoeconomicos.update', $usuarioGrupoeconomico->id], 'method' => 'patch']) !!}

@endif
        
            <div class="card-body">
                <div class="row">
                    @include('usuario_grupoeconomicos.fields')
                </div>
            </div>

            <div class="card-footer">
                {!! Form::submit('Guardar', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('usuario_grupoeconomicos.index') }}" class="btn btn-default"> Cancelar </a>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection
