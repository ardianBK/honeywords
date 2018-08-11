<!DOCTYPE html>   
  <html>   
  <head>   
   <meta charset="UTF-8">   
   <title>  honeywords   
   </title>   
 </head>   
 <body>   
   <h2>Lupa Password</h2>

<?php
$email = array(
    'name'  => 'email',
    'id'  => 'email',
    'value' => set_value('email'),
  'maxlength' => 80,
  'size'  => 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<div id="infoMessage"><?php echo $this->session->flashdata('err_message'); ?></div>
   <p>Untuk melakukan reset password, silakan masukkan alamat email anda.   
<table>
  <tr>
    <td><?php echo form_label('Email Address', $email['id']); ?></td>
    <td><?php echo form_input($email); ?></td>
    <td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
  </tr>
    </table>
<?php echo form_submit('submit', 'kirim'); ?>
<?php echo form_close(); ?>
</body>
</html>