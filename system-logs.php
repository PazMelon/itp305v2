<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
Auth::requireAdmin();

$pageTitle = "System Logs";
$pageDescription = "View system activity logs";
require_once 'includes/header.php';
?>

<div class="dashboard-card">
    <h2><i class="fas fa-clipboard-list"></i> System Logs</h2>
    <p>View all system activities and events.</p>

    <div class="table-responsive">
        <table id="logsTable" class="display compact" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event</th>
                    <th>Description</th>
                    <th>Performed By</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#logsTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "includes/get-logs.php",
        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "description" },
            { 
                "data": "creator",
                "render": function(data, type, row) {
                    return row.creator_name || 'System';
                }
            },
            { 
                "data": "datetime_added",
                "render": function(data) {
                    return new Date(data).toLocaleString();
                }
            }
        ],
        "order": [[0, 'desc']],
        "pageLength": 25
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>