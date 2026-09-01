<?php
$title = "Profile";
include_once 'header.php';

$query = "SELECT u.*, r.login_id as sponsor_login_id, r.name as sponsor_name FROM user as u"
        . " LEFT JOIN user as r ON r.uid=u.refer_id WHERE u.uid='".$uid."'";
$row = mysqli_fetch_object(my_query($query));

$locarr = array(
    'city' => array('a' => 1, 'ml' => 50, 'rq' => 0),
    'state' => array('a' => 1, 'ml' => 50, 'rq' => 0),
);
$acarr = array(
    'ifsc' => array('a' => 1, 'ml' => 20, 'rq' => 0),
    'bank_name' => array('a' => 1, 'ml' => 100, 'rq' => 0),
    'branch_name' => array('a' => 1, 'ml' => 100, 'rq' => 0),
);
$otherarr = array(
    'bitcoin' => array(
        'a' => 1,
        'ml' => 100,
        'rq' => 1,
        'nm' => 'USDT.BEP20 Address',
    ),
);

$reward_arr = function_exists('get_reward') ? get_reward() : array();
$rank_label = isset($reward_arr[(int) $row->reward]) ? $reward_arr[(int) $row->reward] : 'Member';
$is_active = ((int) $row->status === 0);
$has_bot = false;
$botCheck = @my_query("SELECT recid FROM investments WHERE uid = '" . (int) $uid . "' AND ipid = 4 AND is_closed = 0 LIMIT 1");
if ($botCheck && my_num_rows($botCheck) > 0) {
    $has_bot = true;
}

$name_parts = preg_split('/\s+/', trim((string) $row->name));
$initials = '';
if (!empty($name_parts[0])) {
    $initials .= strtoupper(substr($name_parts[0], 0, 1));
}
if (!empty($name_parts[1])) {
    $initials .= strtoupper(substr($name_parts[1], 0, 1));
}
if ($initials === '') {
    $initials = 'U';
}
?>

<style>
/* Profile UI — match Desktop profile.html */
.content-header { display: none !important; }

.ge-profile {
  max-width: 900px;
  width: 100%;
  margin: 0 auto;
  padding: 0.25rem 0 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  box-sizing: border-box;
  font-family: "Montserrat", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
  color: #fff;
  font-size: 16px;
  line-height: 1.55;
  -webkit-font-smoothing: antialiased;
}
.ge-profile *,
.ge-profile *::before,
.ge-profile *::after {
  box-sizing: border-box;
}

.ge-profile-head h1 {
  margin: 0;
  font-size: clamp(1.15rem, 2.5vw, 1.35rem);
  font-weight: 700;
  color: #fff;
}
.ge-profile-head p {
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  color: #9ca3af;
}

.ge-panel {
  border-radius: 14px;
  border: 1px solid rgba(212, 175, 55, 0.22);
  background: #141414;
  padding: 1.25rem 1.35rem;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.35);
}
@media (min-width: 640px) {
  .ge-panel { padding: 1.5rem 1.75rem; }
  .ge-panel.hero { padding: 1.75rem; }
}

.ge-hero {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 1.25rem;
}
@media (min-width: 640px) {
  .ge-hero {
    flex-direction: row;
    align-items: center;
  }
}

.ge-avatar {
  width: 80px;
  height: 80px;
  border-radius: 999px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: 700;
  color: #1a1408;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  letter-spacing: 0.02em;
}

.ge-hero h2 {
  margin: 0;
  font-size: clamp(1.35rem, 3vw, 1.6rem);
  font-weight: 700;
  color: #fff;
}
.ge-hero .uid {
  margin: 0.35rem 0 0;
  font-size: 0.9rem;
  color: #9ca3af;
}
.ge-hero .uid span {
  color: #d4af37;
  font-weight: 600;
}

.ge-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
}
.ge-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.28rem 0.7rem;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}
.ge-badge-success {
  background: rgba(34, 197, 94, 0.15);
  color: #22c55e;
  border: 1px solid rgba(34, 197, 94, 0.35);
}
.ge-badge-gold {
  background: rgba(212, 175, 55, 0.15);
  color: #f5c842;
  border: 1px solid rgba(212, 175, 55, 0.4);
}
.ge-badge-silver {
  background: rgba(192, 192, 192, 0.12);
  color: #c0c0c0;
  border: 1px solid rgba(192, 192, 192, 0.3);
}
.ge-badge-muted {
  background: rgba(255, 255, 255, 0.06);
  color: #9ca3af;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.ge-panel > h3 {
  margin: 0 0 1rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
}

.ge-form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 640px) {
  .ge-form-grid { grid-template-columns: 1fr 1fr; }
}
.ge-form-grid .span-2 {
  grid-column: 1 / -1;
}

.ge-field label {
  display: block;
  margin: 0 0 0.5rem;
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #d4af37;
}
.ge-field input,
.ge-field select {
  width: 100%;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #fff;
  padding: 0.75rem 0.9rem;
  font-size: 0.95rem;
  font-weight: 500;
  font-family: inherit;
  line-height: 1.4;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.ge-field select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 36px;
}
.ge-field input:focus,
.ge-field select:focus {
  outline: none;
  border-color: #d4af37;
  box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
}
.ge-field input[readonly],
.ge-field input:disabled,
.ge-field select:disabled {
  background: #111;
  color: #9ca3af;
  cursor: not-allowed;
}
.ge-field input::placeholder {
  color: #6b7280;
}

.ge-btn-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.4rem;
  border: none;
  border-radius: 12px;
  font-family: inherit;
  font-size: 0.95rem;
  font-weight: 700;
  color: #1a1408;
  cursor: pointer;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  box-shadow: 0 6px 18px rgba(212, 175, 55, 0.28);
  text-decoration: none;
  transition: filter 0.2s ease, transform 0.2s ease;
}
.ge-btn-gold:hover {
  filter: brightness(1.06);
  color: #1a1408;
  text-decoration: none;
}

.ge-btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.7rem 1.25rem;
  border-radius: 12px;
  font-family: inherit;
  font-size: 0.95rem;
  font-weight: 650;
  color: #f5c842;
  background: transparent;
  border: 1px solid rgba(212, 175, 55, 0.65);
  cursor: pointer;
  text-decoration: none;
  transition: background 0.2s ease, border-color 0.2s ease;
}
.ge-btn-outline:hover {
  background: rgba(212, 175, 55, 0.1);
  color: #ffe566;
  text-decoration: none;
}

.ge-hint {
  margin: 1rem 0 0;
  font-size: 0.78rem;
  color: #9ca3af;
}

.ge-pass-stack {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-width: 28rem;
}

.animate-in {
  animation: geFadeUp 0.4s ease both;
}
@keyframes geFadeUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="ge-profile">
  <div class="ge-profile-head">
    <h1>Profile</h1>
    <p>Account security &amp; registration details</p>
  </div>

  <!-- Hero (profile.html) -->
  <section class="ge-panel hero ge-hero animate-in">
    <div class="ge-avatar"><?php echo htmlspecialchars($initials); ?></div>
    <div class="flex-1" style="min-width:0;">
      <h2><?php echo htmlspecialchars($row->name); ?></h2>
      <p class="uid">User ID: <span><?php echo htmlspecialchars($row->login_id); ?></span></p>
      <div class="ge-badges">
        <?php if ($is_active): ?>
          <span class="ge-badge ge-badge-success">Active</span>
        <?php else: ?>
          <span class="ge-badge ge-badge-muted">Inactive</span>
        <?php endif; ?>
        <span class="ge-badge ge-badge-gold"><?php echo htmlspecialchars($rank_label); ?></span>
        <?php if ($has_bot): ?>
          <span class="ge-badge ge-badge-silver">Bot Active</span>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Account details -->
  <section class="ge-panel animate-in">
    <h3>Account details</h3>
    <form action="profile_model.php" method="post" class="ge-form-grid">
      <div class="ge-field">
        <label for="name">Full name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row->name); ?>" maxlength="50" required="required" <?php if (!empty($row->name)) { echo "readonly='readonly'"; } ?> pattern="[a-zA-Z ]+">
      </div>

      <div class="ge-field">
        <label for="email">Email (unique)</label>
        <input type="email" id="email" name="email" <?php if (!empty($row->email)) { echo "readonly='readonly'"; } ?> value="<?php echo htmlspecialchars($row->email); ?>" maxlength="50" required="required">
      </div>

      <div class="ge-field">
        <label for="mobile">Mobile (unique)</label>
        <input type="text" id="mobile" name="mobile" <?php if (!empty($row->mobile)) { echo "readonly='readonly'"; } ?> value="<?php echo htmlspecialchars($row->mobile); ?>" maxlength="10" required="required" pattern="[0-9]{10,10}">
      </div>

      <div class="ge-field">
        <label for="sponsor_login_id">Sponsor ID</label>
        <input type="text" id="sponsor_login_id" name="sponsor_login_id" value="<?php echo htmlspecialchars($row->sponsor_login_id); ?>" readonly="readonly" maxlength="20">
      </div>

      <div class="ge-field">
        <label for="login_id">User ID</label>
        <input type="text" id="login_id" name="login_id" value="<?php echo htmlspecialchars($row->login_id); ?>" maxlength="100" required="required" pattern="\w{6,100}" <?php if (!empty($row->login_id)) { echo "readonly='readonly'"; } ?>>
      </div>

      <div class="ge-field">
        <label for="gender">Gender</label>
        <select id="gender" name="gender">
          <option value="" disabled="disabled" <?php if (empty($row->gender)) echo "selected='selected'"; ?>>-- Select Gender --</option>
          <option value="Male" <?php if ($row->gender == "Male") echo "selected='selected'"; ?>>Male</option>
          <option value="Female" <?php if ($row->gender == "Female") echo "selected='selected'"; ?>>Female</option>
        </select>
      </div>

      <div class="ge-field">
        <label for="datetime">Date of joining</label>
        <input type="text" id="datetime" name="datetime" value="<?php echo date("d M, Y h:i A", strtotime($row->datetime)); ?>" readonly="readonly">
      </div>

      <div class="ge-field">
        <label for="sponsor_name">Sponsor name</label>
        <input type="text" id="sponsor_name" name="sponsor_name" value="<?php echo htmlspecialchars($row->sponsor_name); ?>" readonly="readonly" maxlength="50">
      </div>

      <div class="ge-field span-2">
        <label for="country">Country</label>
        <select id="country" name="country" required="required" <?php if (!empty($row->country)) { echo "disabled"; } ?>>
          <?php
          $result2 = my_query("SELECT country_id, short_name FROM country");
          while ($row2 = my_fetch_object($result2)) {
          ?>
          <option value="<?php echo $row2->country_id; ?>" <?php if ($row2->country_id == $row->country) echo "selected='selected'"; ?>><?php echo htmlspecialchars($row2->short_name); ?></option>
          <?php } ?>
        </select>
      </div>

      <?php foreach ($locarr as $key => $value) {
          if ($value['a']) {
      ?>
      <div class="ge-field">
        <label for="<?php echo $key; ?>"><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
        <input <?php if (!empty($row->$key)) { echo "readonly='readonly'"; } ?> type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars((string) $row->$key); ?>" maxlength="<?php echo isset($value['ml']) && $value['ml'] ? (int) $value['ml'] : 100; ?>" <?php echo isset($value['rq']) && $value['rq'] ? 'required="required"' : ''; ?>>
      </div>
      <?php }} ?>

      <?php foreach ($otherarr as $key => $value) {
          if ($value['a']) {
      ?>
      <div class="ge-field span-2">
        <label for="<?php echo $key; ?>"><?php echo htmlspecialchars(isset($value['nm']) && $value['nm'] ? $value['nm'] : ucwords(str_replace('_', ' ', $key))); ?></label>
        <input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars((string) $row->$key); ?>" maxlength="<?php echo isset($value['ml']) && $value['ml'] ? (int) $value['ml'] : 100; ?>" <?php if (!empty($row->$key)) { echo "readonly='readonly'"; } ?>>
      </div>
      <?php }} ?>

      <!-- Banking kept commented (same as before) -->
      <?php foreach ($acarr as $key => $value) {
          if ($value['a']) {
      ?>
      <!-- bank field: <?php echo $key; ?> -->
      <?php }} ?>

      <div class="span-2">
        <input type="hidden" name="uid" value="<?php echo (int) $row->uid; ?>" />
        <button type="submit" class="ge-btn-gold">Save Changes</button>
      </div>
    </form>
    <p class="ge-hint">One unique mobile + one unique email required. Keep login credentials secure.</p>
  </section>

  <!-- Change password (profile.html) -->
  <section class="ge-panel animate-in">
    <h3>Change password</h3>
    <form action="change_password_model.php" method="post" class="ge-pass-stack">
      <div class="ge-field">
        <label for="old_password">Current password</label>
        <input type="password" id="old_password" name="old_password" placeholder="••••••••" maxlength="20" required="required">
      </div>
      <div class="ge-field">
        <label for="password">New password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" maxlength="20" required="required" onchange="this.form.confirm_password.pattern = this.value;">
      </div>
      <div class="ge-field">
        <label for="confirm_password">Confirm new password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" maxlength="20" required="required">
      </div>
      <div>
        <button type="submit" class="ge-btn-outline">Update Password</button>
      </div>
    </form>
  </section>
</div>

<?php include_once 'footer.php'; ?>
