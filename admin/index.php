<?php
require_once '../includes/header.php';
check_role('admin');

global $conn;

// Totals
$total_patients  = (int)$conn->query("SELECT COUNT(*) AS c FROM patients")->fetch_assoc()['c'];
$total_doctors   = (int)$conn->query("SELECT COUNT(*) AS c FROM doctors")->fetch_assoc()['c'];

// Upcoming = scheduled and in the future (or now)
$upcoming_appointments = (int)$conn->query("
    SELECT COUNT(*) AS c 
    FROM appointments 
    WHERE status = 'scheduled' AND appointment_date >= NOW()
")->fetch_assoc()['c'];

// Completed
$completed_appointments = (int)$conn->query("
    SELECT COUNT(*) AS c 
    FROM appointments 
    WHERE status = 'completed'
")->fetch_assoc()['c'];
?>

<h2>Admin Dashboard</h2>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Patients</h5>
                <p class="card-text"><?= $total_patients; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Doctors</h5>
                <p class="card-text"><?= $total_doctors; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Upcoming Appointments</h5>
                <p class="card-text"><?= $upcoming_appointments; ?></p>
            </div>
        </div>
    </div>

    <!-- New stat -->
    <div class="col-md-4 mt-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Completed Appointments</h5>
                <p class="card-text"><?= $completed_appointments; ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
