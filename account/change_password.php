<?php
// Uncomment while debugging:
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../includes/functions.php'; // loads config + helpers
require_login(); // any logged-in user can access

global $conn;
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_verify($token)) {
        $error = "Invalid request. Please refresh and try again.";
    } else {
        $old     = $_POST['current_password']  ?? '';
        $new     = $_POST['new_password']      ?? '';
        $confirm = $_POST['confirm_password']  ?? '';

        if ($new !== $confirm) {
            $error = "New password and confirmation do not match.";
        } else {
            try {
                change_password($conn, (int)$_SESSION['user_id'], $old, $new);
                $success = "Password changed successfully.";
                // Optional: force re-login after change:
                // session_destroy(); header("Location: ../login.php"); exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Change Password - HMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">HMS</a>
    <div class="navbar-nav">
      <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a class="nav-link" href="../admin/index.php">Dashboard</a>
        <a class="nav-link" href="../admin/manage_users.php">Users</a>
        <a class="nav-link" href="../admin/manage_doctors.php">Doctors</a>
        <a class="nav-link" href="../admin/manage_patients.php">Patients</a>
        <a class="nav-link" href="../admin/manage_appointments.php">Appointments</a>
        <a class="nav-link" href="../admin/view_records.php">Records</a>
        <a class="nav-link" href="../admin/reports.php">Reports</a>
      <?php elseif (($_SESSION['role'] ?? '') === 'doctor'): ?>
        <a class="nav-link" href="../doctor/index.php">Dashboard</a>
        <a class="nav-link" href="../doctor/view_appointments.php">Appointments</a>
        <a class="nav-link" href="../doctor/view_patients.php">Patients</a>
        <a class="nav-link" href="../doctor/add_record.php">Add Record</a>
      <?php elseif (($_SESSION['role'] ?? '') === 'receptionist'): ?>
        <a class="nav-link" href="../receptionist/index.php">Dashboard</a>
        <a class="nav-link" href="../receptionist/register_patient.php">Register Patient</a>
        <a class="nav-link" href="../receptionist/manage_patients.php">Patients</a>
        <a class="nav-link" href="../receptionist/book_appointment.php">Book Appointment</a>
        <a class="nav-link" href="../receptionist/view_appointments.php">Appointments</a>
      <?php endif; ?>
      <a class="nav-link active" aria-current="page" href="../account/change_password.php">Change Password</a>
      <a class="nav-link" href="../logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h2>Change Password</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post" style="max-width:480px">
    <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
    <div class="mb-3">
      <label class="form-label">Current Password</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Password</label>
      <input type="password" name="new_password" class="form-control" required minlength="8"
             placeholder="At least 8 characters">
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm New Password</label>
      <input type="password" name="confirm_password" class="form-control" required minlength="8">
    </div>
    <button type="submit" class="btn btn-primary">Update Password</button>
  </form>
</div>

</body>
</html>
