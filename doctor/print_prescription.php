<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../lib/tcpdf/tcpdf.php';
check_role('doctor');

if (!isset($_GET['record_id'])) {
    die("Record ID not provided");
}

$record_id = $_GET['record_id'];
$stmt = $conn->prepare("SELECT mr.*, p.name as patient_name, d.name as doctor_name 
                        FROM medical_records mr 
                        JOIN patients p ON mr.patient_id = p.id 
                        JOIN doctors d ON mr.doctor_id = d.id 
                        WHERE mr.id = ?");
$stmt->bind_param("i", $record_id);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();

if (!$record) {
    die("Record not found");
}

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Add hospital logo (optional, if available)
if (file_exists('../assets/images/logo.png')) {
    $pdf->Image('../assets/images/logo.png', 10, 10, 30);
}

// Write prescription details
$pdf->Ln(40);
$pdf->Write(0, "Hospital Management System\n", '', 0, 'C');
$pdf->Write(0, "Prescription\n", '', 0, 'C');
$pdf->Ln(10);
$pdf->Write(0, "Patient: {$record['patient_name']}\n");
$pdf->Write(0, "Doctor: {$record['doctor_name']}\n");
$pdf->Write(0, "Date: {$record['visit_date']}\n");
$pdf->Ln(10);
$pdf->Write(0, "Symptoms:\n{$record['symptoms']}\n");
$pdf->Write(0, "Diagnosis:\n{$record['diagnosis']}\n");
$pdf->Write(0, "Prescription:\n{$record['prescription']}\n");

// Output PDF
$pdf->Output('prescription_' . $record_id . '.pdf', 'D');
?>