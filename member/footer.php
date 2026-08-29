                <!-- <div class="bottom-nav">
                    <a href="dashboard.php" class="nav-item active">
                        <i class="fas fa-home nav-icon"></i>
                        <span class="nav-label">Home</span>
                    </a>
                    <a href="invest.php" class="nav-item">
                        <i class="fas fa-chart-line nav-icon"></i>
                        <span class="nav-label">Trade</span>
                    </a>
                    <a href="deposit_block.php" class="nav-item">
                        <i class="fas fa-wallet nav-icon"></i>
                        <span class="nav-label">Deposit</span>
                    </a>
                    <a href="sharing_social.php" class="nav-item">
                       <i class="fas fa-gift nav-icon"></i>
                       <span class="nav-label">Earn</span>
                    </a>
                    <a href="profile.php" class="nav-item">
                        <i class="fas fa-user nav-icon"></i>
                        <span class="nav-label">Profile</span>
                    </a>
                </div> -->
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
                        color: #007bff;
                    }

                    .nav-item:hover {
                        color: #007bff;
                    }

                    .nav-icon {
                        font-size: 20px;
                        margin-bottom: 4px;
                    }

                    .nav-label {
                        font-size: 10px;
                    }
                </style>
                </body>

                </html>