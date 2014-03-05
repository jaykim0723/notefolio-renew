<?php 

?>
<div class="main">
  <div class="main-inner">
      <div class="container">
        <div class="widget ">
          <div class="widget-header">
            <i class="icon-picture"></i>
            <h3>작품 목록</h3>
          </div>
          <!-- /widget-header -->
          <div class="widget-content">
            <div class="container">
              <div class="col-lg-2 col-sm-2" style="border-bottom: 1px solid #efefef;">
                <h4>검색옵션</h4>
              </div>
              <div class="col-lg-10 col-sm-10" style="border-bottom: 1px solid #efefef;">
                <input type="checkbox" name="sp_nz" id="sp_nz" 
                  value="true"<?=(isset($args['sp_nz']) && filter_var($args['sp_nz'], FILTER_VALIDATE_BOOLEAN) )?' checked':''?>
                  onchange="javascript:url_go_to('sp_nz', 'TRUE', !($(this).is(':checked')));">
                <label for="sp_nz" style="display:inline-block;">Staffpoint 부여된 것만 보기</label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="only_deleted" id="only-deleted" 
                  value="true"<?=(isset($args['only_deleted']) && filter_var($args['only_deleted'], FILTER_VALIDATE_BOOLEAN) )?' checked':''?>
                  onchange="javascript:url_go_to('only_deleted', 'TRUE', !($(this).is(':checked')));">
                <label for="only_deleted" style="display:inline-block;">삭제된 것만 보기</label>
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
                  <option value="newest"<?=($args['order']=="newest")?' selected':''?>>최신순</option>
                  <option value="newest"<?=($args['order']=="oldest")?' selected':''?>>과거순</option>
                  <option value="noted"<?=($args['order']=="noted")?' selected':''?>>인기순</option>
                  <option value="viewed"<?=($args['order']=="viewed")?' selected':''?>>조회순</option>
                  <option value="viewed"<?=($args['order']=="nofol_rank")?' selected':''?>>노폴랭크순</option>
                  <option value="comment_desc"<?=($args['order']=="comment_desc")?' selected':''?>>댓글순</option>
                </select>
              </div>
              
              <div class="col-lg-3 col-sm-6" style="border-bottom: 1px solid #efefef;">
                <div class="input-group">
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

              <div class="col-lg-1 col-sm-6"></div>

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
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>순번</th>
                    <th>&nbsp;</th>
                    <th>작성자</th>
                    <th>공개여부</th>
                    <th>피드백</th>
                    <th>노폴랭크</th>
                    <th>생성일</th>
                    <th>보기</th>
                    <th>수정</th>
                    <th>삭제</th>
                  </tr>
                </thead>
                <tbody>
              <?php
                  if(isset($list)&&count($list)>0) {
                    foreach($list as $k=>$v){
              ?>
                  <tr class="" data-id="<?=$v->work_id?>">
                    <td><a href="/acp/work/works/view/id/<?=$v->work_id?>"><?=$v->work_id?></a></td>
                    <td><a href="/<?=$v->user->username?>/<?=$v->work_id?>">
                      <img src="/data/covers/<?=$v->work_id?>_t2.jpg" width="200"><br/>
                      <p><?=mb_substr($v->title, 0, 50, 'UTF-8')?>
                            <?=((mb_strlen($v->title, 'UTF-8')>50)?'...':'')?> / <?=$v->regdate?></p>
                    </a></td>  
                    <td><a href="/acp/user/member/view/id/<?=$v->user->id?>">
                      <?=$v->user->realname?>
                        </a></td>
                    <td><?=$v->status?></td>
                    <td>
                      조회  : <?=$v->hit_cnt?><br />
                      추천  : <?=$v->note_cnt?><br />
                      댓글  : <?=$v->comment_cnt?><br />
                      콜렉트 : <?=$v->collect_cnt?><br />
                    </td>
                    <td>
                      피드백총계 : <?=$v->nofol_rank?><br />

                      Likability  : <?=$v->discoverbility?><br />
                      Likability기간계  : <?=round($v->discoverbility_by_period, 3)?><br />
                      피드백기간계 : <?=round($v->feedback_point, 3)?><br />
                      스탭점수 : <?=$v->staffpoint?><br />
                      랭크총계 : <?=round($v->rank_point, 3)?><br />

                      스탭포인트 수정: <br/>
                      <input type="text" value="<?=$v->staffpoint?>" name="staffpoint" style="width: 4em;"  />
                      <a class="staffpoint-btn" href="#">
                        <span class="btn btn-info">수정</span>
                      </a>
                    </td>
                    <td><?=$v->regdate?></td>
                    <td><a class="btn" href="/acp/work/works/view/id/<?=$v->work_id?>">보기</a></td>
                    <td>
                      <a class="btn btn-primary" href="/acp/work/works/modify/id/<?=$v->work_id?>">수정</a><br/>
                      <a class="btn btn-primary" href="/gallery/update/<?=$v->work_id?>">작품수정</a>
                    </td>
                    <td><a class="btn btn-danger" href="/acp/work/works/delete/id/<?=$v->work_id?>">삭제</a></td>
                  </tr>
              <?php
                    }
                  } else {
              ?>
                  <tr class="info">
                    <td colspan="7">작품이 없습니다.</td>
                  </tr>
              <?php   
                  }
              ?>
                </tbody>
              </table>
              <div class="row-fluid">
                <?=get_paging($params=array('now_page'=>$page, 'last_page'=>$all_page, 'url'=> '/work/works/list', 'url_affix'=>$args))?>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- /container -->
  </div> <!-- /main-inner -->
</div>