<?php
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'feedback_data');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Fetch data from the database
$result = $conn->query("SELECT * FROM road_maintenence_data");
$rowNumber = 1;

// Add header
$sheet->setCellValue('A' . $rowNumber, 'SR_No.');
$sheet->setCellValue('B' . $rowNumber, 'Name');
$sheet->setCellValue('C' . $rowNumber, 'Email');
$sheet->setCellValue('D' . $rowNumber, 'Phone');
$sheet->setCellValue('E' . $rowNumber, 'City');
$sheet->setCellValue('F' . $rowNumber, 'State');
$sheet->setCellValue('G' . $rowNumber, 'Zip');
$sheet->setCellValue('H' . $rowNumber, 'Location');
$sheet->setCellValue('I' . $rowNumber, 'Feedback');
$sheet->setCellValue('J' . $rowNumber, 'Urgency');
$sheet->setCellValue('K' . $rowNumber, 'Image');

// Enable text wrapping for name, location, and feedback fields
$sheet->getStyle('B1:F1')->getAlignment()->setWrapText(true);

// Set reasonable fixed column widths for text fields
$sheet->getColumnDimension('B')->setWidth(20); // Name column
$sheet->getColumnDimension('E')->setWidth(30); // Location column
$sheet->getColumnDimension('F')->setWidth(40); // Feedback column

// Add data rows
while ($row = $result->fetch_assoc()) {
    $rowNumber++;
    $sheet->setCellValue('A' . $rowNumber, $row['SR_No']);
    $sheet->setCellValue('B' . $rowNumber, $row['name']);
    $sheet->setCellValue('C' . $rowNumber, $row['email']);
    $sheet->setCellValue('D' . $rowNumber, $row['phone']);
    $sheet->setCellValue('E' . $rowNumber, $row['city']);
    $sheet->setCellValue('F' . $rowNumber, $row['state']);
    $sheet->setCellValue('G' . $rowNumber, $row['zip']);
    $sheet->setCellValue('H' . $rowNumber, $row['location']);
    $sheet->setCellValue('I' . $rowNumber, $row['feedback']);
    $sheet->setCellValue('J' . $rowNumber, $row['urgency']);

    // Enable text wrapping for data rows
    $sheet->getStyle('B' . $rowNumber . ':F' . $rowNumber)->getAlignment()->setWrapText(true);

    // Adjust row height based on the text wrapping
    $sheet->getRowDimension($rowNumber)->setRowHeight(-1); // -1 automatically adjusts the height based on content

    // Get the image path from the database
    $imagePath = $row['Location_Image'];

    // Check if the image file exists before adding it to the sheet
    if (file_exists($imagePath)) {
        // Create a new Drawing object for the image
        $drawing = new Drawing();
        $drawing->setName('Road Issue Image');
        $drawing->setDescription('Image related to the road maintenance issue');
        $drawing->setPath($imagePath); // Set the path of the image
        $drawing->setCoordinates('K' . $rowNumber); // Position the image in the respective row and column

        // Set fixed dimensions for the image to ensure it's not too large
        $drawing->setWidth(80);  // Set the image width to 80px
        $drawing->setHeight(80); // Set the image height to 80px (keep it square)

        // Attach the image to the sheet
        $drawing->setWorksheet($sheet);

        // Set row height to accommodate the image
        $sheet->getRowDimension($rowNumber)->setRowHeight(100); // Adjust height to fit the image clearly
    } else {
        // If no image exists, place 'No Image' in the cell
        $sheet->setCellValue('K' . $rowNumber, 'No Image');
    }
}

// Auto-size other columns based on the content (except text columns B, E, F)
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);

// Save the Excel file
$writer = new Xlsx($spreadsheet);
$filename = 'feedback_data_' . date('Y-m-d_H-i-s') . '.xlsx'; // Timestamped filename
$writer->save($filename); // Save to your desired location

echo "Data exported to Excel successfully!";
?>
