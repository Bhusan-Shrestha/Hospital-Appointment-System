<?php
require_once '../includes/header.php';
check_role('admin'); // only admin

global $conn;

/**
 * Add / Update doctor
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_doctor'])) {
    // NOTE: when editing, the visible <select> is disabled, so we carry user_id via hidden field
    $doctor_id     = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
    $user_id       = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $name          = trim($_POST['name'] ?? '');
    $specialization= trim($_POST['specialization'] ?? '');
    $availability  = trim($_POST['availability'] ?? ''); // plain text like: Mon:09:00-12:00,Wed:14:00-17:00

    if ($doctor_id > 0) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE doctors SET name = ?, specialization = ?, availability = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $specialization, $availability, $doctor_id);
        $ok = $stmt->execute();
        echo $ok
            ? "<div class='alert alert-success'>Doctor updated successfully!</div>"
            : "<div class='alert alert-danger'>Error updating doctor: ".$conn->error."</div>";
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO doctors (user_id, name, specialization, availability) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $specialization, $availability);
        $ok = $stmt->execute();
        echo $ok
            ? "<div class='alert alert-success'>Doctor added successfully!</div>"
            : "<div class='alert alert-danger'>Error adding doctor: ".$conn->error."</div>";
    }
}

/**
 * Delete doctor profile (does not delete login account)
 */
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    echo $stmt->execute()
        ? "<div class='alert alert-success'>Doctor deleted successfully!</div>"
        : "<div class='alert alert-danger'>Error deleting doctor.</div>";
}

/**
 * Doctor being edited
 */
$editDoctor = null;
if (isset($_GET['edit_id'])) {
    $eid = (int)$_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $editDoctor = $stmt->get_result()->fetch_assoc();
}

/**
 * Lists
 */
$doctors = $conn->query("
    SELECT d.*, u.username
    FROM doctors d
    JOIN users u ON u.id = d.user_id
    ORDER BY d.id DESC
");

$current_user_id = $editDoctor['user_id'] ?? 0;
// allow picking any doctor user NOT already linked, plus the one we're editing
$users = $conn->query("
    SELECT id, username
    FROM users
    WHERE role = 'doctor'
      AND (id NOT IN (SELECT user_id FROM doctors) OR id = {$current_user_id})
    ORDER BY username ASC
");
?>

<h2>Manage Doctors</h2>

<!-- Add/Edit Form (plain-text availability) -->
<form method="POST" class="mb-4">
    <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($editDoctor['id'] ?? '') ?>">

    <div class="mb-3">
        <label class="form-label">Doctor User (Login Account)</label>
        <select class="form-control" name="user_id" <?= $editDoctor ? 'disabled' : 'required' ?>>
            <option value="">-- Select Doctor User --</option>
            <?php while ($u = $users->fetch_assoc()): ?>
                <option value="<?= $u['id']; ?>" <?= ($editDoctor && (int)$editDoctor['user_id'] === (int)$u['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['username']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <?php if ($editDoctor): ?>
            <!-- Disabled controls don't submit; carry it via hidden input -->
            <input type="hidden" name="user_id" value="<?= (int)$editDoctor['user_id']; ?>">
        <?php endif; ?>
        <small class="form-text text-muted">Only users with role “doctor” appear here.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control" name="name" required
               value="<?= htmlspecialchars($editDoctor['name'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Specialization</label>
        <input type="text" class="form-control" name="specialization" required
               value="<?= htmlspecialchars($editDoctor['specialization'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Availability (plain text)</label>
        <textarea class="form-control" name="availability" rows="2"
                  placeholder="e.g., Mon:09:00-12:00,Wed:14:00-17:00,Fri:10:00-13:00"><?= htmlspecialchars($editDoctor['availability'] ?? '') ?></textarea>
        <small class="form-text text-muted">
            Format: <code>Day:HH:MM-HH:MM</code> separated by commas. Days: Mon,Tue,Wed,Thu,Fri,Sat,Sun.
        </small>
    </div>

    <button type="submit" name="save_doctor" class="btn btn-primary">
        <?= $editDoctor ? 'Update Doctor' : 'Add Doctor' ?>
    </button>
    <?php if ($editDoctor): ?>
        <a href="manage_doctors.php" class="btn btn-secondary">Cancel</a>
    <?php endif; ?>
</form>

<!-- Doctors List -->
<h3 class="mt-4">Doctor List</h3>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username (Login)</th>
            <th>Name</th>
            <th>Specialization</th>
            <th>Availability</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($doctors && $doctors->num_rows > 0): ?>
            <?php while ($row = $doctors->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row['id']; ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['specialization']); ?></td>
                    <td><?= htmlspecialchars($row['availability'] ?: ''); ?></td>
                    <td>
                        <a href="?edit_id=<?= (int)$row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete_id=<?= (int)$row['id']; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this doctor?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No doctors found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
