<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Test</title>
</head>
<body>
    <h2>Simple File Upload Test</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="test_text">Test Text:</label><br>
        <input type="text" id="test_text" name="test_text"><br><br>
        
        <label for="test_file">Select File:</label><br>
        <input type="file" id="test_file" name="test_file"><br><br>
        
        <input type="submit" value="Upload Test File">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h3>POST Data:</h3>";
        var_dump($_POST);
        
        echo "<h3>FILES Data:</h3>";
        var_dump($_FILES);
        
        // This will stop the script here and display the output immediately
        exit("Script finished."); 
    }
    ?>
</body>
</html> 