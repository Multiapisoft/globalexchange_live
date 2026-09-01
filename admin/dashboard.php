<?php $title = 'Dashboard';
include_once 'header.php';
?>


<style>
    @media (min-width: 768px) {
    .navbar-brand {
        font-weight: 700;
        font-size: 21px !important;
    }
}
</style>

<div class=row>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-3">
            <h2><span class=count-number2><?php echo get_count('user', 'uid');?></span></h2>
            <div class=small>Users </div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class=count-number2><?php echo get_sum('user', 'wallet')*1;?></span></h2>
            <div class=small>Wallet</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class=count-number2><?php echo get_sum('user', 'wallet_topup')*1;?></span></h2>
            <div class=small>Topup Wallet</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('investments', 'amount', "amount>0")*1;?></span></h2>
            <div class=small>Investments</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_growth', 'amount')*1;?></span></h2>
            <div class=small>ROI Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_binary', 'amount')*1;?></span></h2>
            <div class=small>Matching Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>*/?>
    <?php /* Referral Income / Bonus card hidden
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_direct', 'amount', 'type=0')*1;?></span></h2>
            <div class=small>Referral Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    */ ?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_direct', 'amount', 'type=1')*1;?></span></h2>
            <div class=small>Stacking subscription package- Generation Distribution</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=0')*1;?></span></h2>
            <div class=small>subscription package- Generation Distribution</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=1')*1;?></span></h2>
            <div class=small>Level Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>*/ ?>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=2')*1;?></span></h2>
            <div class=small>Level ROI Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <?php /*
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=3')*1;?></span></h2>
            <div class=small>Gold Auto Pool Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=4')*1;?></span></h2>
            <div class=small>Diamond Auto Pool Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>*/?>
    <?php /* Reward Income & Royalty Income removed from admin dashboard
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_royalty', 'amount', 'type=0')*1;?></span></h2>
            <div class=small>Reward Income</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_royalty', 'amount', 'type=1')*1;?></span></h2>
            <div class=small>Fast Track Bonus</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_royalty', 'amount', 'type=2')*1;?></span></h2>
            <div class=small>Royalty Income</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_royalty', 'amount', 'type=2')*1;?></span></h2>
            <div class=small>Special Reward Income</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_royalty', 'amount', 'type=3')*1;?></span></h2>
            <div class=small>Pool Income</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>*/?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_royalty', 'amount', 'type=2')*1;?></span></h2>
            <div class=small>Airdrop Income</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_direct', 'amount', 'type=2')*1;?></span></h2>
            <div class=small>Referral Airdrop Rewad Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-4">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=1')*1;?></span></h2>
            <div class=small>Level Airdrop Income</div>
            <i class="ti-bag statistic_icon"></i>
            <div class="sparkline4 text-center"></div>
        </div>
    </div>*/?>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-2">
            <h2><span class=count-number2><?php echo get_sum('income_level', 'amount', 'type=3')*1;?></span></h2>
            <div class=small>Dream Income</div>
            <i class="ti-user statistic_icon"></i>
            <div class="sparkline2 text-center"></div>
        </div>
    </div>*/?>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class=count-number2><?php echo get_sum('deposit_block', 'amount')*1;?></span></h2>
            <div class=small>Deposit</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-1">
            <h2><span class=count-number2><?php echo get_sum('deposit_block', 'amount_coin')*1;?></span></h2>
            <div class=small>Deposit Token</div>
            <i class="ti-server statistic_icon"></i>
            <div class="sparkline1 text-center"></div>
        </div>
    </div>*/?>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-3">
            <h2><span class=count-number2><?php echo get_sum('withdrawal_block', 'amount', "status = 1")*1;?></span></h2>
            <div class=small>Withdrawal USDT</div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    <?php /*<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-3">
            <h2><span class=count-number2><?php echo get_sum('withdrawal_block', 'amount_coin', "type2 = '".SITE_CURRENCY_TKN."'")*1;?></span></h2>
            <div class=small>Withdrawal Token</div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-3">
            <h2><span class=count-number2><?php echo get_sum('withdrawal_block', 'amount', "type2 != '".SITE_CURRENCY_TKN."'")*1;?></span></h2>
            <div class=small>Withdrawal USDT</div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="statistic-box statistic-filled-3">
            <h2><span class=count-number2><?php echo get_sum('withdrawal_block', 'amount', "type2 = '".SITE_CURRENCY_TKN."'")*1;?></span></h2>
            <div class=small>Withdrawal Token</div>
            <i class="ti-world statistic_icon"></i>
            <div class="sparkline3 text-center"></div>
        </div>
    </div>*/?>
</div>
<?php include_once 'footer.php'; ?>