<?php
require_once '../includes/header.php';
check_role('doctor');

global $conn;
$user_id = $_SESSION['user_id'];

// Upcoming Appointments
$stmt = $conn->prepare("SELECT a.id, p.name as patient_name, a.appointment_date 
                        FROM appointments a 
                        JOIN patients p ON a.patient_id = p.id 
                        JOIN doctors d ON a.doctor_id = d.id 
                        WHERE d.user_id = ? AND a.status = 'scheduled'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result();

// Doctor availability (stored in doctors table)
$schedule_stmt = $conn->prepare("SELECT availability FROM doctors WHERE user_id = ?");
$schedule_stmt->bind_param("i", $user_id);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();
$doctor = $schedule_result->fetch_assoc();

// Parse availability string into array
$availabilityMap = [];
if (!empty($doctor['availability'])) {
    $items = explode(',', $doctor['availability']); // e.g., Mon:09:00-17:00,Tue:...
    foreach ($items as $item) {
        $parts = explode(':', $item);
        if (count($parts) == 2) {
            $times = explode('-', $parts[1]);
            $availabilityMap[$parts[0]] = [
                'start' => $times[0],
                'end' => $times[1]
            ];
        }
    }
}

$days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
?>

<h2>Doctor Dashboard</h2>

<h4>Upcoming Appointments</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($appointments->num_rows > 0): ?>
            <?php while ($row = $appointments->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                    <td>
                        <a href="add_record.php?appointment_id=<?= $row['id']; ?>" class="btn btn-sm btn-primary">Add Record</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" class="text-center">No upcoming appointments</td></tr>
        <?php endif; ?>
    </tbody>
</table>


<?php require_once '../includes/footer.php'; ?>
