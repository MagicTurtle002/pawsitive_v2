<?php
require __DIR__ . '/../config/init.php';
session_start();
require_once __DIR__ . '/../config/dbh.inc.php';

$userId = $_SESSION['UserId'];
$userName = $_SESSION['FirstName'] . ' ' . $_SESSION['LastName'];
$role = $_SESSION['Role'] ?? 'Role';
$email = $_SESSION['Email'];

function renderExpandablePetDetails($row)
{
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <h3 class="font-medium text-gray-700">Pet Information</h3>
            <p class="text-sm text-gray-600">Breed: <?= $row['PetBreed'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Age: <?= $row['PetAge'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Weight: <?= $row['PetWeight'] ?? 'Unknown' ?> kg</p>
        </div>
        <div>
            <h3 class="font-medium text-gray-700">Owner Information</h3>
            <p class="text-sm text-gray-600">Name: <?= $row['OwnerName'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Phone: <?= $row['OwnerPhone'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Email: <?= $row['OwnerEmail'] ?? 'Unknown' ?></p>
        </div>
        <div>
            <h3 class="font-medium text-gray-700">Medical History</h3>
            <p class="text-sm text-gray-600">Vaccinations: <?= $row['Vaccinations'] ?? 'Up to date' ?></p>
            <p class="text-sm text-gray-600">Last Treatment: <?= $row['LastTreatment'] ?? 'None' ?></p>
            <a href="medical_history.php?id=<?= $row['PetCode'] ?>" class="text-[#007b8a] hover:underline text-sm">View Full
                History</a>
        </div>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include '../components/head-components.php'; ?>
</head>

<body>
    <div class="flex min-h-screen bg-white">
        <?php include '../components/sidebar/sidebar.php'; ?>
        <!-- Main Content -->
        <div class="ml-64 flex-1 p-5">
            <div class="mb-5">
                <?php
                include '../components/search/search_filter_form.php';

                renderSearchHeader([
                    'title' => 'Record',
                    'action' => 'record.php',
                    'searchPlaceholder' => 'Search record...',
                    'filters' => [
                        'Pet Name' => 'pet_name',
                        'Services' => 'pet_service',
                        'Owner Name' => 'owner_name',
                    ],
                    'buttons' => [
                        ['label' => 'Archive', 'href' => 'archive_list.php'],
                        ['label' => '+ Add Owner and Pet', 'href' => 'add_owner_pet.php']
                    ]
                ]);

                ?>
            </div>
            <?php
            // Include the TableComponent file
            include '../components/table/TableComponent.php';

            // Define the columns for the table
            $columns = [
                'PetCode' => 'Pet ID',
                'PetName' => 'Pet Name',
                'PetType' => 'Pet Type',
                'PetStatus' => 'Status',
                'LastVisit' => 'Last Visit',
                'NextVisit' => 'Next Visit'
            ];

            // Define the data for the table
            $pets = [
                [
                    'PetCode' => 'P001',
                    'PetName' => 'Buddy',
                    'PetType' => 'Dog',
                    'PetStatus' => 'Active',
                    'LastVisit' => '2025-04-10',
                    'NextVisit' => '2025-06-10',
                    'PetBreed' => 'Golden Retriever',
                    'PetAge' => '3 years',
                    'PetWeight' => '28',
                    'OwnerName' => 'John Smith',
                    'OwnerPhone' => '555-123-4567',
                    'OwnerEmail' => 'john.smith@example.com'
                ],
                [
                    'PetCode' => 'P002',
                    'PetName' => 'Whiskers',
                    'PetType' => 'Cat',
                    'PetStatus' => 'Active',
                    'LastVisit' => '2025-04-15',
                    'NextVisit' => '2025-07-15',
                    'PetBreed' => 'Siamese',
                    'PetAge' => '2 years',
                    'PetWeight' => '4.5',
                    'OwnerName' => 'Sarah Johnson',
                    'OwnerPhone' => '555-987-6543',
                    'OwnerEmail' => 'sarah.j@example.com'
                ],
                [
                    'PetCode' => 'P003',
                    'PetName' => 'Max',
                    'PetType' => 'Dog',
                    'PetStatus' => 'Inactive',
                    'LastVisit' => '2025-03-20',
                    'NextVisit' => '2025-06-20',
                    'PetBreed' => 'German Shepherd',
                    'PetAge' => '5 years',
                    'PetWeight' => '34',
                    'OwnerName' => 'Michael Brown',
                    'OwnerPhone' => '555-456-7890',
                    'OwnerEmail' => 'michael.b@example.com'
                ]
            ];

            // For pagination demo (in a real app, this would be calculated from database)
            $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 0;
            $totalItems = count($pets);
            $itemsPerPage = 10;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Render the table using the renderTable function
            renderTable([
                'columns' => $columns,
                'data' => $pets,
                'tableId' => 'petsTable',
                'tableClass' => 'staff-table',
                'expandable' => true,
                'expandCallback' => 'renderExpandablePetDetails'
            ]);
            ?>
        </div>
    </div>

    <?php if (isset($_SESSION['errors']['duplicate'])): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Duplicate Entry",
                    text: "<?= htmlspecialchars($_SESSION['errors']['duplicate']) ?>",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
        </script>
        <?php unset($_SESSION['errors']['duplicate']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Success!",
                    text: "Pet added successfully!",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            });
        </script>
    <?php endif; ?>

    <script>
        // Toggle filter dropdown
        document.querySelector('.filter-btn').addEventListener('click', function () {
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            if (!e.target.matches('.filter-btn') && !e.target.closest('.dropdown-content')) {
                const dropdowns = document.querySelectorAll('.dropdown-content');
                dropdowns.forEach(function (dropdown) {
                    if (!dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });

        // Toggle three-dot menu
        document.querySelectorAll('.three-dot-btns').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const dropdown = this.nextElementSibling;
                dropdown.classList.toggle('hidden');
            });
        });

        // Close three-dot menu when clicking outside
        window.addEventListener('click', function (e) {
            if (!e.target.matches('.three-dot-btns')) {
                const dropdowns = document.querySelectorAll('.dropdown-menus');
                dropdowns.forEach(function (dropdown) {
                    if (!dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('hidden');
                    }
                });
            }
        });

        // Show the kebab button on row hover
        document.querySelectorAll('.staff-table tbody tr').forEach(function (row) {
            row.addEventListener('mouseenter', function () {
                const kebabBtn = this.querySelector('.three-dot-btns');
                if (kebabBtn) {
                    kebabBtn.style.opacity = '1';
                }
            });

            row.addEventListener('mouseleave', function () {
                const kebabBtn = this.querySelector('.three-dot-btns');
                if (kebabBtn) {
                    kebabBtn.style.opacity = '0';
                }
            });
        });

        // Toggle pet details row
        function togglePetDetails(row) {
            const detailsRow = row.nextElementSibling;
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
            } else {
                detailsRow.classList.add('hidden');
            }
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            const radioButtons = document.querySelectorAll('input[type="radio"]');
            radioButtons.forEach(function (radio) {
                radio.checked = false;
            });
        }

        // Confirm archive
        function confirmArchive(petId) {
            Swal.fire({
                title: "Archive Pet",
                text: "Are you sure you want to archive this pet?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, archive it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "archive_pet.php?pet_id=" + petId;
                }
            });
        }

        // Confirm delete
        function confirmDelete(petId) {
            Swal.fire({
                title: "Delete Pet",
                text: "Are you sure you want to delete this pet? This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "delete_pet.php?pet_id=" + petId;
                }
            });
        }
    </script>
</body>

</html>