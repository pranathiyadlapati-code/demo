
<? include 'db.php'?>
<?php
session_start();
$regno=$_SESSION['reg'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Vignan University::Vadlamudi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            width: 100%;

        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            width: 80%;
            margin-left:10%;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        input[type="button"]:hover {
            background-color: #0056b3;
        }
        input[type="button"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .image-container {
            width: 100%;
            margin-left: 5%;
            display: flex;
            flex-wrap: wrap;
        }

        .image-container  img {
            width: 19%;
            height: auto;
            padding: 5px;
            margin-left: 1%;
            box-sizing: border-box;
        }
        .image-container .clearfix {
            clear: both;
        }
        .blink {
                animation: blinker 1.5s linear infinite;
                color: red;
                font-family: sans-serif;
            }
            @keyframes blinker {
                50% {
                    opacity: 0;
                }
            }
            .mar{
                color: red;
            }

    </style>
</head>
<body>
    <div class="up">
    <h1>Upload Image</h1>
    <form action="upload_image.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" id="image" accept="image/*" required>
        <input type="submit" name="upload" id="submit" value="Upload"><br>
        
    </div>
    <hr>
    <div class="mar">
    <marquee class="blink" width="1400px" direction="right"  scrollamount="20" height="100px">
      <h1 class="mar">Cherish The Moment</h1>
      <div>
</marquee>
    <hr>
    <div class="view">   
    <div class="image-container">
        <?php
        echo '<script type="text/JavaScript">';
        echo'alert($regno)';

        echo '</script>';
        $host = 'localhost';
        $port = '3307'; 
        $dbname = 'learn';
        $username = 'root';
        $password = '@@ramesh$$9199';
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<div class="image-container">';
            $imageCount = 0;
            $stmt = $pdo->query("SELECT image_data, image_type FROM images where regno='$regno'");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
                echo "<img src='data:" . $row['image_type'] . ";base64," . base64_encode($row['image_data']) . "'>";
               
                $imageCount++;
                if ($imageCount % 5 === 0) {
                  
                    echo '<div class="clearfix"></div>';
                }
            }
            echo '</div>';
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        
        ?>

    </div>
    </form>
    
    <?php
require('footer.php');
?>
</body>
</html>




