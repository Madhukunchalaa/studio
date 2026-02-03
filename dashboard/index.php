<?php
require_once 'db.php';
check_auth();

$page_title = "Overview";
$stats = get_leads_stats();

// Trend Data for Tabs
$trend_all = get_leads_trend(7);
$trend_video = get_leads_trend(7, 'Video Production');
$trend_ai_video = get_leads_trend(7, 'AI Video Production');
$trend_brand = get_leads_trend(7, 'Brand Videos');
$trend_avatar = get_leads_trend(7, 'Avatar Strategy');
$trend_social_marketing = get_leads_trend(7, 'Social Media Marketing');
$trend_social = get_leads_trend(7, 'Contact Page'); // Using Contact Page as catch-all or social if suitable? Or leave Social Media empty if unused.
// Actually, let's keep Social Media if user plans to add it, but 'Contact Page' is useful.
// Let's replace 'Social Media' with 'Contact Page' (General) for now as it has data.
$trend_general = get_leads_trend(7, 'Contact Page');

// Common Dates
$dates = array_keys($trend_all);
$display_dates = array_map(function ($date) {
    return date('M d', strtotime($date));
}, $dates);
?>
<?php include 'includes/header.php'; ?>

<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-title">Total Leads <i class="bi bi-people"></i></div>
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <small class="text-secondary" style="font-size: 0.75rem;">All time</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="border-bottom: 2px solid var(--accent-gold);">
            <div class="stat-title text-gold">New Inquiries <i class="bi bi-bell-fill"></i></div>
            <div class="stat-value text-gold"><?php echo $stats['new']; ?></div>
            <small class="text-secondary" style="font-size: 0.75rem;">Action needed</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-title">This Week <i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-value"><?php echo array_sum($trend_all); ?></div>
            <small class="text-success" style="font-size: 0.75rem;"><i class="bi bi-arrow-up"></i> Trending up</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-title">Pending Contact <i class="bi bi-clock"></i></div>
            <div class="stat-value"><?php echo $stats['new']; ?></div>
        </div>
    </div>
</div>

<!-- ANALYTICS CHART SECTION -->
<div class="row mb-4">
    <div class="col-12">
        <div class="table-card h-100">
            <div
                class="card-header border-0 pb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h3 class="card-heading mb-1">Lead Growth Analytics</h3>
                    <p class="text-secondary small mb-0">Daily inquiry volume over the last 7 days</p>
                </div>

                <!-- Chart Tabs -->
                <div class="nav nav-pills custom-pills" id="chartTabs" role="tablist">
                    <button class="nav-link active" onclick="updateChart('all', this)">All</button>
                    <button class="nav-link" onclick="updateChart('video', this)">Video Production</button>
                    <button class="nav-link" onclick="updateChart('ai_video', this)">AI Video Production</button>
                    <button class="nav-link" onclick="updateChart('brand', this)">Brand Films</button>
                    <button class="nav-link" onclick="updateChart('avatar', this)">AI Avatars</button>
                    <button class="nav-link" onclick="updateChart('social_marketing', this)">Social Marketing</button>
                    <button class="nav-link" onclick="updateChart('general', this)">General Inquiries</button>
                </div>
            </div>

            <div class="p-4">
                <div style="height: 350px; width: 100%; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Leads -->
<div class="row">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header">
                <h3 class="card-heading">Recent Inquiries</h3>
                <a href="leads.php" class="text-gold text-decoration-none small fw-bold">View All <i
                        class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_leads = array_slice(get_all_leads(), 0, 5);
                        if (!empty($recent_leads)):
                            foreach ($recent_leads as $lead):
                                ?>
                                <tr onclick="window.location='leads.php'">
                                    <td><?php echo htmlspecialchars($lead['name']); ?></td>
                                    <td><span
                                            style="color: var(--accent-gold); font-size: 0.85rem;"><?php echo htmlspecialchars($lead['page_source']); ?></span>
                                    </td>
                                    <td class="text-muted"><?php echo date('M d, H:i', strtotime($lead['date'])); ?></td>
                                    <td>
                                        <?php if ($lead['status'] == 'New'): ?>
                                            <span class="badge badge-new">New</span>
                                        <?php else: ?>
                                            <span class="badge badge-contacted">Contacted</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent leads.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Pill Styles */
    .custom-pills {
        gap: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 5px;
    }

    .custom-pills .nav-link {
        background: rgba(255, 255, 255, 0.05);
        color: #888;
        border: 1px solid transparent;
        border-radius: 50px;
        padding: 6px 16px;
        font-size: 0.85rem;
        cursor: pointer;
        white-space: nowrap;
    }

    .custom-pills .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .custom-pills .nav-link.active {
        background: rgba(207, 170, 110, 0.15);
        color: var(--accent-gold);
        border-color: rgba(207, 170, 110, 0.3);
        font-weight: 600;
    }
</style>

<?php include 'includes/footer.php'; ?>

<!-- Override Header Chart.js if needed, but safe to use robust CDN here -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // PREPARE DATASETS
        const chartDatasets = {
            all: <?php echo json_encode(array_values($trend_all)); ?>,
            video: <?php echo json_encode(array_values($trend_video)); ?>,
            ai_video: <?php echo json_encode(array_values($trend_ai_video)); ?>,
            brand: <?php echo json_encode(array_values($trend_brand)); ?>,
            avatar: <?php echo json_encode(array_values($trend_avatar)); ?>,
            social_marketing: <?php echo json_encode(array_values($trend_social_marketing)); ?>,
            general: <?php echo json_encode(array_values($trend_general)); ?>
        };

        const canvas = document.getElementById('trendChart');
        if (!canvas) {
            console.error("Canvas element not found");
            return;
        }

        const ctx = canvas.getContext('2d');

        // Gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(207, 170, 110, 0.2)'); // Gold Low Opacity
        gradient.addColorStop(1, 'rgba(207, 170, 110, 0)');

        const gold = '#cfaa6e';
        const border = 'rgba(255,255,255,0.1)';

        // Init Chart
        window.trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($display_dates); ?>,
                datasets: [{
                    label: 'Inquiries',
                    data: chartDatasets.all,
                    backgroundColor: gradient,
                    borderColor: gold,
                    borderWidth: 2,
                    pointBackgroundColor: '#000',
                    pointBorderColor: gold,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#151515',
                        titleColor: '#fff',
                        bodyColor: '#ccc',
                        borderColor: '#333',
                        borderWidth: 1,
                        displayColors: false,
                        padding: 10,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: border },
                        ticks: { color: '#666', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#888' }
                    }
                }
            }
        });

        // Update Function
        window.updateChart = function (key, btn) {
            if (!window.trendChart) return;

            // Toggle Active Class
            document.querySelectorAll('#chartTabs .nav-link').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');

            // Update Data
            window.trendChart.data.datasets[0].data = chartDatasets[key];
            window.trendChart.update();
        }
    });
</script>