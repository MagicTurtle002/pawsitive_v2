<?php
function renderPagination($currentPage, $totalPages, $baseUrl = '?page=')
{
    if ($totalPages <= 1)
        return;

    echo '<nav aria-label="Page navigation" class="mt-5">';
    echo '<ul class="flex justify-center">';

    // First Page
    if ($currentPage > 0) {
        echo '<li><a href="' . $baseUrl . '0" aria-label="First"
            class="py-2 px-4 mx-1 border border-gray-300 rounded-md no-underline text-[#007b8a] font-medium hover:bg-[#005f6b] hover:text-white transition-colors duration-300">First</a></li>';
    }

    // Previous Page
    if ($currentPage > 0) {
        echo '<li><a href="' . $baseUrl . max(0, $currentPage - 1) . '" aria-label="Previous"
            class="py-2 px-4 mx-1 border border-gray-300 rounded-md no-underline text-[#007b8a] font-medium hover:bg-[#005f6b] hover:text-white transition-colors duration-300">&laquo; Previous</a></li>';
    }

    // Page numbers with ellipsis
    for ($i = 0; $i < $totalPages; $i++) {
        if ($i == 0 || $i == $totalPages - 1 || abs($i - $currentPage) <= 2) {
            $active = $i == $currentPage ? 'bg-[#007b8a] text-white border-[#007b8a]' : 'text-[#007b8a] hover:bg-[#005f6b] hover:text-white';
            echo '<li><a href="' . $baseUrl . $i . '" class="py-2 px-4 mx-1 border border-gray-300 rounded-md no-underline ' . $active . ' transition-colors duration-300">' . ($i + 1) . '</a></li>';
        } elseif ($i == 1 || $i == $totalPages - 2) {
            echo '<li><span class="py-2 px-4 mx-1">...</span></li>';
        }
    }

    // Next Page
    if ($currentPage < $totalPages - 1) {
        echo '<li><a href="' . $baseUrl . ($currentPage + 1) . '" aria-label="Next"
            class="py-2 px-4 mx-1 border border-gray-300 rounded-md no-underline text-[#007b8a] font-medium hover:bg-[#005f6b] hover:text-white transition-colors duration-300">Next &raquo;</a></li>';
    }

    // Last Page
    if ($currentPage < $totalPages - 1) {
        echo '<li><a href="' . $baseUrl . ($totalPages - 1) . '" aria-label="Last"
            class="py-2 px-4 mx-1 border border-gray-300 rounded-md no-underline text-[#007b8a] font-medium hover:bg-[#005f6b] hover:text-white transition-colors duration-300">Last</a></li>';
    }

    echo '</ul>';
    echo '<p class="text-center mt-2">Page ' . ($currentPage + 1) . ' of ' . $totalPages . '</p>';
    echo '</nav>';
}