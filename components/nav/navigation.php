<?php
// File: navigation.php
// This file would be included in your main layout or page files

/**
 * Generate navigation buttons with modal trigger functionality
 * 
 * @param array $buttons Array of button configurations
 * @return string HTML for the navigation buttons
 */
function generateNavButtons($buttons) {
    $html = '<div class="flex space-x-4 mb-6">';
    
    foreach ($buttons as $button) {
        // Set default classes
        $classes = $button['classes'] ?? 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow';
        
        // Check if this button should trigger a modal
        $modalAttr = isset($button['modal']) ? 'onclick="return showModal(\'' . $button['modal'] . '\')"' : '';
        
        $html .= '<a href="' . htmlspecialchars($button['href']) . '" class="' . $classes . '" ' . $modalAttr . '>';
        $html .= htmlspecialchars($button['label']);
        $html .= '</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}
