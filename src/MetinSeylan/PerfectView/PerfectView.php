<?php

namespace MetinSeylan\PerfectView;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;

class PerfectView {

    /* Assets */
    private $style = array();
    private $script = array();
    /* Custom Tags meta,link vs */
    public $tag;
    /* Wrap view */
    private $wrapped = array();
    private $wrappedData;
    /* page title */
    private $title;
    
    public function tag($tag = false, $content = false) {
        if(!$tag)
            return $this->tag;
        
        $values = '';
        foreach ($content as $key => $val) {
            $string = ' ' . $key . '="' . $val . '"';
            $values .= $string;
        }

        $this->tag .= '<' . $tag . $values . '>' . chr(10);

        return $this;
    }

    public function asset($source) {
        
        $type = (pathinfo($source, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';

        if ($type == 'style') {
            
            array_push($this->style, asset(Config::get('PerfectView::assetFolder') . '/' . $source));
            
        } elseif ($type == 'script') {
            
            
           array_push($this->script, asset(Config::get('PerfectView::assetFolder') . '/' . $source));

        }

        return $this;
    }

    public function style() {
        $nret = '';
        foreach($this->style as $style){
            $nret .= HTML::style($style);
        }
        return $nret;
  
    }
    

    public function script() {
        $nret = '';
        foreach($this->script as $script){
            $nret .= HTML::script($script);
        }
        return $nret;
    }

    public function title($title = false) {
        if($title){
            $this->title = $title;
            return $this;
        }
        
        return $this->title;
    }
    
    public function wrap($view, $data = array()){
        
        array_push($this->wrapped, array('view' => $view, 'data' => $data));
        return $this;
        
    }
    
    
    

    public function make($view, $data = array(), $option = array('ajax' => true, 'onlyContent' => false, 'jsonData' => false, 'nonBase' => false)) {
        
        if(Request::ajax() AND @$option['ajax'] OR @$option['jsonData']){
            return $this->makeJson($view, $data, @$option['jsonData']);
        }
        
        if(@$option['onlyContent']){
            
            if(@$option['nonBase'])
            return View::make($view, $data);
            else
            return View::make(Config::get('PerfectView::baseView'))->nest("content", $view, $data);
            
        }
        
        /* Wrap */
        if($this->wrapped){
            
          krsort($this->wrapped);
          $i=0;
          foreach($this->wrapped as $key => $wrap){
               if($i == 0){
                   $i++;
                   $this->wrappedData = View::make($wrap['view'], $wrap['data'])->nest('content', $view, $data);
               }else{
                  
                   $wrap['data']['content'] =  $this->wrappedData;
                   $this->wrappedData = View::make($wrap['view'], $wrap['data']);
               }
           } 
            
        }
        
        if($this->wrappedData){
            $data['content'] = $this->wrappedData;
            return View::make(Config::get('PerfectView::baseView'), $data);
        }
        
        if(@$option['nonBase'])
            return View::make($view, $data);
        else
            return View::make(Config::get('PerfectView::baseView'))->nest("content", $view, $data);
    }
    
    
    private function makeJson($view, $data, $jsonData = false){
        
        if($jsonData)
            return Response::json(array('jsonData' => $data, 'title' => $this->title));

        $response['template'] = View::make($view, $data)->render();
        $response['style'] = $this->style;
        $response['script'] = $this->script;
        $response['title'] = $this->title;
        
        return Response::json($response);
        
    }
    
}