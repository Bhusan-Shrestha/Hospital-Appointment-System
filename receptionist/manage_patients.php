<?php
require_once '../includes/header.php';
check_role('receptionist');

global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_patient'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $medical_history = $_POST['medical_history'];

    $stmt = $conn->prepare("UPDATE patients SET name = ?, dob = ?, gender = ?, contact = ?, address = ?, medical_history = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $name, $dob, $gender, $contact, $address, $medical_history, $id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Patient updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating patient.</div>";
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$patients = $conn->query("SELECT * FROM patients WHERE name LIKE '%$search%'");
?>

<h2>Manage Patients</h2>
<form method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" class="form-control" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Contact</th>
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
                <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                </td>
            </tr>
            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Patient</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($row['dob']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-control" name="gender" required>
                                        <option value="male" <?php if ($row['gender'] == 'male') echo 'selected'; ?>>Male</option>
                                        <option value="female" <?php if ($row['gender'] == 'female') echo 'selected'; ?>>Female</option>
                                        <option value="other" <?php if ($row['gender'] == 'other') echo 'selected'; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="text" class="form-control" name="contact" value="<?php echo htmlspecialchars($row['contact']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" name="address"><?php echo htmlspecialchars($row['address']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="medical_history" class="form-label">Medical History</label>
                                    <textarea class="form-control" name="medical_history"><?php echo htmlspecialchars($row['medical_history']); ?></textarea>
                                </div>
                                <button type="submit" name="update_patient" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>