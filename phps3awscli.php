<?php

class Phps3awscli {

    public $access_key_id;
    public $secret_access_key;
    public $endpoint_url;

    public function __construct($access_key_id='', $secret_access_key='', $endpoint_url=''){
        $this->access_key_id = $access_key_id;
        $this->secret_access_key = $secret_access_key;
        $this->endpoint_url = $endpoint_url;
        putenv('AWS_ACCESS_KEY_ID='.$access_key_id);
        putenv('AWS_SECRET_ACCESS_KEY='.$secret_access_key);
    }

    public function set_access_key_id($access_key_id){
        $this->access_key_id = $access_key_id;
        putenv('AWS_ACCESS_KEY_ID='.$access_key_id);
    }

    public function get_access_key_id(){
        return $this->access_key_id;
    }

    public function set_secret_access_key($secret_access_key){
        $this->secret_access_key = $secret_access_key;
        putenv('AWS_SECRET_ACCESS_KEY='.$secret_access_key);
    }

    public function get_secret_access_key(){
        return $this->secret_access_key;
    }

    public function set_endpoint_url($endpoint_url){
        $this->endpoint_url = $endpoint_url;
    }

    public function get_endpoint_url(){
        return $this->endpoint_url;
    }

    private function exe_command($command){
        return shell_exec($command.' --endpoint-url '.$this->endpoint_url);
    }

    public function list_buckets(){
        $output = $this->exe_command('aws s3api list-buckets --query "Buckets[].Name"');
        return json_decode($output);
    }

    public function check_bucket($bucket){
        $output = $this->exe_command('aws s3api list-buckets --query "Buckets[?Name==\''.$bucket.'\'].Name"');
        $ar_output = json_decode($output);
        return count($ar_output)>0;
    }

    private function copy($file_src, $file_dest, $acl=''){
        $output = $this->exe_command('aws s3 cp "'.$file_src.'" "'.$file_dest.'"'.($acl ? ' --acl ' : '').$acl);
        if($output===NULL){
            return "";
        }else{
            $ar_output = explode("\r", rtrim($output));
            if(count($ar_output)==2){
                return $ar_output[1];
            }else{
                return $output;
            }
        }
    }

    # Uploading a single file to bucket
    # $file_dest is whithout bucket name and no leading slice, any sub directory will be created automatically
    public function push($bucket, $file_src, $file_dest, $acl=''){
        $file_dest = 's3://'.$bucket.'/'.$file_dest;
        $output = $this->copy($file_src, $file_dest, $acl);
        $key = 'upload: ';
        if(substr($output, 0, strlen($key))==$key){
            $ar_output = explode(" to ", substr($output, strlen($key)));
            return $ar_output[1];
        }else{
            return "";
        }
    }

    # Uploading directory recursively to bucket
    # $path_dest is whithout bucket name and no leading slice, any sub directory will be created automatically
    public function push_all($bucket, $path_src, $path_dest, $acl=''){
        $path_dest = 's3://'.$bucket.'/'.$path_dest;
        $output = $this->exe_command('aws s3 cp '.$path_src.' "'.$path_dest.'" --recursive'.($acl ? ' --acl ' : '').$acl);
        if($output===NULL){
            return array();
        }else{
            $ar_return = array();
            $ar_output = explode("\n", rtrim($output));
            $output = implode("\r", $ar_output);
            $ar_output = explode("\r", $output);
            foreach($ar_output as $el_output){
                $key = 'upload: ';
                if(substr($el_output, 0, strlen($key))==$key){
                    $ar_output1 = explode(" to ", substr($el_output, strlen($key)));
                    $ar_return[] = trim($ar_output1[1]);
                }
            }
            return $ar_return;
        }
    }

    # Downloading a single file from bucket to local
    # $file_src is whithout bucket name and no leading slice
    # Any subdirectory in the $file_dest will be created automatically
    public function pull($bucket, $file_src, $file_dest, $acl=''){
        $file_src = 's3://'.$bucket.'/'.$file_src;
        $output = $this->copy($file_src, $file_dest, $acl);
        $key = 'download: ';
        if(substr($output, 0, strlen($key))==$key){
            $ar_output = explode(" to ", substr($output, strlen($key)));
            return $ar_output[1];
        }else{
            return "";
        }
    }

    # Downloading directory recursively to bucket
    # $src_dest is whithout bucket name and no leading slice
    # Any sub directory in the $path_dest will be created automatically
    public function pull_all($bucket, $path_src, $path_dest, $acl=''){
        $path_src = 's3://'.$bucket.'/'.$path_src;
        $output = $this->exe_command('aws s3 cp "'.$path_src.'" "'.$path_dest.'" --recursive'.($acl ? ' --acl ' : '').$acl);
        if($output===NULL){
            return array();
        }else{
            $ar_return = array();
            $ar_output = explode("\n", rtrim($output));
            $output = implode("\r", $ar_output);
            $ar_output = explode("\r", $output);
            foreach($ar_output as $el_output){
                $key = 'download: ';
                if(substr($el_output, 0, strlen($key))==$key){
                    $ar_output1 = explode(" to ", substr($el_output, strlen($key)));
                    $ar_return[] = trim($ar_output1[1]);
                }
            }
            return $ar_return;
        }
    }

}

?>