<?php
/**
 * Forgotten password text email
 */
?>
Hi <?php echo $user[$alias]['username']; ?>,

You have requested to have your password reset on <?php echo SITE_NAME; ?>. Please click the link below to reset your password now :

<?php echo $link; ?>

If above link does not work please copy and paste the URL link (above) into your browser address bar to get to the Page to reset password.

Choose a password you can remember and please keep it secure.