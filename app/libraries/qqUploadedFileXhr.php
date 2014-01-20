<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    var $error = true;
    var $tmpfile;

    function makeTempFile() {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        $this->tmpfile = $temp;
        
        if ($realSize != $this->getSize()){
            $this->error = true;

            return $this;
        }

        $this->error = false;
        
        return $this;
    }
    

    function save($path) {
        $temp = $this->makeTempFile()->tmpfile;
        
        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        $this->error = false;
        
        return $this;
    }

    function getName() {
        return $_GET['qqfile'];
    }
    
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

    function toFileArray() {
        list($width, $height, $type) = getimagesize($this->tmpfile);
        $tmp_file = stream_get_meta_data($this->tmpfile);
        return array(
          'type' => $type,
          'size' => $this->getSize(),
          'name' => $this->getName(),
          'tmp_name' => $tmp_file['uri']
        );
    }
}