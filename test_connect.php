<?php
// Turn on error reporting for this test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";
echo "Current PHP Version: " . phpversion() . "<br><br>";

// Include the DB file
$db_file = __DIR__ . '/dashboard/db.php';
if (file_exists($db_file)) {
    echo "Found dashboard/db.php<br>";
    require_once $db_file;
} else {
    die("ERROR: Could not find dashboard/db.php");
}

echo "Attempting to connect with:<br>";
echo "Host: " . DB_HOST . "<br>";
echo "User: " . DB_USER . "<br>";
echo "DB Name: " . DB_NAME . "<br><br>";

// Test Connection Directly to see the error
try {
    echo "Connecting raw...<br>";
    // Enable exception throwing for mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    echo "<h3 style='color: green;'>✅ SUCCESS: Connected to MySQL Database!</h3>";
    $conn->set_charset("utf8mb4");

    // Test Table Existence
    $result = $conn->query("SHOW TABLES LIKE 'leads'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Table 'leads' exists.<br>";

        // Show recent entries
        $count_res = $conn->query("SELECT count(*) as count FROM leads");
        if ($count_res) {
            $count = $count_res->fetch_assoc()['count'];
            echo "📊 Total entries in 'leads' table: $count<br>";
        } else {
            echo "⚠️ Could not count leads: " . $conn->error . "<br>";
        }

    } else {
        echo "❌ Table 'leads' NOT found. Did you run the SQL setup?<br>";
        echo "Trying to create table now...<br>";

        $sql = "CREATE TABLE IF NOT EXISTS `leads` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `phone` varchar(50) DEFAULT NULL,
            `company_name` varchar(255) DEFAULT NULL,
            `service_interest` varchar(255) DEFAULT NULL,
            `budget` varchar(100) DEFAULT NULL,
            `timeline` varchar(100) DEFAULT NULL,
            `message` text,
            `page_source` varchar(100) DEFAULT NULL,
            `status` enum('New','Contacted','Qualified','Lost') DEFAULT 'New',
            `remarks` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        if ($conn->query($sql) === TRUE) {
            echo "✅ Table 'leads' created successfully.<br>";
        } else {
            echo "❌ Error creating table: " . $conn->error . "<br>";
        }
    }

} catch (mysqli_sql_exception $e) {
    echo "<h3 style='color: red;'>❌ CONNECT ERROR: " . $e->getMessage() . "</h3>";
    echo "Error Code: " . $e->getCode() . "<br>";
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ EXCEPTION: " . $e->getMessage() . "</h3>";
}
?>