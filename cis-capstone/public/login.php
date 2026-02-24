<?php ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title> Login </title>
        <link rel="stylesheet" href="/public/assets/login.css">
   </head> 

  <body>
    <main style="max-width: 420px; margin: 40px auto; padding: 16px;">
       <h1>Login</h1>
       <form id="loginForm">
          <label>Email<br>
          <input type="email" id="email" required>
          </label>
           <br><br>
            <label>Password<br>
            <input type="password" id="password" required>
            </label>
             <br><br>
             <button type="submit">Login</button>
       </form>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
       $(document).ready(function() {
          $('#loginForm').on('submit', async function (e) {
             e.preventDefault();
            $('errorBox').hide().text('');

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
             $('errorBox').show().text(data.error || 'Login failed');
             return;
           }
           window.location.href = '/';
           } catch (err) {
            $('errorBox').show().text('Network error. Please try again.');
           }
       });
          });
       
</script>
</body>
</html>