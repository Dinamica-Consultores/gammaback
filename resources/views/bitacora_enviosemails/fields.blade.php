<!-- Id User Field -->
<div class="form-group col-sm-12">
    {!! Form::label('id_user', 'Usuarios a Enviar Hitos:') !!}
    {!! Form::select('id_user', $users, $id_user, ['class' => 'form-control custom-select','multiple'=>'multiple','name'=>'id_user[]'])!!}
    </div>
@push('page_scripts')
<script>
    var informacion={!! json_encode($users) !!};
    var todasCompaniasSelect=[]
    <?php
    if(isset($infoEdit)){
        if(count($infoEdit['id_user'])>0){
         
        ?>

todasCompaniasSelect={!! json_encode($infoEdit['id_user']) !!};
        <?php
           
        }
    }
    ?>
    function agregarOptionesNivel(listado,multipleCancelButton,todasCompaniasSelect){
    multipleCancelButton.clearChoices()
    multipleCancelButton.clearStore()
        $('#id_user').find('option').remove().end();
    listado.forEach(element => {
        $('#id_user').append($('<option>', { 
        value: element.id,
        text : element.email 
    }));
    })
    multipleCancelButton.setChoices(listado.map((element)=>{
        let selected=false
        if(todasCompaniasSelect.find((das)=>Number(das.id)===Number(element.id))){
            selected=true
        }
        return({
        value:element.id,label:element.email ,selected:selected
    })}),'value',
  'label',
  false,)
    
 
}
    
$(document).ready(function(){
    var multipleCancelButton = new Choices('#id_user', {
       removeItemButton: true,
       searchResultLimit:5,
       renderChoiceLimit:5,
     }); 
    agregarOptionesNivel(informacion,multipleCancelButton,todasCompaniasSelect)
     

});

</script>
@endpush