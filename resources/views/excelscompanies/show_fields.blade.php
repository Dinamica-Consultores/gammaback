<!-- Path Field -->
<div class="col-sm-12">
    {!! Form::label('path', 'Archivo :') !!}
    <p>{{ $excelscompany->path }}</p>
</div>

<!-- Version Field -->
<div class="col-sm-12">
    {!! Form::label('version', 'Version:') !!}
    <p>{{ $excelscompany->version }}</p>
</div>

<!-- Date Field -->
<div class="col-sm-12">
    {!! Form::label('date', 'Fecha:') !!}
    <p>{{ $excelscompany->date }}</p>
</div>

<!-- Id Company Field -->
<div class="col-sm-12">
    {!! Form::label('id_company', 'Compañia:') !!}
    <p>{{ $excelscompany->company->razon_social }}</p>
</div>

