<?php foreach ($rows as $key => $row): ?>
<?php $this->load->view('profile/my_recent_thumbnail_inc_view', array('row'=>$row)) ?>
<?php endforeach ?>