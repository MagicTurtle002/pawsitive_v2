<?php
/**
 * Species Chart Component - A reusable component for displaying pet species distribution
 * 
 * @param array $speciesChart - Associative array of species counts ['Dog' => 25, 'Cat' => 15, etc.]
 */

// If not provided directly, get species stats from the database
if (!isset($speciesChart) && function_exists('getSpeciesStats') && isset($pdo)) {
    $speciesChart = getSpeciesStats($pdo);
}

// Extract counts for main categories
$dogsCount = $speciesChart['Dog'] ?? 0;
$catsCount = $speciesChart['Cat'] ?? 0;
$otherPetsCount = 0;

// Calculate "Other" category total
foreach ($speciesChart as $species => $count) {
    if ($species !== 'Dog' && $species !== 'Cat') {
        $otherPetsCount += $count;
    }
}

// Prepare data for chart
$chartLabels = array_keys($speciesChart);
$chartCounts = array_values($speciesChart);

// Convert to JSON for JavaScript
$labelsJson = json_encode($chartLabels);
$countsJson = json_encode($chartCounts);
?>

<!-- Species Analytics Chart Component -->
<div class="species-analytics-container mt-5">
    <h2 class="text-xl font-semibold text-center text-gray-700">Pets Per Species Analytics</h2>

    <!-- Filter Controls -->
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

    <!-- Chart Container -->
    <div class="max-w-md mx-auto mt-8 bg-white shadow-lg rounded-lg p-4">
        <canvas id="speciesPieChart" data-labels='<?= $labelsJson ?>' data-counts='<?= $countsJson ?>'></canvas>
    </div>
</div>

<!-- Chart Initialization Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initializeSpeciesChart();

        // Set up filter event listener
        document.getElementById('speciesFilter').addEventListener('change', function () {
            filterSpeciesChart(this.value);
        });
    });

    function initializeSpeciesChart() {
        const speciesCtx = document.getElementById('speciesPieChart').getContext('2d');
        const speciesLabels = JSON.parse(document.getElementById('speciesPieChart').dataset.labels);
        const speciesCounts = JSON.parse(document.getElementById('speciesPieChart').dataset.counts);

        // Store original data for filtering
        window.speciesChartOriginalData = {
            labels: [...speciesLabels],
            counts: [...speciesCounts]
        };

        // Create color arrays based on number of species
        const backgroundColors = generateColors(speciesLabels.length, 0.6);
        const borderColors = generateColors(speciesLabels.length, 1);

        window.speciesChart = new Chart(speciesCtx, {
            type: 'pie',
            data: {
                labels: speciesLabels,
                datasets: [{
                    label: 'Species Distribution',
                    data: speciesCounts,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function filterSpeciesChart(species) {
        if (!window.speciesChart || !window.speciesChartOriginalData) return;

        const chart = window.speciesChart;
        const originalData = window.speciesChartOriginalData;

        if (species === 'all') {
            // Restore original data
            chart.data.labels = [...originalData.labels];
            chart.data.datasets[0].data = [...originalData.counts];
        } else {
            // Filter for selected species
            const filteredLabels = [];
            const filteredCounts = [];

            originalData.labels.forEach((label, index) => {
                if (label === species) {
                    filteredLabels.push(label);
                    filteredCounts.push(originalData.counts[index]);
                }
            });

            chart.data.labels = filteredLabels;
            chart.data.datasets[0].data = filteredCounts;
        }

        chart.update();
    }

    function generateColors(count, alpha) {
        // Predefined colors for consistency
        const baseColors = [
            `rgba(255, 99, 132, ${alpha})`,   // Red
            `rgba(54, 162, 235, ${alpha})`,   // Blue
            `rgba(255, 206, 86, ${alpha})`,   // Yellow
            `rgba(75, 192, 192, ${alpha})`,   // Teal
            `rgba(153, 102, 255, ${alpha})`,  // Purple
            `rgba(255, 159, 64, ${alpha})`,   // Orange
            `rgba(201, 203, 207, ${alpha})`,  // Grey
            `rgba(99, 255, 132, ${alpha})`,   // Green
            `rgba(255, 99, 255, ${alpha})`,   // Pink
            `rgba(99, 148, 255, ${alpha})`    // Light blue
        ];

        // If we need more colors than our predefined set, generate them
        const colors = [];
        for (let i = 0; i < count; i++) {
            if (i < baseColors.length) {
                colors.push(baseColors[i]);
            } else {
                // Generate a random color if we run out of predefined colors
                const r = Math.floor(Math.random() * 255);
                const g = Math.floor(Math.random() * 255);
                const b = Math.floor(Math.random() * 255);
                colors.push(`rgba(${r}, ${g}, ${b}, ${alpha})`);
            }
        }

        return colors;
    }
</script>