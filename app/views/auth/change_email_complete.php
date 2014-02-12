<?php
echo form_open('', array(
	'name' => 'change_email_form',
	'role' => 'form',
	'id'   => 'change_email'
), array(
	'go_to'      => '', 
	'submitting' => 1
));
?>
<div class="biggroup">
	<div class="form-group">
		<?php if($is_success){ ?>
		<p><?php echo $this->lang->line('auth_message_new_email_activated') ?></p>
		<?php
		}
		else { ?>
		<p><?php echo $this->lang->line('auth_message_new_email_failed') ?></p>
		<?php
		} ?>
	</div>
</div>

	<div class='center'>
		
		<?php if($is_success){ ?>
		<a class='btn btn-darkgray btn-block pure-button-big pure-button' href="/auth/login">로그인</a>
		<?php
		}?>
	</div>

<?php echo form_close(); ?>