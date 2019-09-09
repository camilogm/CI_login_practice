

<?php if ($Message==true){ ?>

    <div class="alert-primary">
        <p><strong>Cuenta verificada con éxito</strong></p>
    </div>
<?php } else { ?>
    <div class="alert-danger">
        <p>No se pudo verificar la cuenta, solicite un nuevo correo<strong><a href="<?=base_url()?>login/solicitarverificacion/0">Aquí</a></strong></p>
    </div>
<?php }?>