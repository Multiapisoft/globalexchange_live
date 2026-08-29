<?php include_once '../lib/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE_NAME; ?> | Terms and Conditions</title>
    <link rel="shortcut icon" href="images/nexabot-logo.png" type="image/x-icon">
    
    <!-- Bootstrap -->
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome -->
    <link href="../assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="../assets/dist/css/component_ui.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/dist/css/skins/component_ui_black.css" rel="stylesheet" type="text/css"/>
    
    <style>
        /* Modern Terms Page Styling */
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: #334155;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .terms-wrapper {
            padding: 40px 20px;
            min-height: 100vh;
        }

        .terms-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
        }

        .terms-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #06b6d4);
        }

        .terms-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 40px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .terms-header img {
            width: 120px;
            margin-bottom: 20px;
        }

        .terms-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .terms-header p {
            color: #64748b;
            font-size: 16px;
            margin: 0;
        }

        .terms-content {
            padding: 40px;
        }

        .terms-section {
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
        }

        .terms-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-number {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .section-content {
            color: #475569;
            line-height: 1.7;
            font-size: 15px;
        }

        .section-content p {
            margin-bottom: 12px;
        }

        .section-content ul {
            margin: 15px 0;
            padding-left: 20px;
        }

        .section-content li {
            margin-bottom: 8px;
            position: relative;
        }

        .section-content li::marker {
            color: #4f46e5;
        }

        .highlight-box {
            background: rgba(79, 70, 229, 0.05);
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .highlight-box h4 {
            color: #4f46e5;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .contact-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            border: 1px solid #e2e8f0;
        }

        .contact-info h3 {
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            color: #475569;
        }

        .contact-item i {
            color: #4f46e5;
            width: 20px;
            text-align: center;
        }

        .back-button {
            position: fixed;
            top: 30px;
            left: 30px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-button:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.4);
            color: white;
            text-decoration: none;
        }

        .last-updated {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        @media (max-width: 768px) {
            .terms-wrapper {
                padding: 20px 10px;
            }
            
            .terms-header {
                padding: 30px 20px;
            }
            
            .terms-content {
                padding: 30px 20px;
            }
            
            .back-button {
                top: 20px;
                left: 20px;
                padding: 10px 16px;
                font-size: 14px;
            }
            
            .section-title {
                font-size: 18px;
            }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Print styles */
        @media print {
            .back-button {
                display: none;
            }
            
            body {
                background: white;
            }
            
            .terms-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <a href="register.php" class="back-button">
        <i class="fa fa-arrow-left"></i>
        Back to Registration
    </a>

    <div class="terms-wrapper">
        <div class="terms-container">
            <div class="terms-header">
                <img src="images/nexabot-logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                <h1>Terms and Conditions</h1>
                <p>Please read these terms carefully before registering with <?php echo SITE_NAME; ?></p>
            </div>

            <div class="terms-content">
                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">1</span>
                        Acceptance of Terms
                    </div>
                    <div class="section-content">
                        <p>By registering as a distributor/member of <?php echo SITE_NAME; ?>, you agree to comply with these Terms and Conditions and the policies outlined by <?php echo SITE_NAME; ?>. If you do not agree, please refrain from using our services.</p>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">2</span>
                        Eligibility
                    </div>
                    <div class="section-content">
                        <p>To become a member of <?php echo SITE_NAME; ?>:</p>
                        <ul>
                            <!--<li>You must be at least 18 years old.</li>-->
                            <!--<li>You must be an Indian citizen or legally residing in India.</li>-->
                            <li>You must not be involved in any prior MLM fraud or legal dispute.</li>
                        </ul>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">3</span>
                        Registration & Membership
                    </div>
                    <div class="section-content">
                        <ul>
                            <!--<li>Accurate personal and bank information must be provided during registration.</li>-->
                            <li>One individual can hold only one active <?php echo SITE_NAME; ?> account.</li>
                            <li>You are solely responsible for your login credentials and any activities under your account.</li>
                        </ul>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">4</span>
                        Compensation and Earnings
                    </div>
                    <div class="section-content">
                        <ul>
                            <li>Earnings are based on the  <?php echo SITE_NAME; ?>-approved team performance.</li>
                            <li>No fixed or guaranteed income is promised. Income depends on your individual efforts.</li>
                            <li>All commissions are released as per the Compensation Plan published on our official portal.</li>
                        </ul>
                        
                        <div class="highlight-box">
                            <h4><i class="fa fa-exclamation-triangle"></i> Important Notice</h4>
                            <p>Income depends on your individual efforts and market conditions. Past performance does not guarantee future results.</p>
                        </div>
                    </div>
                </div>

                <!--<div class="terms-section">-->
                <!--    <div class="section-title">-->
                <!--        <span class="section-number">5</span>-->
                <!--        Product Purchase and Refund Policy-->
                <!--    </div>-->
                <!--    <div class="section-content">-->
                <!--        <ul>-->
                <!--            <li>Product purchase is optional for joining but necessary to earn commissions.</li>-->
                <!--            <li>Refunds (if applicable) will follow <?php echo SITE_NAME; ?>'s official Refund & Return Policy, as available on our website.</li>-->
                <!--            <li>Members are encouraged to understand the product benefits before promoting them.</li>-->
                <!--        </ul>-->
                <!--    </div>-->
                <!--</div>-->

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">5</span>
                        Code of Conduct
                    </div>
                    <div class="section-content">
                        <p>All <?php echo SITE_NAME; ?> members must:</p>
                        <ul>
                            <li>Follow ethical business practices.</li>
                            <li>Avoid false promises or exaggerated income claims.</li>
                            <li>Not recruit <?php echo SITE_NAME; ?> members into other direct-selling/MLM companies.</li>
                            <li>Not use spamming, misleading ads, or misrepresentation.</li>
                        </ul>
                        
                        <div class="highlight-box">
                            <h4><i class="fa fa-warning"></i> Violation Warning</h4>
                            <p>Violation of this code may lead to suspension or permanent ban of your account without notice.</p>
                        </div>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">6</span>
                        Termination of Membership
                    </div>
                    <div class="section-content">
                        <ul>
                            <li><?php echo SITE_NAME; ?> reserves the right to terminate or suspend your membership at any time for breach of terms, fraud, or policy violation.</li>
                            <li>Members may also voluntarily terminate their account by submitting a written request.</li>
                        </ul>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">7</span>
                        Legal Compliance
                    </div>
                    <div class="section-content">
                        <ul>
                            <li><?php echo SITE_NAME; ?> operates under the Consumer Protection (Direct Selling) Rules, 2021, and FDSA and complies with all applicable laws of India.</li>
                            <li>Members are required to comply with the Banning of Unregulated Deposit Schemes Act, 2019.</li>
                            <li>Pyramid schemes or chain money activities are strictly prohibited.</li>
                        </ul>
                    </div>
                </div>

                

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">8</span>
                        Limitation of Liability
                    </div>
                    <div class="section-content">
                        <ul>
                            <li><?php echo SITE_NAME; ?> shall not be held liable for any indirect, incidental, or consequential losses due to membership use or earnings.</li>
                            <li>We do not guarantee uninterrupted platform access, and technical issues may arise at times.</li>
                        </ul>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">9</span>
                        Modification of Terms
                    </div>
                    <div class="section-content">
                        <p><?php echo SITE_NAME; ?> may update these Terms and Conditions at any time. Updates will be posted on the official website. Continued use of the system after updates implies acceptance.</p>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">10</span>
                        Jurisdiction
                    </div>
                    <div class="section-content">
                        <p>This agreement shall be governed by and construed in accordance with the laws of India. All disputes are subject to the jurisdiction of [Your Company's Registered City] Courts only.</p>
                    </div>
                </div>

                <div class="terms-section">
                    <div class="section-title">
                        <span class="section-number">11</span>
                        Contact Information
                    </div>
                    <div class="section-content">
                        <p>For support or clarification:</p>

                        <!--<div class="contact-info">-->
                        <!--    <h3><i class="fa fa-phone-square"></i> Get in Touch</h3>-->
                        <!--    <div class="contact-item">-->
                        <!--        <i class="fa fa-envelope"></i>-->
                        <!--        <span>Email: support@<?php echo strtolower(SITE_NAME); ?>.in</span>-->
                        <!--    </div>-->
                        <!--    <div class="contact-item">-->
                        <!--        <i class="fa fa-phone"></i>-->
                        <!--        <span>Phone: +91-XXXXXXXXXX</span>-->
                        <!--    </div>-->
                        <!--    <div class="contact-item">-->
                        <!--        <i class="fa fa-globe"></i>-->
                        <!--        <span>Website: https://www.<?php echo strtolower(SITE_NAME); ?>.in</span>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                </div>

                <div class="last-updated">
                    <p><i class="fa fa-calendar"></i> Last Updated: <?php echo date('F d, Y'); ?></p>
                    <p>© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../assets/plugins/jQuery/jquery-1.12.4.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Print functionality
        function printTerms() {
            window.print();
        }

        // Add print button functionality if needed
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printTerms();
            }
        });
    </script>
</body>
</html>
