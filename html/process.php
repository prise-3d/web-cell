<?php
const PROGRAMME_C = '/app/bin/bin/cell';

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
    $curent_dir = getcwd();
    $path=$_FILES['file']['name'];
    $pathto="/tmp/".$path;
    move_uploaded_file( $_FILES['file']['tmp_name'],$pathto) or die( "Could not copy file!");

    $command = PROGRAMME_C . ' ' . escapeshellarg("/tmp/".$path);
    chdir('/tmp/');
    exec($command);

    // Générez le lien de téléchargement pour l'image générée
    $imagePath = pathinfo ('/tmp/' . $path);

    $fileToSend = $imagePath['dirname'] ."/". $imagePath['filename'].".svg";
    $fileForDownload = $curent_dir.'/uploads/'.$imagePath['filename'].".svg";

    $statsToSend = $imagePath['dirname'] ."/". $imagePath['filename'].".cvs";
    $statsForDownload = $curent_dir.'/uploads/'.$imagePath['filename'].".cvs";
    
    $logToSend = $imagePath['dirname'] ."/out.log";
    $logForDownload = $curent_dir.'/uploads/'.$imagePath['filename'].".log";

    rename($logToSend,$logForDownload);
    $success = rename($fileToSend,$fileForDownload);


}
else {
    die("No file specified!");
}

?>
<html>
<head>
<title>Uploading Complete</title>
<style>
      button {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #0074d9;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        text-align: center;
      }
      button span {
        cursor: pointer;
        display: inline-block;
        position: relative;
        transition: 0.5s;
      }
      button span:after {
        content: "\00bb";
        position: absolute;
        opacity: 0;
        top: 0;
        right: -20px;
        transition: 0.5s;
      }
      button:hover {
        background-color: #f7c2f9;
      }
      button:hover span {
        padding-right: 25px;
      }
      button:hover span:after {
        opacity: 1;
        right: 0;
      }

        /* Style for the container div */
            .svg-container {
            width: 100%; /* Adjust the width as needed */
            height: auto; /* Allow the height to adjust proportionally */
            overflow: auto; /* Enable scrolling if the SVG exceeds container dimensions */
        }

        /* Style for the SVG itself */
        .svg-content {
            width: 100%; /* Take up full width of container */
            transition: transform 0.2s; /* Add smooth transition for zoom effect */
        }
    </style>
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
        
    if ($success) 
    { 
     // Display the download button

     echo <<<HTML
    <form id="downloadForm" method="get" action="">
    <button type="button" onclick="downloadSVG()">Download SVG image</button>
    <button type="button" onclick="downloadCSV()">Download CSV stats</button>
    <button type="button" onclick="downloadLOG()">Download log file</button>
    </form>

    <script>
    function downloadSVG() {
        document.getElementById("downloadForm").action = "./uploads/{$imagePath['filename']}.svg";
        document.getElementById("downloadForm").submit();
    }

    function downloadCSV() {
        document.getElementById("downloadForm").action = "./uploads/{$imagePath['filename']}.csv";
        document.getElementById("downloadForm").submit();
    }

    function downloadLOG() {
        document.getElementById("downloadForm").action = "./uploads/{$imagePath['filename']}.log";
        document.getElementById("downloadForm").submit();
    }
    </script>
    HTML;
        
       
    }
    else
    {
        echo <<<HTML
        <form id="downloadForm" method="get" action="">
        <button type="button" onclick="downloadLOG()">Download log file</button>
        </form>
    
        <script>
    
        function downloadLOG() {
            document.getElementById("downloadForm").action = "./uploads/{$imagePath['filename']}.log";
            document.getElementById("downloadForm").submit();
        }
        </script>
        HTML;
    }    
?>

<br>


<div class="svg-container">
  <?php echo file_get_contents( $fileForDownload ); ?>
</div>

</body>
</html>