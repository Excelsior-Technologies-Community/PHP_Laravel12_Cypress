#  PHP_Laravel12_Cypress

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Cypress](https://img.shields.io/badge/Cypress-E2E-green)
![MySQL](https://img.shields.io/badge/Database-MySQL-orange)

---

##  Overview

This project demonstrates how to integrate **Laravel 12** with **Cypress End-to-End (E2E) testing** using **Laracasts Cypress**.
It includes authentication using **Laravel Breeze**, separate testing database configuration, and backend-driven Cypress testing (without UI typing).

---

##  Features

* Laravel 12 setup
* Laravel Breeze Authentication
* MySQL database configuration
* Separate testing database
* Cypress E2E setup
* Laracasts Cypress integration
* Backend login (no UI interaction)
* Automatic migrate:fresh before tests

---

##  Project Structure

```
PHP_Laravel12_Cypress/
│
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── tests/
│   └── cypress/
│       ├── integration/
│       │   └── login.cy.js
│       ├── plugins/
│       ├── support/
│       ├── screenshots/
│       └── videos/
├── .env
├── .env.cypress
├── cypress.config.js
└── composer.json
```

---

##  Requirements

Install the following first:

* PHP 8.2+
* Composer
* Node.js + npm
* MySQL
* XAMPP (Optional)

---

## STEP 1 — Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Cypress
```

---

##  STEP 2 — Database Setup

### Create Databases

Create 2 databases in MySQL:

* Cypress
* Cypress_testing

### Configure `.env`

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Cypress
DB_USERNAME=root
DB_PASSWORD=
```

### Run Migration

```bash
php artisan migrate
```

---

##  STEP 3 — Install Authentication (Laravel Breeze)

```bash
composer require laravel/breeze --dev

php artisan breeze:install

npm install

npm run build

php artisan migrate
```

### Available Routes

* /login
* /register
* /dashboard

---

##  STEP 4 — Install Cypress

```bash
npm install cypress --save-dev

npx cypress open
```

After folders are created → Close Cypress.

---

##  STEP 5 — Install Laracasts Cypress

```bash
composer require laracasts/cypress --dev

php artisan cypress:boilerplate
```
<img width="589" height="287" alt="Screenshot 2026-03-02 163920" src="https://github.com/user-attachments/assets/2eba0968-140d-480f-b54a-a899d91d8da7" />

---

##  STEP 6 — Configure Testing Environment

### Edit `.env.cypress`

```
APP_NAME=cypress
APP_ENV=local
APP_KEY=base64:W0amejyDKaq+i6xPfhGrV4KjADj7u6JcdztJFcta+5Q=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Cypress_testing
DB_USERNAME=root
DB_PASSWORD=
```

### Run Testing Migration

```bash
php artisan migrate --env=cypress
```

---

##  STEP 7 — Fix CSRF Endpoint (IMPORTANT)

Open file:

```
tests/cypress/support/laravel-commands.js
```

Make sure this exists:

```js
url: '/__cypress__/csrf_token',
```

 Use DOUBLE underscore: `__cypress__`

---

##  STEP 8 — Configure Cypress

Open:

```
cypress.config.js
```

```js
const { defineConfig } = require('cypress')

module.exports = defineConfig({
    chromeWebSecurity: false,
    retries: 2,
    defaultCommandTimeout: 5000,
    watchForFileChanges: false,
    videosFolder: 'tests/cypress/videos',
    screenshotsFolder: 'tests/cypress/screenshots',
    fixturesFolder: 'tests/cypress/fixture',

    e2e: {
        baseUrl: 'http://127.0.0.1:8000',

        setupNodeEvents(on, config) {
            return require('./tests/cypress/plugins/index.js')(on, config)
        },

        specPattern: 'tests/cypress/integration/**/*.cy.{js,jsx,ts,tsx}',
        supportFile: 'tests/cypress/support/index.js',
    },
})
```

 DO NOT use `localhost`

---

##  STEP 9 — Start Laravel in Cypress Mode

```bash
php artisan serve --env=cypress
```

### Test CSRF Route

Open in browser:

```
http://127.0.0.1:8000/__cypress__/csrf_token
```
<img width="449" height="117" alt="Screenshot 2026-03-02 174811" src="https://github.com/user-attachments/assets/b979bdb3-42bb-41bc-afe2-713d5b8ea5e9" />


If token appears → Setup is correct 

---

##  STEP 10 — Create Cypress Test

Create file:

```
tests/cypress/integration/login.cy.js
```

```js
describe('Dashboard Test', () => {

    beforeEach(() => {
        cy.refreshDatabase()
    })

    it('User can login without UI', () => {

        // Create user
        cy.create('App\\Models\\User', {
            name: 'Harry',
            email: 'test@example.com',
            password: 'password'
        })

        // Direct backend login
        cy.login({ attributes: { email: 'test@example.com' } })

        // Visit dashboard
        cy.visit('/dashboard')

        // Assertion
        cy.contains('Dashboard')

    })

})
```

---

##  STEP 11 — Run Cypress

```bash
npx cypress open
```

1. Cypress E2E Testing Configuration

   <img width="1186" height="794" alt="Screenshot 2026-03-02 172643" src="https://github.com/user-attachments/assets/ad7b1ee9-7afa-4daa-bcae-81d0d8b3fa97" />
---
2. Browser Selection Screen

   <img width="1187" height="793" alt="Screenshot 2026-03-02 173013" src="https://github.com/user-attachments/assets/d7c16d8c-d252-4ce2-9ed6-3a5349f411a6" />
---
3. Cypress Test Runner – Spec List ( Click: login.cy.js )

   <img width="1919" height="1033" alt="Screenshot 2026-03-02 173133" src="https://github.com/user-attachments/assets/27114a87-d6bb-4efd-afc3-2358b1d1c4cd" />
---
4. Login Test Execution

<img width="1919" height="949" alt="Screenshot 2026-03-02 173522" src="https://github.com/user-attachments/assets/c4a62f4d-7b50-4607-8755-2c437a0e88ac" />

---




