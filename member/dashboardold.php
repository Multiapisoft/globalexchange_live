<?php
$title = 'Dashboard';
include_once 'header.php';
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
$sponsor = get_user_details($user->refer_id);
//$child_levels = get_child_levels($uid);
$reward_arr = get_reward();
$_address = strtolower(SITE_CURRENCY_) . '_address';
function get_child_bv_total3($uid, $p = 'L'){
    $amt = @my_fetch_object(my_query("SELECT (teamb + topup) as amount FROM user WHERE placement_id = '".$uid."' AND position = '".$p."'"))->amount;
    $amt = ($amt > 0) ? $amt : 0;
    return $amt;
}
$total_in = get_sum('income_royalty', 'amount', "uid='" . $uid . "'") + get_sum('income_growth', 'amount', "uid='" . $uid . "'") + get_sum('income_level', 'amount', "uid='" . $uid . "'") + get_sum('income_direct', 'amount', "uid='" . $uid . "'");

$max = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 0,1"))->amount;
$max = ($max) ? $max : 0;
// $max2 = @mysqli_fetch_object(mysqli_query($link, "SELECT (teamb+topup) AS amount FROM user WHERE refer_id = '".$uid."' AND status = 0 ORDER BY (teamb+topup) DESC LIMIT 1,1"))->amount;
// $max2 = ($max2) ? $max2 : 0;
$max2 = 0;
$max3 = $user->teamb - $max - $max2;
$max3 = ($max3 > 0) ? $max3 : 0;
?>
<style>
    .content-header {
        display: none;
    }
    
    .spacer {
    border-bottom: 1px solid hsla(0, 0%, 100%, .2);
    /*flex: 1;*/
    position: relative;
}

.right-text
{
    float:right;
    font-size: 100%;
    font-weight: 700;
    color : #ffcb05;
}

.space
{
    margin-top:1%;
}

.small, small {
    font-size: 100%;
    font-weight: 700;
}

.social-img
{
    width: 23px;
}

.ref-link
{
    font-size: 14.3px;
}

@media only screen and (max-width: 600px) {
  .reffLink
  {
      font-size: 14px !important;
  }
}
</style>

<br><br><br>
<div class=row>
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <a href="javascript:void(0);" onclick="CopyToClipboard('left_link');" id="left_link_copy" style="position: relative;top: -15px;right: -10px;font-size: 10px;float: right;color:#fff;" class="btn btn-flat .btn-sm bg-yellow">Copy</a>
                <h4 class="header-title m-t-0"><b>Referral Link:</b> <a class="reffLink" href="https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>" target="_blank" id="left_link">https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>
                &nbsp;
                <a href="https://www.facebook.com/sharer/sharer.php?u=https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>" target="_blank"><img class="social-img" src="images/facebook.png"></a>
                <!--&nbsp;-->
                <!--<a href="https://www.instagram.com/share?url=https://lizacoin.live/soft/member/register.php?r=100" target="_blank"><img class="social-img" src="images/social.png"></a>-->
                &nbsp;
                <a href="https://twitter.com/intent/tweet?url=https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>" target="_blank"><img class="social-img" src="images/twitter.png"></a>
                &nbsp;
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>" target="_blank"><img class="social-img" src="images/linkedin.png"></a>
                &nbsp;
                <a href="https://t.me/share/url?url=https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>" target="_blank"><img class="social-img" src="images/telegram.png"></a>
                &nbsp;
                <a href="https://wa.me/?text=https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>" target="_blank"><img class="social-img" src="images/whats.png"></a></a></h4>
            </div>
        </div>
    </div>
    
    
   
     <div >
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-4 col-xxl-3 col-sm-6">
						<div class="card overflow-hidden">
							<div class="card-body">
								<div class="c-con">
									<h4 class="heading mb-0">Congratulations <strong><?php echo $user->name;?>!!</strong><img src="images1/crm/party-popper.png" alt=""></h4>
									<!-- <span>Best seller of the week</span> -->
								</div>
								<div class="c-con-3d">
									<div class="c-con-prise">
										<h3 class="mb-0 text-primary" style="font-size: 22px !important;"><?php echo $uid;?></h3>
										<span class="d-block mb-2" style="font-size: 16px;color: #000;    font-weight: 500;">User ID </span>
										<a href="javascript:void(0)" class="btn btn-primary btn-sm" style="    font-size: 14px !important;">Activation Date : <?php echo ($user->topup_datetime != '0000-00-00 00:00:00') ? date('d M, Y', strtotime($user->topup_datetime)) : '-';?></a>
									</div>
									<img src="images1/crm/Object.png" alt="">
								</div>	
							</div>
						</div>
					</div>
					<div class="col-xl-2 col-xxl-3 col-sm-6">
						<div class="card crm-cart bg-secondary border-0">
							<div class="card-header border-0 pb-0">
								<span class="text-white fs-16">+38%<i class="fa-solid fa-chevron-up ms-1"></i></span>
								<div class="icon-box bg-white">
									<svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M11.4642 13.7074C11.4759 12.1252 10.8504 10.8738 9.60279 9.99009C8.6392 9.30968 7.46984 8.95476 6.33882 8.6137C3.98274 7.89943 3.29927 7.52321 3.29927 6.3965C3.29927 5.14147 4.93028 4.69493 6.32655 4.69493C7.34341 4.69493 8.51331 5.01109 9.23985 5.47964L10.6802 3.24887C9.73069 2.6333 8.43112 2.21342 7.14783 2.0831V0H4.49076V2.22918C2.12884 2.74876 0.640949 4.29246 0.640949 6.3965C0.640949 7.87005 1.25327 9.03865 2.45745 9.86289C3.37331 10.4921 4.49028 10.83 5.56927 11.1572C7.88027 11.8557 8.81873 12.2813 8.80805 13.691L8.80799 13.7014C8.80799 14.8845 7.24005 15.3051 5.89676 15.3051C4.62786 15.3051 3.248 14.749 2.46582 13.9222L0.535522 15.7481C1.52607 16.7957 2.96523 17.5364 4.4907 17.8267V20.0001H7.14783V17.8735C9.7724 17.4978 11.4616 15.9177 11.4642 13.7074Z" fill="var(--primary)"></path>
									</svg>
								</div>
							</div>
							<div class="card-body">
								<div class="crm-cart-data">
									<p><?php echo $reward_arr[$user->reward];?></p>
									<span class="d-block mb-3 text-black" style="font-size: 15px;    color: #000 !important;    font-weight: 500;">Rank</span>
									<!-- <span class="badge bg-white text-black border-0">Last 4 Month</span> -->
								</div>
							</div>
						</div>
					</div>
                    <div class="col-xl-2 col-xxl-3 col-sm-4">
						<div class="card crm-cart bg-primary border-0">
							<div class="card-header border-0 pb-0">
								<span class="text-white fs-16">+34%<i class="fa-solid fa-chevron-up ms-1"></i></span>
								<div class="icon-box bg-white">
									<svg id="_x31__px" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m17.5 13c-3.584 0-6.5-2.916-6.5-6.5s2.916-6.5 6.5-6.5 6.5 2.916 6.5 6.5-2.916 6.5-6.5 6.5zm0-12c-3.033 0-5.5 2.467-5.5 5.5s2.467 5.5 5.5 5.5 5.5-2.467 5.5-5.5-2.467-5.5-5.5-5.5z"/><path d="m17.5 10c-.276 0-.5-.224-.5-.5v-6c0-.276.224-.5.5-.5s.5.224.5.5v6c0 .276-.224.5-.5.5z"/><path d="m20.5 7h-6c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h6c.276 0 .5.224.5.5s-.224.5-.5.5z"/><path d="m19.5 17h-13c-.238 0-.443-.168-.49-.402l-2-10c-.03-.147.009-.299.103-.415.095-.116.237-.183.387-.183h4c.276 0 .5.224.5.5s-.224.5-.5.5h-3.39l1.8 9h12.18l.277-1.385c.054-.271.317-.448.588-.392.271.054.446.317.392.588l-.357 1.787c-.047.234-.252.402-.49.402z"/><path d="m6.5 17c-.233 0-.442-.164-.49-.402l-2.479-12.394c-.14-.699-.759-1.206-1.471-1.206h-.001l-1.559.002c-.276 0-.5-.224-.5-.5s.223-.5.5-.5l1.558-.002h.002c1.188 0 2.219.845 2.452 2.01l2.478 12.394c.054.271-.122.534-.392.588-.033.007-.066.01-.098.01z"/><path d="m21.5 19h-17c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5h2c.276 0 .5.224.5.5s-.224.5-.5.5h-2c-.276 0-.5.224-.5.5s.224.5.5.5h17c.276 0 .5.224.5.5s-.224.5-.5.5z"/><path d="m8 24c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zm0-3c-.551 0-1 .449-1 1s.449 1 1 1 1-.449 1-1-.449-1-1-1z"/><path d="m17 24c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zm0-3c-.551 0-1 .449-1 1s.449 1 1 1 1-.449 1-1-.449-1-1-1z"/></svg>
								</div>
							</div>
							<div class="card-body">
								<div class="crm-cart-data">
									<p class="text-white"><?php echo $user->teamc;//count(get_single_dimensional($child_levels)); ?></p>
									<span class="d-block mb-3 text-white" style="font-size: 15px;    color:#fff !important;    font-weight: 500;">Team</span>
									<!-- <span class="badge bg-white text-black border-0">Last 6 Month</span> -->
								</div>
							</div>
						</div>
					
					</div>		
					<div class="col-xl-2 col-xxl-3 col-sm-4 clm-chart">
						<div class="card crm-cart">
							<div class="card-header border-0 pb-0">
								<div>
									<h4 class="mb-0"><?php echo $user->teamb*1;//count(get_single_dimensional($child_levels)); ?></h4>
									<span class="d-block" style="    font-size: 14px;color: #000000 !important;">Team Business</span>
								</div>	
							</div>
							<div class="card-body custome-tooltip">
								<div id="columnChart"></div>
							</div>
						</div>
					</div>	
					<div class="col-xl-2 col-xxl-3 col-sm-4">
						<div class="card crm-cart">
							<div class="card-header border-0 pb-0">
								<div>
									<h4 class="mb-0"><?php echo $total_in; ?></h4>
									<span class="d-block" style="    font-size: 14px;color: #000000 !important;">Total Income</span>
								</div>	
							</div>
							<div class="card-body d-flex justify-content-center pt-2">
								<div id="AllProject" class="ms-0"></div>
							</div>
						</div>
					</div>	
                    <div class="col-xl-3 col-sm-6">
                        <div class="card box-hover">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bg-success-light rounded">
                                        <i class="fa-solid fa-briefcase text-success"></i>
                                    </div>
                                    <div class="total-projects ms-3">
                                        <h3 class="text-success count"><?php echo round($user->wallet * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h3> 
                                        <span>Wallet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card box-hover">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bg-primary-light rounded">
                                        <i class="fa-solid fa-cart-shopping text-primary"></i>

                                    </div>
                                    <div class="total-projects ms-3">
                                        <h3 class="text-primary count"><?php echo round($user->wallet_topup * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h3> 
                                        <span>Topup Wallet</span>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card box-hover">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bg-warning-light rounded">
                                        <i class="fa-solid fa-users text-warning"></i>
                                    </div>
                                    <div class="total-projects ms-3">
                                        <h3 class="text-warning count"><?php echo round(get_sum('investments', 'amount', "uid='" . $uid . "'") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h3> 
                                        <span>Total Investments</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card box-hover">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box icon-box-lg bg-danger-light rounded">
                                        <i class="fa-solid fa-hand-holding-dollar text-danger"></i>
                                    </div>
                                    <div class="total-projects ms-3">
                                        <h3 class="text-danger count"><?php echo round(get_sum('deposit_block', 'amount', "uid='" . $uid . "' AND status=1") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h3> 
                                        <span>Deposit Fund</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="col-xl-6 col-xxl-7">
						<div class="row">
							<div class="col-xl-6 col-sm-6">
								<div class="card ds-2">
									<div class="card-body">
										<div class="">
											<h3 ><?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?><span class="slight" style="color: #fff !important;"> <?php echo SITE_CURRENCY; ?></span></h3>
											<h4 style="color: #fff !important;">ROI Income</h4>
										</div>	
										<div class="progress-box">
											<div class="d-flex justify-content-between mb-2">
												<!--<p class="mb-0">Complete income</p>-->
												<!-- <p class="mb-0">20/28</p> -->
											</div>
											<div class="progress">
												<div class="progress-bar bg-white" style="width:50%; height:5px; border-radius:4px;" role="progressbar"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-6 col-sm-6">
								<div class="card">
									<div class="card-body">
										<div class="ds-head">
											<h3 class="d-flex align-items-center justify-content-between"><?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h3>
											<h4>Referral Income</h4>
										</div>
										<div class="card-body p-0 custome-tooltip">
											<div id="activeCustomers1"></div>
										</div>	
									</div>
								</div>
							</div>
							<div class="col-xl-6 col-sm-6">
								<div class="card">
									<div class="card-body">
										<div class="ds-head">
											<h4 class="mb-0">Direct Business</h4>
											<h3><?php echo round(get_sum('investments', 'amount', "uid IN (SELECT uid FROM user WHERE status = 0 AND topup>0 AND refer_id='" . $uid . "')") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h3>
										</div>
										<!-- <div class="d-team">
											<span class="d-block text-black">Our Star</span>
											<div class="d-flex align-items-center">
												<div class="avatar-list avatar-list-stacked">
													<img src="images/contacts/pic666.jpg" class="avatar avatar-lg  rounded-circle" alt="">
													<img src="images/contacts/pic555.jpg" class="avatar avatar-lg rounded-circle" alt="">
													<img src="images/contacts/pic1.jpg" class="avatar avatar-lg rounded-circle" alt="">
													<span class="avatar avatar-lg  rounded-circle bg-primary text-white">P</span>
													<img src="images/contacts/pic666.jpg" class="avatar avatar-lg  rounded-circle" alt="">
													<span class="avatar avatar-lg  rounded-circle bg-danger text-white">H</span>
												</div>
												<a href="javascript:void(0)">21+ Team</a>
											</div>
										</div>	 -->
									</div>
								</div>
							</div>
							<div class="col-xl-6 col-sm-6">
								<div class="card">
									<div class="card-body">
										<div class="ds-head">
											<img src="images/uidesgn.png" alt="">
										</div>
										<div class="">
											<h4><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=2") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h4>
											<p style=" font-size: 16px; color: #000 !important;">Level Generation Income</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-6 col-xxl-7">
						<div class="row">
						
							
								<div class="col-xl-6 col-sm-6">
									<div class="card sale-card">
										<div class="card-header pb-0 border-0 align-items-baseline">
											<div>
												<span style=" font-size: 15px; color: #000;">CLD Income</span>
												<h4><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span><i class="fa-solid fa-arrow-trend-up ms-1"></i></h4>
											</div>
											<span class="badge badge-primary border-0">3.5<i class="fa-solid fa-caret-up ms-1"></i></span>
										</div>
										<div class="card-body p-0 custome-tooltip">
											<div id="totalSale"></div>
										</div>
										<div class="card-footer border-0">
											<span class="tag bg-primary">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
											</span>
										</div>	
									</div>
								</div>
								<div class="col-xl-6 col-sm-6">
									<div class="card sale-card">
										<div class="card-header pb-0 border-0 align-items-baseline">
											<div>
												<span style=" font-size: 15px; color: #000;">Upline Income</span>
												<h4><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span> <i class="fa-solid fa-arrow-trend-down ms-1"></i></h4>
											</div>
											<span class="badge badge-secondary border-0">5.5<i class="fa-solid fa-caret-down ms-1"></i></span>
										</div>
										<div class="card-body p-0 custome-tooltip">
											<div id="totalPurchase"></div>
										</div>
										<div class="card-footer border-0">
											<span class="tag bg-secondary">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
											</span>
										</div>	
									</div>
								</div>
								<div class="col-xl-6 col-sm-6">
									<div class="card sale-card">
										<div class="card-header pb-0 border-0 align-items-baseline">
											<div>
												<span style="font-size: 14px;color: #000 !important;">Withdrawal USDT</span>
												<h4><?php echo round(get_sum('withdrawal_block', 'amount', "uid='" . $uid . "'") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span> <i class="fa-solid fa-arrow-trend-down ms-1"></i></h4>
											</div>
											<span class="badge badge-info border-0">6.5<i class="fa-solid fa-caret-down ms-1"></i></span>
										</div>
										<div class="card-body p-0 custome-tooltip">
											<div id="activeCustomers"></div>
										</div>
										<div class="card-footer border-0">
											<span class="tag bg-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
											</span>
										</div>	
									</div>
								</div>
								<!-- <div class="col-xl-6 col-sm-6">
									<div class="card sale-card">
										<div class="card-header pb-0 border-0 align-items-baseline">
											<div>
												<span>Active Customers</span>
												<h4>3,431k <i class="fa-solid fa-arrow-trend-down ms-1"></i></h4>
											</div>
											<span class="badge badge-info border-0">6.5<i class="fa-solid fa-caret-down ms-1"></i></span>
										</div>
										<div class="card-body p-0 custome-tooltip">
											<div id="activeCustomers1"></div>
										</div>
										<div class="card-footer border-0">
											<span class="tag bg-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
											</span>
										</div>	
									</div>
								</div> -->
						</div>
					</div>
				</div>
			</div>
        </div>
        
        
     <script src="vendor/global/global.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
	<script src="vendor/chart-js/chart.bundle.min.js"> </script>
	<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<script src="vendor/apexchart/apexchart.js"></script>
	<script src="vendor/peity/jquery.peity.min.js"></script>
	<!-- Dashboard 1 -->
	<script src="js/dashboard/crm.js"></script>
	<script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
	<script src="vendor/datatables/js/dataTables.buttons.min.js"></script>
	<script src="vendor/datatables/js/buttons.html5.min.js"></script>
	<script src="vendor/datatables/js/jszip.min.js"></script>
	<script src="js/plugins-init/datatables.init.js"></script>
	<!-- Vectormap -->
   <script src="js/custom.min.js"></script>
	<script src="js/deznav-init.js"></script>
	<script src="vendor/global/global.min.js"></script>
	<script src="vendor/chart-js/chart.bundle.min.js"></script>
	<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<script src="vendor/apexchart/apexchart.js"></script>
	<script src="js/dashboard/dashboard-4.js"></script>
   <script src="js/custom.min.js"></script>
	<script src="js/deznav-init.js"></script>
     <link class="main-css" href="css/style.css" rel="stylesheet">
    
      <style>
      
 @media only screen and (max-width: 600px) {
    .reffLink {
        font-size: 11px !important;
    }
}
      @media(max-width:767px){
       .panel-bd{
           margin-top: 70px;
       }
          .fgfh {
    display: block !important;
    margin-top: 138px;
}
    }
    @media(max-width:348px){
        #sparkline8>canvas{
            width: 228px !important;
        }
        #sparkline9>canvas{
            width: 265px !important;
        }
    }
    .total-projects span {
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #000 !important;
}
    .card-body {
    padding: 26px !important;
}
.card-title {
    font-size: 15px !important;
    font-weight: 700 !important;
}
@media (min-width: 768px) {
    .navbar-brand>img {
        height: 68px !important;
        margin-left: 39px !important;
        width: 86px !important;
    }
}
@media only screen and (max-width: 600px) {
    .card-graph {
        display: inline-block;
        width: 364px;
        height: 40px;
        vertical-align: top;
    }
}
.h4, h4 {
    font-size: 15px;
}
.panel-body {
    padding: 15px;
    margin-bottom: -21px !important;
}
.content-header .header-icon {
    font-size: 45px;
    width: 4px !important;
    float: left;
    line-height: 1;
}
.content-header {
    position: relative;
    padding: unset !important;
    margin: unset !important;
}
 .dropdown{
	      display:none !important;
	  }
	  .navbar {
    position: fixed ;
    display: block !important;
    flex-wrap: unset !important;
    align-items: normal !important;
    justify-content: inherit !important;
    padding: unset !important;
}

ol, ul {
    padding-left: 0rem !important;
}

.heading {
    font-size: 2rem !important;
    font-weight: 700 !important;
}
.fgfh {
       display: block !important; 
}
.collapse.in {
    display: block !important;
}
</style>
    
    
    
    
    
    
    
    
    <?php /*<div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <a href="javascript:void(0);" onclick="CopyToClipboard('left_link');" id="left_link_copy" style="position: relative;top: -15px;right: -10px;font-size: 12px;float: right;color:#fff;" class="btn btn-flat .btn-sm bg-yellow">Copy</a>
                <h4 class="header-title m-t-0"><b>Left Referral Link:</b> <a href="https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>&p=L" target="_blank" id="left_link">https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>&p=L</a></h4>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-body">
                <a href="javascript:void(0);" onclick="CopyToClipboard('right_link');" id="right_link_copy" style="position: relative;top: -15px;right: -10px;font-size: 12px;float: right;color:#fff;" class="btn btn-flat .btn-sm bg-yellow">Copy</a>
                <h4 class="header-title m-t-0"><b>Right Referral Link:</b> <a href="https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>&p=R" target="_blank" id="right_link">https://<?php echo SITE_URL;?>/soft/member/register.php?r=<?php echo $uid;?>&p=R</a></h4>
            </div>
        </div>
    </div>
    
    <div class="col-sm-12">
        <span class=small> User ID</span>
        <span class="count-number2 right-text"><?php echo $uid;?></span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Name</span>
        <span class="count-number2 right-text"><?php echo $user->name;?></span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Activation Date</span>
        <span class="count-number2 right-text"><?php echo ($user->topup_datetime != '0000-00-00 00:00:00') ? date('d M, Y', strtotime($user->topup_datetime)) : '-';?></span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Rank</span>
        <span class="count-number2 right-text"><?php echo $reward_arr[$user->reward];?></span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Team</span>
        <span class="count-number2 right-text"><?php echo $user->teamc;//count(get_single_dimensional($child_levels)); ?></span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Team Business</span>
        <span class="count-number2 right-text"><?php echo $user->teamb*1;//count(get_single_dimensional($child_levels)); ?></span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Wallet</span>
        <span class="count-number2 right-text"><?php echo round($user->wallet * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Topup Wallet</span>
        <span class="count-number2 right-text"><?php echo round($user->wallet_topup * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Token</span>
        <span class="count-number2 right-text"><?php echo round($user->wallet_token * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Total Investments</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('investments', 'amount', "uid='" . $uid . "'") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> ROI Income</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
     <div class="col-sm-12 space">
        <span class=small> Referral Income</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type = 0") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Level Generation Income</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=2") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
     <div class="col-sm-12 space">
        <span class=small> Salary Income</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 0") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
     <div class="col-sm-12 space">
        <span class=small> Reward Income</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 1") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    
    <div class="col-sm-12 space">
        <span class=small> Withdrawal USDT</span>
        <span class="count-number2 right-text"><?php echo round(get_sum('withdrawal_block', 'amount', "uid='" . $uid . "'") * 1, 2); ?> USDT</span>
        <div class="spacer"></div>
    </div>
    */?>
    
    
</div>
  <?php /*<div class=row>
     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $uid;?></span></h2>
            <div class=small>User ID</div>
            <img class="statistic_icon" src="images/user1.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $user->name;?></span></h2>
            <div class=small>Name</div>
            <!--<i class="ti-server statistic_icon"></i>-->
            <img class="statistic_icon" src="images/name.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    
   <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo B_RATE_;?></span><span class="slight"> USDT</span></h2>
            <div class=small>Token Rate </div>
            <img class="statistic_icon" src="images/tokenization.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>*/?>
    
    
     <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo ($user->topup_datetime != '0000-00-00 00:00:00') ? date('d M, Y', strtotime($user->topup_datetime)) : '-';?></span></h2>
            <div class=small>Activation Date </div>
            <img class="statistic_icon" src="images/calendar.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    
     <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo ($user->ex_date != '0000-00-00 00:00:00') ? date('d M, Y', strtotime($user->ex_date)) : '-';?></span></h2>
            <div class=small>Ex. Date </div>
            <img class="statistic_icon" src="images/calendar.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>*/?>
    
    
    <?php /*  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number1><?php echo $reward_arr[$user->reward];?></span></h2>
            <div class=small>Rank </div>
            <img class="statistic_icon" src="images/top-three.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-0023">
            <h2><span class=count-number2><?php echo round($user->tbl*1, 2);//round(get_child_bv_total2($uid, 'L')*1, 2);?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Total Left B</div>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-0023">
            <h2><span class=count-number2><?php echo round($user->tbr*1, 2);//round(get_child_bv_total2($uid, 'R')*1, 2);?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Total Right B</div>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>*/?>
    
    
     <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $user->teamc;//count(get_single_dimensional($child_levels)); ?></span></h2>
            <div class=small>Team</div>
            <img class="statistic_icon" src="images/group.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $user->teamb*1;//count(get_single_dimensional($child_levels)); ?></span></h2>
            <div class=small>Team Business</div>
            <img class="statistic_icon" src="images/group.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $max*1; ?></span></h2>
            <div class=small>Strong Leg Business</div>
            <img class="statistic_icon" src="images/group.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $max3*1; ?></span></h2>
            <div class=small>Other leg Business</div>
            <img class="statistic_icon" src="images/group.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>*/?>
     <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $total_in; ?></span></h2>
            <div class=small>Total Income</div>
            <img class="statistic_icon" src="images/group.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    
    
    
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round($user->wallet * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Wallet</div>
            <img class="statistic_icon" src="images/purse1.png" style="width: 16%;">
            <!--<div class="sparkline1 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round($user->wallet_topup * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Topup Wallet</div>
            <img class="statistic_icon" src="images/bitcoin01.png" style="width: 16%;">
            <!--<div class="sparkline1 text-center"></div>-->
        </div>
    </div>
    
    <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class="count-number2"><?php echo round($user->wallet_token * 1, 2); ?></span> <span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Token</div>
            <img class="statistic_icon" src="images/bitcoin01.png" style="width: 16%;">
            <!--<div class="sparkline1 text-center"></div>-->
        </div>
    </div>
    
    
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $user->topup > 0 ? 10 : 0; ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Free Auto Pool Bonus</div>
            <img class="statistic_icon" src="images/bitcoin01.png" style="width: 16%;">
            <!--<div class="sparkline1 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo $user->topup > 0 ? 10 : 0; ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Free Gaming Bonus</div>
            <img class="statistic_icon" src="images/bitcoin01.png" style="width: 16%;">
            <!--<div class="sparkline1 text-center"></div>-->
        </div>
    </div>*/?>
    
     <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('investments', 'amount', "uid='" . $uid . "'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Total Investments</div>
            <img class="statistic_icon" src="images/Total-Investments.png" style="width: 16%;">
            <!--<div class="sparkline2 text-center"></div>-->
        </div>
    </div>
    
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('investments', 'amount', "uid='" . $uid . "'") * 0.6, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Your Binance Fund</div>
            <img class="statistic_icon" src="images/Total-Investments.png" style="width: 16%;">
            <!--<div class="sparkline2 text-center"></div>-->
        </div>
    </div>*/?>
    
    <?php /*  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('deposit_block', 'amount', "uid='" . $uid . "' AND status=1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Deposit Fund</div>
            <img class="statistic_icon" src="images/Total-Investments.png" style="width: 16%;">
            <!--<div class="sparkline2 text-center"></div>-->
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_growth', 'amount', "uid='" . $uid . "'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>ROI Income</div>
            <img class="statistic_icon" src="images/ROI-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    
    
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_binary', 'amount', "uid='" . $uid . "' AND type = 0") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Matching Income</div>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    */?>
    
    <?php /*  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Referral Income</div>
            <img class="statistic_icon" src="images/Referral-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('investments', 'amount', "uid IN (SELECT uid FROM user WHERE status = 0 AND topup>0 AND refer_id='" . $uid . "')") * 1, 2); ?><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Direct Business</div>
            <img class="statistic_icon" src="images/Referral-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Stacking Referral Income</div>
            <img class="statistic_icon" src="images/Referral-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>*/?>
    <?php /**/?>
    
    
     <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=2") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Level Generation Income</div>
            <img class="statistic_icon" src="images/ROI-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=0") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>CLD Income</div>
            <img class="statistic_icon" src="images/Level-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Upline Income</div>
            <img class="statistic_icon" src="images/Level-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class="count-number2"><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 0") * 1, 2); ?></span> <span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Salary Income</div>
            <img class="statistic_icon" src="images/ROI-Income.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=3") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Gold Auto Pool Income</div>
            <img class="statistic_icon" src="images/coin01.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=4") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Diamond Auto Pool Income</div>
            <img class="statistic_icon" src="images/diamond.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 0") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Royalty Income</div>
             <img class="statistic_icon" src="images/crown.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    
    
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Reward Income</div>
            <img class="statistic_icon" src="images/gift-box.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    */?>
    
    
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('investments', 'amount_coin', "uid='" . $uid . "'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Stake Token</div>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Community Development Income</div>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 3") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Pool Income</div>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>*/?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_royalty', 'amount', "uid='" . $uid . "' AND type = 2") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Airdrop Income</div>
            <img class="statistic_icon" src="images/airdrop.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_direct', 'amount', "uid='" . $uid . "' AND type=2") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Referral Airdrop Rewad Income</div>
            <img class="statistic_icon" src="images/airdropBonus.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type=1") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Level Airdrop Income</div>
            <img class="statistic_icon" src="images/airdropLevel.png" style="width: 16%;">
            <!--<div class="sparkline4 text-center"></div>-->
        </div>
    </div>*/?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('investments', 'amount', "uid='" . $uid . "'") * 0.02, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Secure Fund</div>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('income_level', 'amount', "uid='" . $uid . "' AND type = 3") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Dream Income</div>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>*/?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('investments', 'amount', "uid='" . $uid . "'")*4 - get_sum('withdrawal_block', 'amount', "uid='" . $uid . "'"), 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>4X - Withdrawal Limit</div>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    */?>
    
     <?php /* <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('withdrawal_block', 'amount', "uid='" . $uid . "'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Withdrawal USDT</div>
            <img class="statistic_icon" src="images/withdrawal.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    
    
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('withdrawal_block', 'amount_coin', "uid='" . $uid . "'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Withdrawal Token Bonus</div>
            <img class="statistic_icon" src="images/bonus.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>*/?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('withdrawal_block', 'amount', "uid='" . $uid . "' AND type2 != '".SITE_CURRENCY_TKN."'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY; ?></span></h2>
            <div class=small>Withdrawal USDT</div>
            <img class="statistic_icon" src="images/withdrawal.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-002">
            <h2><span class=count-number2><?php echo round(get_sum('withdrawal_block', 'amount', "uid='" . $uid . "' AND type2 = '".SITE_CURRENCY_TKN."'") * 1, 2); ?></span><span class="slight"> <?php echo SITE_CURRENCY_TKN; ?></span></h2>
            <div class=small>Withdrawal Token Bonus</div>
            <img class="statistic_icon" src="images/bonus.png" style="width: 16%;">
            <!--<div class="sparkline3 text-center"></div>-->
        </div>
    </div>*/?>
</div>
<br><br>
<div class=row>
    <div class="col-sm-6">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="header-title">
                    <h3>Latest News</h3>
                </div>
            </div>
            <div class="panel-body">
                <marquee onmouseout="start();" onmouseover="stop();" scrollamount="2" direction="up" behavior="scroll" height="202" width="100%">
                    <?php
                    $news_res = my_query("SELECT title, description FROM cms WHERE mid=1 ORDER BY datetime DESC");
                    while ($news = my_fetch_object($news_res)) {
                        ?>                     
                        <p style="padding-left: 5px; padding-right: 5px; font-size: 16px;"><Strong style="color: #000;"><?php echo strtoupper($news->title); ?> :</Strong> <?php echo $news->description; ?></p>
                    <?php } ?>
                </marquee>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<?php if(SITE_CURRENCY_ == 'BNB'){?>
<script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
<script type="text/javascript" src="../contract/bnb/index.js"></script>
<script type="text/javascript" src="../contract/bnb/login.js"></script>
<?php }else{?>
<script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
<script src="../contract/eth/index.js"></script>
<script src="../contract/eth/login.js"></script>
<?php }?>
<script>
function CopyToClipboard(containerid) {
    if (window.getSelection) {
        if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
        }
    } else if (document.selection) {  // IE?
        document.selection.empty();
    }
    
    if (document.selection) { 
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("Copy"); 
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
        document.execCommand("Copy");
        $('#'+containerid+'_copy').text('Copied');
        $('#'+containerid+'_copy').addClass('bg-green');
        $('#'+containerid+'_copy').removeClass('bg-yellow');
    }
}

$( "#coin_address_copy, #bitcoin_address_copy, #ltc_address_copy, #doge_address_copy, #left_link_copy, #right_link_copy" ).mouseleave(function() {
    $(this).text('Copy');
    $(this).addClass('bg-yellow');
    $(this).removeClass('bg-green');
});
</script>
<?php $hot_news = my_fetch_object(my_query("SELECT * FROM hot_news WHERE recid=1"));
if($hot_news->image && $hot_news->status == 0){
  ?>
    <!-- Boostrap modal dialog -->
    <div id="myModal-22" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?php echo SITE_NAME; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <?php echo '<img src="../uploads/'.$hot_news->image.'" width="100%" />';?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
        $(window).load(function(){
            $('#myModal-22').modal('show');
        });
    </script>
<?php }?>