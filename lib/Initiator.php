<?php

namespace rakesh\apartment;

class Initiator extends \Controller_Addon {
    public $addon_name = 'r_apartment';

    function init(){
        parent::init();
        
        $contact_model = $this->add('xepan\base\Model_Contact');
        if(!$contact_model->loadLoggedIn())
            $this->add('View_Error')->set('not found');
        
        // if(!($this->app->apartment = $this->app->recall($this->app->auth->model->id.'_apartment',false))){
        //     $this->app->apartment = $this->add('rakesh\apartment\Model_Apartment')->tryLoadBy('created_by_id',$contact_model->id);
        //     $this->app->memorize($this->app->auth->model.'_apartment', $this->app->apartment);
        // }
        // $this->app->apartment = $this->app->recall($this->app->auth->model->id.'_apartment');
        
    }

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

        

        $this->app->exportFrontEndTool('rakesh\apartment\Tool_Dashboard','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Course','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Login','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Carousel','apartment');

        $memeber_model = $this->add('rakesh\apartment\Model_Member');
        $this->app->addHook('userCreated',[$memeber_model,'createNewMember']);

    	return $this;
    }
}