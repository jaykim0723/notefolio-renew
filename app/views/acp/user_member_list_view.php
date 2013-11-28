<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="alert alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <h4>주의하세요!</h4>
          이 페이지에 나온 기능은 전부 동작하지 않는 기능입니다.
        </div>
      </div>
      <div class="row">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
              </tr>
            </thead>
            <tbody>
              <? foreach ($rows as $key=>$row) { ?>
              <tr>
                <td><?=$row['id']?></td>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
              </tr>
              <? } ?>
            </tbody>
          </table>
        </div>
        <!-- /table-responsive --> 
      </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->