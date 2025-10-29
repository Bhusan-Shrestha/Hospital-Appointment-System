<?php
require_once '../includes/header.php';
check_role('receptionist');

global $conn;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $medical_history = $_POST['medical_history'];

    // Backend check: Prevent future DOB
    if (strtotime($dob) > strtotime(date('Y-m-d'))) {
        echo "<div class='alert alert-danger'>Date of Birth cannot be in the future.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO patients (name, dob, gender, contact, address, medical_history) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $dob, $gender, $contact, $address, $medical_history);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Patient registered successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error registering patient.</div>";
        }
    }
}
?>

<h2>Register Patient</h2>
<form method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input 
            type="date" 
            class="form-control" 
            id="dob" 
            name="dob" 
            required
            max="<?php echo date('Y-m-d'); ?>"
        >
    </div>
    <div class="mb-3">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-control" id="gender" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="contact" class="form-label">Contact</label>
        <input type="text" class="form-control" id="contact" name="contact" required>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address"></textarea>
    </div>
    <div class="mb-3">
        <label for="medical_history" class="form-label">Medical History</label>
        <textarea class="form-control" id="medical_history" name="medical_history"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php require_once '../includes/footer.php'; ?>
