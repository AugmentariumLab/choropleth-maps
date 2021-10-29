# Choropleth Visualizer
This is an interactive choropleth map which can visualize average road distances to various points of interests in the SF Bay Area.
For instance, it can visualize the distances from Airbnb listings to their nearest health care facilities and average the results by the zip code of the Airbnb listing.

## Running using docker (Recommended)
1. Clone this git repository.
2. Fill in `APP_MAPBOX_ACCESS_TOKEN` in `docker-compose.yml` with a [token from Mapbox](https://account.mapbox.com/access-tokens/).
3. Run `docker-compose up --build`. Note that the first run will download and import the roads database which may take a while.
4. Navigate to `localhost:8000` in your browser and login with email `admin@example.com` and password `password`.

## Running manually
This is a Laravel project. To get started, do the following:
1. Install PHP and JS dependencies with `composer install` and `npm ci`.
2. Copy `.env.example` to `.env` and fill out the configuration. Create any necessary databases and add your the mapbox token.
3. Run `php artisan key:generate` which will generate an application key.
4. Generate compiled JS and CSS with `npm run dev` or `npm run prod`.
5. For development, run `php artisan serve` and navigate to `localhost:8000`.
6. For deployment, point your web server with PHP to the public folder.

### Importing databases
This project relies on two databases:
1. The roadsindb database can be downloaded [here](https://roadsindb.com/what-is-our-technology/demo-time/). Adding `CREATE SCHEMA bayarea; SET search_path = bayarea;` to the beginning of `bayarea_plsql.sql` and import it into your roads database.
2. Import `./scripts/roads_db/bayarea_uploaded_datasets` into the roads database.
3. Create a user by running `php artisan tinker < ./scripts/login_db/create_user.sh`.
