
CLI:
docker-compose exec postgres psql -U laravel_user -d laravel_db


🧭 Connection & Navigation

    psql -U username -d dbname — Connect to a database

    \c dbname — Switch to another database

    \q — Quit the psql shell

    \conninfo — Show current connection info

📚 Database & Table Management

    \l — List all databases

    \dt — List all tables in the current database

    \d table_name — Describe a table (columns, types, constraints)

    \d+ table_name — Detailed table info including size

    \dv — List views

    \df — List functions

    \di — List indexes

👤 User & Role Management

    \du — List all users and roles

    CREATE ROLE username LOGIN PASSWORD 'secret'; — Create a new user

    GRANT ALL PRIVILEGES ON DATABASE dbname TO username; — Grant access

🔍 Querying & Data Exploration

    SELECT * FROM table_name; — Fetch all rows

    SELECT COUNT(*) FROM table_name; — Count rows

    \x — Toggle expanded output (great for wide tables)

    EXPLAIN SELECT ...; — Show query plan

📦 Import/Export & File Operations

    \copy table_name FROM 'file.csv' CSV HEADER; — Import CSV

    \copy (SELECT * FROM table_name) TO 'file.csv' CSV HEADER; — Export CSV

    \i filename.sql — Run SQL commands from a file

    \! bash_command — Run shell commands (e.g. \! ls)

🧠 Help & Meta Commands

    \? — List all psql meta-commands

    \h — Help on SQL syntax (e.g. \h SELECT)

    \s — Show command history

    \e — Open editor to write a query


 View Table Structure

\d+ products

=============================================================

docker-compose exec postgres psql -U laravel_user -d laravel_db -c "\dt"

===============================================================

Option: Using pgAdmin (GUI)

    Access pgAdmin at http://localhost:8082

        Login: admin@admin.com / admin

    Connect to PostgreSQL Server

        Host: postgres (Docker container name)

        Port: 5432

        Username: laravel_user

        Password: secret

    View/Create Tables

        Expand Servers > PostgreSQL > Databases > laravel_db > Schemas > public > Tables

        Right-click → Create > Table

        Or run SQL queries in the Query Tool.


=================================================================

If you don't see any servers listed in pgAdmin after logging in, you need to manually add the PostgreSQL server connection. Here's how to do it step by step:
Steps to Add PostgreSQL Server in pgAdmin:

    Log in to pgAdmin

        Go to: http://localhost:8082

        Email: admin@admin.com

        Password: admin

    Right-click "Servers" → Register → Server...

        This opens the "Register - Server" dialog.

    General Tab

        Name: Give it a friendly name (e.g., Laravel PostgreSQL).

    Connection Tab

        Host name/address: postgres (this is the Docker container name, not localhost)

        Port: 5432

        Maintenance database: laravel_db

        Username: laravel_user

        Password: secret (as defined in docker-compose.yml)

    Click "Save"

Why This Works:

    Since both postgres and pgadmin are on the same Docker network (laravel), they can communicate using the container name (postgres) as the hostname.

    Do not use localhost or 127.0.0.1—these would refer to the pgAdmin container itself, not the PostgreSQL container.


    -----------------------------------


docker-compose build app  # If using Dockerfile
docker-compose up -d


check postgre php extensions are installed:
    docker-compose exec app php -m | findstr pgsql