<?php require_once('includes/header.php'); ?>

    <?php // Fixed navbar ?>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="row">
                <?php // Nav bar left ?>
                <ul class="nav navbar-nav">
                    <li<?php echo ($active == 'admin/dashboard') ? ' class="active"' : ''; ?>><a href="/admin/dashboard"><?php echo lang('admin nav dashboard'); ?></a></li>
                    <li<?php echo ($active == 'admin/users') ? ' class="active"' : ''; ?>><a href="/admin/users"><?php echo lang('admin nav users'); ?></a></li>
                </ul>
                <?php // Nav bar right ?>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/logout"><?php echo lang('core button logout'); ?></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php // Main body ?>
    <div class="container">

        <div class="row">
            <?php // Page title ?>
            <div class="col-md-8">
                <h1><?php echo $page_title; ?></h1>
            </div>
            <?php // Main controls ?>
            <div class="col-md-4" id="controls">
                <?php if (isset($controls)) : ?>
                    <br />
                    <?php foreach ($controls as $control) : ?>
                        <a class="btn <?php echo $control['bootstrap_button_class']; ?> pull-right" href="<?php echo $control['url']; ?>"><span class="glyphicon <?php echo $control['bootstrap_icon_class']; ?>"></span> <?php echo $control['title']; ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php // System messages ?>
        <?php if ($this->session->flashdata('message')) : ?>
            <div class="row alert alert-success">
                <?php echo $this->session->flashdata('message'); ?>
            </div>
        <?php elseif ($this->session->flashdata('error')) : ?>
            <div class="row alert alert-danger">
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php // Main content ?>
        <?php echo $content; ?>

    </div>

<?php require_once('includes/footer.php'); ?>