<?php
include '../../tcpdf/tcpdf.php';
include 'conn.php';

$date_range = $_GET['date_range'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';
$format = strtolower($_GET['format'] ?? 'pdf');

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('EggTrak');
$pdf->SetTitle('Expenses Report');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, 10);

$sql = "SELECT * FROM expenses";
$where = [];
$params = [];
$title = 'Expenses Report';

if ($date_range === 'monthly' && !empty($month)) {
    $month_date = DateTime::createFromFormat('Y-m', $month);
    $start_date = $month_date->format('Y-m-01');
    $end_date = $month_date->format('Y-m-t');
    
    $where[] = "date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    
    $title .= ' - ' . $month_date->format('F Y');
} elseif ($date_range === 'annually' && !empty($year)) {
    $where[] = "YEAR(date) = ?";
    $params[] = $year;
    
    $title .= ' - Year ' . $year;
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY date ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$html = '<h2 style="text-align:center;">' . $title . '</h2>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:20%;"><b>Date</b></th>
                    <th style="width:20%;"><b>Category</b></th>
                    <th style="width:40%;"><b>Description</b></th>
                    <th style="width:20%;text-align:right;"><b>Amount (₱)</b></th>
                </tr>
            </thead>
            <tbody>';

$total = 0;
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
                <td style="width:20%;">' . date('M j, Y', strtotime($row['date'])) . '</td>
                <td style="width:20%;">' . htmlspecialchars($row['category']) . '</td>
                <td style="width:40%;">' . htmlspecialchars($row['description']) . '</td>
                <td style="width:20%;text-align:right;">₱' . number_format($row['amount'], 2) . '</td>
              </tr>';
    $total += $row['amount'];
}

$html .= '<tr style="font-weight:bold;">
            <td colspan="3" style="text-align:right;">Total:</td>
            <td style="text-align:right;">₱' . number_format($total, 2) . '</td>
          </tr>';

$html .= '</tbody></table>';

if ($format === 'pdf') {
    $pdf->SetHeaderData('', 0, $title, 'Generated on: ' . date('F j, Y H:i:s'));
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('Expenses_Report_' . ($date_range ? $date_range . '_' : '') . ($month ? $month : ($year ? $year : '')) . '.pdf', 'D');
} elseif ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Expenses_Report_' . ($date_range ? $date_range . '_' : '') . ($month ? $month : ($year ? $year : '')) . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, ['Date', 'Category', 'Description', 'Amount (₱)']);
    
    $result->data_seek(0);
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            date('M j, Y', strtotime($row['date'])),
            $row['category'],
            $row['description'],
            number_format($row['amount'], 2)
        ]);
    }
    
    fputcsv($output, ['', '', 'Total:', number_format($total, 2)]);
    
    fclose($output);
}

exit;
?>