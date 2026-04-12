<?php ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="styles.css">
      <link rel="icon" href="data:,">
       <title> Login </title>
   </head> 

  <body>
     <header class="login-header">
        <div class="login-header-inner">
           <h1>Maintenance Work Order System</h1>
        </div>
        </header>
     <div class="login-subbar"></div>
     
    <main class="login-page">
       <div class="login-grid">
          <section class="card">
       <h2>Credentials</h2>
       <p>Please enter your credentials to login. Use one of the following test accounts to test different user roles/permissions.</p>
             <div class="table-scroll">
       <table class="test-users-table">
          <thead>
          <tr>
             <th>Email</th>
             <th>Password</th>
             <th>Role / Access</th>
             </tr>
             </thead>
          <tbody>
          <tr>
             <td>admin@school.edu</td>
             <td>password123</td>
             <td><span class="role-chip">Admin</span><br>Can view all work orders, update work order status, and assign/re-assign work orders to technicians.</td>
             </tr>
          <tr>
             <td>tech@school.edu</td>
             <td>password123</td>
             <td><span class="role-chip">Technician</span><br>Can view work orders assigned to them, or currently unassigned.</td>
             </tr>
          <tr>
             <td>requester@school.edu</td>
             <td>password123</td>
             <td><span class="role-chip">Requester</span><br>Can view work orders they submitted.</td>
             </tr>
          </tbody>
          </table>
          </div>
              </section>

          <section class="card">
            <h2>Sign In</h2>

             
       <div id="errorBox"></div>
             
       <form id="loginForm" class="login-form">
          <div class="form-row">
          <label for="email">Email</label>
          <input type="email" id="email" required>
              </div>
          <div class="form-row">
            <label for="password">Password<br></label>
            <input type="password" id="password" required>
       </div>
          
          <button type="submit" class="login-btn">Login</button>
       </form>

             <p class="helper-text">For demo purposes, use one of the test account logins listed.</p>
          </section>
          </div>
    </main>
     

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
       $(document).ready(function() {
          // attach a submit handler to the login form
          $('#loginForm').on('submit', async function (e) {
             // stop the form from normal submission behavior
             e.preventDefault();
             // clear any previous error messages
            $('#errorBox').hide().text('');

             // Build the data object we want to send to the login API endpoint
             // email is trimmed to remove any accidental whitespace
             // password is not trimmed as spaces are allowed in passwords
          const payload = {
             email: $('#email').val().trim(),
              password: $('#password').val()
          };

         try {
            // Send the login request to the API endpoint
            const res = await fetch('/api/auth/login.php', {
               method: 'POST',
               headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
            });
           // Parse the JSON response
           const data = await res.json();

            // If the response is not OK or the data indicates failure, show the error
           if (!res.ok || !data.ok) {
             $('#errorBox').show().text(data.error || 'Login failed');
             return;
           }
            // If login is successful, redirect user to the dashboard
           window.location.href = '/';
           } catch (err) {
            // show a network error message if the request fails
            $('#errorBox').show().text('Network error. Please try again.');
           }
       });
          });
       
</script>
</body>
</html>