<?php function redirect($location){echo '<script>window.location.href="'.$location.'";</script>';}
redirect('member/');die;
?>
<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <head>
        <title><?php echo SITE_NAME;?> Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/x-icon" href="../contract/Content/assets/images/favicon.png">

        <!--Google Fonts-->
        <link href="https://fonts.googleapis.com/css?family=Comfortaa:300,400,500,700" rel="stylesheet">
        <!--Font icons-->
        <link href="../contract/Content/Front-theme/fonts/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- BEGIN VENDOR CSS-->
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/fonts/themify/style.min.css">
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/fonts/flag-icon-css/css/flag-icon.min.css">
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/vendors/animate/animate.min.css">
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/vendors/flipclock/flipclock.css">
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/vendors/swiper/css/swiper.min.css">
        <!-- END VENDOR CSS-->
        <!-- END CRYPTO CSS-->
        <!-- BEGIN Page Level CSS-->
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/theme-assets/css/template-counter.css">
        <!-- END Page Level CSS-->
        <!-- BEGIN Custom CSS-->
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/assets/css/style.css">
        <!-- END Custom CSS-->


        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
        <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">

        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/css/util.css">
        <link rel="stylesheet" type="text/css" href="../contract/Content/Front-theme/css/main.css">
        <script src="../contract/js/jquery.min.js"></script>
        <!--===============================================================================================-->
        <script src="https://kit.fontawesome.com/8490f1dbbb.js" crossorigin="anonymous"></script>
        <?php if(SITE_CURRENCY_ == 'TRX'){?>
        <script src="https://cdn.jsdelivr.net/npm/tronweb@2.4.1/dist/TronWeb.node.min.js"></script>
        <?php /*<script src="contract/tron/TronWeb.js"></script>*/?>
        <script src="contract/tron/index.js"></script>
        <script src="contract/tron/login.js"></script>
        <?php }elseif(SITE_CURRENCY_ == 'BNB'){?>
        <script src="https://cdn.ethers.io/lib/ethers-5.0.umd.min.js" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
        <script src="https://cdn.ethers.io/lib/ethers-5.0.umd.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.3.5/web3.min.js" integrity="sha512-S/O+gH5szs/+/dUylm15Jp/JZJsIoWlpSVMwT6yAS4Rh7kazaRUxSzFBwnqE2/jBphcr7xovTQJaopiEZAzi+A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script type="text/javascript" src="contract/bnb/index.js"></script>
        <script type="text/javascript" src="contract/bnb/login.js"></script>
        <script type="text/javascript" src="contract/bnb/script.js"></script>
        <?php }else{?>
        <script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
        <script src="contract/eth/index.js"></script>
        <script src="contract/eth/login.js"></script>
        <?php }?>
        <script>
            $(document).ready(function () {
                SigninPage();
            });
        </script>
        <script async defer id="github-bjs" src="../contract/js/buttons.js"></script>
        <script src="../contract/js/socialjs.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                socialjs.init({
                    container: '.socialjs',
                    https: true
                });
            });
        </script>
        <style>
            .w-100{

            }
            .login100-more{
                background: #062d54 !important; 
            }
            body > div.limiter > div > div > div.login100-more > div.w-100.m-t-100.m-l-auto.m-r-auto.text-center.download-buttons > a{
                margin-bottom: 50px !important;
            }
            body > div.limiter > div > div > div.login100-more > div.w-100.m-t-100.m-l-auto.m-r-auto.text-center.download-buttons{
                margin-top: 0px !important;
            }
            @media(max-width: 700px){
                .w-100{
                    width: 100% !important;
                }   
            }
            .login100-more {
  background: #fff !important;
}
.login100-form {
  background-color: #fff;
}
.text-white {
  color: #000 !important;
}
.text-azure {
  color: #000 !important;
}
.input100 {
  color: #000;
}
        </style>
    </head>
    <body>
        <div id="particles-js"></div>
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100" style="z-index: 2;">
                    <div class="login100-form">
                        <form class="w-100" method="post" action="login_model.php">
                            <span class="login100-form-title p-b-10 text-white">
                                Account Login
                            </span>
                            <div class="w-100">
                                <?php /*<img src="../contract/Content/assets/img/wallets.png" class="w-100 p-b-34" />*/?>
                            </div>
                            <div class="wrap-input100 validate-input m-b-20" data-validate="Type user name">
                                <input id="ethaddress" class="input100" type="text" name="login_id" placeholder="Enter <?php echo SITE_CURRENCY_TOKEN;?> address" required="required" readonly="readonly">
                                <span class="focus-input100"></span>
                            </div>

                            <p class="error m-b-10 alert alert-danger" style="display:none">

                            </p>
                            
                            <?php echo getMessage();?>
                            
                            <p class="m-b-15 text-right text-white">Dont have account? <a href="register.php" class="text-white"> Sign up</a></p>

                            <div class="container-login100-form-btn">
                                <button type="submit" class="btn-success login100-form-btn" id="lgmbtn">
                                    Sign in
                                </button>
                            </div>

                            <div class="container-login100-form-btn m-t-5">
                                <button type="button" class="btn btn-info w-100 lauto dis-none" id="lgambtn" style="display:none" onclick="automatically()">
                                    Sign In  Automatically
                                </button>
                            </div>
                            <div class="w-full text-center m-t-30">
                                <p class="text-white">Contract address</p>
                                <?php if(SITE_CURRENCY_ == 'TRX'){?>
                                <a href="https://tronscan.org/#/contract/<?php echo CONTRACT_ADDRESS;?>" class="txt3" target="_blank">
                                    <?php echo CONTRACT_ADDRESS;?> <i class="fa fa-external-link"></i>
                                </a>
                                <?php }elseif(SITE_CURRENCY_ == 'BNB'){?>
                                <a href="https://bscscan.com/address/<?php echo CONTRACT_ADDRESS;?>" class="txt3" target="_blank">
                                    <?php echo CONTRACT_ADDRESS;?> <i class="fa fa-external-link"></i>
                                </a>
                                <?php }else{?>
                                <a href="https://etherscan.io/address/<?php echo CONTRACT_ADDRESS;?>" class="txt3" target="_blank">
                                    <?php echo CONTRACT_ADDRESS;?> <i class="fa fa-external-link"></i>
                                </a>
                                <?php }?>
                            </div>
                        </form>
                        
                        <?php /*<div class="col-xl-12 col-lg-12 col-md-12 text-center d-flex d-block m-t-10">
                            <div class="row">
                                <p class="text-white col-lg-12">Social Links</p>
                                <div class="socialjs col-lg-12 m-t-10">

                                    <a class="sharebutton facebook btn " target="_blank" style="background-color:#3b5998;color:white" href="https://www.facebook.com/"><i class=" fa fa-facebook"></i></a>

                                    <a class="sharebutton twitter btn" target="_blank" style="background-color:#00acee;color:white" href="https://twitter.com/"><i class=" fa fa-twitter"></i> </a>
                                    <a class="sharebutton linkedin btn" target="_blank" style="background-color:#0e76a8;color:white" href="#"><i class=" fa fa-linkedin"></i> </a>

                                    <a class="sharebutton telegram btn" target="_blank" style="background-color:#0088cc;color:white" href="https://t.me/"><i class="fa fa-telegram"></i> </a>
                                    <a class="sharebutton telegram btn" target="_blank" style="background-color:#ff0000;color:white" href="#"><i class="fa fa-youtube"></i> </a>
                                    <a class="sharebutton telegram btn" target="_blank" style="background-color:#dd2a7b ;color:white" href="http://instagram.com/"><i class="fa fa-instagram"></i> </a>
                                </div>
                            </div>
                        </div>*/?>
                        
                    </div> 
                    <div class="login100-more ">
                        <div class="col-lg-12 m-t-30" style="text-align: center;margin-bottom: 30px;">
                            <a href="https://<?php echo SITE_URL;?>" style="font-size: 48px;color: #fff;"> 
                            <?php //echo SITE_NAME;?>
                            <img src="../contract/Content/assets/images/favicon.png" style="" class="w-100" />
                            </a>
                        </div>
                        <div class="w-100 m-t-100 m-l-auto m-r-auto text-center download-buttons">
                            <?php if(SITE_CURRENCY_ == 'TRX'){?>
                            <a href="https://www.tronlink.org/" target="_blank" class="m-b-5 btn btn-info">Download TronLink</a>
                            <?php }else{?>
                            <a href="https://trustwallet.com/deeplink/" target="_blank" class="m-b-5 btn btn-success" style="background-color:#062d54;border-color:#062d54">Trust Wallet</a>
                            <?php /*<a href="https://metamask.io/" target="_blank" class="m-b-5 btn btn-success" style="background-color:#062d54;border-color:#062d54">Download Metamask</a>*/?>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="dropDownSelect1"></div>

        <!-- particle js-->
        <script src="../contract/Content/assets/js/particles.js"></script>
        <script src="../contract/Content/assets/js/app.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
    </body>
</html>