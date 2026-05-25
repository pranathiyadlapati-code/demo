<!DOCTYPE html>
<?php include 'connect.php' ?>
<?php
session_start();
if(!isset($_SESSION['reg'])){
    header('Location:index.php');
    die;
  }
$regno = $_SESSION['reg'];
header("Cache-Control:no-cache,private,must-revalidate");
?>
<html>

<head>
    <title>Vignan University::Vadlamudi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- <style>
        @media screen(max-width:500px) {
            .opinion-box {
                width: 200%;

            }

        }

        body {
            animation: joy 8s linear infinite;
            /* background-image: linear-gradient(135deg, #fceabb 10%, #f8b500 100%); */
            background-image: linear-gradient(to left, #FFFF33, lightblue);
            background-size: 200% 200%;
        }

        @keyframes joy {
            0% {
                background-position: 0% 20%;
            }

            50% {
                background-position: 50% 25%;
            }

            100% {
                background-position: 100% 50%;
            }
        }

        h1 {
            text-align: center;
            color: #333;
        }


        .opinion-box p {
            margin-bottom: 10px;
            opacity: 0;
            animation: fade 1s ease forwards;
        }


        @keyframes fade {
            to {
                opacity: 1;
            }
        }



        .opinion-form {
            position: absolute;
            margin-left: 6%;
            justify-content: center;
            position: relative;

            left: -2%;
            margin-top: 30px;
            max-width: 500px;
            margin: 0 auto;
            background-image: linear-gradient(to right, lightblue, light);
            background-color: lightcyan;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 6px lightblue;
        }

        .opinion-form label,
        .opinion-form textarea,
        .opinion-form button {
            display: block;
            margin-bottom: 10px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        textarea {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            /* transition: border-color 0.3s; */

        }

        #rollNumber,
        #rollNumber1 {
            width: 50%;
            transition: width 1.5s;
            transition-timing-function: linear;

        }

        #rollNumber:hover,
        #rollNumber1:hover {
            width: 100%;
        }

        textarea {
            resize: vertical;
            min-height: 10px;
        }

        input[type="text"]:hover,
        textarea:hover {
            outline: none;
            border-color: #0056b3;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

   

        .opinion-box p {
            font-family: cursive;
        }

        .footer {
            width: 100%;
            position: absolute;
            top: 92%;
            text-emphasis-style: bold;
            font-size: 20px;
        }
        table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
}

/* Table header row */
table th {
  background-color: #333;
  color: white;
  font-weight: bold;
  text-align: left;
  padding: 10px;
}

/* Table data rows */
table td {
  border: 1px solid #ddd;
  padding: 10px;
}

/* Alternating row background colors */
table tr:nth-child(even) {
  background-color: lightblue;
}

/* Hover effect on rows */
table tr:hover {
  background-color:lightcoral;
}
    </style> -->

<!-- 30-03-2026 commented and added styling -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
        :root {
            --gold: #D4A017;
            --gold-light: #F5C842;
            --navy: #1A2744;
            --navy-mid: #243256;
            --border: rgba(212,160,23,0.3);
            --input-bg: rgba(255,255,255,0.06);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--navy);
            background-image: radial-gradient(circle at 20% 30%, #1e2d50 0%, var(--navy) 100%);
            color: #fff;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: #fff !important;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Container Styling */
        .opinion-form {
            max-width: 500px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .form-label {
            font-size: 0.75rem;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            background: var(--input-bg) !important;
            border: 1px solid var(--border) !important;
            color: #fff !important;
            border-radius: 10px !important;
            padding: 12px !important;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(212, 160, 23, 0.05) !important;
            border-color: var(--gold) !important;
            box-shadow: 0 0 15px rgba(212, 160, 23, 0.2) !important;
            outline: none;
        }

        /* Button Styling */
        /* Buttons styling - Smaller version */
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%) !important;
            color: var(--navy) !important;
            border: none !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            
            /* Decreased dimensions */
            padding: 4px 10px !important; /* Original was 12px 30px */
            font-size: 0.85rem !important;  /* Original was 1rem or 0.85rem */
            border-radius: 8px !important;
            
            transition: transform 0.2s, box-shadow 0.2s !important;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 160, 23, 0.4) !important;
        }

        /* Table Styling */
        table {
            width: 80%;
            margin: 50px auto;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.02);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        table th {
            background-color: var(--gold) !important;
            color: var(--navy) !important;
            padding: 18px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
            border: none;
        }

        table td {
            padding: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e0e0e0;
            font-family: 'DM Sans', sans-serif; /* Cleaner than cursive for readability */
            font-size: 1rem;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.03);
        }

        table tr:hover {
            background-color: rgba(212, 160, 23, 0.05) !important;
            transition: background 0.3s;
        }

        /* Removing the legacy footer positioning */
        .footer {
            display: none;
        }


        /* for heading opinion page */
        /* Heading same as Explore Here */

.stylish-text {
    font-family: 'Playfair Display', serif;
    font-size: 2.8rem;
    color: #fff;
    text-align: center;
}

.stylish-text em {
    font-style: italic;
    color: #F5C842;
}

/* spacing + structure */
h3 {
    width: 100%;
    text-align: center;
    padding: 10px 0 0;
}

/* small subtitle above heading */
/* h3::before {
    content: 'Opinions';
    display: block;
    font-size: .7rem;
    color: #D4A017;
    letter-spacing: .14em;
    text-transform: uppercase;
    margin-bottom: 10px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 500;
} */

/* gold line below heading */
h3::after {
    content: '';
    display: block;
    width: 48px;
    height: 3px;
    background: #D4A017;
    border-radius: 2px;
    margin: 16px auto 0;
}

/* 16-04-2026 */
/* Mobile view only */
@media (max-width: 768px) {

    body {
        padding: 0 !important;   /* remove all outer space */
    }

    .opinion-form {
        width: 100% !important;
        max-width: 100% !important;
        margin: 5px !important;   /* very small side gap */
        padding: 18px !important;
        border-radius: 12px;      /* slightly smaller for mobile */
    }

}

    </style>


</head>

<body>

 
    <div class="contair-fluid p-5">
        <div class="opinion-form">
            <div class="row justify-content-evenly">
                <!-- <div class="col-md-3"></div> -->
                <!-- <h1 for="rollNumber" class="form-label">Opinion Page</h1> -->
                 <h3 class="text-center mt-2 stylish-text" style="margin-bottom: 30px;">Opinion <em>Page</em></h3>
                <div class="col-md-12">
                    <div>
                        <form action="submit2.php" method="post">
                            <div class="mb-3">
                                <!-- 16-04-2026 -->
                                <label for="rollNumber" class="form-label">Enter Your Roll Number:</label>
                                <input type="text" class="form-control" id="rollNumber" name="roll" value=<?php echo $regno; ?> placeholder="Roll Number" readonly>
                            </div>
                            <div class="mb-3">
                                 <!-- 16-04-2026 -->
                                <label for="rollNumber1" class="form-label">Enter Your Friend Number:</label>
                                <input type="text" class="form-control" id="rollNumber1" name="frndroll"
                                    placeholder="Roll Number" required>
                            </div>
                            <div class="mb-3">
                                <label for="opinion" class="form-label">Share Your Opinion:</label>
                                <textarea id="opinion" class="form-control" name="opi"
                                    placeholder="Type your opinion here..."></textarea>
                            </div>
                            <div class="d-grid gap-2 btn-primary">
                                <button type="submit" class="btn btn-primary justify-content-center">Submit</button>
                            </div>
                            <div class="d-grid gap-2 btn-primary">
                                <button type="button" class="btn btn-primary justify-content-center" onclick="window.location.href='checkdetails.php'">back</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- <div class="col-md-3"></div> -->
            </div>
        </div>
       

    </div>
    <div id="reviews-container" class="reviews-container">

</div>


    <?php
  
  
      $id=$regno;

      $sql = "SELECT * FROM slambook_opnion WHERE user_id = '$id'";
      $result = mysqli_query($conn, $sql);
      

      
     
  if ($result->num_rows > 0) {
      echo '<table>';
      echo '<tr>';
      echo '<th>FRIEND</th>';
      echo '<th>OPINION</th>';
      
      echo '</tr>';

      while ($row = $result->fetch_assoc()) {
          echo '<tr>';
          echo '<td>' . $row['frnd'] . '</td>';
          echo '<td>' . $row['opinion'] . '</td>';
          
          echo '</tr>';
      }
          echo '</table>';
      } 
      else {
          echo 'No data found for the selected year.';
      }
 // }
  
 
  ?>





</body>

</html>