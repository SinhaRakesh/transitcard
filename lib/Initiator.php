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

        $model = $this->add('rakesh\apartment\Model_Member');
        $this->app->addHook('userCreated',[$model,'createNewMember']);

        $this->app->apartmentmember = $memeber_model = $this->add('rakesh\apartment\Model_MemberAbstract');
        if($memeber_model->loadLoggedIn()){
            if(!($this->app->apartment = $this->app->recall($this->app->auth->model->id.'_apartment',false)) AND $memeber_model['apartment_id']){
                $this->app->apartment = $this->app->apartmentmember->ref('apartment_id');
                $this->app->memorize($this->app->auth->model->id.'_apartment', $this->app->apartment);
            }

            if($memeber_model['is_apartment_admin']){
                $this->app->userIsApartmentAdmin = true;
            }else
                $this->app->userIsApartmentAdmin = false;
        }

        $this->app->apartment = $this->app->recall($this->app->auth->model->id.'_apartment');
        
        $this->app->exportFrontEndTool('rakesh\apartment\Tool_Dashboard','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Course','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Login','apartment');
        // $this->app->exportFrontEndTool('rakesh\apartment\Tool_Carousel','apartment');
    	return $this;
    }
}