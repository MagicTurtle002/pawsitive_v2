<?php
/**
 * Display alert messages (success, error, etc)
 * 
 * @param string|array $message The message or array of messages to display
 * @param string $type The type of alert (success, error, warning, info)
 * @param bool $dismissible Whether the alert can be dismissed
 * @return void
 */
function show_alert($message, $type = 'success', $dismissible = false) {
    // Define color schemes for different alert types
    $colorSchemes = [
        'success' => [
            'bg' => 'bg-green-100',
            'border' => 'border-green-500',
            'text' => 'text-green-700',
            'icon' => 'fa-check-circle'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-500',
            'text' => 'text-red-800',
            'icon' => 'fa-exclamation-circle'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-500',
            'text' => 'text-yellow-800',
            'icon' => 'fa-exclamation-triangle'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-500',
            'text' => 'text-blue-800',
            'icon' => 'fa-info-circle'
        ]
    ];
    
    // Default to success if type not found
    if (!isset($colorSchemes[$type])) {
        $type = 'success';
    }
    
    $colors = $colorSchemes[$type];
    
    // Start alert container
    echo '<div class="' . $colors['bg'] . ' border-l-4 ' . $colors['border'] . ' ' . $colors['text'] . ' p-4 mb-6 rounded shadow-sm">';
    
    // Add dismissible button if requested
    if ($dismissible) {
        echo '<div class="flex justify-between">';
        echo '<div class="flex flex-grow">';
    } else {
        echo '<div class="flex">';
    }
    
    // Icon container
    echo '<div class="flex-shrink-0">';
    echo '<i class="fas ' . $colors['icon'] . ' mt-0.5"></i>';
    echo '</div>';
    
    // Message container
    echo '<div class="ml-3">';
    
    // Handle single message or array of messages
    if (is_array($message)) {
        echo '<ul class="list-disc pl-5 space-y-1">';
        foreach ($message as $item) {
            echo '<li>' . htmlspecialchars($item) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>' . htmlspecialchars($message) . '</p>';
    }
    
    echo '</div>'; // Close message container
    
    // Add dismiss button if dismissible
    if ($dismissible) {
        echo '</div>'; // Close flex-grow div
        echo '<div class="flex-shrink-0">';
        echo '<button type="button" class="text-' . substr($colors['text'], 5) . '-500 hover:text-' . substr($colors['text'], 5) . '-600" onclick="this.parentElement.parentElement.remove();">';
        echo '<i class="fas fa-times"></i>';
        echo '</button>';
        echo '</div>';
    }
    
    echo '</div>'; // Close flex container
    echo '</div>'; // Close alert container
}