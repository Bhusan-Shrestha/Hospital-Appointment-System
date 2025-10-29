<?php
require_once '../includes/header.php';
check_role('doctor');

global $conn;

$success = $error = "";

// Handle submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $patient_id     = $_POST['patient_id'];
    $doctor_id      = $_POST['doctor_id'];
    $visit_date     = $_POST['visit_date'];
    $symptoms       = $_POST['symptoms'];
    $diagnosis      = $_POST['diagnosis'];
    $prescription   = $_POST['prescription'];

    // --- Backend validation: visit_date must not be in the future ---
    $visit_ts = strtotime($visit_date);
    if ($visit_ts === false) {
        $error = "Invalid visit date/time.";
    } elseif ($visit_ts > time()) {
        $error = "Visit date/time cannot be in the future.";
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, doctor_id, visit_date, symptoms, diagnosis, prescription) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $patient_id, $doctor_id, $visit_date, $symptoms, $diagnosis, $prescription);
        if ($stmt->execute()) {
            $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $success = "Record added successfully!";
        } else {
            $error = "Error adding record.";
        }
    }
}

// Load appointment context (for the form)
if (isset($_GET['appointment_id'])) {
    $stmt = $conn->prepare("SELECT a.id, a.patient_id, a.doctor_id, a.appointment_date, 
                                   p.name as patient_name, d.name as doctor_name 
                            FROM appointments a 
                            JOIN patients p ON a.patient_id = p.id 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.id = ?");
    $stmt->bind_param("i", $_GET['appointment_id']);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
}

// Precompute max value for datetime-local (now, local server time)
$nowMax = date('Y-m-d\TH:i');
?>

<h2>Add Medical Record</h2>

<?php if (!empty($success)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (isset($appointment)): ?>
<form method="POST">
    <input type="hidden" name="appointment_id" value="<?php echo (int)$appointment['id']; ?>">
    <input type="hidden" name="patient_id" value="<?php echo (int)$appointment['patient_id']; ?>">
    <input type="hidden" name="doctor_id" value="<?php echo (int)$appointment['doctor_id']; ?>">

    <div class="mb-3">
        <label for="patient_name" class="form-label">Patient</label>
        <input type="text" class="form-control" id="patient_name" 
               value="<?php echo htmlspecialchars($appointment['patient_name']); ?>" disabled>
    </div>
    <div class="mb-3">
        <label for="doctor_name" class="form-label">Doctor</label>
        <input type="text" class="form-control" id="doctor_name" 
               value="<?php echo htmlspecialchars($appointment['doctor_name']); ?>" disabled>
    </div>

    <div class="mb-3">
        <label for="visit_date" class="form-label">Visit Date</label>
        <input 
            type="datetime-local" 
            class="form-control" 
            id="visit_date" 
            name="visit_date" 
            required
            max="<?php echo $nowMax; ?>"   <!-- Frontend: block future times -->
            value="<?php echo isset($_POST['visit_date']) ? htmlspecialchars($_POST['visit_date']) : $nowMax; ?>"
        >
        <small class="text-muted">You can only select now or earlier.</small>
    </div>

    <div class="mb-3">
        <label for="symptoms" class="form-label">Symptoms</label>
        <textarea class="form-control" id="symptoms" name="symptoms" required><?php 
            echo isset($_POST['symptoms']) ? htmlspecialchars($_POST['symptoms']) : ''; 
        ?></textarea>
    </div>
    <div class="mb-3">
        <label for="diagnosis" class="form-label">Diagnosis</label>
        <textarea class="form-control" id="diagnosis" name="diagnosis" required><?php 
            echo isset($_POST['diagnosis']) ? htmlspecialchars($_POST['diagnosis']) : ''; 
        ?></textarea>
    </div>
    <div class="mb-3">
        <label for="prescription" class="form-label">Prescription</label>
        <textarea class="form-control" id="prescription" name="prescription"><?php 
            echo isset($_POST['prescription']) ? htmlspecialchars($_POST['prescription']) : ''; 
        ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Add Record</button>
</form>
<?php else: ?>
  <div class="alert alert-warning">No appointment selected.</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
