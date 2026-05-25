<?php 
// Database connection parameters
$host = 'localhost';
$dbname = 'learn';
$username = 'root';
$password = '@@ramesh$$9199';
$port='3307';

session_start();
$regno=$_SESSION['reg'];
echo '<script type="text/JavaScript">';
echo "alert('$regno')";
echo '</script>';

try {
   // Create a PDO instance for database connection
    
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['upload'])) {
        // Check if a file was uploaded
        if (isset($_FILES['image'])) {
            $image_name = $_FILES['image']['name'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $image_type = $_FILES['image']['type'];

            // Insert the image data into the database
            $stmt = $pdo->prepare("INSERT INTO images (image_data, image_type, image_name,regno) VALUES (?, ?, ?,?)");
            $stmt->bindParam(1, $image_data, PDO::PARAM_LOB);
            $stmt->bindParam(2, $image_type);
            $stmt->bindParam(3, $image_name);
            $stmt->bindParam(4,$regno);
            $stmt->execute();
?> 
    <script type="text/javascript">
        alert("Image upload Successfully");
        window.location.href="form2.php";
        </script>
<?php
           // echo "Image uploaded successfully.";
        } else {
            echo "No image uploaded.";
        }
    }

    
        // // Create a PDO instance for database connection
        // $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // // Check if 'image' is set in the POST request (assumes you're using a form to upload the image)
        // if (isset($_FILES['image'])) {
        //     $image_data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
        //     $image_type = $_FILES['image']['type'];
    
        //     // Insert the base64-encoded image data into the database
        //     $stmt = $pdo->prepare("INSERT INTO images (image_data, image_type) VALUES (?, ?)");
        //     $stmt->bindParam(1, $image_data);
        //     $stmt->bindParam(2, $image_type);
        //     $stmt->execute();
    
        //     echo "Image uploaded successfully.";
        // } else {
        //     echo "No image uploaded.";
        // }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
