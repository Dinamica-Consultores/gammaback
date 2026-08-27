<!-- Razon Social Field -->
<div class="form-group col-sm-6">
    {!! Form::label('razon_social', 'Razon Social:') !!}
    {!! Form::text('razon_social', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>

<!-- Per Cont Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('per_cont_name', 'Nombre de la Persona de Contacto:') !!}
    {!! Form::text('per_cont_name', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>

<!-- Per Cont Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('per_cont_email', 'Correo de la Persona de Contacto:') !!}
    {!! Form::text('per_cont_email', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>

<!-- Per Cont Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('per_cont_phone', 'Telefono de la Persona de Contacto:') !!}
    {!! Form::text('per_cont_phone', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255, 'id' => 'per_cont_phone']) !!}
</div>

<!-- Responsable Field -->
<div class="form-group col-sm-6">
    {!! Form::label('responsable', 'Responsable de Cliente:') !!}
    {!! Form::text('responsable', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>

<!-- Año Fiscal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ano', 'Año Fiscal Inicio:') !!}
    {!! Form::text('ano', null, ['class' => 'form-control', 'required', 'minlength' => 1, 'maxlength' => 255]) !!}
</div>

<!-- Mes Fiscal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mes', 'Mes Fiscal Inicio:') !!}
    {!! Form::select('mes', ['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'], null, ['class' => 'form-control custom-select', 'required']) !!}
</div>

<!-- Moneda Field -->
<div class="form-group col-sm-12">
    {!! Form::label('id_moneda', 'Moneda Funcional:') !!}
    {!! Form::select('id_moneda', ['0'=>'Pesos Uruguayo','1'=>'Dolares Americanos'], null, ['class' => 'form-control custom-select', 'required']) !!}
</div>

<!-- Checkboxes -->
<div class="form-group col-sm-6">
    {!! Form::label('ispresupuesto', 'Presupuesto:') !!}
    {!! Form::checkbox('ispresupuesto', '1') !!}
</div>

<div class="form-group col-sm-6">
    {!! Form::label('isestadosp', 'Estado situacion Patrimonial:') !!}
    {!! Form::checkbox('isestadosp', '1') !!}
</div>

<!-- Logo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('logo', 'Logo:') !!}
    {!! Form::file('logo', ['class' => 'form-control']) !!}
</div>

<!-- Destinatario Documentos Field -->
<div class="form-group col-sm-6">
    {!! Form::label('destinatario_documentos', 'Vencimiento de Documentos - E-mail de responsables a notificar:') !!}
    {!! Form::textarea('destinatario_documentos', null, ['class' => 'form-control', 'rows' => 2]) !!}
    <small id="emailHelp" class="form-text text-muted">
        Ingresar los correos separados por comas. Ejemplo: email1@empresa.com, email2@empresa.com
    </small>
</div>

<!-- Información Cliente Field -->
<div class="form-group col-sm-6">
    {!! Form::label('campo', 'Información de cliente:') !!}
    {!! Form::textarea('campo', null, ['class' => 'form-control', 'rows' => 3]) !!}
</div>

<!-- Sección Ambiente Fiscal -->
<div class="col-sm-12 mt-4 mb-2">
    <h4 class="text-primary"><strong>Ambiente Fiscal</strong></h4>
    <hr>
</div>

<!-- Cuenta de Anticipos de I.Pat. Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cuenta_anticipos_ipat', 'Cuenta de Anticipos de I.Pat.:') !!}
    {!! Form::text('cuenta_anticipos_ipat', null, ['class' => 'form-control', 'placeholder' => 'Ingrese cuenta del plan contable']) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('cuenta_anticipos_irae', 'Cuenta de Anticipos de I.Rae.:') !!}
    {!! Form::text('cuenta_anticipos_irae', null, ['class' => 'form-control', 'placeholder' => 'Ingrese cuenta del plan contable']) !!}
</div>

<!-- % IRAE Field -->
<div class="form-group col-sm-3">
    {!! Form::label('porcentaje_irae', '% IRAE:') !!}
    {!! Form::number('porcentaje_irae', old('porcentaje_irae', $company->porcentaje_irae ?? 25.00), ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required']) !!}
</div>

<!-- % IPat Field -->
<div class="form-group col-sm-3">
    {!! Form::label('porcentaje_ipat', '% IPat:') !!}
    {!! Form::number('porcentaje_ipat', old('porcentaje_ipat', $company->porcentaje_ipat ?? 1.50), ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'required']) !!}
</div>

@push('page_scripts')
<script>
$(document).ready(function(){
    const input = document.querySelector("#per_cont_phone");
    window.intlTelInput(input, {
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js",
        countrySearch: false,
        initialCountry: "UY",
        nationalMode: true,
        strictMode: true,
        onlyCountries: ['UY']
    });
});
</script>
@endpush