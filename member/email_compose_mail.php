<?php
$title = "Compose Mail";
$_is_dashboard = 1;
include_once 'header.php';
user();
$login_id = isset($user->login_id) ? $user->login_id : '';
?>

<style>
/* Compose Mail — match panel dark/gold theme */
.content-header { display: none !important; }

.ge-mail {
  max-width: 900px;
  width: 100%;
  margin: 0 auto;
  padding: 0.5rem 0 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  box-sizing: border-box;
  font-family: "Montserrat", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
  color: #fff;
  font-size: 16px;
  line-height: 1.55;
}
.ge-mail *,
.ge-mail *::before,
.ge-mail *::after { box-sizing: border-box; }

.ge-mail-head h1 {
  margin: 0;
  font-size: clamp(1.15rem, 2.5vw, 1.35rem);
  font-weight: 700;
  color: #fff;
}
.ge-mail-head p {
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
  .ge-panel.form-card { padding: 1.75rem; }
}

.ge-mail-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.ge-mail-tabs a {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 0.95rem;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.22);
  background: #0a0a0a;
  color: #9ca3af;
  font-size: 0.82rem;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.15s, border-color 0.15s, background 0.15s;
}
.ge-mail-tabs a:hover {
  color: #fff;
  border-color: rgba(212, 175, 55, 0.45);
}
.ge-mail-tabs a.active {
  color: #0a0a0a;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  border-color: transparent;
}

.ge-form-head {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  margin-bottom: 1.25rem;
}
.ge-form-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #d4af37;
  background: rgba(212, 175, 55, 0.12);
  border: 1px solid rgba(212, 175, 55, 0.35);
  flex-shrink: 0;
}
.ge-form-head h2 {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.ge-form-head p {
  margin: 0.2rem 0 0;
  font-size: 0.85rem;
  color: #9ca3af;
}

.ge-field { margin-bottom: 1rem; }
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
.ge-field textarea {
  width: 100%;
  border-radius: 10px;
  border: 1px solid rgba(212, 175, 55, 0.28);
  background: #0a0a0a;
  color: #fff;
  padding: 0.75rem 0.9rem;
  font-size: 0.95rem;
  font-family: inherit;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.ge-field textarea {
  min-height: 180px;
  resize: vertical;
  line-height: 1.6;
}
.ge-field input::placeholder,
.ge-field textarea::placeholder { color: #6b7280; }
.ge-field input:focus,
.ge-field textarea:focus {
  border-color: rgba(212, 175, 55, 0.65);
  box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.12);
}

.ge-mail-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 0.5rem;
}
.ge-btn-ghost {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.75rem 1.25rem;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.14);
  background: transparent;
  color: #9ca3af;
  font-size: 0.9rem;
  font-weight: 600;
  font-family: inherit;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.15s, border-color 0.15s;
}
.ge-btn-ghost:hover {
  color: #fff;
  border-color: rgba(255, 255, 255, 0.35);
}
.ge-btn-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.75rem 1.4rem;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  color: #0a0a0a;
  font-size: 0.9rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  box-shadow: 0 6px 18px rgba(212, 175, 55, 0.28);
  transition: filter 0.15s;
}
.ge-btn-gold:hover { filter: brightness(1.06); }

@media (max-width: 575px) {
  .ge-mail-actions {
    flex-direction: column-reverse;
  }
  .ge-btn-ghost,
  .ge-btn-gold {
    width: 100%;
  }
}
</style>

<div class="ge-mail">
  <div class="ge-mail-head">
    <h1>Compose Mail</h1>
    <p>Send a message to support<?php echo $login_id !== '' ? ' · ' . htmlspecialchars($login_id) : ''; ?></p>
  </div>

  <div class="ge-panel">
    <nav class="ge-mail-tabs" aria-label="Mailbox">
      <a href="email_inbox.php"><i class="fa fa-inbox"></i> Inbox</a>
      <a href="email_compose_mail.php" class="active"><i class="fa fa-pencil"></i> Compose</a>
      <a href="email_sent_mail.php"><i class="fa fa-paper-plane"></i> Sent</a>
    </nav>
  </div>

  <div class="ge-panel form-card">
    <div class="ge-form-head">
      <div class="ge-form-icon"><i class="fa fa-envelope"></i></div>
      <div>
        <h2>New Message</h2>
        <p>Your message will be delivered to the support team.</p>
      </div>
    </div>

    <form action="email_compose_mail_model.php" method="post">
      <div class="ge-field">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" maxlength="100" required placeholder="What is this about?">
      </div>
      <div class="ge-field">
        <label for="textarea">Message</label>
        <textarea name="message" id="textarea" rows="8" required placeholder="Write your message here..."></textarea>
      </div>
      <div class="ge-mail-actions">
        <a href="email_inbox.php" class="ge-btn-ghost">Discard</a>
        <button type="submit" class="ge-btn-gold"><i class="fa fa-paper-plane"></i> Send</button>
      </div>
    </form>
  </div>
</div>

<?php include_once 'footer.php'; ?>
