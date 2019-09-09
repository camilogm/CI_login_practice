
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-sm-6"></div>
            <div class="col-sm-6">   
                <?php if ($Message=='null') {?>            
                        <?= form_open("login/cambiarpass/".$Token,array('autocomplete'=>'off'));?>                            
                            <div class="form-group">
                                 <?= form_input(array('name'=>'Password','type'=>'password','placeholder'=>'Ingrese la nueva contraseña','class'=>'form-control')) ?>
                            </div>
                            <div class="form-group">
                                <?= form_input(array('name'=>'PasswordConf','type'=>'password','placeholder'=>'Ingrese la nueva contraseña','class'=>'form-control')) ?>
                            </div>  
                            <div class="form-group">
                                <?= form_submit(array('name'=>'submit','value'=>'Cambiar','class'=>'btn btn-primary btn-user btn-block'))?>
                            </div>        
                            
                        <?= form_close();?>
                        <?= validation_errors()?>
                <?php } elseif($Message=='true') {?>
                            <div class="form-group alert-success ">   
                                <p>Se ha cambiado la contraseña correctamente</p>          
                            </div>                
                <?php } elseif($Message=='false') {?>
                            <div class="form-group alert-info">   
                                <p>El cambio de contraseña experiró</p>
                                <p>solicitelo de nuevo <a href="<?=base_url()?>login/recuperarpass">aquí</a></p>             
                            </div>
                <?php } ?>
            </div>
        </div>
    </div>


</section>
<section class="py-5"></section>
<section class="py-5"></section>
