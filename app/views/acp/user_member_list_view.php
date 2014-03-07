<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-user"></i>
            <h3>회원 목록</h3>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <div class="container">
              <div class="col-lg-2 col-sm-2" style="border-bottom: 1px solid #efefef;">
                <h4>검색옵션</h4>
              </div>
              <div class="col-lg-10 col-sm-10" style="border-bottom: 1px solid #efefef;">
                -
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </div>

              <div class="col-lg-2 col-sm-2" style="border-bottom: 1px solid #efefef;">
                <h4>카테고리</h4>
              </div>
              <div class="col-lg-10 col-sm-10" style="border-bottom: 1px solid #efefef;">
              <?php 
              $this->load->config('keyword', TRUE);
              $keyword_list = $this->config->item('keyword', 'keyword');

              foreach ($keyword_list as $key => $keyword) { ?>
                <input type="checkbox" name="cat_<?php echo $key?>" id="cat_<?php echo $key?>" 
                  value="true"<?=(isset($args['cat_'.$key]) && filter_var($args['cat_'.$key], FILTER_VALIDATE_BOOLEAN) )?' checked':''?>
                  onchange="javascript:url_go_to('cat_<?php echo $key?>', 'TRUE', !($(this).is(':checked')));">
                <label for="cat_<?php echo $key?>" style="display:inline-block;"><?php echo $keyword;?></label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <?php } ?>
              </div>

              <div class="col-lg-3 col-sm-6" style="border-bottom: 1px solid #efefef;">
                <select name="order" id="order" onchange="javascript:url_go_to('order', $(this).val());">
                  <option value="idlarger"<?=($args['order']=="idlarger")?' selected':''?>>번호역순</option>
                  <option value="idsmaller"<?=($args['order']=="idsmaller")?' selected':''?>>번호순</option>
                  <option value="newest"<?=($args['order']=="newest")?' selected':''?>>최신순</option>
                  <option value="oldest"<?=($args['order']=="oldest")?' selected':''?>>과거순</option>
                  <option value="noted"<?=($args['order']=="noted")?' selected':''?>>인기순</option>
                  <option value="viewed"<?=($args['order']=="viewed")?' selected':''?>>조회순</option>
                  <option value="nofol_rank"<?=($args['order']=="nofol_rank")?' selected':''?>>노폴랭크순</option>
                  <option value="comment_desc"<?=($args['order']=="comment_desc")?' selected':''?>>댓글순</option>
                </select>
              </div>
              
              <div class="col-lg-4 col-sm-6" style="border-bottom: 1px solid #efefef;">
                <div class="input-group">가입일기준 
                    <!-- <span class="input-group-addon"></span> -->
                  <select class="" name="period" id="period" onchange="javascript:url_go_to('period', $(this).val());">
                    <option value="all"<?=($args['period']=="all")?' selected':''?>>전체 기간</option>
                    <option value="day"<?=($args['period']=="day")?' selected':''?>>오늘</option>
                    <option value="week"<?=($args['period']=="week")?' selected':''?>>이번 주</option>
                    <option value="month"<?=($args['period']=="month")?' selected':''?>>이번 달</option>
                    <option value="month3"<?=($args['period']=="month3")?' selected':''?>>최근 3달</option>
                  </select>
                </div>
              </div>


              <div class="col-lg-5 col-sm-6">
                <div class="col-lg-8 col-sm-10 search-center pull-left">
                  <input class="form-control" type="text" name="q" id="search_q" placeholder="검색어" value="<?=urldecode($args['q'])?>"/>
                </div>
                <div class="col-lg-4 col-sm-2 search-center pull-right">
                  <button class="btn btn-info" onclick="javascript:url_go_to('q', encodeURIComponent($('#search_q').val()));">검색</button>
                </div>
              </div>
            </div>
            <div class="container">
              <p>전체: <?=$all_count?>개</p>
              <p>페이지: <?=$page?>/<?=$all_page?> 페이지</p>
            </div>
            <div class="container">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>아이디</th>
                      <th>실명</th>
                      <th>이메일</th>
                      <th>가입일</th>
                      <th>
                        <? for ($i=0;$i<11;$i++) { ?>
                        <i class="icon-wrench"></i>
                        <? } ?>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <? foreach ($rows as $key=>$row) { ?>
                    <tr>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->id?></a></td>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->username?></a></td>
                      <td><a href="/acp/user/member/view/id/<?=$row->id?>"><?=$row->realname?></a></td>
                      <td><?=$row->email?></td>
                      <td><?=$row->created?></td>
                      <td>
                        <a href="/acp/user/member/view/id/<?=$row->id?>"><span class="btn btn-info">보기</span></a>
                        <a href="/acp/user/member/edit/id/<?=$row->id?>"><span class="btn">수정</span></a>
                        <a href="/acp/user/member/del/id/<?=$row->id?>"><span class="btn btn-danger">삭제</span></a>
                      </td>
                    </tr>
                    <? } ?>
                  </tbody>
                </table>
              </div>
              <!-- /table-responsive -->
              <?=get_paging($params=array('now_page'=>$page, 'last_page'=>$all_page, 'url'=> '/user/member/list', 'url_affix'=>$args))?>
            </div>
          </div>
          <!-- /widget-content -->
        </div>
      </div>
      
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->