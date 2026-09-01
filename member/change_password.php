<?php
$title = "Change Password";
include_once 'header.php';
?>

<style>
/* Change Password — match profile.html / profile.php theme */
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
}

.ge-panel > h3 {
  margin: 0 0 1rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #fff;
}

.ge-pass-stack {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-width: 28rem;
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
.ge-field input {
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
.ge-field input:focus {
  outline: none;
  border-color: #d4af37;
  box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
}
.ge-field input::placeholder {
  color: #6b7280;
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
  transition: filter 0.2s ease;
}
.ge-btn-gold:hover {
  filter: brightness(1.06);
  color: #1a1408;
  text-decoration: none;
}

.ge-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: center;
  margin-top: 0.25rem;
}

.ge-hint {
  margin: 1rem 0 0;
  font-size: 0.78rem;
  color: #9ca3af;
}

.ge-secure {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  border-radius: 14px;
  border: 1px solid rgba(212, 175, 55, 0.22);
  background: linear-gradient(90deg, rgba(212, 175, 55, 0.08), transparent);
  padding: 1.15rem 1.25rem;
}
.ge-secure-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  color: #1a1408;
  font-size: 1.1rem;
}
.ge-secure h2 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #fff;
}
.ge-secure p {
  margin: 0.3rem 0 0;
  font-size: 0.85rem;
  color: #9ca3af;
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
    <h1>Change Password</h1>
    <p>Update your account security</p>
  </div>

  <section class="ge-secure animate-in">
    <div class="ge-secure-icon"><i class="fas fa-key"></i></div>
    <div>
      <h2>Keep your account secure</h2>
      <p>Use a strong password and never share it. You will need your current password to confirm the change.</p>
    </div>
  </section>

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

      <div class="ge-actions">
        <button type="submit" class="ge-btn-outline">Update Password</button>
        <a href="profile.php" class="ge-btn-gold">Back to Profile</a>
      </div>
    </form>
    <p class="ge-hint">Choose a password you don’t use elsewhere. Confirm carefully before saving.</p>
  </section>
</div>

<?php include_once 'footer.php'; ?>
