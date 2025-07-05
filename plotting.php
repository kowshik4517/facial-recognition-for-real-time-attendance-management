<?php
// Example data from a database (can be dynamic)
$data = [10, 20, 30, 40, 50];
$labels = ["Jan", "Feb", "Mar", "Apr", "May"];
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myChart"></canvas>

    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Sales Data',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'blue'
                }]
            }
        });
    </script>
</body>
</html>
