<?php
require __DIR__ . '/../config/init.php';

session_start();
require __DIR__ . '/../config/dbh.inc.php';
require __DIR__ . '/../includes/dashboard_functions.php';
require __DIR__ . '/../helpers/notifications.php';

$totalRecords = getTotalRecords($pdo);

$appointmentData = [
    'day' => getAppointmentStats($pdo, '30 DAY', 'DATE(AppointmentDate)', 'label'),
    'week' => getAppointmentStats($pdo, '12 WEEK', "CONCAT(YEAR(AppointmentDate), '-W', WEEK(AppointmentDate))", 'label'),
    'month' => getAppointmentStats($pdo, '1 YEAR', "DATE_FORMAT(AppointmentDate, '%Y-%m')", 'label'),
    'year' => getAppointmentStats($pdo, '5 YEAR', "YEAR(AppointmentDate)", 'label')
];

$upcomingAppointments = getUpcomingAppointments($pdo);
$recentActivities = getRecentActivities($pdo);
$speciesChart = getSpeciesStats($pdo);

$speciesChart = getSpeciesStats($pdo);
$dogsCount = $speciesChart['Dog'] ?? 0;
$catsCount = $speciesChart['Cat'] ?? 0;
$otherPetsCount = $speciesChart['Other'] ?? 0;

$unreadCount = getUnreadNotificationsCount($pdo);
$recentNotifications = getRecentNotifications($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../components/head-components.php'; ?>
</head>

<body class="flex min-h-screen bg-white font-poppins">

    <?php include '../components/sidebar/sidebar.php'; ?>
    <!-- Content Wrapper -->
    <div class="flex ml-64 w-full">
        <!-- Main Content -->
        <div class="flex-1 p-5 pr-[320px] bg-white min-h-screen">
            <h1 class="text-2xl font-bold mb-5">Overview</h1>

            <!-- Overview Section -->
            <section class="flex justify-between mb-5">
                <div>
                    <h2 class="text-lg text-gray-600">Total Records of Registered Pets</h2>
                    <p class="text-4xl font-bold text-gray-800"><?= htmlspecialchars($totalRecords); ?></p>
                </div>
                <section class="flex justify-between items-center gap-5">
                    <!-- Pet Items -->
                    <div class="flex flex-col items-center text-center bg-gray-50 rounded-xl shadow-lg p-4 w-32">
                        <img src="../assets/images/Icons/Dogs.png" alt="Dog Icon" class="w-10 h-10 mb-2">
                        <p class="text-sm font-bold text-gray-800 mb-1">Dogs</p>
                        <span class="text-xl font-bold text-teal"><?= htmlspecialchars($dogsCount); ?></span>
                    </div>
                    <div class="flex flex-col items-center text-center bg-gray-50 rounded-xl shadow-lg p-4 w-32">
                        <img src="../assets/images/Icons/Cats.png" alt="Cat Icon" class="w-10 h-10 mb-2">
                        <p class="text-sm font-bold text-gray-800 mb-1">Cats</p>
                        <span class="text-xl font-bold text-teal"><?= htmlspecialchars($catsCount); ?></span>
                    </div>
                    <div class="flex flex-col items-center text-center bg-gray-50 rounded-xl shadow-lg p-4 w-32">
                        <img src="../assets/images/Icons/Other Pet.png" alt="Other Pets Icon" class="w-10 h-10 mb-2">
                        <p class="text-sm font-bold text-gray-800 mb-1">Other Pets</p>
                        <span class="text-xl font-bold text-teal"><?= htmlspecialchars($otherPetsCount); ?></span>
                    </div>
                </section>
            </section>
            <div class="my-4 border-t border-gray-200">
                <div class="mt-5">
                    <h2 class="text-xl font-semibold text-center text-gray-700">Appointment Analytics</h2>
                    <div class="mt-5">
                        <label for="appointmentRange" class="font-medium text-gray-700">Filter by Range:</label>
                        <select id="appointmentRange" class="ml-2 border rounded-md p-2 focus:ring-2 focus:ring-teal">
                            <option value="day">Per Day</option>
                            <option value="week">Per Week</option>
                            <option value="month" selected>Per Month</option>
                            <option value="year">Per Year</option>
                        </select>
                    </div>
                    <div class="w-full h-96 mt-4">
                        <canvas id="appointmentsChart" data-appointments='<?= json_encode($appointmentData); ?>'
                            width="800" height="400"></canvas>
                    </div>
                </div>
            </div>

            <!-- Species Analytics -->
            <div class="mt-5">
                <h2 class="text-xl font-semibold text-center text-gray-700">Pets Per Species Analytics</h2>
                <div class="mt-5">
                    <label for="speciesFilter" class="font-medium text-gray-700">Filter by Species:</label>
                    <select id="speciesFilter" class="ml-2 border rounded-md p-2 focus:ring-2 focus:ring-teal">
                        <option value="all">All Species</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Rabbit">Rabbit</option>
                        <option value="Bird">Bird</option>
                        <option value="Hamster">Hamster</option>
                        <option value="Guinea Pig">Guinea Pig</option>
                        <option value="Reptile">Reptile</option>
                        <option value="Ferret">Ferret</option>
                        <option value="Fish">Fish</option>
                    </select>
                </div>
                <div class="max-w-md mx-auto mt-8 bg-white shadow-lg rounded-lg p-4">
                    <canvas id="speciesPieChart" data-labels='["Dog", "Cat", "Bird", "Rabbit"]'
                        data-counts='[25, 15, 5, 8]'></canvas>
                </div>
            </div>
        </div>
        <?php include '../components/notification.php' ?>
        <!-- Right Section -->
        <div
            class="absolute right-5 top-5 w-72 flex flex-col items-start gap-5 max-h-[calc(100vh-40px)] overflow-y-auto bg-white p-4">
            <h2 class="text-xl font-semibold">Quick Actions</h2>


            <button
                class="w-full bg-teal text-white py-3 px-6 rounded font-bold text-base hover:bg-teal-dark transition-colors">
                <a href="add_owner_pet.php" class="text-white no-underline">Add Owner and Pet</a>
            </button>

            <!-- Upcoming Appointments -->
            <div class="w-full bg-white rounded-lg p-4">
                <h2 class="text-m font-semibold mb-2">Upcoming Appointments</h2>
                <div>
                    <?php if (!empty($upcomingAppointments)): ?>
                        <?php foreach ($upcomingAppointments as $appointment): ?>
                            <div id="appointment-<?= htmlspecialchars($appointment['AppointmentId']) . '-' . htmlspecialchars($appointment['PetId']); ?>"
                                class="bg-white rounded-xl p-4 shadow-lg mb-4">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-xl"><?= htmlspecialchars($appointment['PetName']); ?></h3>
                                    <div class="relative">
                                        <button id="menu-btn-<?= $appointment['AppointmentId']; ?>"
                                            class="bg-transparent border-none text-black p-1 m-1 cursor-pointer flex items-center justify-center"
                                            onclick="toggleMenu(<?= $appointment['AppointmentId']; ?>)">⋮</button>
                                        <div id="menu-<?= $appointment['AppointmentId']; ?>"
                                            class="hidden absolute top-8 right-0 bg-white border border-gray-200 shadow-md z-50 rounded">
                                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100"
                                                onclick="updateAppointmentStatus(<?= $appointment['AppointmentId']; ?>, 'Done', <?= $appointment['PetId']; ?>); return false;"
                                                <?= ($appointment['Status'] === 'Done' || $appointment['Status'] === 'Declined') ? 'style="pointer-events: none; color: gray; cursor: not-allowed;"' : ''; ?>>
                                                Mark as Done
                                            </a>
                                            <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100"
                                                onclick="updateAppointmentStatus(<?= $appointment['AppointmentId']; ?>, 'Declined', <?= $appointment['PetId']; ?>); return false;"
                                                <?= ($appointment['Status'] === 'Done' || $appointment['Status'] === 'Declined') ? 'style="pointer-events: none; color: gray; cursor: not-allowed;"' : ''; ?>>
                                                Decline
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <p><strong>Service:</strong> <?= htmlspecialchars($appointment['ServiceName']); ?></p>
                                <p><strong>Date:</strong> <?= htmlspecialchars($appointment['AppointmentDate']); ?></p>
                                <p><strong>Time:</strong> <?= htmlspecialchars($appointment['AppointmentTime']); ?></p>
                                <p><strong>Status:</strong> <span><?= htmlspecialchars($appointment['Status']); ?></span></p>
                                <div
                                    id="buttons-<?= htmlspecialchars($appointment['AppointmentId']) . '-' . htmlspecialchars($appointment['PetId']); ?>">
                                    <!-- Invoice button -->
                                    <?php if ($appointment['Status'] === 'Done'): ?>
                                        <button
                                            class="w-full bg-teal text-white py-2 px-4 rounded mt-2 hover:bg-teal-dark transition-colors"
                                            onclick="generateInvoice(<?= htmlspecialchars($appointment['AppointmentId']); ?>, <?= htmlspecialchars($appointment['PetId']); ?>)">
                                            Invoice and Billing
                                        </button>
                                    <?php else: ?>
                                        <button class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded mt-2 cursor-not-allowed"
                                            disabled>
                                            Invoice and Billing
                                        </button>
                                    <?php endif; ?>

                                    <!-- Consultation/Confirm button -->
                                    <?php if ($appointment['Status'] === 'Confirmed'): ?>
                                        <?php if ($appointment['ServiceName'] === 'Pet Vaccination & Deworming'): ?>
                                            <button
                                                class="w-full bg-teal text-white py-2 px-4 rounded mt-2 hover:bg-teal-dark transition-colors"
                                                onclick="promptVitalsVaccine('<?= htmlspecialchars($appointment['AppointmentId']); ?>', '<?= htmlspecialchars($appointment['PetId']); ?>')">
                                                Start Consultation
                                            </button>
                                        <?php else: ?>
                                            <button
                                                class="w-full bg-teal text-white py-2 px-4 rounded mt-2 hover:bg-teal-dark transition-colors"
                                                onclick="promptVitalsUpdate('<?= htmlspecialchars($appointment['AppointmentId']); ?>', '<?= htmlspecialchars($appointment['PetId']); ?>')">
                                                Start Consultation
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <!-- Only show this button if not confirmed, done, or declined -->
                                        <?php if (!in_array($appointment['Status'], ['Confirmed', 'Done', 'Declined'])): ?>
                                            <button
                                                class="w-full bg-teal text-white py-2 px-4 rounded mt-2 hover:bg-teal-dark transition-colors"
                                                onclick="updateAppointmentStatus(<?= htmlspecialchars($appointment['AppointmentId']); ?>, 'Confirmed', <?= htmlspecialchars($appointment['PetId']); ?>)">
                                                Confirm
                                            </button>
                                        <?php else: ?>
                                            <!-- Show a disabled button for statuses that can't be confirmed -->
                                            <button class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded mt-2 cursor-not-allowed"
                                                disabled>
                                                Confirm
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No upcoming appointments.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Recent Activities -->
            <div class="w-full bg-white rounded-lg p-4">
                <h2 class="text-lg font-semibold mb-2">Recent Activities</h2>
                <div>
                    <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="bg-white rounded-xl p-4 shadow-lg mb-4">
                                <h3><?= htmlspecialchars($activity['UserName']); ?></h3>
                                <p><strong>Role:</strong> <?= htmlspecialchars($activity['Role']); ?></p>
                                <p><strong>Activity:</strong> <?= htmlspecialchars_decode($activity['ActionDetails']); ?></p>
                                <p><strong>Timestamp:</strong> <?= date('Y-m-d h:i:s A', strtotime($activity['CreatedAt'])); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recent activities.</p>
                    <?php endif; ?>

                    <?php if (!empty($recentActivities)): ?>
                        <button
                            class="w-full bg-teal text-white py-2 px-4 rounded mt-2 hover:bg-teal-dark transition-colors"
                            onclick="window.location.href='appointment_list.php';">
                            See All
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu(appointmentId) {
            const menu = document.getElementById(`menu-${appointmentId}`);

            // Close any other open menus
            document.querySelectorAll('[id^="menu-"]').forEach(item => {
                if (item !== menu) {
                    item.classList.add('hidden');
                }
            });

            // Toggle visibility of the clicked menu
            menu.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/appointment.js"></script>
    <script>
        const ctx = document.getElementById('appointmentsChart').getContext('2d');
        const appointmentData = JSON.parse(document.getElementById('appointmentsChart').dataset.appointments);

        const labels = appointmentData.map(entry => entry.month);
        const data = appointmentData.map(entry => entry.total);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Appointments',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/charts.js"></script>
</body>

</html>