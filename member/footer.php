                <?php
                $ge_page = basename($_SERVER['PHP_SELF']);
                $ge_home = in_array($ge_page, ['dashboard.php', 'dsbd.php'], true);
                $ge_wallet = in_array($ge_page, ['deposit_block.php', 'deposit_block2.php', 'withdrawal_block.php', 'fund_transfer.php', 'fund_transfer2.php', 'fund_transfer3.php', 'recharge.php', 'report_deposit_block.php', 'report_withdrawal_block.php', 'report_fund_transfer.php'], true);
                $ge_plans = in_array($ge_page, ['invest.php', 'invest_now.php', 'report_invest.php', 'trade.php', 'live.php', 'live_trading.php'], true);
                $ge_team = in_array($ge_page, ['direct_referral.php', 'downline.php', 'tree_view.php', 'report_direct.php', 'report_level.php', 'report_binary.php'], true);
                $ge_more = in_array($ge_page, ['profile.php', 'change_password.php', 'email_inbox.php', 'email.php', 'report_growth.php', 'report_royalty.php', 'report_reward.php'], true);
                ?>
                <nav class="ge-bottom-nav" aria-label="Mobile navigation">
                    <a href="dashboard.php" class="<?php echo $ge_home ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="deposit_block.php" class="<?php echo $ge_wallet ? 'active' : ''; ?>">
                        <i class="fas fa-wallet"></i>
                        <span>Add fund</span>
                    </a>
                    <a href="invest.php" class="<?php echo $ge_plans ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Plans</span>
                    </a>
                    <a href="direct_referral.php" class="<?php echo $ge_team ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Team</span>
                    </a>
                    <a href="profile.php" class="<?php echo $ge_more ? 'active' : ''; ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </nav>
                </div> <!-- /.main content -->
                </div><!-- /#page-wrapper -->
                </div><!-- /#wrapper -->
                <!-- START CORE PLUGINS -->
                <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
                <script src="../assets/plugins/jquery-ui-1.12.1/jquery-ui.min.js"></script>
                <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
                <script src="../assets/plugins/metisMenu/metisMenu.min.js"></script>
                <script src="../assets/plugins/lobipanel/lobipanel.min.js"></script>
                <script src="../assets/plugins/animsition/js/animsition.min.js"></script>
                <script src="../assets/plugins/fastclick/fastclick.min.js"></script>
                <script src="../assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
                <!-- STRAT PAGE LABEL PLUGINS -->
                <script src="../assets/plugins/icheck/icheck.min.js"></script>
                <script src="../assets/plugins/datatables/dataTables.min.js"></script>
                <?php if ($_SERVER["PHP_SELF"] == '/soft/member/dashboard.php' || (isset($_is_dashboard) && $_is_dashboard)) { ?>
                    <script src="../assets/plugins/toastr/toastr.min.js"></script>
                    <script src="../assets/plugins/sparkline/sparkline.min.js"></script>
                    <script src="../assets/plugins/counterup/jquery.counterup.min.js"></script>
                    <script src="../assets/plugins/counterup/waypoints.js"></script>
                    <script src="../assets/plugins/emojionearea/emojionearea.min.js"></script>
                    <script src="../assets/plugins/monthly/monthly.min.js"></script>
                    <script src="../assets/plugins/amcharts/amcharts.js"></script>
                    <script src="../assets/plugins/amcharts/ammap.js"></script>
                    <script src="../assets/plugins/amcharts/worldLow.js"></script>
                    <script src="../assets/plugins/amcharts/serial.js"></script>
                    <script src="../assets/plugins/amcharts/export.min.js"></script>
                    <script src="../assets/plugins/amcharts/dark.js"></script>
                    <script src="../assets/plugins/amcharts/pie.js"></script>
                <?php } ?>
                <!-- START THEME LABEL SCRIPT -->
                <script src="../assets/dist/js/app.min.js"></script>
                <script>
                    // Disable metisMenu on #side-menu — custom accordion handles it.
                    // metisMenu was collapsing/hiding top-level items on non-dashboard pages.
                    (function ($) {
                        $(function () {
                            var $menu = $('#side-menu');
                            if (!$menu.length) return;
                            try {
                                if (typeof $menu.metisMenu === 'function') {
                                    $menu.metisMenu('dispose');
                                }
                            } catch (e) {}
                            $menu.find('a').off('.metisMenu');
                            $menu.find('ul').removeClass('collapse collapsing in mm-collapse mm-show');
                            $menu.find('li').removeClass('mm-active mm-collapse').css({
                                display: 'block',
                                visibility: 'visible',
                                opacity: 1,
                                height: 'auto',
                                maxHeight: 'none'
                            });
                            $menu.children('li').show();
                        });
                    })(jQuery);
                </script>
                <script>
                    (function ($) {
                        function initGeScrollTop() {
                            var oldBtn = document.getElementById('toTop');
                            if (oldBtn && oldBtn.parentNode) {
                                oldBtn.parentNode.removeChild(oldBtn);
                            }

                            var btn = document.createElement('button');
                            btn.type = 'button';
                            btn.id = 'toTop';
                            btn.className = 'ge-scroll-top';
                            btn.setAttribute('aria-label', 'Scroll to top');
                            btn.setAttribute('title', 'Back to top');
                            btn.innerHTML = '<span class="ge-scroll-top-glow" aria-hidden="true"></span><i class="fas fa-chevron-up" aria-hidden="true"></i>';
                            document.body.appendChild(btn);

                            var threshold = 260;
                            var isVisible = false;
                            var ticking = false;

                            function updateButton() {
                                var scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
                                var show = scrollY > threshold;
                                if (show !== isVisible) {
                                    isVisible = show;
                                    btn.classList.toggle('is-visible', show);
                                }
                                ticking = false;
                            }

                            window.addEventListener('scroll', function () {
                                if (!ticking) {
                                    window.requestAnimationFrame(updateButton);
                                    ticking = true;
                                }
                            }, { passive: true });

                            updateButton();

                            btn.addEventListener('click', function (e) {
                                e.preventDefault();
                                btn.classList.add('is-pressed');
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                                window.setTimeout(function () {
                                    btn.classList.remove('is-pressed');
                                }, 420);
                            });

                            btn.addEventListener('keydown', function (e) {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    btn.click();
                                }
                            });
                        }

                        $(function () {
                            initGeScrollTop();
                        });
                    })(jQuery);
                </script>
                <?php if ($_SERVER["PHP_SELF"] == '/soft/member/dashboard.php' || (isset($_is_dashboard) && $_is_dashboard)) { ?>
                    <script src="../assets/dist/js/page/dashboard_dark.js"></script>
                <?php } ?>
                <script src="../assets/dist/js/jQuery.style.switcher.js"></script>
                <script>
                    $(document).ready(function() {
                        "use strict"; // Start of use strict

                        $('.i-check input').iCheck({
                            checkboxClass: 'icheckbox_polaris',
                            radioClass: 'iradio_polaris'
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        "use strict"; // Start of use strict

                        $('#dataTableExample1').DataTable({
                            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>t<'row'<'col-sm-6'i><'col-sm-6'p>>",
                            "lengthMenu": [
                                [6, 25, 50, -1],
                                [6, 25, 50, "All"]
                            ],
                            "iDisplayLength": 6
                        });

                        $("#dataTableExample2").DataTable({
                            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
                            "lengthMenu": [
                                [10, 25, 50, -1],
                                [10, 25, 50, "All"]
                            ],
                            buttons: [{
                                    extend: 'copy',
                                    className: 'btn-sm'
                                },
                                {
                                    extend: 'csv',
                                    title: 'ExampleFile',
                                    className: 'btn-sm'
                                },
                                {
                                    extend: 'excel',
                                    title: 'ExampleFile',
                                    className: 'btn-sm'
                                },
                                {
                                    extend: 'pdf',
                                    title: 'ExampleFile',
                                    className: 'btn-sm'
                                },
                                {
                                    extend: 'print',
                                    className: 'btn-sm'
                                }
                            ]
                        });
                    });




                    // (function() {
                    //     const originalConsole = window.console || {};
                    //     ['log', 'warn', 'error', 'info', 'debug'].forEach(function(method) {
                    //         console[method] = Function.prototype.bind.call(originalConsole[method] || function() {}, originalConsole);
                    //     });
                    // })();

                   
                </script>
                <style>
                    /* Bottom Navigation - Light Theme */
                    .bottom-nav {
                        display: none;
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        background: #ffffff;
                        justify-content: space-around;
                        padding: 10px 0;
                        border-top: 1px solid rgba(0, 0, 0, 0.1);   
                        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
                        z-index: 1000;
                    }

                    @media screen and (max-width: 768px) {
                        /* Switch to mobile app view */

                        .bottom-nav {
                            display: flex;
                        }
                    }

                    .nav-item {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        text-decoration: none;
                        color: #6c757d;
                        transition: color 0.3s ease;
                    }

                    .nav-item.active {
                        color: #d4af37;
                    }

                    .nav-item:hover {
                        color: #f5c842;
                    }

                    .nav-icon {
                        font-size: 20px;
                        margin-bottom: 4px;
                    }

                    .nav-label {
                        font-size: 10px;
                    }
                </style>
                <!-- Force dashboard theme tokens after page-level styles -->
                <style id="ge-theme-force">
                    :root {
                        --bg-primary: #0a0a0a !important;
                        --bg-secondary: #141414 !important;
                        --bg-accent: #1a1a1a !important;
                        --bg-soft: #111111 !important;
                        --text-primary: #ffffff !important;
                        --text-secondary: #9ca3af !important;
                        --text-muted: #6b7280 !important;
                        --brand-primary: #d4af37 !important;
                        --brand-secondary: #b8860b !important;
                        --brand-bright: #f5c842 !important;
                        --success: #22c55e !important;
                        --warning: #d4af37 !important;
                        --danger: #ef4444 !important;
                        --info: #6366f1 !important;
                        --border: rgba(212, 175, 55, 0.22) !important;
                        --ge-gold: #d4af37 !important;
                        --ge-bg: #0a0a0a !important;
                        --ge-bg-card: #141414 !important;
                        --primary: #d4af37 !important;
                        --accent-color: #d4af37 !important;
                    }
                    html, body {
                        font-family: "Montserrat", system-ui, sans-serif !important;
                        background: #0a0a0a !important;
                        color: #ffffff !important;
                    }
                </style>
                </body>

                </html>