<?php

namespace MetinSeylan\PerfectView;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;

class PerfectView {

    /* Assets */
    private $style;
    private $script;
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
            $this->style .= HTML::style(Config::get('PerfectView::assetFolder') . '/' . $source);
        } elseif ($type == 'script') {
            $this->script .= HTML::script(Config::get('PerfectView::assetFolder') . '/' . $source);
        }

        return $this;
    }

    public function style() {
        return $this->style;
    }
    

    public function script() {
        return $this->script;
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
        
        if(Request::ajax() AND $option['ajax']){
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
            
          rsort($this->wrapped);
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
        
        $response['template'] = View::make($view, $data);
        $response['style'] = $this->style;
        $response['script'] = $this->script;
        $response['title'] = $this->title;
        
        return Response::json($response);
        
    }

}