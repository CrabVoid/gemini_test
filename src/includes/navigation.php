<?php
// =========================================================================
// SECTION: Navigation Component
// Purpose: Reusable navigation partial for consistent site-wide navigation.
// Usage: Include this file at the top of any view to display navigation.
// =========================================================================

/**
 * Get the current page name based on the request
 * @return string The name of the current page
 */
function getCurrentPage() {
    $script = basename($_SERVER['SCRIPT_NAME']);
    
    if ($script === 'index.php' || $script === 'home.php') {
        return 'home';
    } elseif ($script === 'customers.php') {
        return 'customers';
    } elseif ($script === 'orders.php') {
        return 'orders';
    }
    
    return '';
}

$currentPage = getCurrentPage();
?>

<!-- Navigation Component -->
<nav style="background: #2c3e50; padding: 0; margin: 0 -20px 30px -20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; padding: 0 20px;">
        <!-- Logo / Home Link -->
        <a href="/" style="display: flex; align-items: center; padding: 15px 20px; color: white; text-decoration: none; font-weight: bold; font-size: 1.3em; border-right: 1px solid #34495e;">
            📊 Tasker
        </a>
        
        <!-- Navigation Links -->
        <div style="display: flex; flex: 1; margin-left: 0;">
            <!-- Home Link -->
            <a href="/" style="padding: 15px 20px; color: white; text-decoration: none; transition: background 0.2s; background: <?php echo ($currentPage === 'home') ? '#3498db' : 'transparent'; ?> 100%;" 
               onmouseover="this.style.background='#3498db'" onmouseout="this.style.background='<?php echo ($currentPage === 'home') ? '#3498db' : 'transparent'; ?>'">
                🏠 Home
            </a>
            
            <!-- Customers Link -->
            <a href="/customers.php" style="padding: 15px 20px; color: white; text-decoration: none; transition: background 0.2s; background: <?php echo ($currentPage === 'customers') ? '#3498db' : 'transparent'; ?> 100%;" 
               onmouseover="this.style.background='#3498db'" onmouseout="this.style.background='<?php echo ($currentPage === 'customers') ? '#3498db' : 'transparent'; ?>'">
                👥 Customers
            </a>
            
            <!-- Orders Link -->
            <a href="/orders.php" style="padding: 15px 20px; color: white; text-decoration: none; transition: background 0.2s; background: <?php echo ($currentPage === 'orders') ? '#3498db' : 'transparent'; ?> 100%;" 
               onmouseover="this.style.background='#3498db'" onmouseout="this.style.background='<?php echo ($currentPage === 'orders') ? '#3498db' : 'transparent'; ?>'">
                📦 Orders
            </a>
        </div>
        
        <!-- Right-side Actions -->
        <div style="display: flex; gap: 0;">
            <!-- Initialize DB Link -->
            <a href="/init-db.php" style="padding: 15px 20px; color: white; text-decoration: none; transition: background 0.2s; border-left: 1px solid #34495e;" 
               onmouseover="this.style.background='#e74c3c'" onmouseout="this.style.background='transparent'">
                🔄 Reset
            </a>
        </div>
    </div>
</nav>

<!-- =========================================================================
END SECTION: Navigation Component
========================================================================= -->
