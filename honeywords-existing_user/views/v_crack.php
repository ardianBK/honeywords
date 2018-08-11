 <!DOCTYPE html>   
  <html>   
  <head>   
   <meta charset="UTF-8">   
   <title>   
     honeywords  
   </title>   
 </head>   
 <body>   
<?php
$username = array(
  'name'  => 'username',
  'id'  => 'username',
  'value' => set_value('username'),
  'maxlength' => $this->config->item('password_max_length', 'authentic'),
  'size'  => 30,
);
$password = array(
  'name'  => 'password',
  'id'  => 'password',
  'value' => set_value('password'),
  'maxlength' => $this->config->item('password_max_length', 'authentic'),
  'size'  => 30,
);
$confirm_password = array(
  'name'  => 'confirm_password',
  'id'  => 'confirm_password',
  'value' => set_value('confirm_password'),
  'maxlength' => $this->config->item('password_max_length', 'authentic'),
  'size'  => 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
   <h2>Deteksi Cracking</h2>   
   <h5>Hello , Silakan isi password baru anda.</h5>   
   <div id="infoMessage"><?php echo $this->session->flashdata('err_message'); ?></div>
   <table>
    <tr>
        <td><?php echo form_label('Username', $username['id']); ?></td>
        <td><?php echo form_password($username); ?></td>
        <td style="color: red;"><?php echo form_error($username['name']); ?></td>
      </tr>
       <tr>
        <td><?php echo form_label('Password', $password['id']); ?></td>
        <td><?php echo form_password($password); ?></td>
        <td style="color: red;"><?php echo form_error($password['name']); ?></td>
      </tr>
      <tr>
        <td><?php echo form_label('Confirm Password', $confirm_password['id']); ?></td>
        <td><?php echo form_password($confirm_password); ?></td>
        <td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
      </tr>
</table>
<?php echo form_submit('submit', 'reset password'); ?>
<?php echo form_close(); ?>

</body>
</html> 