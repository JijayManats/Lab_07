LAB 07 - Login, Registration & User Dashboard
================================================

WHAT CHANGED FROM THE ORIGINAL FILES
-------------------------------------
- initialize.php -> moved to includes/initialize.php. Now reads DB config
  from environment variables (with local-XAMPP defaults as a fallback),
  supports SSL (required by Aiven), and sets up database-backed sessions.
- Added includes/db_session_handler.php: stores PHP sessions in a
  `sessions` table instead of local disk files. This is required on
  Vercel, where each request can land on a fresh, ephemeral container --
  file-based sessions would make you appear logged out immediately.
- Added includes/auth.php with require_login() to protect pages.
- db.php is no longer needed; dropped from this version.
- The old single-file "Create New User" form is now:
    - login.php / login_process.php   (sign in)
    - register.php / register_process.php  (public sign up)
    - dashboard.php -> "Create User" tab (admin adds a user, via AJAX)
  Passwords are hashed with password_hash()/password_verify().
- user_records.php is now the "User Records" tab inside dashboard.php,
  loaded via AJAX with pagination, search, skeleton loading states, and
  a delete action (api/get_users.php, api/delete_user.php).
- Added vercel.json, .vercelignore, and certs/ for the Vercel + Aiven
  deployment described below.


OPTION A: RUN LOCALLY WITH XAMPP (unchanged workflow)
-------------------------------------------------------
1. Start Apache and MySQL in the XAMPP control panel.
2. Create the `lab_app` database, then run schema.sql in phpMyAdmin.
3. Copy this folder into C:\xampp\htdocs.
4. Open: http://localhost/lab_07_login_dashboard/
No environment variables needed -- it falls back to localhost/root/no
password/lab_app automatically.


OPTION B: DEPLOY WITH AIVEN (database) + VERCEL (hosting)
-------------------------------------------------------------

STEP 1 -- Create the database on Aiven
1. Sign up / log in at https://aiven.io and create a new service:
   choose "MySQL", pick a free/hobby plan and a cloud region close to you.
2. Wait for the service status to go green ("Running").
3. On the service's Overview page, note down:
     Host, Port, User (usually avnadmin), Password, and the default
     database name (usually `defaultdb`).
4. Download the CA certificate from the Overview page and save it into
   this project as: certs/aiven-ca.pem  (see certs/README.txt).
5. Open the Aiven Console's built-in query editor (or connect with any
   MySQL client using the details above) and run everything in
   schema.sql to create the `users` and `sessions` tables.

STEP 2 -- Push this project to GitHub
1. Create a new GitHub repo and push this whole folder to it
   (the certs/aiven-ca.pem file included -- it's just a public CA cert,
   not a secret, so it's fine to commit).

STEP 3 -- Deploy to Vercel
1. Go to https://vercel.com, sign in, and click "Add New... > Project".
2. Import the GitHub repo you just pushed.
3. Vercel will detect vercel.json, which tells it to run every .php
   file through the community "vercel-php" runtime -- no build step
   needed, leave the default settings.
4. Before deploying (or right after, then redeploy), go to
   Project Settings > Environment Variables and add:
     DB_HOST      = <Host from Aiven>
     DB_PORT      = <Port from Aiven>
     DB_USER      = <User from Aiven, e.g. avnadmin>
     DB_PASSWORD  = <Password from Aiven>
     DB_NAME      = <defaultdb, or whatever DB you created>
     DB_SSL       = true
5. Deploy. Once it's live, visit your-project.vercel.app -- you should
   land on the login page. Register an account, log in, and try both
   dashboard tabs.

NOTES
-----
- Tailwind is loaded via the CDN script (cdn.tailwindcss.com), so it
  works the same locally and on Vercel -- no build step required.
- Aiven requires an SSL/TLS connection; DB_SSL=true switches
  includes/initialize.php to connect with mysqli's ssl_set() using the
  certs/aiven-ca.pem file. Locally (DB_SSL unset), it connects to XAMPP
  the plain way, same as the original lab.
- Sessions live in the `sessions` table (see schema.sql) rather than on
  disk, since Vercel's filesystem is not persistent between requests.
- The vercel-php runtime is a community project, not an official Vercel
  runtime -- solid for a lab/coursework deployment, but worth knowing if
  you ever need official long-term support.
