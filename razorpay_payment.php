<?php
session_start();
require 'db_connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['appointment'])) {
    header("Location: booking.php");
    exit();
}

$appointment = $_SESSION['appointment'];
$amount = $appointment['amount'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Razorpay Payment - Barbershop</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h2>Processing Your Payment...</h2>

    <script>
        var options = {
            "key": "rzp_test_S9gmYY9vQQd3b3", 
            "amount": "<?= $amount * 100 ?>", 
            "currency": "INR",
            "name": "Barbershop",
            "description": "Appointment Booking",
            "handler": function (response) {
                window.location.href = "appointment_success.php?payment_id=" + response.razorpay_payment_id 
                + "&id=<?= $appointment['booking_id'] ?>";
            },
            "prefill": {
                "name": "<?= htmlspecialchars($appointment['name']) ?>",
                "email": "<?= htmlspecialchars($appointment['email']) ?>",
                "contact": "<?= htmlspecialchars($appointment['phone']) ?>"
            },
            "theme": { "color": "pink" }
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();

        rzp1.on('payment.failed', function (response) {
            alert("Payment Failed: " + response.error.description);
            window.location.href = "booking.php";
        });
    </script>
</body>
</html>
