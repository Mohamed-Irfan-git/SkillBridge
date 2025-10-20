<!-- footer.php -->
<footer  style="background: linear-gradient(135deg, #003f7d, #003f7d, #0059a0); color: #fff; padding: 50px ;">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-md-4 mb-4">
                <h5 style="color:#00bfff; font-weight: bold; font-family:'Poppins', sans-serif;" >SkillBridge</h5>
                <p class="text-white-50" >
                    SkillBridge allows you to exchange skills, manage tasks, track notifications, and access a personalized dashboard — all in one platform.
                </p>
            </div>

            <!-- Use Cases -->
            <div class="col-md-4 mb-4">
                <h5 style="color:#00bfff; font-weight: bold; font-family:'Poppins', sans-serif;">Use Cases</h5>
                <ul class="list-unstyled text-white-50">
                    <li><a href="../tasks/tasks.php" class="text-white-50 text-decoration-none">Task Management</a></li>
                    <li><a href="../notifications/notifications.php" class="text-white-50 text-decoration-none">Notifications</a></li>
                    <li><a href="../dashboard/dashboard.php" class="text-white-50 text-decoration-none">Dashboard Overview</a></li>
                    <li><a href="../auth/register.php" class="text-white-50 text-decoration-none">Sign Up</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-4 mb-4">
                <h5 style="color:#00bfff; font-weight: bold; font-family:'Poppins', sans-serif;">Follow Us</h5>
                <div class="d-flex gap-3 mt-2">
                    <a href="https://facebook.com" target="_blank">
                        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/facebook.svg" alt="Facebook" width="32" height="32">
                    </a>
                    <a href="https://twitter.com" target="_blank">
                        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/twitter.svg" alt="Twitter" width="32" height="32">
                    </a>
                    <a href="https://linkedin.com" target="_blank">
                        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" alt="LinkedIn" width="32" height="32">
                    </a>
                    <a href="https://instagram.com" target="_blank">
                        <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/instagram.svg" alt="Instagram" width="32" height="32">
                    </a>
                </div>
            </div>
        </div>

        <hr style="border-color: rgba(255,255,255,0.2);">

        <div class="text-center">
            <small class="text-white-50">&copy; <?php echo date("Y"); ?> SkillBridge. All rights reserved.</small>
        </div>
    </div>
</footer>

<style>
.footer a:hover {
    color: #00bfff !important;
    text-decoration: underline;
}
</style>
