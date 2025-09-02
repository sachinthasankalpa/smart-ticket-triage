# Smart Ticket Triage

A Laravel 11 + Vue 3 application for classifying and triaging support tickets. It runs with Laravel Sail (Docker), MySQL, Redis, and a dedicated background worker service.

## Quick Start

1) Clone the repository
   - Why: Get the project source locally.
   ```bash
   git clone https://github.com/sachinthasankalpa/smart-ticket-triage.git smart-ticket-triage
   cd smart-ticket-triage 
   ```

2) Create your environment file
   - Set at least:
     - QUEUE_CONNECTION=redis (so jobs go to Redis)
     - OPENAI_API_KEY=... (required if AI classification is enabled)
   ```bash
   cp .env.example .env
   ```

3) Install PHP dependencies
   ```bash
   composer install
   ```
   - If composer is not installed locally
   ```bash
   docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
   ```

4) Start the Dockerized stack with Sail
   - Boots PHP (laravel.test), MySQL, Redis, and the background worker.
   ```bash
   ./vendor/bin/sail up -d
   ```

5) Generate app key 
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6) Run database migrations and seeders
   ```bash
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

7) Install Node.js dependencies
   ```bash
   npm install
   ```

8) Build front‑end assets
   ```bash
   npm run dev
   # or npm run build
   ```

9) Open the app
   - Visit: http://localhost

10) (Optional) Run bulk classification
   ```bash
   ./vendor/bin/sail artisan tickets:bulk-classify
#./vendor/bin/sail artisan tickets:bulk-classify --count=1000 --only-unclassified=false --chunk=100
   ```

## Notes

- Background jobs: You do NOT need to run `queue:work` manually. The `worker` service is started by Sail and processes the Redis queue automatically.
- Supervisor + Horizon: The worker container runs Horizon under Supervisor to ensure the process is kept alive. 
- After the stack is up, open the Horizon dashboard at /horizon (e.g., http://localhost/horizon)

## Assumptions & Trade-offs

- Docker development via Sail
    - Assumption: Docker is installed.
    - Startup is straightforward, no separate MySQL/Redis/worker setup required.

- Authentication & Authorization
    - Absence of a user authentication system.

- State Management
    - The frontend uses Vue's built-in reactive function to create simple, global stores (tickets.js, theme.js, etc.). This approach was chosen to adhere to the "minimal third-party packages" constraint.
    - The trade-off is that it lacks the structured organization, developer tools, and advanced features of a dedicated state management library like Pinia.

- Vue in a simple build setup
    - Assumption: Vue is bundled with the rest of the app assets for a straightforward DX.

- Testing: 
    - The project does not include an automated testing. This was a deliberate trade-off to focus on delivering the required features within the time constraints.

## What I’d do with more time

- Robustness and scalability
    - Introduce idempotency and deduplication on classification jobs.
    - Add backoff/retry strategies tuned to API error types and rate limits.
    - Cache computed statistics and invalidate on writes to reduce DB load.

- Implement Full Authentication
    - Integrate Laravel Sanctum for API authentication and create a full login/registration flow on the Vue frontend.

- Adopt Pinia for State Management: 
    - Refactor the simple reactive stores into a formal Pinia store. This would provide better structure, devtools support for easier debugging, and a more scalable pattern for managing application state.

- Build a Robust Testing Suite: 
  - Backend: 
    - Write feature tests with Pest/PHPUnit to cover all API endpoints and business logic.
  - Frontend: 
    - Use Vitest and Vue Testing Library to write unit and integration tests for all Vue components.

- Styling quality
    - Improve the use of BEM naming conventions.

- Advanced Frontend Filtering: 
    - Add more advanced, client-side controls to the ticket list to filter by status or category.
