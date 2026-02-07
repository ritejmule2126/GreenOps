<?php
session_start();
require 'db_connect.php'; // Ensure this file properly sets up `$conn`

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $service = mysqli_real_escape_string($conn, $_POST['service']);
    $barber = mysqli_real_escape_string($conn, $_POST['barber']);
    $datetime = mysqli_real_escape_string($conn, $_POST['datetime']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    // Fetch service price from services table
    $service_query = "SELECT price FROM services WHERE service_name = '$service'";
    $result = mysqli_query($conn, $service_query);
    $row = mysqli_fetch_assoc($result);
    $amount = $row ? $row['price'] : 500; // Default amount if service not found

    // Insert appointment into database
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, phone, service, barber, datetime, payment_method, notes, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $status = ($payment_method == "pay_after_service") ? 'Pending' : 'Paid';

    if ($stmt) {
        $stmt->bind_param("ssssssssds", $name, $email, $phone, $service, $barber, $datetime, $payment_method, $notes, $amount, $status);
        $stmt->execute();
        $booking_id = $stmt->insert_id; // Get the inserted ID
        $stmt->close();
    }

    if ($payment_method == "pay_after_service") {
        header("Location: appointment_success.php?id=$booking_id");
        exit();
    } elseif ($payment_method == "razorpay") {
        $_SESSION['appointment'] = [
            'booking_id' => $booking_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'service' => $service,
            'barber' => $barber,
            'datetime' => $datetime,
            'notes' => $notes,
            'amount' => $amount
        ];
        header("Location: razorpay_payment.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Barbershop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/img.jpg');
            /* Set background image */
            background-size: cover;
            /* Cover the whole screen */
            background-position: center;
            /* Center the background image */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            margin: 20px;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.8);
            /* White background with opacity for readability */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 30px;
        }

        label {
            font-size: 18px;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            /* Ensures padding is included in width */
        }

        input[type="datetime-local"] {
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: pink;
            border: none;
            color: white;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: grey;
        }

        /* Ensure the input and button fields have a uniform look */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #ff9900;
            /* Highlight border on focus */
        }

        /* Optional styling for the additional notes (textarea) */
        textarea {
            height: 120px;
            resize: vertical;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Book Your Appointment</h2>
        <form action="booking.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" maxlength="10" pattern="[0-9]{10}" required 
       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" 
       placeholder="Enter 10-digit phone number">

            <label for="service">Select Service</label>
<select id="service" name="service" required>
    <?php
    require 'db_connect.php'; // Ensure the database connection is included

    $query = "SELECT service_name FROM services"; // Adjust table/column name as per your DB structure
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . htmlspecialchars($row['service_name']) . '">' . htmlspecialchars($row['service_name']) . '</option>';
        }
    } else {
        echo '<option value="">Error loading services</option>';
    }
    ?>
</select>


            <label for="barber">Select Barber</label>
            <select id="barber" name="barber" required>
            <option value="Barber 1">Barber 1</option>
            <option value="Barber 2">Barber 2</option>
            <option value="Barber 3">Barber 3</option>
        </select>

            <label for="date">Select Date and Time</label>
            <input type="datetime-local" id="date" name="datetime" required>

            <label for="payment_method">Select Payment method</label>
            <select id="payment_method" name="payment_method" required>
            <option value="pay_after_service">Pay after Service</option>
            <option value="razorpay">Razorpay</option>
        </select>

            <label for="notes">Additional Notes</label>
            <textarea id="notes" name="notes"></textarea>

            <button type="submit">Book Appointment</button>
        </form>
    </div>

</body>

</html>