<?php
$hidden_field = array(
    'mode' => 'delete',
    'id' => isset($field->work_id)?$field->work_id:0,
);

?>

<div class="main">
  <div class="main-inner">
      <div class="container">
        
        <?php echo form_open("acp/work/works/proc", $form_attr); ?>
        <?php echo form_hidden($hidden_field); ?>
        <div class="alert alert-error">
            삭제할 정보가 맞는지 다시한번 확인 바랍니다. 삭제한 정보는 되돌릴 수 없습니다.
        </div>

        <table class="table table-hover">
          <tbody>

          </tbody>
        </table>
        <div class="alert alert-error">
            "삭제" 버튼을 누른 이후에는 다시 되살릴 수 없습니다. 삭제하시겠습니까?
        </div>
        <table class="table table-hover">
          <tbody>
            <tr>
              <th>작품을 완전히 삭제합니다</th>
              <td><?=form_checkbox('force_delete', 'y', TRUE);?><?=form_label('완전히 삭제', 'force_delete');?></td>
            </tr>
          </tbody>
        </table>
        <div style="width:100%;text-align:center;">
            <a class="btn btn-danger" href="javascript:$('#works_delete_form').submit();">삭제</a>
            <a class="btn" href='/acp/work/works/list'>취소</a>
        </div>
        <?php echo form_close(); ?>

      </div> <!-- /container -->
  </div> <!-- /main-inner -->
</div>
