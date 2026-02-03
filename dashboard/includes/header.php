<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in (Basic Auth Protection)
// Skip check for login page itself to avoid loops if this header is used there (though usually login has own header)
if (!isset($_SESSION['user_logged_in']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio X Dashboard</title>

    <!-- Bootstrap CSS (for Grid mainly) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Dashboard CSS -->
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <?php if (basename($_SERVER['PHP_SELF']) != 'login.php'): ?>
        <div class="dashboard-layout">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-header">
                    <a href="index.php" class="sidebar-logo">Studio<span>X</span> Console</a>
                </div>
                <nav class="sidebar-nav">
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="bi bi-grid-1x2-fill"></i> Overview
                    </a>
                    <a href="leads.php" class="nav-link <?php echo ($current_page == 'leads.php') ? 'active' : ''; ?>">
                        <i class="bi bi-people-fill"></i> Leads
                    </a>
                    <a href="#" class="nav-link">
                        <i class="bi bi-bar-chart-fill"></i> Analytics
                    </a>
                    <a href="#" class="nav-link">
                        <i class="bi bi-gear-fill"></i> Settings
                    </a>
                </nav>
                <div class="p-4 border-top border-secondary border-opacity-10">
                    <a href="logout.php" class="text-secondary text-decoration-none d-flex align-items-center gap-2">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </aside>

            <!-- Main Content Wrapper -->
            <main class="main-content">
                <!-- Top Mobile Header (visible only on mobile ideally, but keeping simple for now) -->

                <div class="top-bar">
                    <h1 class="page-title">
                        <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                    </h1>

                    <div class="user-profile">
                        <span class="text-secondary small">Welcome, Admin</span>
                        <div class="avatar-circle">A</div>
                    </div>
                </div>
            <?php endif; ?>