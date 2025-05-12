<?php
function renderExpandableStaffDetails($row)
{
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <h3 class="font-medium text-gray-700">Staff Information</h3>
            <p class="text-sm text-gray-600">Gender: <?= $row['PetBreed'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Date of Birth: <?= $row['PetAge'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Date of Hired: <?= $row['PetWeight'] ?? 'Unknown' ?> kg</p>
        </div>
        <div>
            <h3 class="font-medium text-gray-700">Contact Information</h3>
            <p class="text-sm text-gray-600">Phone: <?= $row['OwnerPhone'] ?? 'Unknown' ?></p>
            <p class="text-sm text-gray-600">Email: <?= $row['OwnerEmail'] ?? 'Unknown' ?></p>
        </div>
        <div>
            <h3 class="font-medium text-gray-700">Account Information</h3>
            <p class="text-sm text-gray-600">Created By: <?= $row['Vaccinations'] ?? 'Up to date' ?></p>
            <p class="text-sm text-gray-600">Last Updated: <?= $row['LastTreatment'] ?? 'None' ?></p>
            <a href="medical_history.php?id=<?= $row['PetCode'] ?>"
                class="text-[#007b8a] hover:underline text-sm">Remarks</a>
        </div>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: '#007b8a',
                        'teal-light': '#e6f7f9',
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>
    <div class="flex min-h-screen bg-white">
        <?php include '../components/sidebar/sidebar.php'; ?>
        <div class="ml-64 flex-1 p-5">
            <div class="container mx-auto">
                <?php include '../components/search/search_filter_form.php';

                renderSearchHeader([
                    'title' => 'Staff List',
                    'action' => 'staff_view.php',
                    'searchPlaceholder' => 'Search by name or email',
                    'filters' => [
                        'All' => '',
                        'Active' => 'active',
                        'Inactive' => 'inactive',
                    ],
                    'buttons' => [
                        ['label' => 'Add Staff', 'href' => 'add_staff.php'],
                    ],
                ]);
                ?>
            </div>
            <?php
            // Include the TableComponent file
            include '../components/table/TableComponent.php';

            // Define the columns for the table
            $columns = [
                'Staff ID' => 'Staff ID',
                'Staff Name' => 'Staff Name',
                'Staff Email' => 'Staff Email',
                'Staff Phone' => 'Staff Phone',
                'Staff Role' => 'Staff Role',
                'Staff Status' => 'Staff Status',
                'Last Login' => 'Last Login',
                'Actions' => 'Actions'
            ];

            // Define the data for the table
            $staff = [
                [
                    'Staff ID' => 'STF001',
                    'Staff Name' => 'Dr. Angela Ramos',
                    'Staff Email' => 'angela.ramos@clinic.com',
                    'Staff Phone' => '09171234567',
                    'Staff Role' => 'Veterinarian',
                    'Staff Status' => 'Active',
                    'Last Login' => '2025-05-06 08:45:23',
                ],
                [
                    'Staff ID' => 'STF002',
                    'Staff Name' => 'John Cruz',
                    'Staff Email' => 'john.cruz@clinic.com',
                    'Staff Phone' => '09987654321',
                    'Staff Role' => 'Receptionist',
                    'Staff Status' => 'Active',
                    'Last Login' => '2025-05-05 15:22:11',
                ],
                [
                    'Staff ID' => 'STF003',
                    'Staff Name' => 'Maria Santos',
                    'Staff Email' => 'maria.santos@clinic.com',
                    'Staff Phone' => '09181231234',
                    'Staff Role' => 'Veterinarian',
                    'Staff Status' => 'Inactive',
                    'Last Login' => '2025-04-29 09:03:55'
                ],
                [
                    'Staff ID' => 'STF004',
                    'Staff Name' => 'Carlos Dela Cruz',
                    'Staff Email' => 'carlos.delacruz@clinic.com',
                    'Staff Phone' => '09051239876',
                    'Staff Role' => 'Admin',
                    'Staff Status' => 'Active',
                    'Last Login' => '2025-05-06 07:10:43'
                ]
            ];

            // For pagination demo (in a real app, this would be calculated from database)
            $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 0;
            $totalItems = count($staff);
            $itemsPerPage = 10;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Render the table using the renderTable function
            renderTable([
                'columns' => $columns,
                'data' => $staff,
                'tableId' => 'staffTable',
                'tableClass' => 'staff-table',
                'expandable' => true,
                'expandCallback' => 'renderExpandableStaffDetails',
            ]);
            ?>
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