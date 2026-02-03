<?php
// dashboard/db.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DATABASE CONFIGURATION
// !IMPORTANT: CHANGE THESE TO YOUR ACTUAL DATABASE CREDENTIALS
define('DB_HOST', 'localhost');
define('DB_USER', 'u931655514_studiox');     // Your DB Username
define('DB_PASS', 'StudioX@123');         // Your DB Password
define('DB_NAME', 'u931655514_studiox'); // Your DB Name

// Connect to Database
function get_db_connection()
{
    // Enable error reporting for mysqli to ensure we catch exceptions
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        // Log error but don't crash the user facing page
        error_log("DB Connection failed: " . $e->getMessage());
        return null; // Return null so the form handler can continue (e.g. at least send email)
    }
}

// SAVE LEAD (Used by form handlers)
function save_lead($data)
{
    $conn = get_db_connection();
    if (!$conn)
        return false;

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO leads (name, email, phone, company_name, service_interest, budget, timeline, message, page_source, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'New', NOW())");

    // Bind parameters
    // s = string
    // Schema: name, email, phone, company, service, budget, timeline, message, source

    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $company = $data['company'] ?? '';
    $service = $data['service'] ?? '';
    $budget = $data['budget'] ?? '';
    $timeline = $data['timeline'] ?? '';
    $message = $data['message'] ?? '';
    $source = $data['source'] ?? 'Website';

    $stmt->bind_param("sssssssss", $name, $email, $phone, $company, $service, $budget, $timeline, $message, $source);

    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $result;
}

// --- HELPER FUNCTIONS FOR DASHBOARD ---

function get_all_leads($from_date = null, $to_date = null)
{
    $conn = get_db_connection();
    if (!$conn)
        return []; // Return empty if DB fails

    $sql = "SELECT * FROM leads";
    $params = [];
    $types = "";

    // Build WHERE clause
    $clauses = [];
    if ($from_date) {
        $clauses[] = "created_at >= ?";
        $params[] = $from_date . " 00:00:00";
        $types .= "s";
    }
    if ($to_date) {
        $clauses[] = "created_at <= ?";
        $params[] = $to_date . " 23:59:59";
        $types .= "s";
    }

    if (!empty($clauses)) {
        $sql .= " WHERE " . implode(" AND ", $clauses);
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $leads = [];
    while ($row = $result->fetch_assoc()) {
        // Map DB columns to Dashboard expected keys if they differ, 
        // but here they are consistent: name, email, phone, message...
        // 'date' in dashboard was expected, but DB has 'created_at'.
        // We map it here or update dashboard to use created_at.
        // Let's alias it for compatibility.
        $row['date'] = $row['created_at'];
        $leads[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $leads;
}

function get_leads_trend($days = 7, $source_filter = null)
{
    $conn = get_db_connection();
    if (!$conn)
        return [];

    // Initialize array with 0 for last $days
    $trend = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $trend[$date] = 0;
    }

    $sql = "SELECT DATE(created_at) as lead_date, COUNT(*) as count FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
    $types = "i";
    $params = [$days];

    if ($source_filter) {
        $sql .= " AND page_source = ?";
        $types .= "s";
        $params[] = $source_filter;
    }

    $sql .= " GROUP BY DATE(created_at)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $trend[$row['lead_date']] = (int) $row['count'];
    }

    $stmt->close();
    $conn->close();

    return $trend;
}

function get_leads_stats()
{
    $conn = get_db_connection();
    if (!$conn)
        return ['total' => 0, 'new' => 0, 'contacted' => 0];

    // Get Total
    $total_res = $conn->query("SELECT COUNT(*) as count FROM leads");
    $total = $total_res->fetch_assoc()['count'];

    // Get New
    $new_res = $conn->query("SELECT COUNT(*) as count FROM leads WHERE status = 'New'");
    $new_leads = $new_res->fetch_assoc()['count'];

    // Get Contacted
    $cont_res = $conn->query("SELECT COUNT(*) as count FROM leads WHERE status = 'Contacted'");
    $contacted = $cont_res->fetch_assoc()['count'];

    $conn->close();

    return [
        'total' => $total,
        'new' => $new_leads,
        'contacted' => $contacted,
    ];
}

function update_lead_status($id, $status, $remarks = '')
{
    $conn = get_db_connection();
    if (!$conn)
        return false;

    $sql = "UPDATE leads SET status = ?, remarks = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $remarks, $id);
    $success = $stmt->execute();

    $stmt->close();
    $conn->close();
    return $success;
}

// Simple Auth Check (Unchanged)
function check_auth()
{
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}
?>