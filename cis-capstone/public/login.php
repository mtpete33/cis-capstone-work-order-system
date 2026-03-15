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
       <h2>Login</h2>
       <p>Please enter your credentials to login. Use one of the following test accounts to test different user roles/permissions.</p>
       <table class="test-users-table">
          <thead>
          <tr>
             <td>Email</td>
             <td>Password</td>
             <td>Role / Access</td>
             </tr>
             </thead>
          <tbody>
          <tr>
             <td>admin@school.edu</td>
             <td>password123</td>
             <td><span class="role-chip">Admin</span><br>Can view all work orders.</td>
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

             <p class="helper-text">For demo purposes, use one of the test accounts listed on the left.</p>
          </section>
          </div>
    </main>
     

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
       $(document).ready(function() {
          $('#loginForm').on('submit', async function (e) {
             e.preventDefault();
            $('#errorBox').hide().text('');

          const payload = {
             email: $('#email').val().trim(),
              password: $('#password').val()
          };

         try {
            const res = await fetch('/api/auth/login.php', {
               method: 'POST',
               headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
            });

           const data = await res.json();
           if (!res.ok || !data.ok) {
             $('#errorBox').show().text(data.error || 'Login failed');
             return;
           }
           window.location.href = '/';
           } catch (err) {
            $('#errorBox').show().text('Network error. Please try again.');
           }
       });
          });
       
</script>
</body>
</html>