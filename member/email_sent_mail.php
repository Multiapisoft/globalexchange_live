<?php
$title = "Sent Mail";
$_is_dashboard = 1;
include_once 'header.php';
user();
$login_id = isset($user->login_id) ? $user->login_id : '';

$query = "SELECT m.`recid`, m.`sender`, m.`receiver`, LEFT(m.subject,50) as subject, LEFT(m.message,100) as message, m.`filename`, m.`datetime`, m.`read`"
        . ", r.login_id, r.name FROM `message` as m"
        . " LEFT JOIN user as s ON s.uid=m.sender"
        . " LEFT JOIN user as r ON r.uid=m.receiver"
        . " WHERE m.sender='$uid'"
        . " ORDER BY m.datetime DESC";
$result = my_query($query);
$rows = array();
while ($row = mysqli_fetch_object($result)) {
    $rows[] = $row;
}
$total = count($rows);
?>

<style>
/* Sent Mail — match inbox/compose dark/gold theme */
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

.ge-mail-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 1rem;
}
.ge-mail-meta h2 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #fff;
}
.ge-mail-meta span {
  font-size: 0.8rem;
  color: #9ca3af;
}

.ge-inbox-list {
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
}

.ge-inbox-item {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 0.9rem 1rem;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.06);
  background: #0a0a0a;
  color: inherit;
  text-decoration: none;
  transition: border-color 0.15s, background 0.15s;
}
.ge-inbox-item:hover {
  border-color: rgba(212, 175, 55, 0.4);
  background: #111;
  color: inherit;
  text-decoration: none;
}

.ge-inbox-avatar {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: rgba(212, 175, 55, 0.12);
  border: 1px solid rgba(212, 175, 55, 0.3);
  color: #d4af37;
  font-size: 0.85rem;
  font-weight: 700;
}

.ge-inbox-body {
  flex: 1;
  min-width: 0;
}
.ge-inbox-top {
  display: flex;
  flex-wrap: wrap;
  align-items: baseline;
  justify-content: space-between;
  gap: 0.35rem 0.75rem;
  margin-bottom: 0.25rem;
}
.ge-inbox-from {
  font-size: 0.9rem;
  font-weight: 600;
  color: #e5e7eb;
}
.ge-inbox-from em {
  font-style: normal;
  color: #9ca3af;
  font-weight: 500;
  font-size: 0.78rem;
}
.ge-inbox-date {
  font-size: 0.72rem;
  color: #9ca3af;
  white-space: nowrap;
}
.ge-inbox-subject {
  margin: 0 0 0.2rem;
  font-size: 0.88rem;
  font-weight: 600;
  color: #d4af37;
}
.ge-inbox-preview {
  margin: 0;
  font-size: 0.8rem;
  color: #9ca3af;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ge-inbox-empty {
  text-align: center;
  padding: 2.5rem 1rem;
  color: #9ca3af;
}
.ge-inbox-empty i {
  display: block;
  margin-bottom: 0.75rem;
  font-size: 1.75rem;
  color: #d4af37;
  opacity: 0.7;
}
.ge-inbox-empty p {
  margin: 0 0 1rem;
  font-size: 0.9rem;
}
.ge-btn-gold {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.7rem 1.25rem;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #ffe566 0%, #d4af37 50%, #b8860b 100%);
  color: #0a0a0a;
  font-size: 0.88rem;
  font-weight: 700;
  font-family: inherit;
  text-decoration: none;
  box-shadow: 0 6px 18px rgba(212, 175, 55, 0.28);
}
.ge-btn-gold:hover {
  filter: brightness(1.06);
  color: #0a0a0a;
  text-decoration: none;
}
</style>

<div class="ge-mail">
  <div class="ge-mail-head">
    <h1>Sent Mail</h1>
    <p>Messages you have sent<?php echo $login_id !== '' ? ' · ' . htmlspecialchars($login_id) : ''; ?></p>
  </div>

  <div class="ge-panel">
    <nav class="ge-mail-tabs" aria-label="Mailbox">
      <a href="email_inbox.php"><i class="fa fa-inbox"></i> Inbox</a>
      <a href="email_compose_mail.php"><i class="fa fa-pencil"></i> Compose</a>
      <a href="email_sent_mail.php" class="active"><i class="fa fa-paper-plane"></i> Sent</a>
    </nav>
  </div>

  <div class="ge-panel">
    <div class="ge-mail-meta">
      <h2>Sent Messages</h2>
      <span><?php echo (int) $total; ?> total</span>
    </div>

    <?php if ($total === 0) { ?>
      <div class="ge-inbox-empty">
        <i class="fa fa-paper-plane"></i>
        <p>You have not sent any messages yet.</p>
        <a href="email_compose_mail.php" class="ge-btn-gold"><i class="fa fa-pencil"></i> Compose Mail</a>
      </div>
    <?php } else { ?>
      <div class="ge-inbox-list">
        <?php foreach ($rows as $row) {
            $to_name = ((int) $row->receiver !== 0)
                ? trim($row->name . (!empty($row->login_id) ? ' (' . $row->login_id . ')' : ''))
                : 'Admin';
            $initial = strtoupper(substr($to_name, 0, 1));
            if ($initial === '') {
                $initial = 'A';
            }
            $dt = strtotime($row->datetime);
        ?>
          <a href="email.php?recid=<?php echo (int) $row->recid; ?>" class="ge-inbox-item">
            <div class="ge-inbox-avatar"><?php echo htmlspecialchars($initial); ?></div>
            <div class="ge-inbox-body">
              <div class="ge-inbox-top">
                <div class="ge-inbox-from"><em>To:</em> <?php echo htmlspecialchars($to_name); ?></div>
                <div class="ge-inbox-date">
                  <?php echo date('h:i A', $dt); ?> · <?php echo date('d M Y', $dt); ?>
                </div>
              </div>
              <div class="ge-inbox-subject"><?php echo htmlspecialchars($row->subject); ?></div>
              <p class="ge-inbox-preview"><?php echo htmlspecialchars($row->message); ?>…</p>
            </div>
          </a>
        <?php } ?>
      </div>
    <?php } ?>
  </div>
</div>

<?php include_once 'footer.php'; ?>
