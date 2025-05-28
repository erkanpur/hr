<?php

class HomeController {
    public function index() {
        // Data to pass to the view (and then to the layout)
        global $title; // Or use a more structured way to pass data
        $title = 'Dashboard';

        // Normally, you would load a view file here.
        // For now, just echo some content.
        echo "<h2>Welcome to the HRMS Dashboard!</h2>";
        echo "<p>This is the main page content loaded by the HomeController.</p>";

        // Example of how to use the Database connection (optional for this step)
        /*
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            // $stmt = $conn->query("SELECT 'Database Connected' AS message;");
            // $result = $stmt->fetch();
            // echo "<p>" . htmlspecialchars($result['message']) . "</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Database Connection Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        */
    }

    public function about() {
        global $title;
        $title = 'About Us';
        echo "<h2>About HRMS</h2><p>This is the Human Resources Management System.</p>";
    }
}

?>
