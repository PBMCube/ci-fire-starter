<?php echo form_open('', array('class'=>'form-signin')); ?>

    <?php if (validation_errors()) : ?>
        <div class="alert alert-danger">
            <?php echo validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?php echo form_input(array('name'=>'username', 'id'=>'username', 'class'=>'form-control', 'placeholder'=>lang('auth input username_email'))); ?>
    <?php echo form_password(array('name'=>'password', 'id'=>'password', 'class'=>'form-control', 'placeholder'=>lang('auth input password'), 'autocomplete'=>'off')); ?>
    <?php echo form_submit(array('name'=>'submit', 'class'=>'btn btn-lg btn-primary btn-block'), lang('auth button login')); ?>

<?php echo form_close(); ?>

<a href="/"><?php echo lang('core button home'); ?></a>