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
		
		// $view->add('View')->setHtml('<div class="row">
		// 		  <div class="col-md-8">
		// 		    <!-- MAP & BOX PANE -->
		// 		    <div class="box box-success">
		// 		      <div class="box-header with-border">
		// 		        <h3 class="box-title">Visitors Report</h3>

		// 		        <div class="box-tools pull-right">
		// 		          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		// 		          </button>
		// 		          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		// 		        </div>
		// 		      </div>
		// 		      <!-- /.box-header -->
		// 		      <div class="box-body no-padding">
		// 		        <div class="row">
		// 		          <div class="col-md-9 col-sm-8">
		// 		            <div class="pad">
		// 		              <!-- Map will be created here -->
		// 		              <div id="world-map-markers" style="height: 325px;"></div>
		// 		            </div>
		// 		          </div>
		// 		          <!-- /.col -->
		// 		          <div class="col-md-3 col-sm-4">
		// 		            <div class="pad box-pane-right bg-green" style="min-height: 280px">
		// 		              <div class="description-block margin-bottom">
		// 		                <div class="sparkbar pad" data-color="#fff">90,70,90,70,75,80,70</div>
		// 		                <h5 class="description-header">8390</h5>
		// 		                <span class="description-text">Visits</span>
		// 		              </div>
		// 		              <!-- /.description-block -->
		// 		              <div class="description-block margin-bottom">
		// 		                <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
		// 		                <h5 class="description-header">30%</h5>
		// 		                <span class="description-text">Referrals</span>
		// 		              </div>
		// 		              <!-- /.description-block -->
		// 		              <div class="description-block">
		// 		                <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
		// 		                <h5 class="description-header">70%</h5>
		// 		                <span class="description-text">Organic</span>
		// 		              </div>
		// 		              <!-- /.description-block -->
		// 		            </div>
		// 		          </div>
		// 		          <!-- /.col -->
		// 		        </div>
		// 		        <!-- /.row -->
		// 		      </div>
		// 		      <!-- /.box-body -->
		// 		    </div>
		// 		    <!-- /.box -->
		// 		    <div class="row">
		// 		      <div class="col-md-6">
		// 		        <!-- DIRECT CHAT -->
		// 		        <div class="box box-warning direct-chat direct-chat-warning">
		// 		          <div class="box-header with-border">
		// 		            <h3 class="box-title">Direct Chat</h3>

		// 		            <div class="box-tools pull-right">
		// 		              <span data-toggle="tooltip" title="3 New Messages" class="badge bg-yellow">3</span>
		// 		              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		// 		              </button>
		// 		              <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="Contacts"
		// 		                      data-widget="chat-pane-toggle">
		// 		                <i class="fa fa-comments"></i></button>
		// 		              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
		// 		              </button>
		// 		            </div>
		// 		          </div>
		// 		          <!-- /.box-header -->
		// 		          <div class="box-body">
		// 		            <!-- Conversations are loaded here -->
		// 		            <div class="direct-chat-messages">
		// 		              <!-- Message. Default to the left -->
		// 		              <div class="direct-chat-msg">
		// 		                <div class="direct-chat-info clearfix">
		// 		                  <span class="direct-chat-name pull-left">Alexander Pierce</span>
		// 		                  <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
		// 		                </div>
		// 		                <!-- /.direct-chat-info -->
		// 		                <img class="direct-chat-img" src="'.$path.'/dist/img/user1-128x128.jpg" alt="message user image">
		// 		                <!-- /.direct-chat-img -->
		// 		                <div class="direct-chat-text">
		// 		                  Is this template really for free? That\'s unbelievable!
		// 		                </div>
		// 		                <!-- /.direct-chat-text -->
		// 		              </div>
		// 		              <!-- /.direct-chat-msg -->

		// 		              <!-- Message to the right -->
		// 		              <div class="direct-chat-msg right">
		// 		                <div class="direct-chat-info clearfix">
		// 		                  <span class="direct-chat-name pull-right">Sarah Bullock</span>
		// 		                  <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
		// 		                </div>
		// 		                <!-- /.direct-chat-info -->
		// 		                <img class="direct-chat-img" src="'.$path.'/dist/img/user3-128x128.jpg" alt="message user image">
		// 		                <!-- /.direct-chat-img -->
		// 		                <div class="direct-chat-text">
		// 		                  You better believe it!
		// 		                </div>
		// 		                <!-- /.direct-chat-text -->
		// 		              </div>
		// 		              <!-- /.direct-chat-msg -->

		// 		              <!-- Message. Default to the left -->
		// 		              <div class="direct-chat-msg">
		// 		                <div class="direct-chat-info clearfix">
		// 		                  <span class="direct-chat-name pull-left">Alexander Pierce</span>
		// 		                  <span class="direct-chat-timestamp pull-right">23 Jan 5:37 pm</span>
		// 		                </div>
		// 		                <!-- /.direct-chat-info -->
		// 		                <img class="direct-chat-img" src="'.$path.'/dist/img/user1-128x128.jpg" alt="message user image">
		// 		                <!-- /.direct-chat-img -->
		// 		                <div class="direct-chat-text">
		// 		                  Working with AdminLTE on a great new app! Wanna join?
		// 		                </div>
		// 		                <!-- /.direct-chat-text -->
		// 		              </div>
		// 		              <!-- /.direct-chat-msg -->

		// 		              <!-- Message to the right -->
		// 		              <div class="direct-chat-msg right">
		// 		                <div class="direct-chat-info clearfix">
		// 		                  <span class="direct-chat-name pull-right">Sarah Bullock</span>
		// 		                  <span class="direct-chat-timestamp pull-left">23 Jan 6:10 pm</span>
		// 		                </div>
		// 		                <!-- /.direct-chat-info -->
		// 		                <img class="direct-chat-img" src="'.$path.'/dist/img/user3-128x128.jpg" alt="message user image">
		// 		                <!-- /.direct-chat-img -->
		// 		                <div class="direct-chat-text">
		// 		                  I would love to.
		// 		                </div>
		// 		                <!-- /.direct-chat-text -->
		// 		              </div>
		// 		              <!-- /.direct-chat-msg -->

		// 		            </div>
		// 		            <!--/.direct-chat-messages-->

		// 		            <!-- Contacts are loaded here -->
		// 		            <div class="direct-chat-contacts">
		// 		              <ul class="contacts-list">
		// 		                <li>
		// 		                  <a href="#">
		// 		                    <img class="contacts-list-img" src="'.$path.'/dist/img/user1-128x128.jpg" alt="User Image">

		// 		                    <div class="contacts-list-info">
		// 		                          <span class="contacts-list-name">
		// 		                            Count Dracula
		// 		                            <small class="contacts-list-date pull-right">2/28/2015</small>
		// 		                          </span>
		// 		                      <span class="contacts-list-msg">How have you been? I was...</span>
		// 		                    </div>
		// 		                    <!-- /.contacts-list-info -->
		// 		                  </a>
		// 		                </li>
		// 		                <!-- End Contact Item -->
		// 		                <li>
		// 		                  <a href="#">
		// 		                    <img class="contacts-list-img" src="'.$path.'/dist/img/user7-128x128.jpg" alt="User Image">

		// 		                    <div class="contacts-list-info">
		// 		                          <span class="contacts-list-name">
		// 		                            Sarah Doe
		// 		                            <small class="contacts-list-date pull-right">2/23/2015</small>
		// 		                          </span>
		// 		                      <span class="contacts-list-msg">I will be waiting for...</span>
		// 		                    </div>
		// 		                    <!-- /.contacts-list-info -->
		// 		                  </a>
		// 		                </li>
		// 		                <!-- End Contact Item -->
		// 		                <li>
		// 		                  <a href="#">
		// 		                    <img class="contacts-list-img" src="'.$path.'/dist/img/user3-128x128.jpg" alt="User Image">

		// 		                    <div class="contacts-list-info">
		// 		                          <span class="contacts-list-name">
		// 		                            Nadia Jolie
		// 		                            <small class="contacts-list-date pull-right">2/20/2015</small>
		// 		                          </span>
		// 		                      <span class="contacts-list-msg">I\'ll call you back at...</span>
		// 		                    </div>
		// 		                    <!-- /.contacts-list-info -->
		// 		                  </a>
		// 		                </li>
		// 		                <!-- End Contact Item -->
		// 		                <li>
		// 		                  <a href="#">
		// 		                    <img class="contacts-list-img" src="'.$path.'/dist/img/user5-128x128.jpg" alt="User Image">

		// 		                    <div class="contacts-list-info">
		// 		                          <span class="contacts-list-name">
		// 		                            Nora S. Vans
		// 		                            <small class="contacts-list-date pull-right">2/10/2015</small>
		// 		                          </span>
		// 		                      <span class="contacts-list-msg">Where is your new...</span>
		// 		                    </div>
		// 		                    <!-- /.contacts-list-info -->
		// 		                  </a>
		// 		                </li>
		// 		                <!-- End Contact Item -->
		// 		                <li>
		// 		                  <a href="#">
		// 		                    <img class="contacts-list-img" src="'.$path.'/dist/img/user6-128x128.jpg" alt="User Image">

		// 		                    <div class="contacts-list-info">
		// 		                          <span class="contacts-list-name">
		// 		                            John K.
		// 		                            <small class="contacts-list-date pull-right">1/27/2015</small>
		// 		                          </span>
		// 		                      <span class="contacts-list-msg">Can I take a look at...</span>
		// 		                    </div>
		// 		                    <!-- /.contacts-list-info -->
		// 		                  </a>
		// 		                </li>
		// 		                <!-- End Contact Item -->
		// 		                <li>
		// 		                  <a href="#">
		// 		                    <img class="contacts-list-img" src="'.$path.'/dist/img/user8-128x128.jpg" alt="User Image">

		// 		                    <div class="contacts-list-info">
		// 		                          <span class="contacts-list-name">
		// 		                            Kenneth M.
		// 		                            <small class="contacts-list-date pull-right">1/4/2015</small>
		// 		                          </span>
		// 		                      <span class="contacts-list-msg">Never mind I found...</span>
		// 		                    </div>
		// 		                    <!-- /.contacts-list-info -->
		// 		                  </a>
		// 		                </li>
		// 		                <!-- End Contact Item -->
		// 		              </ul>
		// 		              <!-- /.contatcts-list -->
		// 		            </div>
		// 		            <!-- /.direct-chat-pane -->
		// 		          </div>
		// 		          <!-- /.box-body -->
		// 		          <div class="box-footer">
		// 		            <form action="#" method="post">
		// 		              <div class="input-group">
		// 		                <input type="text" name="message" placeholder="Type Message ..." class="form-control">
		// 		                <span class="input-group-btn">
		// 		                      <button type="button" class="btn btn-warning btn-flat">Send</button>
		// 		                    </span>
		// 		              </div>
		// 		            </form>
		// 		          </div>
		// 		          <!-- /.box-footer-->
		// 		        </div>
		// 		        <!--/.direct-chat -->
		// 		      </div>
		// 		      <!-- /.col -->

		// 		      <div class="col-md-6">
		// 		        <!-- USERS LIST -->
		// 		        <div class="box box-danger">
		// 		          <div class="box-header with-border">
		// 		            <h3 class="box-title">New Visitors</h3>

		// 		            <div class="box-tools pull-right">
		// 		              <span class="label label-danger">8 New Members</span>
		// 		              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		// 		              </button>
		// 		              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
		// 		              </button>
		// 		            </div>
		// 		          </div>
		// 		          <!-- /.box-header -->
		// 		          <div class="box-body no-padding">
		// 		            <ul class="users-list clearfix">
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user1-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Alexander Pierce</a>
		// 		                <span class="users-list-date">Today</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user8-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Norman</a>
		// 		                <span class="users-list-date">Yesterday</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user7-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Jane</a>
		// 		                <span class="users-list-date">12 Jan</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user6-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">John</a>
		// 		                <span class="users-list-date">12 Jan</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user2-160x160.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Alexander</a>
		// 		                <span class="users-list-date">13 Jan</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user5-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Sarah</a>
		// 		                <span class="users-list-date">14 Jan</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user4-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Nora</a>
		// 		                <span class="users-list-date">15 Jan</span>
		// 		              </li>
		// 		              <li>
		// 		                <img src="'.$path.'/dist/img/user3-128x128.jpg" alt="User Image">
		// 		                <a class="users-list-name" href="#">Nadia</a>
		// 		                <span class="users-list-date">15 Jan</span>
		// 		              </li>
		// 		            </ul>
		// 		            <!-- /.users-list -->
		// 		          </div>
		// 		          <!-- /.box-body -->
		// 		          <div class="box-footer text-center">
		// 		            <a href="javascript:void(0)" class="uppercase">View All Users</a>
		// 		          </div>
		// 		          <!-- /.box-footer -->
		// 		        </div>
		// 		        <!--/.box -->
		// 		      </div>
		// 		      <!-- /.col -->
		// 		    </div>
		// 		    <!-- /.row -->

		// 		    <!-- TABLE: LATEST ORDERS -->
		// 		    <div class="box box-info">
		// 		      <div class="box-header with-border">
		// 		        <h3 class="box-title">Maintenance Amounts</h3>

		// 		        <div class="box-tools pull-right">
		// 		          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		// 		          </button>
		// 		          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		// 		        </div>
		// 		      </div>
		// 		      <!-- /.box-header -->
		// 		      <div class="box-body">
		// 		        <div class="table-responsive">
		// 		          <table class="table no-margin">
		// 		            <thead>
		// 		            <tr>
		// 		              <th>Order ID</th>
		// 		              <th>Amount</th>
		// 		              <th>Status</th>
		// 		              <th></th>
		// 		            </tr>
		// 		            </thead>
		// 		            <tbody>
		// 		            <tr>
		// 		              <td><a href="pages/examples/invoice">OR9842</a></td>
		// 		              <td>800</td>
		// 		              <td><span class="label label-danger">Due</span></td>
		// 		              <td>
		// 		                <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
		// 		              </td>
		// 		            </tr>
		// 		            <tr>
		// 		              <td><a href="pages/examples/invoice">OR9842</a></td>
		// 		              <td>800</td>
		// 		              <td><span class="label label-success">Paid</span></td>
		// 		              <td>
		// 		                <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
		// 		              </td>
		// 		            </tr>
		// 		            <tr>
		// 		              <td><a href="pages/examples/invoice">OR9842</a></td>
		// 		              <td>800</td>
		// 		              <td><span class="label label-success">Paid</span></td>
		// 		              <td>
		// 		                <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
		// 		              </td>
		// 		            </tr>
		// 		            <tr>
		// 		              <td><a href="pages/examples/invoice">OR9842</a></td>
		// 		              <td>800</td>
		// 		              <td><span class="label label-success">Paid</span></td>
		// 		              <td>
		// 		                <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
		// 		              </td>
		// 		            </tr>

		// 		            </tbody>
		// 		          </table>
		// 		        </div>
		// 		        <!-- /.table-responsive -->
		// 		      </div>
		// 		      <!-- /.box-body -->
		// 		      <div class="box-footer clearfix">
		// 		        <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
		// 		      </div>
		// 		      <!-- /.box-footer -->
		// 		    </div>
		// 		    <!-- /.box -->
		// 		  </div>
		// 		  <div class="col-md-4">

		// 		    <!-- /.box -->

		// 		    <!-- PRODUCT LIST -->
		// 		    <div class="box box-primary">
		// 		      <div class="box-header with-border">
		// 		        <h3 class="box-title">Recently Expenses</h3>

		// 		        <div class="box-tools pull-right">
		// 		          <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
		// 		          </button>
		// 		          <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		// 		        </div>
		// 		      </div>
		// 		      <!-- /.box-header -->
		// 		      <div class="box-body">
		// 		        <ul class="products-list product-list-in-box">
		// 		          <li class="item">
		// 		            <div class="product-img">
		// 		              <img src="'.$path.'/dist/img/default-50x50.gif" alt="Product Image">
		// 		            </div>
		// 		            <div class="product-info">
		// 		              <a href="javascript:void(0)" class="product-title">Lift Maintenance
		// 		                <span class="label label-warning pull-right">$1800</span></a>
		// 		              <span class="product-description">
		// 		                    Samsung 32" 1080p 60Hz LED Smart HDTV.
		// 		                  </span>
		// 		            </div>
		// 		          </li>
		// 		          <!-- /.item -->
		// 		          <li class="item">
		// 		            <div class="product-img">
		// 		              <img src="'.$path.'/dist/img/default-50x50.gif" alt="Product Image">
		// 		            </div>
		// 		            <div class="product-info">
		// 		              <a href="javascript:void(0)" class="product-title">Get Together
		// 		                <span class="label label-info pull-right">$700</span></a>
		// 		              <span class="product-description">
		// 		                    26" Mongoose Dolomite Men\'s 7-speed, Navy Blue.
		// 		                  </span>
		// 		            </div>
		// 		          </li>
		// 		          <!-- /.item -->
		// 		          <li class="item">
		// 		            <div class="product-img">
		// 		              <img src="'.$path.'/dist/img/default-50x50.gif" alt="Product Image">
		// 		            </div>
		// 		            <div class="product-info">
		// 		              <a href="javascript:void(0)" class="product-title">Navratri<span
		// 		                  class="label label-danger pull-right">$350</span></a>
		// 		              <span class="product-description">
		// 		                    Xbox One Console Bundle with Halo Master Chief Collection.
		// 		                  </span>
		// 		            </div>
		// 		          </li>
		// 		          <!-- /.item -->
		// 		          <li class="item">
		// 		            <div class="product-img">
		// 		              <img src="'.$path.'/dist/img/default-50x50.gif" alt="Product Image">
		// 		            </div>
		// 		            <div class="product-info">
		// 		              <a href="javascript:void(0)" class="product-title">PlayStation 4
		// 		                <span class="label label-success pull-right">$399</span></a>
		// 		              <span class="product-description">
		// 		                    PlayStation 4 500GB Console (PS4)
		// 		                  </span>
		// 		            </div>
		// 		          </li>
		// 		          <!-- /.item -->
		// 		        </ul>
		// 		      </div>
		// 		      <!-- /.box-body -->
		// 		      <div class="box-footer text-center">
		// 		        <a href="javascript:void(0)" class="uppercase">View All Expenses</a>
		// 		      </div>
		// 		      <!-- /.box-footer -->
		// 		    </div>
		// 		    <!-- /.box -->
		// 		  </div>
		// 		</div>
		// 		');
	}
}