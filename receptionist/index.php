<?php
require_once '../includes/header.php';
check_role('receptionist');

global $conn;
$appointments = $conn->query("SELECT a.*, p.name as patient_name, d.name as doctor_name 
                              FROM appointments a 
                              JOIN patients p ON a.patient_id = p.id 
                              JOIN doctors d ON a.doctor_id = d.id 
                              WHERE a.status = 'scheduled'");
?>

<h2>Receptionist Dashboard</h2>
<h4>Upcoming Appointments</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $appointments->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>