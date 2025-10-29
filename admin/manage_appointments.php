<?php
require_once '../includes/header.php';
check_role('admin');

global $conn;

if (isset($_GET['update_id']) && isset($_GET['status'])) {
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_GET['status'], $_GET['update_id']);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Appointment updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating appointment.</div>";
    }
}

$appointments = $conn->query("SELECT a.*, p.name as patient_name, d.name as doctor_name 
                              FROM appointments a 
                              JOIN patients p ON a.patient_id = p.id 
                              JOIN doctors d ON a.doctor_id = d.id");
?>

<h2>Manage Appointments</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $appointments->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="?update_id=<?php echo $row['id']; ?>&status=completed" class="btn btn-sm btn-success">Complete</a>
                    <a href="?update_id=<?php echo $row['id']; ?>&status=cancelled" class="btn btn-sm btn-danger">Cancel</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>