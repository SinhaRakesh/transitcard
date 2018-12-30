<?php

namespace rakesh\apartment;

class View_AdminDashboard extends \View{

	public $options = [];

	function init(){
		parent::init();
		
		// $this->add('rakesh\apartment\View_Dashboard');

		if(!@$this->app->apartment->id){
			$this->add('View_Error')->set('first update apartment data');
			return;
		}

		$this->app->template->trySet('page_title',$this->app->apartment['name'].' Admin Dashboard');

		$this->add('rakesh\apartment\View_QuickLink');

		$pay_now = $this->add('rakesh\apartment\View_PayNow');
		$view = $this->add('View');
		$view->add('View')->setHtml('<!-- Info boxes -->
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Received</span>
                  <span class="info-box-number">17,000</span>
                  <span class="info-box-text">5-Flats</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Due</span>
                  <span class="info-box-number">3,000</span>
                  <span class="info-box-text">2-Flats</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Expences</span>
                  <span class="info-box-number">18,000</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Balance Amount</span>
                  <span class="info-box-number">20,000</span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->')->addClass('card card-stats');
		$path = "websites/".$this->app->epan['name']."/www";

		$col = $this->add('Columns');
		$col1 = $col->addColumn('6');
		$col1->add('rakesh\apartment\View_NoticeBoard');
	}
}