<?php
if(!function_exists('add_common_js')){
    function add_common_js($file='')
    {
        $str = '';
        $ci = &get_instance();
        $header_js  = $ci->config->item('header_js');
 
        if(empty($file)){
            return;
        }
 
        if(is_array($file)){
            if(!is_array($file) && count($file) <= 0){
                return;
            }
            foreach($file AS $item){
                $header_js[] = $item;
            }
            $ci->config->set_item('header_js',$header_js);
        }else{
            $str = $file;
            $header_js[] = $str;
            $ci->config->set_item('header_js',$header_js);
        }
    }
}
 
//Dynamically add CSS files to header page
if(!function_exists('add_common_css')){
    function add_common_css($file='')
    {
        $str = '';
        $ci = &get_instance();
        $header_css = $ci->config->item('header_css');
 
        if(empty($file)){
            return;
        }
 
        if(is_array($file)){
            if(!is_array($file) && count($file) <= 0){
                return;
            }
            foreach($file AS $item){  
                $header_css[] = $item;
            }
            $ci->config->set_item('header_css',$header_css);
        }else{
            $str = $file;
            $header_css[] = $str;
            $ci->config->set_item('header_css',$header_css);
        }
    }
}
 
if(!function_exists('put_headers_css')){
    function put_headers_css()
    {
        $str = '';
        $ci = &get_instance();
        $header_css = $ci->config->item('header_css');
        foreach($header_css AS $item){
            $str .= '<link rel="stylesheet" href="'.globel_url.'assets/'.$item.'" type="text/css" />'."\n";
        }
        return $str;
    }
}

if(!function_exists('put_headers_js')){
    function put_headers_js()
    {
        $str = '';
        $ci = &get_instance();
        
        $header_js  = $ci->config->item('header_js');
        foreach($header_js AS $item){
            $str .= '<script type="text/javascript" src="'.globel_url.'assets/'.$item.'"></script>'."\n";
        }
 
        return $str;
    }
}
