<?php

require_once __DIR__ . '/../../config/dbh.inc.php';

$userId = $_SESSION['UserId'];
$userName = $_SESSION['FirstName'] . ' ' . $_SESSION['LastName'];
$role = $_SESSION['Role'] ?? 'Role';
$email = $_SESSION['Email'];

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<div class="fixed w-64 bg-teal text-white flex flex-col items-start p-5 rounded-r-2xl shadow-md h-screen">
    <div class="flex items-center text-white mb-12">
        <img src="../assets/images/logo/LOGO 2 WHITE.png" alt="Pawsitive Logo" class="w-52 mr-2">
    </div>
    <nav class="w-full">
        <h3>Hello, <?= htmlspecialchars($userName) ?></h3>
        <h4><?= htmlspecialchars($role) ?></h4>
        <br>
        <ul class="list-none">
            <!-- Overview -->
            <li class="py-2 mb-4 ml-4 text-xl cursor-pointer flex items-center rounded-lg p-2
                <?= $currentPage == 'dashboard.php' ? 'bg-white text-teal' : 'hover:translate-x-1 menu-transition' ?>">
                <a href="dashboard.php"
                    class="flex items-center w-full <?= $currentPage == 'dashboard.php' ? 'text-teal' : 'text-white hover:text-teal-light' ?>">
                    <img src="../assets/images/Icons/Chart 3.png" alt="Chart Icon" class="w-6 h-6 mr-2">Overview
                </a>
            </li>

            <!-- Record -->
            <li class="py-2 mb-4 ml-4 text-xl cursor-pointer flex items-center rounded-lg p-2
                <?= $currentPage == 'record.php' ? 'bg-white text-teal' : 'hover:translate-x-1 menu-transition' ?>">
                <a href="record.php"
                    class="flex items-center w-full <?= $currentPage == 'record.php' ? 'text-teal' : 'text-white hover:text-teal-light' ?>">
                    <img src="../assets/images/Icons/Record 1.png" alt="Record Icon" class="w-6 h-6 mr-2">Record
                </a>
            </li>

            <!-- Staff -->
            <li
                class="py-2 mb-4 ml-4 text-xl cursor-pointer flex items-center rounded-lg p-2
                <?= $currentPage == 'staff_view.php' ? 'bg-white text-teal' : 'hover:translate-x-1 menu-transition' ?>">
                <a href="staff_view.php"
                    class="flex items-center w-full <?= $currentPage == 'staff_view.php' ? 'text-teal' : 'text-white hover:text-teal-light' ?>">
                    <img src="../assets/images/Icons/Staff 1.png" alt="Staff Icon" class="w-6 h-6 mr-2">Staff
                </a>
            </li>
        </ul>
    </nav>
    <!-- Buttons remain unchanged -->
    <div class="mt-auto ml-4">
        <!-- Settings -->
        <button onclick="window.location.href='settings.php';"
            class="bg-transparent border-none text-white p-2 cursor-pointer text-left mb-2 text-xl flex items-center hover:translate-x-1 hover:text-teal-light menu-transition">
            <img src="../assets/images/Icons/Settings 1.png" alt="Settings Icon" class="w-6 h-6 mr-2">Settings
        </button>
        <!-- Logout -->
        <button onclick="window.location.href='logout.php';"
            class="bg-transparent border-none text-white p-2 cursor-pointer text-left text-xl flex items-center hover:translate-x-1 hover:text-teal-light menu-transition">
            <img src="../assets/images/Icons/Logout 1.png" alt="Logout Icon" class="w-6 h-6 mr-2">Log out
        </button>
    </div>
</div>