#!/bin/bash

cd /tmp && \
wget https://s3.amazonaws.com/spatialdatabase/bayarea/oracles_bayarea_0_127.out.tar.gz && \
wget https://s3.amazonaws.com/spatialdatabase/bayarea/bayarea_plsql.sql && \
tar xzf oracles_bayarea_0_127.out.tar.gz && \
sed -i '1s;^;CREATE SCHEMA IF NOT EXISTS bayarea\;\nSET search_path = bayarea\;\n;' bayarea_plsql.sql && \
psql -U $POSTGRES_USER $POSTGRES_DB < ./bayarea_plsql.sql