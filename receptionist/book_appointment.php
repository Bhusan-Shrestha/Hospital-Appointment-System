<?php
require_once '../includes/header.php';
check_role('receptionist');

global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    // Prevent booking in the past
    if (strtotime($appointment_date) < time()) {
        echo "<div class='alert alert-danger'>You cannot book an appointment in the past.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $patient_id, $doctor_id, $appointment_date);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Appointment booked successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error booking appointment.</div>";
        }
    }
}

$patients = $conn->query("SELECT id, name FROM patients");
$doctors = $conn->query("SELECT id, name, specialization, availability FROM doctors");
?>

<h2>Book Appointment</h2>
<form method="POST">
    <div class="mb-3">
        <label for="patient_id" class="form-label">Patient</label>
        <select class="form-control" id="patient_id" name="patient_id" required>
            <?php while ($patient = $patients->fetch_assoc()): ?>
                <option value="<?php echo $patient['id']; ?>"><?php echo htmlspecialchars($patient['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="doctor_id" class="form-label">Doctor</label>
        <select class="form-control" id="doctor_id" name="doctor_id" required>
            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                <option value="<?php echo $doctor['id']; ?>">
                    <?php echo htmlspecialchars($doctor['name'] . ' (' . $doctor['specialization'] . ', ' . $doctor['availability'] . ')'); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="appointment_date" class="form-label">Appointment Date</label>
        <input 
            type="datetime-local" 
            class="form-control" 
            id="appointment_date" 
            name="appointment_date" 
            required
            min="<?php echo date('Y-m-d\TH:i'); ?>"
        >
    </div>
    <button type="submit" class="btn btn-primary">Book Appointment</button>
</form>

<?php require_once '../includes/footer.php'; ?>
