/**
 * Authentication management.
 */
export function logout() {
    // Redirect to the logout handler in PHP
    const isDashboard = window.location.pathname.includes('/admin/');
    const logoutUrl = isDashboard ? 'view_admin.php?logout=1' : 'index.php?logout=1';
    window.location.href = logoutUrl;
}

export function handleExpired() {
    console.warn("Session expired. Redirecting...");
    logout();
}
