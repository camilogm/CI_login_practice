

<section class="py-5">
    <div class="container">
      <div class="row">      
            <div class="col-lg-6 d-none d-lg-block">
                <img src="<?= base_url()?>assets/SB/img1.jpg" style="width:100%;height:100%;">
            </div>

            <div class="col-lg-6"> 
            <?php echo form_open('login/registrarse',array('autocomplete'=>'off')); ?>                     
                <div class="form-group">                
                    <div>
                        <?= form_input(array('name'=>'Credential','type'=>'email','placeholder','placeholder'=>'Ingrese su correo electrónico','class'=>'form-control')) ?>
                    </div>
                </div>
                <div class="form-group">                    
                    <div  >
                        <?= form_input(array('name'=>'Password','type'=>'password','placeholder','placeholder'=>'Ingrese su contraseña','class'=>'form-control')) ?>
                    </div>
                </div>
                <div class="form-group" >
                    <div >
                        <?= form_input(array('name'=>'PasswordConf','type'=>'password','placeholder'=>'Confirmar contraseña','class'=>'form-control')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php
                    echo form_submit(array('name'=>'submit','value'=>'registrarse','class'=>'btn btn-primary btn-user btn-block'));
                    echo form_close();
                    echo validation_errors();                
                    ?>
                </div>
                
                <div class="form-group">
                    <a href="<?=$GURL;?>" class="btn btn-google btn-user btn-block">
                        <i class="fab fa-google fa-fw"></i> 
                        Ingresar con Google
                    </a>
                </div>                
                <div class="form-group">
                    <a href="<?=$FURL;?>" class="btn btn-facebook btn-user btn-block">
                        <i class="fab fa-facebook-f fa-fw"></i> 
                        registrarse 
                    </a>    
                </div>
            </div>            
      </div>
</section>

<section class="py-5"></section>
<section class="py-5"></section>
<section class="py-5"></section>




