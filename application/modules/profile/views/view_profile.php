<p>Welcome back, <?php echo $first_name . " " . $last_name; ?>!</p>
<p>Your username is <?php echo $username; ?>.</p>
<p>Your email address is <?php echo safe_mailto($email, $email); ?>.</p>

<br />
<a href="/"><?php echo lang('core button home'); ?></a> | <a href="/logout"><?php echo lang('core button logout'); ?></a>