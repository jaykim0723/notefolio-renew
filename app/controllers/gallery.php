<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('work_model');
        $this->nf->_member_check(array('create','update','delete'));
    }

    
    public function index()
    {
        $this->listing(1);
    }
    function random(){
        $work = $this->work_model->get_random_work_info();
        redirect($work->username.'/'.$work->work_id);
    }

    /**
     * 리스트 출력에 관한 것
     * @param  integer $page [description]
     * @return [type]        [description]
     */
    function listing($page=1){
        $from = ($this->input->get_post('from'))?
                $this->input->get_post('from'):'all';
        $work_categories = ($this->input->get_post('work_categories'))?
                $this->input->get_post('work_categories'):array();
        $q = ($this->input->get_post('q'))?
                $this->input->get_post('q'):'';
        $order = ($this->input->get_post('order'))?
                $this->input->get_post('order'):'newest';

        $work_list = $this->work_model->get_list(array(
            'page' => $page,
            'allow_enabled'=> true,
            'keywords' => $work_categories,
            'order_by' => $order,
            'from' => $from,
            'q' => $q,
        ));

        
        if(empty($q))
            $title = '갤러리';
        else
            $title = '갤러리 검색: '.strip_tags(preg_replace(array('/&amp;/', '/&lt;/', '/&gt;/', '/&quot;/'), array('&', '<', '>', '"'), $q));

        if(count($work_categories)>0){
            $title .= ' - '.implode(', ', $this->nf->category_to_array(implode('', $work_categories)));
        }

        $work_list->work_categories = $work_categories;
        $work_list->q = $q;
        $work_list->order = $order;
        $work_list->from = $from;
        $this->layout->set_header(array(
            'keywords' => implode(', ', $this->nf->category_to_array(implode('', $work_categories))),
            'title' => $title,
        ))->set_view('gallery/listing_view', $work_list)->render();
    }

    /**
     * 작품의 개별 정보를 불러들인다.
     * @param  string $work_id [description]
     * @return [type]          [description]
     */
    function info($work_id=''){
        $work = $this->work_model->get_info(array(
            'work_id' => $work_id,
            'folder'  => '',
            'get_next_prev'=>true
        ));
        if($work->status==='fail' // 없거나
            or (
                $work->row->status=='disabled' and !($work->row->user->id == USER_ID or $this->nf->admin_is_elevated())
                )
            or (
                $work->row->status=='deleted' and !($this->nf->admin_is_elevated())
                )){
            alert('작품이 존재하지 않습니다.');
        }
        else if($work->row->user->username!=$this->uri->segment(1)){
            redirect('/'.$work->row->user->username.'/'.$this->uri->segment(2));
        }

        $work->row->hit_cnt++;
        $description = '';
        // get description
        if(!empty($work->row->contents)){
            foreach($work->row->contents as $obj){
                // exit(print_r($obj, TRUE));
                if(!is_object($obj))
                    $obj = (object)$obj;
                if($obj->t=='text'){
                    $description = $obj->c;
                    break;
                }
            }
        }

        $this->layout->set_header(array(
            'keywords' => implode(', ', $this->nf->category_to_array($work->row->keywords)).', '.@implode(', ',$work->row->tags),
            'description' => $description,
            'site_name' => $this->config->item('website_name', 'tank_auth'),
            'url' => site_url($work->row->user->username.'/'.$work->row->work_id),
            'image' => site_url('data/covers/'.$work->row->work_id.'_t3.jpg?_='.substr($work->row->moddate, -2)),
            'title' => $work->row->title.' - '.implode(', ', $this->nf->category_to_array($work->row->keywords)),
            'profile' => array(
                'username'  => $work->row->user->username,
                'is_follow' =>$work->row->is_follow,
                'user_id'        =>$work->row->user->id
            )
        ))->set_view('gallery/info_view', $work)->render();

        //-- view count up
        $params = new stdClass();
        $params->user_id = USER_ID;
        $params->work_id = $work_id;
        if(!empty($params->work_id) && $params->work_id>0){
            $result = $this->work_model->post_view($params);

            if($result->status=="done"){
                //-- write activity
                $this->load->library('activity');
                $this->activity->post(array(
                    'crud' => 'create',
                    'area' => 'work',
                    'type'  => 'view',
                    'work_id' => $params->work_id,
                    'user_A' => $params->user_id,
                    ));
            }
        }
    }


    function create(){
        $work_id = $this->work_model->post_info(); // 비어있는 값으로 생성하고
        if(empty($work_id)) alert('작품이 존재하지 않습니다.');


        //-- write activity
        $this->load->library('activity');
        $this->activity->post(array(
            'crud' => 'create',
            'area' => 'work',
            'type'  => 'create',
            'work_id' => $work_id,
            'user_A' => USER_ID,
            ));

        redirect($this->session->userdata('username').'/'.$work_id.'/update');
    }
    function upload(){ // 기존의 주소를 보전하기 위하여
        redirect('gallery/create');
    }



    function update($work_id=''){
        $work = $this->work_model->get_info(array('work_id'=>$work_id)); 
        if($work->status==='fail') alert('작품이 존재하지 않습니다.');
        if(!$this->nf->admin_is_elevated()&&$work->row->user_id!==USER_ID)
         alert('본인의 작품만 수정할 수 있습니다.');

        $this->form($work);
    }
    
    function form($work=NULL){
        $this->load->helper('form');
        $this->layout->set_view('gallery/form_view', $work)->render();
    }

    function save_cover($work_id=0, $upload_id=0){
        // 커버사진을 각 work_id에 임시폴더를 할당해서 저장한다.
        // 그리고 아래의 폼이 전송완료되었을 때에 대체한다.
        // upload_id:184
        // t2[x]:0
        // t2[y]:0
        // t2[w]:760
        // t2[h]:380
        // t3[x]:0
        // t3[y]:0
        // t3[w]:380
        // t3[h]:380

        # do stuff
        $this->load->config('upload', TRUE);
        $this->load->model('upload_model');
        $this->load->library('file_save');
        
        if(empty($upload_id)){
            $upload_id = $this->input->get_post('upload_id');
        }
        if(empty($work_id)){
            $work_id = $this->input->get_post('work_id');
        }

        $upload = $this->upload_model->get(array('id'=>$upload_id));
        if($upload->status=='done')
            $upload = $upload->row;

        $filename = preg_replace(
                        '/^(..)(..)([^\.]+)(\.[a-zA-Z]+)/', 
                        '$1/$2/$1$2$3$4', 
                        $upload->filename
                        );

        list($width, $height) = getimagesize($this->config->item('img_upload_path', 'upload').$filename);

        $size = array('width'=> $width, 'height'=> $height);
        
        $crop_param_t2 = $this->input->get_post('t2');
        $crop_param_t3 = $this->input->get_post('t3');

        $to_crop_t2 = $this->file_save->get_crop_opt($size, array(
                    'width'=>$crop_param_t2['w'],
                    'height'=>$crop_param_t2['h'],
                    'pos_x'=>$crop_param_t2['x'],
                    'pos_y'=>$crop_param_t2['y']
                )
            );

        $to_crop_t3 = $this->file_save->get_crop_opt($size, array(
                    'width'=>$crop_param_t3['w'],
                    'height'=>$crop_param_t3['h'],
                    'pos_x'=>$crop_param_t3['x'],
                    'pos_y'=>$crop_param_t3['y']
                )
            );

        $result_t1 = $this->file_save->make_thumbnail(
            $this->config->item('img_upload_path', 'upload').$filename,
            $this->config->item('temp_upload_path', 'upload').$work_id.'_t1.jpg', 'small');
        $result_t2 = $this->file_save->make_thumbnail(
            $this->config->item('img_upload_path', 'upload').$filename,
            $this->config->item('temp_upload_path', 'upload').$work_id.'_t2.jpg', 'single',
            array('crop_to'=>$to_crop_t2, 'spanning'=>true));
        $result_t3 = $this->file_save->make_thumbnail(
            $this->config->item('img_upload_path', 'upload').$filename,
            $this->config->item('temp_upload_path', 'upload').$work_id.'_t3.jpg', 'wide', 
            array('crop_to'=>$to_crop_t3, 'spanning'=>true));

        $json = array(
            'status'=>($result_t1&&$result_t2&&$result_t3)?'done':'fail',
            'cropped' => array(
                't2'=> $to_crop_t2,
                't3'=> $to_crop_t3
            ),
            'src'=> array(
                $this->config->item('temp_upload_uri', 'upload').$work_id.'_t1.jpg?_='.time(),
                $this->config->item('temp_upload_uri', 'upload').$work_id.'_t2.jpg?_='.time(),
                $this->config->item('temp_upload_uri', 'upload').$work_id.'_t3.jpg?_='.time()
            )
        );
        $this->layout->set_json($json)->render();
    }



    function save(){
        $input = $this->input->post();
        $work = $this->work_model->get_info(array(
            'work_id' => $input['work_id']
        ));
        $input['contents'] = json_decode($input['contents']);
        $created_images = $deleted_images = array();
        $work_images = $input_images = array();

        if(count($input['contents'])==0){
            $this->layout->set_json((object)array(
                'status' => 'fail',
                'message' => '내용이 비어 있으면 저장할 수 없습니다.'
                ))->render();
            exit();
        }

        if(empty($work->row->contents)){
            $old_contents = array();
        
        } else {
            $old_contents = $work->row->contents;
        }

        foreach($old_contents as $row){ // 기존 contents의 이미지 정보들을 수집
            if($row->t=='image' && $row->i!=''){
                $work_images[] = $row->i;
            }
        }
        foreach($input['contents'] as $row){ // 새로 들어온 것들을 비교하면서 최종 작업진행
            if($row->t=='image' && !empty($row->i)){
                $input_images[] = $row->i;
                if(in_array($row->i, $work_images)!==FALSE)
                    $created_images[] = $row->i; // 기존에 없던 것이라면 이것은 추가된 것이다.
            }
        }
        if(count($created_images)>0){
            //-- DB Update
            $this->db
                ->set('work_id', $input['work_id'])
                ->where('type', 'work')
                ->where_in('work_id', $created_images)
                ->update('uploads');
            //-- end
        }
        foreach($work_images as $i){
            if(in_array($i, $input_images)==FALSE)
                $deleted_images[] = $i; // 기존에는 있었지만 새로운 것에 없다면 삭제된 것이다.
        }
        if(count($deleted_images)>0){
            //-- DB Update
            $this->db
                ->set('work_id', 0)
                ->where('type', 'work')
                ->where_in('work_id', $deleted_images)
                ->update('uploads');
            //-- end
        }

        $input['keywords'] = implode('', $input['keywords']);       

        //-- cover_upload_id is not for update
        if(!empty($input['cover_upload_id'])){
            $this->_set_cover(
                array(
                    'work_id'=>$input['work_id'], 
                    'upload_id'=>$input['cover_upload_id']
            ));
        }
        unset($input['cover_upload_id']);
        
        unset($input['_wysihtml5_mode']);

        $input['contents'] = serialize($input['contents']);

        if(!isset($input['is_video'])) $input['is_video'] = 'n';

        $data = $this->work_model->put_info($input);
        $this->layout->set_json($data)->render();


        //-- write activity
        $this->load->library('activity');
        $this->activity->post(array(
            'crud' => 'update',
            'area' => 'work',
            'type'  => $input['status'].
                (($input['status']=='enabled'&&$work->row->status==$input['status'])?'_cont':''),
            'work_id' => $input['work_id'],
            'user_A' => USER_ID,
            ));

        if($input['status']=='enabled'&&$work->row->status!=$input['status']){
            //-- facebook post 
            $fb_query = http_build_query(array(
                'user_id'=>USER_ID,
                'post_type'=>'post_work',
                'work_id'=>$input['work_id'],
                'base_url'=>$this->config->item('base_url')
                ));
            $cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'/../app-cli/cli.php Fbconnect post "'.$fb_query.'"';
            exec($cmd . " > /dev/null &");  
            //error_log($cmd);
            //$this->fbsdk->post_data($this->tank_auth->get_user_id(), array('type'=>'post_work', 'work_id'=>$result));
        }
    }

    function _set_cover($params=array()){
        $params = (object)$params;
        $default_params = (object)array(
            'work_id'   => '',
            'upload_id'   => '' 
        );
        foreach($default_params as $key => $value){
            if(!isset($params->{$key}))
                $params->{$key} = $value;
        }

        try{
            $this->load->config('upload', TRUE);
            
            $this->db
                ->set('work_id', 0)
                ->where('type', 'cover')
                ->where('work_id', $params->work_id)
                ->update('uploads');

            $this->load->model('upload_model');
            $this->upload_model->put(
                array(
                    'id'      =>$params->upload_id, 
                    'work_id' =>$params->work_id, 
                    'type'    =>'cover', 
                    )
                );
            
            for($i=1; $i<=3; $i++){
                copy($this->config->item('temp_upload_path', 'upload').$params->work_id."_t$i.jpg", 
                    $this->config->item('cover_upload_path', 'upload').$params->work_id."_t$i.jpg");
            }
        }
        catch(Exception $e){
            return false;
        }

        return true;

    }

    function delete($work_id=''){
        $result = $this->work_model->delete_info(array('work_id'=>$work_id));
        if($result->status==='fail')
            alert($result->message);

        redirect('/'.$this->tank_auth->get_username());
    }



    function note(){
        $params = (object)$this->input->post();
        //$result = $this->work_model->note($params);
        $params->user_id = USER_ID;
        if(!empty($params->work_id) && $params->work_id>0){
            $note = $params->note;
            unset($params->note);
            switch($note){
                case 'n':
                    $result = $this->_note_cancel($params);
                    $result_collect = $this->_collect_cancel($params);
                break;
                case 'y':
                default:
                    $result = $this->_note_write($params);
                break;
            }
        }
        else {
            $result = (object)array(
                    'status' => 'fail',
                    'message' => 'no_work_id'
                );
        }   

        $this->layout->set_json($result)->render();
    }

    function _note_write($params){
        $result = $this->work_model->post_note($params);

        if(!empty($params->work_id) && $result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'create',
                'area' => 'work',
                'type'  => 'note',
                'work_id' => $params->work_id,
                'user_A' => $params->user_id,
                ));

            //-- facebook post 
            $fb_query = http_build_query(array(
                'user_id'=>$params->user_id, 
                'post_type'=>'post_note', 
                'work_id'=>$params->work_id, 
                'base_url'=>$this->config->item('base_url')
                ));
            $cmd = 'php '.$this->input->server('DOCUMENT_ROOT').'/../app-cli/cli.php Fbconnect post "'.$fb_query.'"';
            exec($cmd . " > /dev/null &");  
            //error_log($cmd);
        }

        return $result;
    }

    function _note_cancel($params){
        $result = $this->work_model->delete_note($params);

        if($result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'delete',
                'area' => 'work',
                'type'  => 'note',
                'work_id' => $params->work_id,
                'user_A' => $params->user_id,
                ));
        }

        return $result;
    }

    function collect(){
        $params = (object)$this->input->post();
        //$result = $this->work_model->collect($params);
        if(USER_ID>0){
            $params->user_id = USER_ID;
            if(!empty($params->work_id) && $params->work_id>0){
                $collect = $params->collect;
                unset($params->collect);
                switch($collect){
                    case 'n':
                        $result = $this->_collect_cancel($params);
                    break;
                    case 'y':
                    default:
                        $result = $this->_collect_write($params);
                    break;
                }
            }
            else {
                $result = (object)array(
                        'status' => 'fail',
                        'message' => 'no_work_id'
                    );
            }   
        }
        else{
            $result = (object)array(
                    'status' => 'fail',
                    'message' => 'not_logged_id'
                );
        }

        $this->layout->set_json($result)->render();
    }

    function _collect_write($params){
        $result = $this->work_model->post_collect($params);

        if($result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'create',
                'area' => 'work',
                'type'  => 'collect',
                'work_id' => $params->work_id,
                'user_A' => $params->user_id,
                'comment' => ''
                ));
        }

        return $result;
    }

    function _collect_cancel($params){
        $result = $this->work_model->delete_collect($params);

        if($result->status=="done"){
            //-- write activity
            $this->load->library('activity');
            $this->activity->post(array(
                'crud' => 'delete',
                'area' => 'work',
                'type'  => 'collect',
                'work_id' => $params->work_id,
                'user_A' => $params->user_id,
                'comment' => ''
                ));
        }

        return $result;
    }



}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */