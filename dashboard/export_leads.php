<?php
require_once 'db.php';
check_auth();

// Get Filters
$from_date = isset($_GET['from']) ? $_GET['from'] : '';
$to_date = isset($_GET['to']) ? $_GET['to'] : '';

// Fetch Data
$leads = get_all_leads($from_date, $to_date);

// Filename
$filename = "leads_export_" . date('Y-m-d') . ".csv";

// Set Headers for Download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open Output Stream
$output = fopen('php://output', 'w');

// Add BOM for Excel compatibility (Important for UTF-8)
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Add Column Headers
fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Company', 'Service Interest', 'Budget', 'Timeline', 'Page Source', 'Status', 'Message', 'Remarks', 'Date Created']);

// Add Rows
foreach ($leads as $lead) {
    fputcsv($output, [
        $lead['id'],
        $lead['name'],
        $lead['email'],
        $lead['phone'],
        $lead['company_name'] ?? '',
        $lead['service_interest'] ?? '',
        $lead['budget'] ?? '',
        $lead['timeline'] ?? '',
        $lead['page_source'],
        $lead['status'],
        $lead['message'],
        $lead['remarks'],
        $lead['created_at']
    ]);
}

fclose($output);
exit;
?>