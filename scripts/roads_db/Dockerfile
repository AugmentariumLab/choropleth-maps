FROM postgres:14.0
RUN apt-get update && apt-get install -y wget
ADD bayarea_uploaded_datasets.sql /docker-entrypoint-initdb.d/
ADD download_roads.sh /docker-entrypoint-initdb.d/