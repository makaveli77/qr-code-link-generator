# Production Queue Worker Setup

1. Ensure your .env (or .env.production) contains:
   QUEUE_CONNECTION=redis
   REDIS_HOST=redis

2. The docker-compose.yml already defines a `queue` service:
   - It runs `php artisan queue:work redis --sleep=3 --tries=3`
   - It depends on both the app and redis services
   - It has a healthcheck and restart policy for robustness

3. To scale workers:
   docker-compose up --scale queue=3 -d
   (This will run 3 queue workers in parallel)

4. To check failed jobs:
   docker-compose exec laravel-app php artisan queue:failed

5. To retry failed jobs:
   docker-compose exec laravel-app php artisan queue:retry all

6. For production, ensure APP_ENV=production and APP_DEBUG=false

7. Monitor your queue with Laravel Horizon for advanced usage (optional)

# End of instructions
