<?php
require_once '../includes/header.php';
check_role('admin');

global $conn;

if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Patient deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting patient.</div>";
    }
}

$patients = $conn->query("SELECT * FROM patients");
?>

<h2>Manage Patients</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Contact</th>
            <th>Address</th>
            <th>Medical History</th>
            <th>Actions</th>
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
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['medical_history']); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>