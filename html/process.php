<?php
const PROGRAMME_C = '/app/bin/cell';

function serveSVGFile($fileForDownload) {
    // Check if the file exists
    if (file_exists($fileForDownload)) {
        // Set the appropriate headers for serving an SVG image
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: attachment; filename="' . basename($fileForDownload) . '"');
        header('Cache-Control: max-age=0');

        // Output the file content
        readfile($fileForDownload);
        exit;
    } else {
        // File not found, you can handle this situation as per your requirements
        echo 'File not found';
    }
}


if( $_FILES['file']['name'] != "" ) {
    $path=$_FILES['file']['name'];
    $pathto="/tmp/".$path;
    move_uploaded_file( $_FILES['file']['tmp_name'],$pathto) or die( "Could not copy file!");

    $command = PROGRAMME_C . ' ' . escapeshellarg("/tmp/".$path);
    exec($command);

    // Générez le lien de téléchargement pour l'image générée
    $imagePath = pathinfo ('/tmp/' . $path);

    $fileToSend = $imagePath['dirname'] ."/". $imagePath['filename'].".svg";
    $fileForDownload = getcwd().'/uploads/'.$imagePath['filename'].".svg";

    $success = rename($fileToSend,$fileForDownload);


}
else {
    die("No file specified!");
}

?>
<html>
<head>
<title>Uploading Complete</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSA5fF5F5/f5O5F5F5F5" crossorigin="anonymous">
</head>
<body>
<h2>Uploaded File Info:</h2>
<ul>
<li>Sent file: <?php echo $_FILES['file']['name'];  ?>
<li>File size: <?php echo $_FILES['file']['size'];  ?> bytes
<li>File type: <?php echo $_FILES['file']['type'];  ?>
</ul>
<a href="cell.html"> back to file submission</a></li>

<h1>SVG Image</h1>


<?php
    if ($success === true) 
    { 
        echo "<a href=".'./uploads/'.$imagePath['filename'].'.svg'." download>Download file</a>" ;
    }
    else
    {
        echo "<b>Error during file analysis</b>" ;
    }
?>


<br>
<?php echo file_get_contents( $fileForDownload ); ?>

</body>
</html>