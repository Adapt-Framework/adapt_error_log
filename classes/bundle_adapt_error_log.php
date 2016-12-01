<?php

namespace adapt\error\log;

defined('ADAPT_STARTED') or die();

class bundle_adapt_error_log extends \adapt\bundle{
    
    public function __construct($data){
        parent::__construct('bundle_adapt_error_log', $data);
    }
    
    public function boot(){
        if (parent::boot()){
            
            \adapt\base::listen(
                \adapt\base::EVENT_ERROR,
                function($data){
                    $file_store = new \adapt\storage_file_system();
                    $file_store->suppress_errors = true;
                    $key = 'error_log/' . date('Y-m-d') . '.log';
                    $file_path = $file_store->write_to_file($key);
                    if (!$file_path){
                        $file_path = TEMP_PATH . $file_store->get_new_key();
                    }
                    
                    $fp = fopen($file_path, "a");
                    if ($fp){
                        fwrite($fp, "======== " . date('Y-m-d H:i:s') . " ========\n");
                        fwrite($fp, "Class: " . get_class($data['object']) . "\n");
                        fwrite($fp, "Error: " . $data['event_data']['error'] . "\n");
                        
                        fclose($fp);
                        $file_store->set_by_file($key, $file_path, "text/plain");
                        unlink($file_path);
                    }
                }
            );
            
            return true;
        }
        
        return false;
    }
    
}
