<!-- Razon Social Field -->
<div class="col-sm-12">
    {!! Form::label('razon_social', 'Razon Social:') !!}
    <p>{{ $company->razon_social }}</p>
</div>

<!-- Per Cont Name Field -->
<div class="col-sm-12">
    {!! Form::label('per_cont_name', 'Nombre de la Persona de Contacto:') !!}
    <p>{{ $company->per_cont_name }}</p>
</div>

<!-- Per Cont Email Field -->
<div class="col-sm-12">
    {!! Form::label('per_cont_email', 'Correo de la Persona de Contacto:') !!}
    <p>{{ $company->per_cont_email }}</p>
</div>

<!-- Per Cont Phone Field -->
<div class="col-sm-12">
    {!! Form::label('per_cont_phone', 'Telefono de la Persona de Contacto:') !!}
    <p>{{ $company->per_cont_phone }}</p>
</div>

<!-- Logo Field -->
<div class="col-sm-12">
    {!! Form::label('logo', 'Logo:') !!}
    <img src="{{ asset('storage/'.$company->logo) }}" style="width:50px;height:50px;"/>

</div>

<!-- Campo Field -->
<div class="col-sm-12">
    {!! Form::label('campo', 'Campo Facturacion:') !!}
    <p>{{ $company->campo }}</p>
</div>




