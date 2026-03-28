Feb 8th 2026
-Created initial project directory structure for work order system: api/, config/, public/, sql/
-Configured Replit to use php82 using router.php.
-Implemented a central database connection helper (db.php) uses PDO and env variables.
-Created health check API endpoing /api/health.php to verify routing database connectivity.
-Verified PHP router is working correctly
-Database connection is verified
-Ready for auth and session management

Next steps: implement session handling and user auth - login/logout, protected dashboard view

Feb 17th 2026
- Added SQL database files into project
- Began authentication build
- Created login/logout/auth API endpoints and simple login UI
- Fixed a routing bug
- Project is ready for testing full logins
- Database and auth foundation is set up

Next to do: Test user login, verify sessions working, work on dashboard behavior.

Feb 23rd 2026
- Fixed an client-side error that was happening when login.php would load.
- Used shell to hash a new password and verified it was hashed in the database
- Tested login
- Verified password handling using password_verify() against the hashed password in the database
- Fixed a routing bug where the logout link redirected to the API path instead of the login page
- Verified logout destroys session

Next steps: Build out dashboard and UI, form/form validation, begin to work with CRUD operations and role-based permissions

3/1/2026
- Implemented dashboard "who am i" session check using /api/auth/check.php
- Built the initial dashboard summary API endpoint dashboard/summary.php
- Resolved some routing issues in router.php
- built the Create Work Order UI and API endpoint and validated form submission
- Successfully inserted new work order entry into the database tied to the logged in userID

Challenges: difficulties pushing to GitHub through Replit. Trying to use remote SSH or personal access tokens from Github doesn't seem to work, so I am having to ask the Replit Agent to push to GitHub for me.

Next steps: work on role-based behavior, improve UI, search/filtering existing work orders, work on editing/updating status changes to work orders

3/8/2026
- UI updates: three-card button system added, single-page app design, added some colors and other styling
- Backend: created search.php endpoint that returns filtered work orders for the search panel, added role-based logic so user only sees work orders for their role
- Added endpoints: statuses.php to return work order statuses from DB, and priorities.php to return priority options from DB for UI dropdown

Challenges: still having trouble with pushing to GitHub-- Ended up creating a second repo to try to avoid the initial problems. Now using the Visual Studio Code terminal to push instead of using Replit Shell. So far it seems better.

Next steps: continue improving UI for simple and intuitive interaction between user and the system, add logic for editing and updating status changes, allow only Admin users to change statuses, etc.

3/15/26
- Created statuses.php, priorities.php, and search.php for search filtering
- Pushing to GitHub is now working using the built-in Replit Git tool

Next steps: Continue improving UI. Add functionality for updating work order status and allowing admin user to assign technicians to work orders.


3/22/2026
- Fixed some bugs in app.js, index.php, and update.php because I was getting console errors when trying to load Recent work orders, and searching / assigning work orders. Now the Admin user can assign work orders to the Technician user.

3/28/2026
- Most of the basic intended functionality is built. Some CSS updates, and maybe some nice-to-have features may be added
- Currently no console errors after testing, and no PHP errors.
- Added some CSS styling to the admin actions elements in the search results table
- Started add explanatory comments to index.php, login.php, and app.js

To do: Continue adding comments to explain how the code works.