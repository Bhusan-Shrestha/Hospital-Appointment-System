<?php
require_once '../includes/header.php';
check_role('admin');

global $conn;
$records = $conn->query("SELECT mr.*, p.name as patient_name, d.name as doctor_name 
                         FROM medical_records mr 
                         JOIN patients p ON mr.patient_id = p.id 
                         JOIN doctors d ON mr.doctor_id = d.id");
?>

<h2>Medical Records</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Visit Date</th>
            <th>Symptoms</th>
            <th>Diagnosis</th>
            <th>Prescription</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $records->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['visit_date']); ?></td>
                <td><?php echo htmlspecialchars($row['symptoms']); ?></td>
                <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
                <td><?php echo htmlspecialchars($row['prescription']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>