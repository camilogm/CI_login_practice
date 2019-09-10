<section class="py-5">
<section class="py-5">
<section class="py-5">
    <div class="container">
<?php if ($Message=='null') {?>
<?= form_open("login/recuperarpass",array('autocomplete'=>'off'));?>
<div class="row"> 
    <div class="col-md-6  ">
     </div>
    <div class="col-md-6">
    <h3>Solicitar contraseña por correo electrónico</h3>
   
        <div class="form-group">
            <?= form_input(array('name'=>'Credential','type'=>'email','placeholder'=>'Ingrese su correo','class'=>'form-control')); ?>            
        </div>
        <div class="form-group">
            <?= form_submit(array('name'=>'submit','value'=>'Enviar','class'=>'btn btn-primary btn-block btn-user'));?> 
        </div>

        <?= form_close();?>
        <?= validation_errors();?>
    </div>
</div>
<?php } elseif ($Message=='true'){ ?>
    <div class="row">
        <div class="col-md-6 d-none"></div>
        <div class="col-md-6 alert-success">
            <p>El correo de restablecimiento de contraseña fue enviado a su correo electrónico</p>
        </div>    
    </div>
<?php } elseif ($Message=='false') {?>
    <div class="row">
        <div class="col-md-6 d-none"></div>
        <div class="col-md-6 alert-danger">
            <p>El correo de restablecimiento de contraseña no pudo ser enviado <strong>inténtenlo de nuevo o más tarde</strong></p>
        </div>    
    </div>
<?php } ?> 
</div>


</section>
<section class="py-5">
<section class="py-5">
<section class="py-5">
<section class="py-5">
<section class="py-5">