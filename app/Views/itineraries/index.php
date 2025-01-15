<?php

/**
 * User: George Garzon
 * Date: 10/7/2024
 * @var $itineraries
 */

echo MAINTENANCE_MODE;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itineraries</title>
</head>

<body>
    <h1>Itineraries</h1>

    <?php if (!empty($itineraries)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Departure Date</th>
                    <th>Interior</th>
                    <th>Oceanview</th>
                    <th>Balcony</th>
                    <th>Suite</th>
                    <th>Port Fees</th>
                    <th>Currency</th>
                    <th>Length</th>
                    <th>Cruise Title</th>
                    <th>Ship Name</th>
                    <th>Departure Port</th>
                    <th>Itinerary</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itineraries as $itinerary): ?>
                    <tr>
                        <td><?= esc($itinerary['id']) ?></td>
                        <td><?= esc($itinerary['departure_date']) ?></td>
                        <td><?= esc($itinerary['interior']) ?></td>
                        <td><?= esc($itinerary['oceanview']) ?></td>
                        <td><?= esc($itinerary['balcony']) ?></td>
                        <td><?= esc($itinerary['suite']) ?></td>
                        <td><?= esc($itinerary['port_fees']) ?></td>
                        <td><?= esc($itinerary['currency']) ?></td>
                        <td><?= esc($itinerary['length']) ?></td>
                        <td><?= esc($itinerary['cruise_title']) ?></td>
                        <td><?= esc($itinerary['ship_name']) ?></td>
                        <td><?= esc($itinerary['departure_port']) ?></td>
                        <td><?= esc($itinerary['itinerary']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No itineraries found.</p>
    <?php endif; ?>
</body>

</html>