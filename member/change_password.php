<?php
$title = "Change Password";
include_once 'header.php';
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

/* Fresh Change Password Theme - Same as Dashboard */
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
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

/* Fresh Card */
.fresh-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    padding: 32px;
    margin-bottom: 24px;
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease-out;
}

.fresh-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

/* Fresh Header */
.fresh-header {
    background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    border-radius: var(--radius-lg);
    padding: 32px;
    color: white;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.fresh-header::before {
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

.fresh-header-content {
    position: relative;
    z-index: 2;
}

.fresh-header h1 {
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.fresh-header p {
    font-size: 2rem;
    font-weight: 600;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.fresh-header-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    opacity: 0.9;
}

/* Fresh Form */
.fresh-form-group {
    margin-bottom: 24px;
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
    padding: 16px 20px;
    color: var(--text-primary);
    font-size: 2rem;
    font-weight: 500;
    transition: all 0.3s ease;
    font-family: inherit;
    margin-bottom: 16px;
}

.fresh-form-control:focus {
    border-color: var(--brand-primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    transform: translateY(-1px);
}

.fresh-form-control::placeholder {
    color: var(--text-muted);
    font-weight: 400;
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

/* Fresh Mobile Responsive */
@media (max-width: 768px) {
    .fresh-container {
        padding: 16px;
    }

    .fresh-header {
        padding: 24px;
    }

    .fresh-header h1 {
        font-size: 2rem;
        font-weight: 900;
    }

    .fresh-header p {
        font-size:2rem;
        font-weight: 600;
    }

    .fresh-card {
        padding: 24px;
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

    .fresh-btn {
        font-size: 2rem;
        font-weight: 800;
        padding: 14px 28px;
    }
}
</style>

<!-- Fresh Change Password Container -->
<div class="fresh-container">
    <!-- Fresh Header -->
    <div class="fresh-header">
        <div class="fresh-header-content">
            <div class="fresh-header-icon">
                <i class="fas fa-key"></i>
            </div>
            <h1>Change Password</h1>
            <p>Update your account security</p>
        </div>
    </div>

    <!-- Fresh Password Form Card -->
    <div class="fresh-card">
        <form action="change_password_model.php" method="post">
            <div class="fresh-form-group">
                <label class="fresh-form-label" for="old_password">Current Password *</label>
                <input class="fresh-form-control" type="password" id="old_password" name="old_password" placeholder="Enter your current password" maxlength="20" required="required">
            </div>

            <div class="fresh-form-group">
                <label class="fresh-form-label" for="password">New Password *</label>
                <input class="fresh-form-control" type="password" id="password" name="password" placeholder="Enter your new password" maxlength="20" required="required" onchange="form.confirm_password.pattern = this.value;">
            </div>

            <div class="fresh-form-group">
                <label class="fresh-form-label" for="confirm_password">Confirm New Password *</label>
                <input class="fresh-form-control" type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" maxlength="20" required="required">
            </div>

            <div style="text-align: center; margin-top: 32px;">
                <button type="submit" class="fresh-btn fresh-btn-primary">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php include_once 'footer.php'; ?>