# Gestione XLS/XLSX in Symfony

[PHPOffice/PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/)

## Installazione

`symfony composer require phpoffice/phpspreadsheet`

## Scrittura:

````
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');
````




## Output verso il browser

Per generare il file e farlo scaricare on-the-fly:

[Redirect output to a client's web browser](https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/#redirect-output-to-a-clients-web-browser)

````
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="myfile.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
````