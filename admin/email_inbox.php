<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$title = "Inbox";

// Check if header file exists
if (!file_exists('header.php')) {
    die("Error: header.php file not found!");
}
include_once 'header.php';

// Check if database connection function exists
if (!function_exists('my_query')) {
    die("Error: my_query() function not found! Please check your database connection file.");
}

// Get filter parameter with validation
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$allowed_filters = ['all', 'unread', 'read'];
if (!in_array($filter, $allowed_filters)) {
    $filter = 'all';
}

try {
    // Build query based on filter
    $where_clause = "WHERE m.receiver=0";
    if ($filter === 'unread') {
        $where_clause .= " AND m.read=0";
    } elseif ($filter === 'read') {
        $where_clause .= " AND m.read=1";
    }

    $query = "SELECT m.`recid`, m.`sender`, m.`receiver`, LEFT(m.subject,50) as subject, LEFT(m.message,100) as message, m.`filename`, m.`datetime`, m.`read`"
            . ", s.login_id, s.name FROM `message` as m"
            . " LEFT JOIN user as s ON s.uid=m.sender"
            . " LEFT JOIN user as r ON r.uid=m.receiver"
            . " $where_clause"
            . " ORDER BY m.datetime DESC";
    
    $result = my_query($query);
    
    if (!$result) {
        throw new Exception("Query failed: " . (function_exists('mysqli_error') ? mysqli_error() : 'Unknown database error'));
    }

    // Get count for each filter with error handling
    $all_count_query = "SELECT COUNT(*) as count FROM message WHERE receiver=0";
    $unread_count_query = "SELECT COUNT(*) as count FROM message WHERE receiver=0 AND `read`=0";
    $read_count_query = "SELECT COUNT(*) as count FROM message WHERE receiver=0 AND `read`=1";

    $all_count_result = my_query($all_count_query);
    $unread_count_result = my_query($unread_count_query);
    $read_count_result = my_query($read_count_query);

    if (!$all_count_result || !$unread_count_result || !$read_count_result) {
        throw new Exception("Count query failed");
    }

    $all_count = mysqli_fetch_object($all_count_result)->count ?? 0;
    $unread_count = mysqli_fetch_object($unread_count_result)->count ?? 0;
    $read_count = mysqli_fetch_object($read_count_result)->count ?? 0;

} catch (Exception $e) {
    echo "<div style='background: #fee; color: #c00; padding: 20px; margin: 20px; border: 1px solid #fcc; border-radius: 5px;'>";
    echo "<h3>Database Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Query:</strong> " . htmlspecialchars($query ?? 'Query not set') . "</p>";
    echo "</div>";
    
    // Set default values to prevent further errors
    $all_count = $unread_count = $read_count = 0;
    $result = false;
}

$i = 0;
?>

<style>
.modern-inbox {
    background: #f8fafc;
    min-height: 100vh;
    padding: 20px 0;
}

.inbox-container {
    max-width: 1200px;
    margin: 0 auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.inbox-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 24px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.inbox-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.inbox-title h2 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
}

.inbox-actions {
    display: flex;
    gap: 12px;
}

.btn-modern {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-primary {
    background: rgba(255,255,255,0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.btn-primary:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

.inbox-content {
    display: flex;
    min-height: 600px;
}

.inbox-sidebar {
    width: 280px;
    background: #f9fafb;
    border-right: 1px solid #e5e7eb;
    padding: 24px 0;
}

.filter-section {
    padding: 0 24px;
    margin-bottom: 32px;
}

.filter-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-item {
    margin-bottom: 4px;
}

.filter-link {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border-radius: 8px;
    text-decoration: none;
    color: #6b7280;
    transition: all 0.2s ease;
    justify-content: space-between;
}

.filter-link:hover {
    background: #e5e7eb;
    color: #374151;
}

.filter-link.active {
    background: #3b82f6;
    color: white;
}

.filter-icon {
    width: 20px;
    height: 20px;
    margin-right: 12px;
}

.filter-count {
    background: rgba(0,0,0,0.1);
    color: inherit;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.filter-link.active .filter-count {
    background: rgba(255,255,255,0.2);
}

.inbox-main {
    flex: 1;
    padding: 24px;
}

.inbox-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.selected-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.selected-actions.show {
    opacity: 1;
}

.inbox-stats {
    color: #6b7280;
    font-size: 14px;
}

.email-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.email-item {
    display: block;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.email-item:hover {
    background: #f9fafb;
    border-color: #e5e7eb;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.email-item.unread {
    background: #fefefe;
    border-left: 4px solid #3b82f6;
    font-weight: 500;
}

.email-item.unread:hover {
    background: #f0f9ff;
}

.email-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.email-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid #d1d5db;
    cursor: pointer;
}

.email-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 18px;
}

.email-details {
    flex: 1;
    min-width: 0;
}

.email-sender {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.email-subject {
    font-size: 14px;
    color: #3b82f6;
    font-weight: 500;
    margin-bottom: 4px;
}

.email-preview {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.email-meta {
    text-align: right;
    color: #9ca3af;
    font-size: 13px;
    min-width: 120px;
}

.email-time {
    font-weight: 500;
    margin-bottom: 4px;
}

.email-date {
    font-size: 12px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    opacity: 0.5;
}

.error-box {
    background: #fee;
    color: #c00;
    padding: 20px;
    margin: 20px;
    border: 1px solid #fcc;
    border-radius: 8px;
    font-family: monospace;
}

.debug-info {
    background: #f0f9ff;
    color: #0369a1;
    padding: 15px;
    margin: 20px 0;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .inbox-container {
        margin: 10px;
        border-radius: 8px;
    }
    
    .inbox-content {
        flex-direction: column;
    }
    
    .inbox-sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .filter-section {
        padding: 16px;
    }
    
    .inbox-main {
        padding: 16px;
    }
    
    .email-meta {
        display: none;
    }
    
    .inbox-header {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
}
</style>

<?php
// Debug information
if (isset($_GET['debug'])) {
    echo "<div class='debug-info'>";
    echo "<h4>Debug Information:</h4>";
    echo "<p><strong>Filter:</strong> " . htmlspecialchars($filter) . "</p>";
    echo "<p><strong>All Count:</strong> " . $all_count . "</p>";
    echo "<p><strong>Unread Count:</strong> " . $unread_count . "</p>";
    echo "<p><strong>Read Count:</strong> " . $read_count . "</p>";
    echo "<p><strong>Query:</strong> " . htmlspecialchars($query ?? 'Not set') . "</p>";
    echo "<p><strong>Result:</strong> " . ($result ? 'Success' : 'Failed') . "</p>";
    if ($result) {
        echo "<p><strong>Rows:</strong> " . mysqli_num_rows($result) . "</p>";
    }
    echo "</div>";
}
?>

<div class="modern-inbox">
    <div class="inbox-container">
        <!-- Header -->
        <div class="inbox-header">
            <div class="inbox-title">
                <svg class="filter-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <h2>Inbox</h2>
            </div>
            <div class="inbox-actions">
                <a href="email_compose_mail.php" class="btn-modern btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15.854.146a.5.5 0 0 1 .11.54L13.026 5.5 16 7.5l-4 4.5L8.5 8.5 3 13 1.5 8.5 6.5 3 11 7.5l1.793-7.146a.5.5 0 0 1 .854-.208z"/>
                    </svg>
                    Compose
                </a>
                <button type="button" class="btn-modern btn-danger" id="deleteSelected" style="display: none;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    Delete
                </button>
            </div>
        </div>

        <div class="inbox-content">
            <!-- Sidebar -->
            <div class="inbox-sidebar">
                <div class="filter-section">
                    <div class="filter-title">Filters</div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <a href="?filter=all" class="filter-link <?php echo $filter === 'all' ? 'active' : ''; ?>">
                                <div style="display: flex; align-items: center;">
                                    <svg class="filter-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    All Messages
                                </div>
                                <span class="filter-count"><?php echo $all_count; ?></span>
                            </a>
                        </li>
                        <li class="filter-item">
                            <a href="?filter=unread" class="filter-link <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                                <div style="display: flex; align-items: center;">
                                    <svg class="filter-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Unread
                                </div>
                                <span class="filter-count"><?php echo $unread_count; ?></span>
                            </a>
                        </li>
                        <li class="filter-item">
                            <a href="?filter=read" class="filter-link <?php echo $filter === 'read' ? 'active' : ''; ?>">
                                <div style="display: flex; align-items: center;">
                                    <svg class="filter-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Read
                                </div>
                                <span class="filter-count"><?php echo $read_count; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="filter-section">
                    <div class="filter-title">Quick Actions</div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <a href="email_compose_mail.php" class="filter-link">
                                <div style="display: flex; align-items: center;">
                                    <svg class="filter-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                    </svg>
                                    Send Mail
                                </div>
                            </a>
                        </li>
                        <li class="filter-item">
                            <a href="email_sent_mail.php" class="filter-link">
                                <div style="display: flex; align-items: center;">
                                    <svg class="filter-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Sent Mail
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="inbox-main">
                <div class="inbox-toolbar">
                    <div class="selected-actions" id="selectedActions">
                        <span id="selectedCount">0 selected</span>
                    </div>
                    <div class="inbox-stats">
                        <?php
                        $filter_text = ucfirst($filter);
                        if ($filter === 'all') $filter_text = 'All';
                        echo "$filter_text messages";
                        ?>
                    </div>
                </div>

                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <div class="email-list">
                        <?php while ($row = mysqli_fetch_object($result)): $i++; ?>
                            <a href="email.php?recid=<?php echo htmlspecialchars($row->recid); ?>" class="email-item <?php if($row->read == 0) echo 'unread'; ?>">
                                <div class="email-content">
                                    <input type="checkbox" class="email-checkbox" name="recid[]" value="<?php echo htmlspecialchars($row->recid); ?>" onclick="event.stopPropagation();">
                                    
                                    <div class="email-avatar">
                                        <?php echo strtoupper(substr($row->login_id ?? 'U', 0, 1)); ?>
                                    </div>
                                    
                                    <div class="email-details">
                                        <div class="email-sender"><?php echo htmlspecialchars($row->login_id ?? 'Unknown'); ?></div>
                                        <div class="email-subject"><?php echo htmlspecialchars($row->subject ?? 'No Subject'); ?></div>
                                        <div class="email-preview"><?php echo htmlspecialchars($row->message ?? 'No Message'); ?></div>
                                    </div>
                                    
                                    <div class="email-meta">
                                        <?php if ($row->datetime): ?>
                                            <div class="email-time"><?php echo date("h:i A", strtotime($row->datetime)); ?></div>
                                            <div class="email-date"><?php echo date("M d, Y", strtotime($row->datetime)); ?></div>
                                        <?php else: ?>
                                            <div class="email-time">--:--</div>
                                            <div class="email-date">No Date</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <svg class="empty-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                        <h3>No messages found</h3>
                        <p>
                            <?php 
                            if (!$result) {
                                echo "Database connection error. Please check your configuration.";
                            } elseif ($filter === 'unread') {
                                echo "You have no unread messages.";
                            } elseif ($filter === 'read') {
                                echo "You have no read messages.";
                            } else {
                                echo "Your inbox is empty.";
                            }
                            ?>
                        </p>
                        <?php if (!$result): ?>
                            <p><small>Add '?debug=1' to the URL to see debug information.</small></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.email-checkbox');
    const selectedActions = document.getElementById('selectedActions');
    const selectedCount = document.getElementById('selectedCount');
    const deleteBtn = document.getElementById('deleteSelected');
    
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.email-checkbox:checked');
        const count = selected.length;
        
        selectedCount.textContent = count + ' selected';
        
        if (count > 0) {
            selectedActions.classList.add('show');
            deleteBtn.style.display = 'flex';
        } else {
            selectedActions.classList.remove('show');
            deleteBtn.style.display = 'none';
        }
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Select all functionality
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            checkboxes.forEach(cb => cb.checked = true);
            updateSelectedCount();
        }
    });
    
    // Delete selected functionality
   deleteBtn.addEventListener('click', function () {
    const selected = document.querySelectorAll('.email-checkbox:checked');
    if (selected.length > 0) {
        if (confirm('Are you sure you want to delete ' + selected.length + ' message(s)?')) {
            const recids = Array.from(selected).map(cb => cb.value);

            fetch('delete_email_messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ recids: recids })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href="email_inbox.php"
                } else {
                    alert('Failed to delete messages');
                }
            });
        }
    }
});
});
</script>

<?php 
// Check if footer file exists before including
if (file_exists('footer.php')) {
    include_once 'footer.php'; 
} else {
    echo "<!-- footer.php not found -->";
}
?>