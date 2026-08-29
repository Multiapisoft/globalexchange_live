<?php
$title = "Profile";
include_once 'header.php';

$query = "SELECT u.*, r.login_id as sponsor_login_id, r.name as sponsor_name FROM user as u"
        . " LEFT JOIN user as r ON r.uid=u.refer_id WHERE u.uid='".$uid."'";
$row = mysqli_fetch_object(my_query( $query));
$locarr = array(
    'city' => array(
        'a' => 1,
        'ml' => 50,
        'rq' => 0,
    ),
    'state' => array(
        'a' => 1,
        'ml' => 50,
        'rq' => 0,
    )
);
$acarr = array(
    'ifsc' => array(
        'a' => 1,
        'ml' => 20,
        'rq' => 0,
    ),
    'bank_name' => array(
        'a' => 1,
        'ml' => 100,
        'rq' => 0,
    ),
    'branch_name' => array(
        'a' => 1,
        'ml' => 100,
        'rq' => 0,
    )
);
$otherarr = array(
    'bitcoin' => array(
        'a' => 1,
        'ml' => 100,
        'rq' => 1,
        'nm' => 'USDT.BEP20 Address',
    )
);

?>

<style>
/* Menu and Submenu Text Colors - Light */
#side-menu li a,
#side-menu li a .menu-text,
.nav-second-level li a,
.nav-second-level li a i,
.sidebar .menu-text {
    color: #eaecef !important;
}

/* Fresh Profile Theme - Same as Dashboard */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    /* Dark Color Palette aligned with dashboard */
    --bg-primary: #0b0e11;
    --bg-secondary: #1e2329;
    --bg-accent: #2b3139;
    --text-primary: #eaecef;
    --text-secondary: #848e9c;
    --text-muted: #6c757d;
    --brand-primary: #4f46e5;
    --brand-secondary: #7c3aed;
    --success: #02c076;
    --warning: #f0b90b;
    --danger: #f6465d;
    --info: #3b82f6;
    --border: #2c3137;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.4);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.6);
    --radius: 12px;
    --radius-lg: 16px;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg-primary);
    color: var(--text-primary);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

#page-wrapper {
    background: var(--bg-primary);
    margin-top: 0;
}

.content-header {
    display: none;
}

/* Fresh Container */
.fresh-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Fresh Card */
.fresh-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    padding: 24px;
    margin-bottom: 24px;
    transition: all 0.3s ease;
}

.fresh-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

/* Fresh Profile Header */
.fresh-profile-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 24px;
}

.fresh-profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fresh-profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    color: white;
    position: relative;
    z-index: 2;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.fresh-profile-info {
    flex: 1;
    position: relative;
    z-index: 2;
}

.fresh-profile-info h1 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fresh-profile-info p {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.95;
    margin-bottom: 18px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-profile-stats {
    display: flex;
    gap: 16px;
}

.fresh-profile-stat {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: var(--radius);
    padding: 12px 16px;
    text-align: center;
    min-width: 100px;
    transition: all 0.3s ease;
}

.fresh-profile-stat:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.fresh-profile-stat-label {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.9;
    margin-bottom: 6px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-profile-stat-value {
    font-size: 2rem;
    font-weight: 800;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

/* Fresh Section Header */
.fresh-section-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: -24px -24px 24px -24px;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.fresh-section-title {
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 14px;
    text-shadow: 0 2px 4px rgba(255, 255, 255, 0.2);
}

.fresh-section-icon {
    font-size: 2rem;
}

/* Fresh Form Styling */
.fresh-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.fresh-form-col {
    display: flex;
    flex-direction: column;
}

.fresh-form-col-full {
    grid-column: 1 / -1;
}

.fresh-form-label {
    display: block;
    margin-bottom: 10px;
    font-size: 2rem;
    color: var(--text-primary);
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-form-control {
    width: 100%;
    background: var(--bg-primary);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 18px;
    color: var(--text-primary);
    font-size: 2rem;
    font-weight: 500;
    transition: all 0.3s ease;
    font-family: inherit;
}

.fresh-form-control:focus {
    border-color: var(--brand-primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.fresh-form-control:disabled,
.fresh-form-control[readonly] {
    background-color: var(--bg-accent);
    opacity: 0.7;
    cursor: not-allowed;
}

.fresh-form-control::placeholder {
    color: var(--text-muted);
}

/* Fresh Button */
.fresh-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    border-radius: 50px;
    font-weight: 800;
    font-size: 2rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.fresh-btn-primary {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: white;
}

.fresh-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.fresh-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.fresh-btn:hover::before {
    left: 100%;
}

/* Fresh Mobile Responsive */
@media (max-width: 768px) {
    .fresh-container {
        padding: 16px;
    }

    .fresh-profile-header {
        flex-direction: column;
        text-align: center;
        gap: 16px;
        padding: 24px;
    }

    .fresh-profile-info h1 {
        font-size: 2rem;
        font-weight: 900;
    }

    .fresh-profile-info p {
        font-size: 2rem;
        font-weight: 600;
    }

    .fresh-profile-stats {
        justify-content: center;
        flex-wrap: wrap;
    }

    .fresh-form-row {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .fresh-card {
        padding: 20px;
        margin-bottom: 20px;
    }

    .fresh-form-label {
        font-size: 2rem;
        font-weight: 700;
    }

    .fresh-form-control {
        font-size: 2rem;
        font-weight: 500;
        padding: 14px 16px;
    }

    .fresh-section-title {
        font-size: 2rem;
        font-weight: 800;
    }

    .fresh-btn {
        font-size: 2rem;
        font-weight: 800;
        padding: 14px 28px;
    }
}

/* Fresh Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fresh-card {
    animation: fadeInUp 0.6s ease-out;
}

.fresh-card:nth-child(1) { animation-delay: 0.1s; }
.fresh-card:nth-child(2) { animation-delay: 0.2s; }
.fresh-card:nth-child(3) { animation-delay: 0.3s; }
.fresh-card:nth-child(4) { animation-delay: 0.4s; }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23848e9c' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 35px;
        padding-top:7px;
    }

    /* Form Grid */
    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: -10px;
    }

    .form-col {
        flex: 1 0 100%;
        padding: 10px;
    }

    @media (min-width: 768px) {
        .form-col-6 {
            flex: 0 0 50%;
        }
    }

    /* Submit Button */
    .btn-submit {
        background: linear-gradient(90deg, #f0b90b, #f8d33a);
        color: #0b0e11;
        border: none;
        border-radius: 6px;
        padding: 12px 25px;
        font-size: 2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    /* Animations */
    @keyframes slideInFade {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .profile-stats {
            justify-content: center;
        }

        .card-body {
            padding: 15px;
        }
    }
</style>

<!-- Fresh Profile Container -->
<div class="fresh-container">
    <!-- Fresh Profile Header -->
    <div class="fresh-profile-header">
        <div class="fresh-profile-avatar">
            <?php echo substr($row->name, 0, 1); ?>
        </div>
        <div class="fresh-profile-info">
            <h1><?php echo $row->name; ?></h1>
            <p>Member since <?php echo date("d M, Y", strtotime($row->datetime)); ?></p>
            <div class="fresh-profile-stats">
                <div class="fresh-profile-stat">
                    <div class="fresh-profile-stat-label">User ID</div>
                    <div class="fresh-profile-stat-value"><?php echo $row->login_id; ?></div>
                </div>
                <div class="fresh-profile-stat">
                    <div class="fresh-profile-stat-label">Sponsor</div>
                    <div class="fresh-profile-stat-value"><?php echo $row->sponsor_name; ?></div>
                </div>
            </div>
        </div>
    </div>

    <form action="profile_model.php" method="post">
        <!-- Personal Information Card -->
        <div class="fresh-card">
            <div class="fresh-section-header">
                <h2 class="fresh-section-title">
                    <i class="fas fa-user"></i>
                    Personal Information
                </h2>
            </div>

            <div class="fresh-form-row">
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="name">Full Name *</label>
                    <input class="fresh-form-control" type="text" id="name" name="name" value="<?php echo $row->name?>" maxlength="50" required="required" <?php if(!empty($row->name)){ echo "readonly='readonly'";}?> pattern="[a-zA-Z ]+">
                </div>
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="login_id">User ID *</label>
                    <input class="fresh-form-control" type="text" <?php if(!empty($row->$key)){ echo "readonly='readonly'";}?> <?php if(!empty($row->login_id)){ echo "readonly='readonly'";}?> id="login_id" name="login_id" value="<?php echo $row->login_id?>" maxlength="100" required="required" pattern="\w{6,100}">
                </div>
            </div>

            <div class="fresh-form-row">
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="email">Email Address *</label>
                    <input class="fresh-form-control" type="email" id="email" name="email" <?php if(!empty($row->email)){ echo "readonly='readonly'";}?> value="<?php echo $row->email?>" maxlength="50" required="required">
                </div>
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="mobile">Mobile Number *</label>
                    <input class="fresh-form-control" type="text" id="mobile" name="mobile" <?php if(!empty($row->mobile)){ echo "readonly='readonly'";}?> value="<?php echo $row->mobile?>" maxlength="10" required="required" pattern="[0-9]{10,10}">
                </div>
            </div>

            <div class="fresh-form-row">
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="gender">Gender</label>
                    <select class="fresh-form-control" id="gender" name="gender">
                        <option value="" disabled="disabled" selected="selected">-- Select Gender --</option>
                        <option value="Male" <?php if($row->gender == "Male"){echo "selected='selected'"; echo "readonly='readonly'";}?>>Male</option>
                        <option value="Female" <?php if($row->gender == "Female"){echo "selected='selected'";}?>>Female</option>
                    </select>
                </div>
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="datetime">Date of Joining</label>
                    <input class="fresh-form-control" type="text" id="datetime" name="datetime" value="<?php echo date("d M, Y h:i A", strtotime($row->datetime));?>" readonly="readonly">
                </div>
            </div>

            <div class="fresh-form-row">
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="sponsor_name">Sponsor Name</label>
                    <input class="fresh-form-control" type="text" id="sponsor_name" name="sponsor_name" value="<?php echo $row->sponsor_name?>" readonly="readonly" maxlength="50">
                </div>
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="sponsor_login_id">Sponsor ID</label>
                    <input class="fresh-form-control" type="text" id="sponsor_login_id" name="sponsor_login_id" value="<?php echo $row->sponsor_login_id?>" readonly="readonly" maxlength="20">
                </div>
            </div>
        </div>

        <!-- Location Information Card -->
        <div class="fresh-card">
            <div class="fresh-section-header">
                <h2 class="fresh-section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Location Information
                </h2>
            </div>

            <div class="fresh-form-row">
                <div class="fresh-form-col fresh-form-col-full">
                    <label class="fresh-form-label" for="country">Country *</label>
                    <select class="fresh-form-control" id="country" name="country" required="required" <?php if(!empty($row->country)){ echo "disabled";}?>>
                        <?php
                        $result2 = my_query("SELECT country_id, short_name FROM country");
                        while ($row2 = my_fetch_object($result2)){
                        ?>
                        <option value="<?php echo $row2->country_id;?>" <?php if($row2->country_id==$row->country){echo "selected='selected'";}?>><?php echo $row2->short_name;?></option>
                        <?php }?>
                    </select>
                </div>
            </div>

            <div class="fresh-form-row">
                <?php foreach ($locarr as $key => $value) {
                    if($value['a']){
                ?>
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="<?php echo $key;?>"><?php echo ucwords(str_replace('_', ' ', $key));?></label>
                    <input class="fresh-form-control" <?php if(!empty($row->$key)){ echo "readonly='readonly'";}?> type="text" id="<?php echo $key;?>" name="<?php echo $key;?>" value="<?php echo $row->$key?>" maxlength="<?php echo isset($value['ml']) && $value['ml'] ? $value['ml'] : 100;?>" <?php echo isset($value['rq']) && $value['rq'] ? 'required="required"' : '';?>>
                </div>
                <?php }}?>
            </div>
        </div>

        <!-- Banking Information Card -->
        <!--<div class="profile-card">-->
        <!--    <div class="card-header">-->
        <!--        <h3><i class="fas fa-university"></i> Banking Information</h3>-->
        <!--    </div>-->
        <!--    <div class="card-body">-->
        <!--        <div class="form-row">-->
        <!--            <div class="form-col form-col-6">-->
        <!--                <label class="form-label" for="account_number">Account Number</label>-->
        <!--                <input class="form-control" type="text" <?php if(!empty($row->account_number)){ echo "readonly='readonly'";}?> id="account_number" name="account_number" value="<?php echo $row->account_number?>" maxlength="20" pattern="[0-9]{8,20}">-->
        <!--            </div>-->
        <!--            <div class="form-col form-col-6">-->
        <!--                <label class="form-label" for="account_holder_name">Account Holder Name</label>-->
        <!--                <input class="form-control" <?php if(!empty($row->account_holder_name)){ echo "readonly='readonly'";}?> type="text" id="account_holder_name" name="account_holder_name" value="<?php echo $row->account_holder_name?>" maxlength="50"  pattern="[a-zA-Z ]+">-->
        <!--            </div>-->
        <!--        </div>-->

        <!--        <div class="form-row">-->
        <!--            <div class="form-col form-col-6">-->
        <!--                <label class="form-label" for="account_type">Account Type</label>-->
        <!--                <select class="form-control" id="account_type" name="account_type" <?php if(!empty($row->account_type)){ echo "disabled";}?>>-->
        <!--                    <option value="" disabled="disabled" selected="selected">-- Select Account Type --</option>-->
        <!--                    <option <?php if($row->account_type=='Saving'){echo "selected='selected'";}?>>Saving</option>-->
        <!--                    <option <?php if($row->account_type=='Current'){echo "selected='selected'";}?>>Current</option>-->
        <!--                </select>-->
        <!--            </div>-->
        <!--        </div>-->

        <!--        <div class="form-row">-->
                    <?php foreach ($acarr as $key => $value) {
                      if($value['a']){
                  ?>
        <!--            <div class="form-col form-col-6">-->
        <!--                <label class="form-label" for="<?php echo $key;?>"><?php echo ucwords(str_replace('_', ' ', $key));?></label>-->
        <!--                <input class="form-control" type="text" <?php if(!empty($row->$key)){ echo "readonly='readonly'";}?> id="<?php echo $key;?>" name="<?php echo $key;?>" value="<?php echo $row->$key?>" maxlength="<?php echo isset($value['ml']) && $value['ml'] ? $value['ml'] : 100;?>">-->
        <!--            </div>-->
        <!--            <?php }}?>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <!-- Cryptocurrency Addresses Card -->
        <div class="fresh-card">
            <div class="fresh-section-header">
                <h2 class="fresh-section-title">
                    <i class="fab fa-bitcoin"></i>
                    Cryptocurrency Addresses
                </h2>
            </div>

            <div class="fresh-form-row">
                <?php foreach ($otherarr as $key => $value) {
                    if($value['a']){
                ?>
                <div class="fresh-form-col">
                    <label class="fresh-form-label" for="<?php echo $key;?>"><?php echo ucwords(str_replace('_', ' ', (isset($value['nm']) && $value['nm']) ? $value['nm'] : $key));?></label>
                    <input class="fresh-form-control" type="text" id="<?php echo $key;?>" name="<?php echo $key;?>" value="<?php echo $row->$key?>" maxlength="<?php echo isset($value['ml']) && $value['ml'] ? $value['ml'] : 100;?>" <?php if(!empty($row->$key)){ echo "readonly='readonly'";}?>>
                </div>
                <?php }}?>
            </div>
        </div>

        <div style="text-align: center; margin-top: 32px; margin-bottom: 32px;">
            <input type="hidden" name="uid" value="<?php echo $row->uid?>" />
            <button type="submit" class="fresh-btn fresh-btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<?php include_once 'footer.php'; ?>