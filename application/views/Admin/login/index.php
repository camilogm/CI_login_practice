


<section class="py-5">
    <div class="container">
      <div class="row">
      
        <div class="col-lg-6">
            <img src="<?= base_url()?>assets/SB/img1.jpg" style="width:100%;height:100%;">
        </div>
        <div clss="col-lg-6">
        <h1>Ingresa a tu cuenta</h1>
            <?php echo form_open('login'); ?>
            <div class="form-group">
                <div class="form-group">
                    <div >
                        <?php echo form_input(array('name'=>'Credential','type'=>'text','placeholder'=>'Ingrese su usuario','class'=>'form-control')); ?>
                    </div>
                </div>
                <div>
                <div class="form-group">                   
                    <div >
                        <?php echo form_input(array('name'=>'Password','type'=>'password','placeholder'=>'Ingrese su contraseña','class'=>'form-control')); ?>
                    </div>
                </div>
            </div>
            <?php
            echo form_submit(array('name'=>'submit','value'=>'Ingresar','class'=>'btn btn-primary btn-user btn-block'));
            echo form_close(); 
            ?>
        </div>
        <div class="form-group">
            <a href="index.html" class="btn btn-google btn-user btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
        </div>
        <div class="form-group">
        <a href="index.html" class="btn btn-facebook btn-user btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a>    

        </div>


      </div>    
   
   
    </div>
  </section>



  <section class="py-5"></section>  
  <section class="py-5"></section>  
  <section class="py-5"></section>