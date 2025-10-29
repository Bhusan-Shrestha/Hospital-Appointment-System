<?php
require_once '../includes/header.php';
check_role('admin');

global $conn;

$total_patients = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$total_doctors = $conn->query("SELECT COUNT(*) as count FROM doctors")->fetch_assoc()['count'];
$total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments")->fetch_assoc()['count'];
$scheduled_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'scheduled'")->fetch_assoc()['count'];
$completed_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'")->fetch_assoc()['count'];
$cancelled_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled'")->fetch_assoc()['count'];
$total_records = $conn->query("SELECT COUNT(*) as count FROM medical_records")->fetch_assoc()['count'];

$recent_records = $conn->query("SELECT mr.id, p.name as patient_name, d.name as doctor_name, mr.visit_date, mr.diagnosis 
                                FROM medical_records mr 
                                JOIN patients p ON mr.patient_id = p.id 
                                JOIN doctors d ON mr.doctor_id = d.id 
                                ORDER BY mr.visit_date DESC 
                                LIMIT 10");
?>

<h2>System Reports</h2>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Patients</h5>
                <p class="card-text"><?php echo $total_patients; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Doctors</h5>
                <p class="card-text"><?php echo $total_doctors; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Appointments</h5>
                <p class="card-text"><?php echo $total_appointments; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Medical Records</h5>
                <p class="card-text"><?php echo $total_records; ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Scheduled Appointments</h5>
                <p class="card-text"><?php echo $scheduled_appointments; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Completed Appointments</h5>
                <p class="card-text"><?php echo $completed_appointments; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Cancelled Appointments</h5>
                <p class="card-text"><?php echo $cancelled_appointments; ?></p>
            </div>
        </div>
    </div>
</div>
<h3>Recent Medical Records</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Visit Date</th>
            <th>Diagnosis</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $recent_records->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['visit_date']); ?></td>
                <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>