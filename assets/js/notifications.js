document.addEventListener("DOMContentLoaded", function () {
  const notificationButton = document.getElementById("notificationButton");
  const notificationDropdown = document.getElementById("notificationDropdown");
  const markAllReadButton = document.getElementById("markAllRead");
  const markReadButtons = document.querySelectorAll(".mark-read-btn");
  const notificationItems = document.querySelectorAll(".notification-item");

  // Toggle dropdown
  notificationButton.addEventListener("click", function () {
    notificationDropdown.classList.toggle("hidden");
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (event) {
    if (
      !notificationButton.contains(event.target) &&
      !notificationDropdown.contains(event.target)
    ) {
      notificationDropdown.classList.add("hidden");
    }
  });

  // Mark all as read
  markAllReadButton.addEventListener("click", function () {
    fetch("api/mark_all_notifications_read.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update UI
          document.querySelectorAll(".notification-item").forEach((item) => {
            item.classList.remove("bg-blue-50");
            item.classList.add("bg-white");
            const readBtn = item.querySelector(".mark-read-btn");
            if (readBtn) readBtn.remove();
          });

          // Remove badge
          const badge = notificationButton.querySelector("span");
          if (badge) badge.remove();
        }
      })
      .catch((error) => console.error("Error:", error));
  });

  // Mark individual notification as read
  markReadButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.stopPropagation();
      const notificationId = this.getAttribute("data-id");

      fetch("api/mark_notification_read.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: notificationId }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Update UI
            const notificationItem = document.querySelector(
              `.notification-item[data-id="${notificationId}"]`
            );
            notificationItem.classList.remove("bg-blue-50");
            notificationItem.classList.add("bg-white");
            this.remove();

            // Update badge
            const unreadCount = parseInt(data.unreadCount);
            const badge = notificationButton.querySelector("span");

            if (unreadCount === 0 && badge) {
              badge.remove();
            } else if (badge) {
              badge.textContent = unreadCount > 9 ? "9+" : unreadCount;
            }
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  });

  // Click on notification to view details
  notificationItems.forEach((item) => {
    item.addEventListener("click", function () {
      const notificationId = this.getAttribute("data-id");
      // Redirect to relevant page or show details modal
      // For now, just mark as read if unread
      if (this.classList.contains("bg-blue-50")) {
        const markReadBtn = this.querySelector(".mark-read-btn");
        if (markReadBtn) {
          markReadBtn.click();
        }
      }
    });
  });

  // Periodically check for new notifications (every 60 seconds)
  setInterval(function () {
    fetch("api/check_new_notifications.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.hasNew) {
          // Update the notification list
          const notificationList = document.getElementById("notificationList");

          // Add badge if not exists
          let badge = notificationButton.querySelector("span");
          if (!badge && data.unreadCount > 0) {
            badge = document.createElement("span");
            badge.className =
              "absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center";
            badge.textContent = data.unreadCount > 9 ? "9+" : data.unreadCount;
            notificationButton.appendChild(badge);
          } else if (badge && data.unreadCount > 0) {
            badge.textContent = data.unreadCount > 9 ? "9+" : data.unreadCount;
          }

          // Update notification list if dropdown is open
          if (!notificationDropdown.classList.contains("hidden")) {
            // Refresh notifications list
            notificationList.innerHTML = "";

            if (data.notifications.length > 0) {
              data.notifications.forEach((notification) => {
                // Create notification item
                // (Similar HTML structure as in PHP part but with JavaScript)
                // This is simplified - you may want to implement a full refresh
              });
            } else {
              notificationList.innerHTML =
                '<div class="p-4 text-center text-gray-500">No notifications</div>';
            }
          }
        }
      })
      .catch((error) => console.error("Error:", error));
  }, 60000);
});
