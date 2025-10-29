<?php
session_start();
require_once(__DIR__ . "/functions.php");
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">HMS</a>
            <div class="navbar-nav">
                <?php if ($_SESSION['role'] == 'admin'): ?>
    <a class="nav-link" href="index.php">Dashboard</a>
    <a class="nav-link" href="manage_users.php">Users</a>
    <a class="nav-link" href="manage_doctors.php">Doctors</a>
    <a class="nav-link" href="manage_patients.php">Patients</a>
    <a class="nav-link" href="manage_appointments.php">Appointments</a>
    <a class="nav-link" href="view_records.php">Records</a>
    <a class="nav-link" href="reports.php">Reports</a>
<?php elseif ($_SESSION['role'] == 'doctor'): ?>
    <a class="nav-link" href="index.php">Dashboard</a>
    <a class="nav-link" href="view_appointments.php">Appointments</a>
    <a class="nav-link" href="view_patients.php">Patients</a>
    <a class="nav-link" href="add_record.php">Add Record</a>
<?php elseif ($_SESSION['role'] == 'receptionist'): ?>
    <a class="nav-link" href="index.php">Dashboard</a>
    <a class="nav-link" href="register_patient.php">Register Patient</a>
    <a class="nav-link" href="manage_patients.php">Patients</a>
    <a class="nav-link" href="book_appointment.php">Book Appointment</a>
    <a class="nav-link" href="view_appointments.php">Appointments</a>
<?php endif; ?>
<a class="nav-link" href="../account/change_password.php">Change Password</a>
<a class="nav-link" href="../logout.php">Logout</a>

            </div>
        </div>
    </nav>
    <div class="container mt-4">