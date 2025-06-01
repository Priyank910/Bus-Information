<?php
// Validate required parameters
if (!isset($_GET['bus_id']) || !isset($_GET['user_source']) || !isset($_GET['user_dest'])) {
    die("Invalid request");
}

$bus_id = intval($_GET['bus_id']);
$user_source = strtolower(trim($_GET['user_source']));
$user_dest = strtolower(trim($_GET['user_dest']));

// Connect to database
$conn = new mysqli("localhost", "root", "", "bus_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get bus details
$bus_result = $conn->query("SELECT * FROM buses WHERE id = $bus_id");
if ($bus_result->num_rows === 0) {
    die("Bus not found");
}
$bus = $bus_result->fetch_assoc();

// Get stops
$stops = [];
$stop_result = $conn->query("SELECT stop_name FROM bus_stops WHERE bus_id = $bus_id ORDER BY id ASC");
while ($row = $stop_result->fetch_assoc()) {
    $stops[] = $row['stop_name'];
}

// Build route
$route = [];
$route[] = strtolower($bus['source']);
foreach ($stops as $s) {
    $route[] = strtolower($s);
}
$route[] = strtolower($bus['destination']);

// Detect if search was reversed
$source_index = array_search($user_source, $route);
$dest_index = array_search($user_dest, $route);
$is_reversed = ($source_index !== false && $dest_index !== false && $source_index > $dest_index);

// Prepare display route
$full_route = array_merge([$bus['source']], $stops, [$bus['destination']]);
if ($is_reversed) {
    $full_route = array_reverse($full_route);
    $display_source = $bus['destination'];
    $display_dest = $bus['source'];
} else {
    $display_source = $bus['source'];
    $display_dest = $bus['destination'];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Route Details - <?= htmlspecialchars($bus['bus_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #FCF8E8;
        }
        .card {
            background-color: #D4E2D4;
        }
        .card-header {
            background-color: #ECB390;
            color: #DF7861;
        }
        .btn-secondary {
            background-color: #DF7861;
            border-color: #DF7861;
        }
        .btn-secondary:hover {
            background-color: #ECB390;
            border-color: #ECB390;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center" style="color: #DF7861;">Route Details - <?= htmlspecialchars($bus['bus_name']) ?></h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($bus['bus_name']) ?></h4>
            <p><strong>Route:</strong> <?= htmlspecialchars($display_source) ?> → <?= htmlspecialchars($display_dest) ?></p>
            <p><strong>Date:</strong> <?= $bus['date'] ?></p>
            <p><strong>Departure Time:</strong> <?= $bus['departure_time'] ?> | <strong>Arrival Time:</strong> <?= $bus['arrival_time'] ?></p>
            <p><strong>Price:</strong> ₹<?= $bus['price'] ?></p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Route Stops (In Order)
        </div>
        <ul class="list-group list-group-flush">
            <?php foreach ($full_route as $i => $stop): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($stop) ?>
                    <?php if ($i === 0): ?> (Start)
                    <?php elseif ($i === count($full_route) - 1): ?> (End)
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="mt-4">
        <a href="search.php" class="btn btn-secondary">← Back to Search</a>
    </div>
</div>
</body>
</html>
