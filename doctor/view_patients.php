<?php
require_once '../includes/header.php';
check_role('doctor');

global $conn;
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT DISTINCT p.* 
                        FROM patients p 
                        JOIN appointments a ON p.id = a.patient_id 
                        JOIN doctors d ON a.doctor_id = d.id 
                        WHERE d.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$patients = $stmt->get_result();
?>

<h2>My Patients</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Contact</th>
            <th>Medical History</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $patients->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['dob']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                <td><?php echo htmlspecialchars($row['medical_history']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>