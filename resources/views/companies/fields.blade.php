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
    {!! Form::text('per_cont_phone', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255,'name'=>'per_cont_phone']) !!}
</div>
<!-- Per Cont Phone Field -->
<div class="form-group col-sm-6">
    {!! Form::label('responsable', 'Responsable de Cliente:') !!}
    {!! Form::text('responsable', null, ['class' => 'form-control', 'required', 'minlength' => 3, 'maxlength' => 255]) !!}
</div>
<!-- Campo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ano', 'Año Fiscal Inicio:') !!}
    {!! Form::text('ano', null, ['class' => 'form-control', 'required', 'minlength' => 1, 'maxlength' => 255]) !!}
</div>
<div class="form-group col-sm-6">
    {!! Form::label('mes', 'Mes Fiscal Inicio:') !!}
    {!! Form::select('mes', ['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'], null, ['class' => 'form-control custom-select','required']) !!}
</div>
<div class="form-group col-sm-12">
    {!! Form::label('id_moneda', 'Moneda Funcional:') !!}
    {!! Form::select('id_moneda', ['0'=>'Pesos Uruguayo','1'=>'Dolares Americanos'], null, ['class' => 'form-control custom-select','required']) !!}
</div>
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
    {!! Form::file('logo', $attributes = array(),['class' => 'form-control']) !!}
</div>

<!-- Campo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('campo', 'Información de cliente:') !!}
    {!! Form::textarea('campo', null, ['class' => 'form-control']) !!}
</div>
@push('page_scripts')
<script>
    
$(document).ready(function(){
    const input = document.querySelector("#per_cont_phone");
  window.intlTelInput(input, {
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js",
    countrySearch:false,
    initialCountry: "UY",
  nationalMode: true,
  strictMode: true,
  onlyCountries:['UY']
  });
})

</script>
@endpush