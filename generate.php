<?php
use Zend\Barcode\Barcode,
    ZendPdf\PdfDocument,
    ZendPdf\Page;

if ($_SERVER["argc"] !== 3) {
    echo "Usage: php generate.php <product number> <directory to store>" . PHP_EOL;
    exit(1);
}

if (!preg_match("/[0-9]{3}/", $_SERVER["argv"][1])) {
    echo "Please provide a 3 digit product number as the first argument" . PHP_EOL;
    exit(2);
}

if (!is_writable($_SERVER["argv"][2])) {
    echo "Please provide a writable path as the second argument" . PHP_EOL;
    exit(3);
}

require __DIR__ . "/vendor/autoload.php";

// The country code (Norway)
$country = "70";

// Our own code
$company = "9003717";

// The product number
$product = $_SERVER["argv"][1];

// File name of the PDF
$fileName = $product . ".pdf";

// Path to use when storing the PDF
$filePath = $_SERVER["argv"][2] . "/" . $fileName;

// Path to the font used in the generated bar code
$fontPath = __DIR__ . "/fonts/OpenSans-Regular.ttf";

// Create the renderer
$renderer = Barcode::factory(
    "EAN-13",
    "pdf",
    array(
        "factor" => 5,
        "font" => $fontPath,
        "text" => $country . $company . $product,
        "withChecksum" => true,
    ), array(
        "horizontalPosition" => "center",
        "verticalPosition" => "middle",
    )
);

// Create a custom size for the PDF document
$resource = new PdfDocument();
$resource->pages[] = new Page(
    '300:200:'
);
$renderer->setResource($resource);

// Draw and save the PDF file
$code = $renderer->draw();
$code->save($filePath);

echo $filePath . " with code \"" . $renderer->getBarCode()->getText() . "\" has been saved." . PHP_EOL;
