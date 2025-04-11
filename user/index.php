<?php include 'header.php'; ?>



<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('close');
    }
    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }
</script>
<script>
    function updateActivity() {
        fetch("update_activity.php"); // Calls PHP script without refreshing
    }

    // Update last_activity every 30 seconds
    setInterval(updateActivity, 30000);
</script>

</body>

</html>