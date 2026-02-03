<?php
require_once 'db.php';
check_auth();

$page_title = "Leads Management";

// Handle Actions (Form Submissions)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_lead') {
        $id = $_POST['lead_id'];
        $status = $_POST['status'];
        $remarks = $_POST['remarks'];
        update_lead_status($id, $status, $remarks);
    }
}

// Handle Filters
$from_date = isset($_GET['from']) ? $_GET['from'] : '';
$to_date = isset($_GET['to']) ? $_GET['to'] : '';

$leads = get_all_leads($from_date, $to_date);
?>
<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header">
                <h3 class="card-heading">Inquiries</h3>
                
                <!-- Date Filter Form -->
                <form method="GET" class="d-flex gap-2 align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-secondary small">From:</label>
                        <input type="date" name="from" class="form-input" value="<?php echo $from_date; ?>" style="padding: 8px 12px;">
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-secondary small">To:</label>
                        <input type="date" name="to" class="form-input" value="<?php echo $to_date; ?>" style="padding: 8px 12px;">
                    </div>
                    <button type="submit" class="btn-primary btn-sm">Filter</button>
                    <?php if($from_date || $to_date): ?>
                        <a href="leads.php" class="text-secondary small text-decoration-none">Clear</a>
                    <?php endif; ?>
                    <div class="d-flex align-items-center gap-2">
                         <a href="export_leads.php?from=<?php echo $from_date; ?>&to=<?php echo $to_date; ?>" class="btn btn-sm btn-outline-light" style="border: 1px solid var(--accent-gold); color: var(--accent-gold);">
                            <i class="bi bi-download"></i> Export to Excel
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Name / Contact</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($leads)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No leads found for this period.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($leads as $lead): ?>
                        <tr onclick="openLeadModal(<?php echo htmlspecialchars(json_encode($lead)); ?>)">
                            <td class="text-muted">#<?php echo $lead['id']; ?></td>
                            <td>
                                <div class="fw-bold text-white"><?php echo htmlspecialchars($lead['name']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($lead['email']); ?></div>
                            </td>
                            <td><span style="color: var(--accent-gold); font-size: 0.9rem;"><?php echo htmlspecialchars($lead['page_source']); ?></span></td>
                            <td>
                                <?php if($lead['status'] == 'New'): ?>
                                    <span class="badge badge-new">New</span>
                                <?php else: ?>
                                    <span class="badge badge-contacted">Contacted</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted">
                                <?php echo $lead['remarks'] ? htmlspecialchars(substr($lead['remarks'], 0, 20)).'...' : '-'; ?>
                            </td>
                            <td class="text-muted" style="white-space: nowrap;">
                                <?php echo date('M d, Y', strtotime($lead['date'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

             <!-- Pagination (Visual) -->
             <div class="p-3 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                <span class="text-muted small">Showing <?php echo count($leads); ?> leads</span>
             </div>
        </div>
    </div>
</div>

<!-- LEAD DETAILS MODAL -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content dark-modal">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Lead Details #<span id="modalLeadId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="leadForm">
                    <input type="hidden" name="action" value="update_lead">
                    <input type="hidden" name="lead_id" id="inputLeadId">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-secondary small mb-1">Client Name</label>
                            <div class="fs-5 fw-bold text-white" id="modalName"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-secondary small mb-1">Source Page</label>
                            <div class="fs-5 text-gold" id="modalSource" style="color: var(--accent-gold);"></div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-secondary small mb-1">Email</label>
                            <div class="text-white" id="modalEmail"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-secondary small mb-1">Phone</label>
                            <div class="text-white" id="modalPhone"></div>
                        </div>
                    </div>

                    <div class="mb-4 p-3" style="background: #000; border-radius: 8px; border: 1px solid #333;">
                        <label class="text-secondary small mb-2 d-block">Message</label>
                        <p class="text-white mb-0" id="modalMessage" style="font-style: italic;"></p>
                    </div>

                    <hr style="border-color: #333;">

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label class="text-secondary small mb-2">Status</label>
                            <select name="status" id="inputStatus" class="form-input w-100">
                                <option value="New">New</option>
                                <option value="Contacted">Contacted</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="text-secondary small mb-2">Remarks / Notes</label>
                            <textarea name="remarks" id="inputRemarks" class="form-input w-100" rows="2" placeholder="Add internal notes here..."></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" style="background: transparent; border: 1px solid #444; color: #ccc;">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // Modal Logic
    const leadModal = new bootstrap.Modal(document.getElementById('leadModal'));

    function openLeadModal(lead) {
        // Populate Data
        document.getElementById('modalLeadId').textContent = lead.id;
        document.getElementById('inputLeadId').value = lead.id;
        
        document.getElementById('modalName').textContent = lead.name;
        document.getElementById('modalSource').textContent = lead.page_source;
        document.getElementById('modalEmail').textContent = lead.email;
        document.getElementById('modalPhone').textContent = lead.phone;
        document.getElementById('modalMessage').textContent = lead.message;
        
        document.getElementById('inputStatus').value = lead.status;
        document.getElementById('inputRemarks').value = lead.remarks;

        // Show Modal
        leadModal.show();
    }
</script>