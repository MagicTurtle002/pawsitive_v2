<div class="notification-bell fixed top-5 right-[310px] z-50">
    <div class="relative inline-block">
        <!-- Bell Icon with Badge -->
        <button id="notificationButton"
            class="relative bg-white p-2 rounded-full shadow-md hover:bg-gray-100 focus:outline-none transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>

            <?php if ($unreadCount > 0): ?>
                <span
                    class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                </span>
            <?php endif; ?>
        </button>

        <!-- Notification Dropdown -->
        <div id="notificationDropdown"
            class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                    <button id="markAllRead" class="text-sm text-teal hover:underline">Mark all as read</button>
                </div>
            </div>

            <div id="notificationList" class="divide-y divide-gray-200">
                <?php if (empty($recentNotifications)): ?>
                    <div class="p-4 text-center text-gray-500">No notifications</div>
                <?php else: ?>
                    <?php foreach ($recentNotifications as $notification): ?>
                        <div class="notification-item p-4 hover:bg-gray-50 <?= $notification['IsRead'] ? 'bg-white' : 'bg-blue-50' ?>"
                            data-id="<?= htmlspecialchars($notification['NotificationId']) ?>">
                            <div class="flex items-start">
                                <!-- Icon based on type -->
                                <div class="flex-shrink-0 mr-3">
                                    <?php if ($notification['Type'] === 'appointment'): ?>
                                        <div class="h-8 w-8 rounded-full bg-teal bg-opacity-20 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    <?php elseif ($notification['Type'] === 'status'): ?>
                                        <div class="h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                    <?php elseif ($notification['Type'] === 'system'): ?>
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    <?php else: ?>
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">
                                        <?= htmlspecialchars($notification['Title']) ?>
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <?= htmlspecialchars($notification['Message']) ?>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?= date('M d, Y h:i A', strtotime($notification['CreatedAt'])) ?>
                                    </p>
                                </div>

                                <!-- Mark as Read Button -->
                                <?php if (!$notification['IsRead']): ?>
                                    <button class="mark-read-btn ml-2 text-gray-400 hover:text-gray-600"
                                        data-id="<?= htmlspecialchars($notification['NotificationId']) ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="p-4 border-t border-gray-200 text-center">
                <a href="notifications.php" class="text-teal hover:underline">View all notifications</a>
            </div>
        </div>
    </div>
</div>