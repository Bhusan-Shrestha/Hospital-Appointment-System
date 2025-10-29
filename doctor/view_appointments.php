<?php
require_once '../includes/header.php';
check_role('doctor');

global $conn;
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT a.*, p.name as patient_name 
                        FROM appointments a 
                        JOIN patients p ON a.patient_id = p.id 
                        JOIN doctors d ON a.doctor_id = d.id 
                        WHERE d.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>

<h2>My Appointments</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $appointments->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>