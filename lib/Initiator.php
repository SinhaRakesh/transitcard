<?php

namespace rakesh\apartment;

class Initiator extends \Controller_Addon {
    public $addon_name = 'r_apartment';

    function setup_admin(){

    	if($this->app->is_admin){
            $this->routePages('rakesh_apartment');
            $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
            ->setBaseURL('../shared/apps/rakesh/');
        }

        $m = $this->app->top_menu->addMenu('Apartment');
        $m->addMenuItem('rakesh_apartment_apartment','Apartment');
        $m->addMenuItem('rakesh_apartment_flat','Flat');
        $m->addMenuItem('rakesh_apartment_member','Member');
    	return $this;
    }

    function setup_pre_frontend(){
        $this->routePages('rakesh_apartment');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
        ->setBaseURL('./shared/apps/rakesh/apartment/');

    }
    
    function setup_frontend(){
        $this->routePages('rakesh_apartment');
        $this->addLocation(array('template'=>'templates','js'=>'templates/js','css'=>['templates/css','templates/js']))
        ->setBaseURL('./shared/apps/rakesh/apartment/');

        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Applicationform','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Course','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Login','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Carousel','apartment');        
    	return $this;
    }
}