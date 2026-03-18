<<<<<<< HEAD
# Mysql-marketplace-webapp
My cs306 database systems course project
=======
## 🗂️ Directory Structure & Documentation

The application's logic is split into two primary domains: **Admin** and **User**, located within the `scripts/` directory. The project utilizes a polyglot persistence architecture, combining **MySQL** for robust relational data (listings, users, transactions) and **MongoDB** for flexible document storage (support tickets).

---

### 🛡️ `scripts/admin`
Provides the administrative interface for managing the marketplace and user support requests.

*   **Dashboard & Authentication:**
    *   `index.php`: The primary administrative dashboard post-login.
    *   `login.php` / `register.php` / `logout.php`: Administrative authentication system.
*   **Support Ticket Management (MongoDB):**
    *   `tickets.php`: Connects to MongoDB to fetch and display a dashboard of all active support tickets submitted by users across the platform.
    *   `ticket_view.php`: Grants administrators the ability to view the full details of a specific ticket, interact, and manage its lifecycle (e.g., closing tickets).

---

### 👤 `scripts/user`
Contains the core user-facing functionality, ranging from marketplace listing generation to interacting with the support system.

*   **Dashboard & Authentication:**
    *   `index.php`: The main landing dashboard for authenticated users.
    *   `login.php` / `register.php` / `logout.php`: Standard user authentication and session management.

*   **Listing Procedures (MySQL Stored Procedures):**
    These files handle complex database inserts using MySQL transactions and **Stored Procedures**. They safely link `Locations`, `Dates`, `Prices`, and category-specific tables together.
    *   `procedure_electronics.php`: UI and transaction logic for publishing an Electronics listing (utilizing `sp_create_electronics_listing`).
    *   `procedure_house.php`: Transaction logic tailored for Real Estate / House listings.
    *   `procedure_vehicle.php`: Transaction logic tailored for Vehicle listings.
    *   `procedure_listing.php`: General listing routing and base procedures.

*   **Constraint & Trigger Testing:**
    These interfaces are designed to demonstrate and test database-level **Triggers** and constraints ensuring data integrity.
    *   `trigger_user.php`: Tests user insertion constraints (e.g., preventing duplicate name/phone combinations) and ensures secure password hashing.
    *   `trigger_electronics.php`: Demonstrates trigger events specific to electronics data.
    *   `trigger_house.php`: Demonstrates trigger events specific to house listings.
    *   `trigger_vehicle.php`: Demonstrates trigger events specific to vehicle listings.

*   **Support & Ticketing (MongoDB):**
    *   `mongo_connect.php`: Initializes the MongoDB connection mapped to the `marketplace.tickets` collection.
    *   `support_create.php`: Form enabling users to generate and submit new support tickets as MongoDB documents.
    *   `support_list.php`: Queries MongoDB to list all historical and active tickets opened by the current user.
    *   `support_view.php` / `support_confirm.php`: Handles the viewing of a specific ticket's replies and user-led state confirmations.

---

### 🛠️ Key Technologies & Architecture Highlights
*   **Dual-Database System:** 
    *   **MySQL:** Engineered to handle the highly structured aspects of the marketplace. Utilizes **Stored Procedures** for atomic, multi-table inserts and **Triggers** to enforce strict business rules at the database level.
    *   **MongoDB:** Utilized exclusively for the Support Ticketing system, benefiting from a document-based NoSQL structure that excels at handling dynamic, unstructured messaging logs and user feedback.
*   **Security:** Features comprehensive SQL injection protection via PHP Prepared Statements (`$mysqli->execute()`) and secure cryptographic credential storage using `password_hash()`.
>>>>>>> 18788cb (Initial commit: MySQL Marketplace WebApp)
